<?php
/**
 * Envato Elements: Template Kit integration
 *
 * Template Kit.
 *
 * @package Envato/Envato_Elements
 * @since 2.0.0
 */

namespace Envato_Elements\Backend;

use Envato_Elements\Utils\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Handles the Template Kit integration.
 * This is our interface to the "Template Kit Import" code.
 *
 * @since 2.0.0
 */
class Template_Kits extends Base {

	public function __construct() {
		add_action( 'before_delete_post', array( $this, 'before_delete_post' ) );
	}

	public function before_delete_post( $post_to_be_deleted ) {
		$post = get_post( $post_to_be_deleted );
		if ( $post && 'elementor_library' === $post->post_type ) {
			// User may be deleting a template from an installed kit, reach into vendor code to handle that case:
			$this->load_template_kit_library();
			\Envato_Template_Kit_Import\Builder_Elementor::get_instance()->check_if_imported_template_is_getting_deleted( $post_to_be_deleted );
		} elseif ( $post && 'envato_tk_import' === $post->post_type ) {
			// User is removing an installed template kit completely.
			$recorded_download_event_id = get_post_meta( $post->ID, 'envato_elements_download_event', true );
			if ( $recorded_download_event_id ) {
				Downloaded_Items::get_instance()->remove_download_event( $recorded_download_event_id );
			}
		}
	}

	/**
	 * We bring in our Template Kit Import ZIP handling code.
	 */
	private function load_template_kit_library() {
		require_once ENVATO_ELEMENTS_DIR . 'vendor/template-kit-import/template-kit-import.php';
	}

	public function process_zip_file( $zip_file ) {
		$this->load_template_kit_library();

		// Reach into our included Template Kit import plugin functions to do the actual import.
		return \Envato_Template_Kit_Import\Importer::get_instance()->install_template_kit_zip_to_db( $zip_file );
	}

	/**
	 * Delete an imported template kit
	 *
	 * @param $template_kit_id
	 *
	 * @return array|\WP_Error
	 */
	public function delete_template_kit( $template_kit_id ) {
		$this->load_template_kit_library();

		// Reach into our included Template Kit import plugin functions to do the actual delete.
		\Envato_Template_Kit_Import\Delete::get_instance()->delete_template_kit( $template_kit_id );

		return true;
	}

	/**
	 * Returns an array of all installed template kits
	 *
	 * @return array
	 */
	public function get_installed_template_kits() {
		$this->load_template_kit_library();
		// Reach into our included Template Kit import plugin functions to do the actual import.
		$uploaded_kits  = \Envato_Template_Kit_Import\CPT_Kits::get_instance()->get_all_uploaded_kits();
		$installed_kits = [];
		try {
			foreach ( $uploaded_kits as $template_kit ) {
				$template_kit_id = $template_kit->ID;
				// Attempt to load this template kit to confirm it's a valid builder type, and get access to helper methods:
				$template_kit_manager = \Envato_Template_Kit_Import\envato_template_kit_import_get_builder( $template_kit_id );
				if ( $template_kit_manager ) {
					// Grab a list of templates from this kit, so we can use the first one as a screenshot url:
					$template_kit_templates = $template_kit_manager->get_available_templates();
					$installed_kits[]       = [
						'id'             => $template_kit_id,
						'screenshot_url' => $template_kit_manager->get_screenshot_url(),
						'title'          => $template_kit->post_title,
						'template_count' => count( $template_kit_templates ),
						'uploaded'       => date_i18n( 'F j, Y g:i:a', strtotime( $template_kit->post_date ) ),
						'builder'        => $template_kit_manager->builder,
					];
				}
			}
		} catch ( \Exception $e ) {

		}

		return $installed_kits;
	}

	/**
	 * Get a single installed template kit by ID
	 *
	 * @param $template_kit_id
	 *
	 * @return array|\WP_Error
	 */
	public function get_installed_template_kit( $template_kit_id ) {
		$this->load_template_kit_library();

		// Ask our vendored code for a reference to the installed template kit:
		$template_kit = \Envato_Template_Kit_Import\envato_template_kit_import_get_builder( $template_kit_id );
		if ( ! $template_kit ) {
			return new \WP_Error( 'kit_not_found', 'Sorry this kit was not found' );
		}

		// Start building up template kit response
		$template_kit_data = [
			'id'           => $template_kit_id,
			'title'        => $template_kit->get_name(),
			'requirements' => $template_kit->get_requirements(),
			'templates'    => [],
			'builder'      => $template_kit->builder,
			'screenshot'   => $template_kit->get_screenshot_url(),
			'sourceZipUrl' => $template_kit->get_source_zip_url(),
		];

		if ( class_exists( '\Elementor\Plugin' ) ) {
			// Copied from Elementor implementation.
			$template_kit_data['elementorImportUrl'] = \Elementor\Plugin::$instance->app->get_base_url() . '#/import';
		}

		// Loop over available templates and include any additional data we might need in the UI:
		foreach ( $template_kit->get_available_templates() as $template_id => $template ) {
			$template['id']                   = $template_id;
			$template['template_kit_id']      = $template_kit_id;
			$template['unmet_requirements']   = $template_kit->get_list_of_unmet_requirements( $template );
			$template_kit_data['templates'][] = $template;
		}

		return $template_kit_data;
	}

	/**
	 * Import a single template from an installed template kit
	 *
	 * @param $template_kit_id
	 * @param $template_id
	 * @param $import_again
	 *
	 * @return array|\WP_Error
	 */
	public function import_single_template( $template_kit_id, $template_id, $import_again ) {
		$this->load_template_kit_library();

		include_once( ABSPATH . 'wp-admin/includes/image.php' );

		$template_kit = \Envato_Template_Kit_Import\envato_template_kit_import_get_builder( $template_kit_id );
		if ( ! $template_kit ) {
			return new \WP_Error( 'kit_not_found', 'Sorry this kit was not found' );
		}

		// If the user wants to import the template again, we don't check for duplicates.
		if ( ! $import_again ) {
			$template_kit_data = $this->get_installed_template_kit( $template_kit_id );

			if ( ! empty( $template_kit_data['templates'] ) && ! empty( $template_kit_data['templates'][ $template_id ] ) ) {
				// We've found a matching template for this template kit

				// Check if we've already imported this template:
				if ( ! empty( $template_kit_data['templates'][ $template_id ]['imports'] ) ) {
					$latest_import = array_pop( $template_kit_data['templates'][ $template_id ]['imports'] );
					if ( $latest_import && ! empty( $latest_import['imported_template_id'] ) ) {
						$latest_import_post = get_post( $latest_import['imported_template_id'] );
						if ( $latest_import_post && $latest_import_post->post_status === 'publish' ) {
							// We've already imported this template, don't import it again.
							return array(
								'imported_template_id' => $latest_import['imported_template_id'],
								'edit_url'             => $template_kit->get_imported_template_edit_url( $latest_import['imported_template_id'] ),
							);
						}
					}
				}
			}
		}

		$imported_template_id = \Envato_Template_Kit_Import\Importer::get_instance()->handle_template_import( $template_kit_id, $template_id );

		if ( is_wp_error( $imported_template_id ) ) {
			return $imported_template_id;
		}


		return array(
			'imported_template_id' => $imported_template_id,
			'edit_url'             => $template_kit->get_imported_template_edit_url( $imported_template_id ),
		);
	}

	/**
	 * Get a single template from an installed template kit
	 *
	 * @param $template_kit_id
	 * @param $template_id
	 *
	 * @return array|\WP_Error
	 */
	public function get_single_template_data( $template_kit_id, $template_id ) {
		$this->load_template_kit_library();

		$template_kit = \Envato_Template_Kit_Import\envato_template_kit_import_get_builder( $template_kit_id );
		if ( ! $template_kit ) {
			return new \WP_Error( 'kit_not_found', 'Sorry this kit was not found' );
		}

		return $template_kit->get_template_data( $template_id );
	}

	/**
	 * Install some custom CSS into the customizer for this template kit.
	 *
	 * @param $template_kit_id int The ID of the locally installed template kit
	 * @param $css_filename string This is the path to the CSS file in the ZIP, passed through from React
	 *
	 * @return bool|\WP_Error
	 */
	public function install_custom_css_into_customizer( $template_kit_id, $css_filename ) {
		$this->load_template_kit_library();

		$template_kit = \Envato_Template_Kit_Import\envato_template_kit_import_get_builder( $template_kit_id );
		if ( ! $template_kit ) {
			return new \WP_Error( 'kit_not_found', 'Sorry this kit was not found' );
		}

		return $template_kit->install_custom_css_into_customizer( $css_filename );
	}

}
