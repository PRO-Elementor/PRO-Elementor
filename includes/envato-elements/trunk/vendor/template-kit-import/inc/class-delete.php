<?php
/**
 * Template Kit Delete:
 *
 * Deletes the template kit from wp_posts and cleans up the uploaded
 * folders and files.
 *
 * @package Envato/Envato_Template_Kit_Delete
 * @since 1.0.1
 */

namespace Envato_Template_Kit_Import;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Delete templage kit and cleanup.
 *
 * @since 1.0.1
 */
class Delete extends Base {

	public function __construct() {
		parent::__construct();
		add_action( 'before_delete_post', array( $this, 'cleanup_template_delete' ) );
	}

	/**
	 * Deletes a template kit when called via ajax
	 *
	 * @since 1.0.1
	 */
	public function delete_template_kit($template_kit_id) {
		wp_delete_post( $template_kit_id, true );
	}

	/**
	 * Clean up from template delete
	 *
	 * @since 1.0.1
	 * @param int $post_id The post id to let the things happen.
	 */
	public function cleanup_template_delete( $post_id ) {
		$post = get_post( $post_id );

		if ( CPT_Kits::get_instance()->cpt_slug === $post->post_type ) {
			require_once ABSPATH . '/wp-admin/includes/class-wp-filesystem-base.php';
			require_once ABSPATH . '/wp-admin/includes/class-wp-filesystem-direct.php';
			$file_system_direct = new \WP_Filesystem_Direct( false );

			$get_upload_dir       = wp_upload_dir();
			$template_kit_path    = $get_upload_dir ['basedir'] . '/template-kits/';
			$template_kit_id_path = get_post_meta( $post_id, 'envato_tk_folder_name', true );

			if ( ! $template_kit_id_path ) {
				return;
			}

			$full_template_kit_id_path = $template_kit_path . $template_kit_id_path;
			$file_system_direct->rmdir( $full_template_kit_id_path, true );
		}
	}
}
