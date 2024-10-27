<?php
/**
 * Template Kit Import: Elementor
 *
 * Elementor template display/import.
 *
 * @package Envato/Envato_Template_Kit_Import
 * @since 0.0.2
 */

namespace Envato_Template_Kit_Import;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Elementor builder code.
 *
 * @since 0.0.2
 */
class Builder_Elementor extends Builder {

	/*
	 * Which builder this is for (e.g. 'elementor' or 'gutenberg')
	 *
	 * @var string
	 */
	public $builder = 'elementor';

	public function __construct() {
		parent::__construct();
		add_action( 'before_delete_post', array( $this, 'check_if_imported_template_is_getting_deleted' ) );
	}

	/**
	 * Imports a template to the Elementor page builder.
	 *
	 * @param $template_index
	 *
	 * @return \WP_Error|int
	 */
	public function import_template( $template_index ) {

		if ( ! $this->kit_id ) {
			return new \WP_Error( 'kit_error', 'Failed to load kit' );
		}

		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return new \WP_Error( 'plugin_error', 'Missing required plugin: Elementor' );
		}

		$template_data = $this->get_template_data( $template_index );

		return $this->import_json_file_to_elementor_library( $template_data['source'], $template_index );

	}

	/**
	 * Get details from the manifest file about a particular template within this kit.
	 *
	 * @param $template_index
	 *
	 * @return \WP_Error|array
	 */
	public function get_template_data( $template_index ) {
		$template_data = parent::get_template_data( $template_index );
		if ( $template_data && ! empty( $template_data['source'] ) ) {
			$template_kit_folder_name       = $this->get_template_kit_temporary_folder();
			$template_json_file             = $template_kit_folder_name . $template_data['source'];
			if(file_exists($template_json_file)) {
				$template_data['template_json'] = json_decode( file_get_contents( $template_json_file ), true );

				return $template_data;
			}
		}

		return new \WP_Error( 'template_error', 'Template Data Error. Please delete the Template Kit and import it again.' );
	}

	/**
	 * Reach out to Elementor to import a local JSON file into the library.
	 * Returns the ID of the locally imported post.
	 *
	 * @param $json_file_path
	 * @param $template_index
	 *
	 * @return int|\WP_Error
	 */
	private function import_json_file_to_elementor_library( $json_file_path, $template_index ) {
		// Found the template to import from the manifest file.
		$template_kit_folder_name = $this->get_template_kit_temporary_folder();
		$template_json_file       = $template_kit_folder_name . $json_file_path;
		$local_json_data          = json_decode( file_get_contents( $template_json_file ), true );
		$source                   = \Elementor\Plugin::$instance->templates_manager->get_source( 'local' );
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// Avoid Elementor Pro errors on import.
			// Hopefully Elementor fixes their import code to handle missing fields better.
			// This prevents our UI from breaking.
			// todo: check on wp-content/plugins/elementor-pro/modules/posts/widgets/posts.php:31
			// and remove this hacky fix once they validate the field exists before importing.
			ini_set( 'display_errors', false );
		}
		// This overwrites the 'template type' so our Elementor Free users can at least import some of the Pro templates.
		// As Elementor only accepts the path to a JSON file for importing we temporarily modify the JSON data and
		// send in that template file name to Elementor for processing. We delete the temporary JSON file after processing below.
		if ( ! empty( $local_json_data['metadata']['elementor_pro_required'] ) && ! class_exists( '\ElementorPro\Plugin' ) ) {
			$local_json_data['type'] = 'page';
		}

		// Elementor deletes JSON files after importing them, so we have to duplicate the JSON file before sending it over.
		require_once ABSPATH . '/wp-admin/includes/file.php';
		$temp_wp_json_file = wp_tempnam('elements-tk-import-');
		file_put_contents( $temp_wp_json_file, json_encode( $local_json_data ) );

		// Pass our temporary JSON file to Elementor for import:
		// FYI, newer versions of Elementor delete a JSON file after successful import
		$result = $source->import_template( basename( $temp_wp_json_file ), $temp_wp_json_file );

		// Cleanup above temporary file
		if ( file_exists( $temp_wp_json_file ) ) {
			unlink( $temp_wp_json_file );
		}

		if ( is_wp_error( $result ) ) {
			return new \WP_Error( 'import_error', 'Failed to import template: ' . esc_html( $result->get_error_message() ) );
		}

		if ( $result[0] && $result[0]['template_id'] ) {
			$imported_template_id = $result[0]['template_id'];
			$this->record_import_event( $template_index, $imported_template_id );
			// Check if we've got any display conditions to import:
			if ( $local_json_data['metadata'] && ! empty( $local_json_data['metadata']['elementor_pro_conditions'] ) ) {
				update_post_meta( $imported_template_id, '_elementor_conditions', $local_json_data['metadata']['elementor_pro_conditions'] );
			}
			if ( $local_json_data['metadata'] && ! empty( $local_json_data['metadata']['wp_page_template'] ) ) {
				// If there is a page template set we keep it the same here
				update_post_meta( $imported_template_id, '_wp_page_template', $local_json_data['metadata']['wp_page_template'] );
			}
			// Record some metadata so we can link back to kit from imported template:
			update_post_meta( $imported_template_id, 'envato_tk_source_kit', $this->kit_id );
			update_post_meta( $imported_template_id, 'envato_tk_source_index', $template_index );

			if ( $local_json_data['metadata'] && ! empty( $local_json_data['metadata']['template_type'] ) && 'global-styles' === $local_json_data['metadata']['template_type'] ) {
				// We set some metadata around the global template so Elementor can interpret them correctly:
				// From: wp-content/plugins/elementor/core/documents-manager.php:366
				update_post_meta( $imported_template_id, '_elementor_edit_mode', 'builder' );
				update_post_meta( $imported_template_id, '_elementor_template_type', 'kit' );
				// Set the global theme styles to this newly imported template:
				update_option( 'elementor_active_kit', $imported_template_id );

				// Update the kit styles title so we can display it nicely in the drop down settings UI.
				wp_update_post(
					array(
						'ID'         => $imported_template_id,
						'post_title' => 'Kit Styles: ' . $this->get_name(),
					)
				);
			}

			return $imported_template_id;
		}

		return new \WP_Error( 'import_error', 'Unknown import error' );
	}

	/**
	 * What text to display on input buttons
	 *
	 * @return string
	 */
	public function get_import_button_text() {
		return esc_html__( 'Import into Elementor Library', 'template-kit-import' );
	}

	/**
	 * Get the URL to the list of imported templates.
	 *
	 * @param $imported_template_id int - Optional imported template ID.
	 *
	 * @return string
	 */
	public function get_imported_template_edit_url( $imported_template_id = 0 ) {
		if ( $imported_template_id && class_exists( '\Elementor\Plugin' ) ) {
			$imported_template = \Elementor\Plugin::$instance->documents->get( $imported_template_id );
			if ( $imported_template ) {
				return $imported_template->get_edit_url();
			}
		}

		return admin_url( 'edit.php?post_type=elementor_library&tabs_group=library' );
	}

	/**
	 * Hook that runs when a user deletes a template from the Elementor library.
	 * This checks if the template getting deleted was imported from  us.
	 * If it was from us, we clean up our manifest metadata and flag this template as not "imported" any more
	 * so that the UI can show the correct buttons/status.
	 *
	 * @param $post_to_be_deleted
	 */
	public function check_if_imported_template_is_getting_deleted( $post_to_be_deleted ) {
		if ( class_exists( '\Elementor\Plugin' ) ) {
			$post = get_post( $post_to_be_deleted );
			if ( $post && 'elementor_library' === $post->post_type ) {
				$source_kit_id = get_post_meta( $post->ID, 'envato_tk_source_kit', true );
				if ( $source_kit_id ) {
					$source_kit_index = get_post_meta( $post->ID, 'envato_tk_source_index', true );
					try {
						$this->load_kit( $source_kit_id );
						$manifest = $this->get_manifest_data();
						if ( $manifest && ! empty( $manifest['templates'] ) ) {
							if ( isset( $manifest['templates'][ $source_kit_index ] ) && isset( $manifest['templates'][ $source_kit_index ]['imports'] ) ) {
								foreach ( $manifest['templates'][ $source_kit_index ]['imports'] as $import_id => $imported_data ) {
									if ( (int) $post_to_be_deleted === (int) $imported_data['imported_template_id'] ) {
										// we've found the record in the 'imports' manifest array that matches the imported template.
										// lets remove it!
										unset( $manifest['templates'][ $source_kit_index ]['imports'][ $import_id ] );
									}
								}
							}
						}
						update_post_meta( $this->kit_id, 'envato_tk_manifest', $manifest );
					} catch ( \Exception $e ) {
						// noop
					}
				}
			}
		}
	}

	/**
	 * Get any unmet requirements for importing this template to WordPress
	 *
	 * @param $template array
	 *
	 * @return array
	 */
	public function get_list_of_unmet_requirements( $template ) {
		$unmet_requirements = array();
		if ( ! empty( $template['elementor_pro_required'] ) && ! class_exists( '\ElementorPro\Plugin' ) ) {
			$unmet_requirements[] = __( 'Elementor Pro is required for this Template.', 'template-kit-export' );
		}
		return $unmet_requirements;
	}


	/**
	 * Gets any required theme for this Template Kit
	 *
	 * @return array
	 */
	public function get_required_theme() {
		$required_theme     = array(
			'slug' => 'hello-elementor',
			'name' => 'Hello Elementor',
		);
		$themes             = wp_get_themes();
		$current_theme      = wp_get_theme();
		$is_hello_installed = false;
		$is_hello_active    = false;
		foreach ( $themes as $theme ) {
			if ( $required_theme['slug'] === $theme->get_template() ) {
				$is_hello_installed = true;
			}
		}
		// get_template() checks for parent theme, so we handle a hello child theme using this just fine:
		if ( $required_theme['slug'] === $current_theme->get_template() ) {
			$is_hello_active = true;
		}
		$theme_action_url = admin_url( 'themes.php' );
		$theme_status     = $is_hello_active ? 'activated' : ( $is_hello_installed ? 'deactivated' : 'install' );
		if ( 'install' === $theme_status ) {
			// We want the user to install this theme
			$theme_action_url = add_query_arg( '_wpnonce', wp_create_nonce( 'install-theme_' . $required_theme['slug'] ), admin_url( 'update.php?action=install-theme&theme=' . $required_theme['slug'] ) );
		} elseif ( 'deactivated' === $theme_status ) {
			// We want the user to activate this theme
			$theme_action_url = add_query_arg( '_wpnonce', wp_create_nonce( 'switch-theme_' . $required_theme['slug'] ), admin_url( 'themes.php?action=activate&stylesheet=' . $required_theme['slug'] ) );
		}
		$required_theme['status'] = $theme_status;
		$required_theme['url']    = $theme_action_url;
		return $required_theme;
	}

	/**
	 * Gets url for entire kit screenshot
	 *
	 * @return string|false
	 */
	public function get_screenshot_url() {
		$template_kit_templates = $this->get_available_templates();

		if ( ! empty( $template_kit_templates[0]['screenshot_url'] ) ) {
			return $template_kit_templates[0]['screenshot_url'];
		}

		return false;
	}

	/**
	 * Gets list of kit requirements
	 *
	 * @return array
	 */
	public function get_requirements() {
		$requirements = parent::get_requirements();

		// Check Elementor default colors and fonts are set.
		// Elementor stores the string 'yes' in the WordPress database if these options are active, and an empty string if these options are not active.
		$is_elementor_color_schemes_disabled_already      = get_option( 'elementor_disable_color_schemes' );
		$is_elementor_typography_schemes_disabled_already = get_option( 'elementor_disable_typography_schemes' );
		if ( $is_elementor_color_schemes_disabled_already !== 'yes' ) {
			$requirements['settings'][] = [
				'name'         => 'Elementor default color schemes',
				'setting_name' => 'elementor_disable_color_schemes'
			];
		}
		if ( $is_elementor_typography_schemes_disabled_already !== 'yes' ) {
			$requirements['settings'][] = [
				'name'         => 'Elementor default typography schemes',
				'setting_name' => 'elementor_disable_typography_schemes'
			];
		}

		return $requirements;
	}
}
