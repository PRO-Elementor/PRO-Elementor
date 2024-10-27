<?php
/**
 * Template Kit Import:
 *
 * This starts things up. Registers the SPL and starts up some classes.
 *
 * @package Envato/Envato_Template_Kit_Import
 * @since 0.0.2
 */

namespace Envato_Template_Kit_Import;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Importer registration and management.
 *
 * @since 0.0.2
 */
class Importer extends Base {

	/**
	 * Called when the user wants to import a single template.
	 *
	 * @param $template_kit_id
	 * @param $template_index
	 *
	 * @return array|\WP_Error
	 */
	public function handle_template_import( $template_kit_id, $template_index ) {

		$template_kit = envato_template_kit_import_get_builder( $template_kit_id );

		if ( ! $template_kit ) {
			return new \WP_Error( 'import_error', 'Invalid Template Kit' );
		}

		return $template_kit->import_template( $template_index );
	}

	/**
	 * Extract
	 *
	 * Performs the extraction of the zip files to a temporary directory.
	 * Returns an error if for some reason the ZipArchive utility isn't available.
	 * Otherwise, Returns a strnig containing the temporary extraction directory
	 *
	 * @param string $temporary_zip_file
	 * @param string $destination_folder
	 *
	 * @return string|\WP_Error
	 */
	private function unpack_template_kit_zip_to_folder( $temporary_zip_file, $destination_folder ) {
		if ( ! class_exists( '\ZipArchive' ) ) {
			return new \WP_Error( 'zip_error', 'ZipArchive extension not enabled. Please ask hosting provider or Google search "Enable ZipArchive for PHP".' );
		}

		$allowed_file_types = [ 'json', 'jpg', 'png', 'css', 'html' ];

		$zip = new \ZipArchive();

		$zip->open( $temporary_zip_file );

		$allowed_files = [];

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		for ( $i = 0; $i < $zip->numFiles; $i ++ ) {
			$filename  = $zip->getNameIndex( $i );
			$extension = pathinfo( $filename, PATHINFO_EXTENSION );

			if ( in_array( $extension, $allowed_file_types, true ) ) {
				$allowed_files[] = $filename;
			}
		}

		$zip->extractTo( $destination_folder, $allowed_files );

		$zip->close();

		$extracted_zip_files = scandir( $destination_folder );
		if ( $extracted_zip_files && is_array( $extracted_zip_files ) ) {
			$file_names = array_diff( $extracted_zip_files, array( '.', '..' ) );
		} else {
			$file_names = array();
		}
		if ( ! $file_names || ! in_array( 'manifest.json', $file_names, true ) ) {
			$this->delete_template_kit_folder( $destination_folder );

			return new \WP_Error( 'zip_error', 'Please upload a valid Template Kit zip file' );
		}

		return $destination_folder;
	}

	/**
	 * Recursively deletes a folder, used to clean up zip extraction.
	 *
	 * @param $folder
	 */
	private function delete_template_kit_folder( $folder ) {
		require_once ABSPATH . '/wp-admin/includes/class-wp-filesystem-base.php';
		require_once ABSPATH . '/wp-admin/includes/class-wp-filesystem-direct.php';
		$file_system_direct = new \WP_Filesystem_Direct( false );
		if ( $file_system_direct->is_dir( $folder ) ) {
			$file_system_direct->rmdir( $folder, true );
		}
	}

	/**
	 * Called when we want to unpack a ZIP file and install the various parts into WordPress
	 * Returns either the imported Kit ID in the WP database, or a WP Error if something went wrong.
	 *
	 * @param $temporary_zip_file
	 *
	 * @return int|\WP_Error
	 */
	public function install_template_kit_zip_to_db( $temporary_zip_file ) {
		$wp_upload_dir                  = wp_upload_dir();
		$template_kit_base_path         = $wp_upload_dir['basedir'] . '/template-kits/';
		$template_kit_base_url          = $wp_upload_dir['baseurl'] . '/template-kits/';
		$template_kit_random_folder     = md5( mt_rand() . time() );
		$template_kit_extract_directory = $template_kit_base_path . $template_kit_random_folder;
		$template_kit_extract_url       = $template_kit_base_url . $template_kit_random_folder;
		wp_mkdir_p( $template_kit_extract_directory );
		// Prevent directory indexing so people cannot find the random template kit ID folders:
		touch( $template_kit_base_path . 'index.php' );

		$unzip_result = $this->unpack_template_kit_zip_to_folder( $temporary_zip_file, $template_kit_extract_directory );

		if ( is_wp_error( $unzip_result ) ) {
			return $unzip_result;
		}

		// Here we assume we've got a valid template kit extracted to the users hosting account.
		// Time to throw it into the Custom Post Type so that we can proceed to the import step or do other fancy things.

		$manifest_data = json_decode( file_get_contents( $template_kit_extract_directory . '/manifest.json' ), true );
		// todo: validate manifest data structure.

		$page_builder = false;
		if ( $manifest_data && ! empty( $manifest_data['manifest_version'] ) && ! empty ( $manifest_data['page_builder'] ) ) {
			// This is one of the Envato kits.
			// page_builder will be 'elementor' or 'gutenberg'
			$page_builder = $manifest_data['page_builder'];
		}

		if ( $manifest_data && ! empty( $manifest_data['version'] ) ) {
			// We're a new 'elementor-kit' style template kit, we direct people over to Elementor import tool for this.
			$page_builder = ENVATO_TEMPLATE_KIT_IMPORT_TYPE_ELEMENTOR;
		}

		$post_data = array(
			'post_title'  => $manifest_data['title'],
			'post_type'   => CPT_Kits::get_instance()->cpt_slug,
			'post_status' => 'publish',
		);
		$post_id   = wp_insert_post( $post_data, true );

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			// successfully stored this post. Add some metadata
			update_post_meta( $post_id, 'envato_tk_manifest', $manifest_data );
			update_post_meta( $post_id, 'envato_tk_folder_name', $template_kit_random_folder );
			update_post_meta( $post_id, 'envato_tk_builder', $page_builder );

			// Keep a copy of the uploaded source zip file.
			// This is needed so the user can download the zip again if they need to move over to the new Elementor import tool.
			$source_file_name = 'source-' . (int) $post_id . '.zip';
			copy( $temporary_zip_file, $template_kit_extract_directory . '/' . $source_file_name );
			update_post_meta( $post_id, 'envato_tk_source_zip_url', $template_kit_extract_url . '/' . $source_file_name );

			return $post_id;
		}

		return new \WP_Error( 'zip_error', 'Failed to extract ZIP file' );
	}


}
