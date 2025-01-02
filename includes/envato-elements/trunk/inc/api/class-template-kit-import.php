<?php
/**
 * Envato Elements: Template Kits Import
 *
 * API for handling template kit imports
 *
 * @package Envato/Envato_Elements
 * @since 2.0.0
 */

namespace Envato_Elements\API;

use Envato_Elements\Backend\Downloaded_Items;
use Envato_Elements\Backend\Template_Kits;
use Envato_Elements\Utils\Limits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * API for handling template kit imports
 *
 * @since 2.0.0
 */
class Template_Kit_Import extends API {

	/**
	 * @return \WP_REST_Response
	 */
	public function fetch_all_installed_template_kits() {

		$installed_kits = Template_Kits::get_instance()->get_installed_template_kits();

		return $this->format_success( $installed_kits );
	}

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function fetch_individual_templates( $request ) {

		$template_kit_id = $request->get_param( 'id' );
		if ( $template_kit_id === 'all' ) {
			// User is requesting templates from all kits.
			$all_template_data = [
				'id'           => 'all',
				'title'        => 'All Installed Kits',
				'requirements' => [],
				'templates'    => [],
			];
			$installed_kits    = Template_Kits::get_instance()->get_installed_template_kits();
			foreach ( $installed_kits as $installed_kit ) {
				$installed_kit_data = Template_Kits::get_instance()->get_installed_template_kit( $installed_kit['id'] );
				if ( $installed_kit_data && ! is_wp_error( $installed_kit_data ) ) {
					$all_template_data['templates'] = $all_template_data['templates'] + $installed_kit_data['templates'];
				}
			}
		} else {
			$template_kit_id = (int) $template_kit_id;

			$all_template_data = Template_Kits::get_instance()->get_installed_template_kit( $template_kit_id );
			if ( is_wp_error( $all_template_data ) ) {
				return $this->format_error(
					'fetchInstalledTemplateKit',
					'not_found',
					'Sorry this Template Kit was not found'
				);
			}
		}

		// Now we split the template data into groups.
		// This list of templates come from the "Template Kit Export" plugin:
		$template_types = [
			'single-page'         => __( 'Single: Page', 'template-kit-export' ),
			'single-home'         => __( 'Single: Home', 'template-kit-export' ),
			'single-post'         => __( 'Single: Post', 'template-kit-export' ),
			'single-product'      => __( 'Single: Product', 'template-kit-export' ),
			'single-404'          => __( 'Single: 404', 'template-kit-export' ),
			'landing-page'        => __( 'Single: Landing Page', 'template-kit-export' ),
			'archive-blog'        => __( 'Archive: Blog', 'template-kit-export' ),
			'archive-product'     => __( 'Archive: Product', 'template-kit-export' ),
			'archive-search'      => __( 'Archive: Search', 'template-kit-export' ),
			'archive-category'    => __( 'Archive: Category', 'template-kit-export' ),
			'section-header'      => __( 'Header', 'template-kit-export' ),
			'section-footer'      => __( 'Footer', 'template-kit-export' ),
			'section-popup'       => __( 'Popup', 'template-kit-export' ),
			'section-hero'        => __( 'Hero', 'template-kit-export' ),
			'section-about'       => __( 'About', 'template-kit-export' ),
			'section-faq'         => __( 'FAQ', 'template-kit-export' ),
			'section-contact'     => __( 'Contact', 'template-kit-export' ),
			'section-cta'         => __( 'Call to Action', 'template-kit-export' ),
			'section-team'        => __( 'Team', 'template-kit-export' ),
			'section-map'         => __( 'Map', 'template-kit-export' ),
			'section-features'    => __( 'Features', 'template-kit-export' ),
			'section-pricing'     => __( 'Pricing', 'template-kit-export' ),
			'section-testimonial' => __( 'Testimonial', 'template-kit-export' ),
			'section-product'     => __( 'Product', 'template-kit-export' ),
			'section-services'    => __( 'Services', 'template-kit-export' ),
			'section-stats'       => __( 'Stats', 'template-kit-export' ),
			'section-countdown'   => __( 'Countdown', 'template-kit-export' ),
			'section-portfolio'   => __( 'Portfolio', 'template-kit-export' ),
			'section-gallery'     => __( 'Gallery', 'template-kit-export' ),
			'section-logo-grid'   => __( 'Logo Grid', 'template-kit-export' ),
			'section-clients'     => __( 'Clients', 'template-kit-export' ),
			'section-other'       => __( 'Other', 'template-kit-export' ),
		];

		$templates_grouped = [];
		foreach ( $all_template_data['templates'] as $template_id => $template ) {
			$template_group = ! empty( $template['metadata'] ) && ! empty( $template['metadata']['template_type'] ) ? $template['metadata']['template_type'] : false;
			if ( $template_group ) {
				if ( ! isset( $templates_grouped[ $template_group ] ) ) {
					$templates_grouped[ $template_group ] = [
						'title'     => isset( $template_types[ $template_group ] ) ? $template_types[ $template_group ] : $template_group,
						'templates' => [],
					];
				}
				$templates_grouped[ $template_group ]['templates'][] = $template;
			} else {
				// If something doesn't have a valid template type, remove it from the list.
				unset( $all_template_data['templates'][ $template_id ] );
			}
		}
		$all_template_data['templates']        = array_values( $all_template_data['templates'] );
		$all_template_data['templatesGrouped'] = array_values( $templates_grouped );

		// We report any missing default settings that are required for template kits.
		if ( ! isset( $all_template_data['requirements']['settings'] ) ) {
			$all_template_data['requirements']['settings'] = [];
		}

		return $this->format_success( $all_template_data );
	}

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function import_single_template( $request ) {

		$template_kit_id = (int) $request->get_param( 'templateKitId' );
		$template_id     = (int) $request->get_param( 'templateId' );
		$import_again    = $request->get_param( 'importAgain' ) === 'true';

		Limits::get_instance()->raise_limits();

		try {
			$imported_template_data = Template_Kits::get_instance()->import_single_template( $template_kit_id, $template_id, $import_again );
			if ( is_wp_error( $imported_template_data ) ) {
				return $this->format_error(
					'importSingleTemplate',
					'generic_api_error',
					$imported_template_data->get_error_message()
				);
			}

			if ( $request->get_param( 'insertToPage' ) === 'true' ) {
				// we have to return additional JSON data so Elementor can insert these widgets on the page.
				\Elementor\Plugin::$instance->editor->set_edit_mode( true );
				$db      = \Elementor\Plugin::$instance->db;
				$content = $db->get_builder( $imported_template_data['imported_template_id'] );
				if ( ! empty( $content ) ) {
					$content = \Elementor\Plugin::$instance->db->iterate_data( $content, function ( $element ) {
						$element['id'] = \Elementor\Utils::generate_random_string();

						return $element;
					} );
				}
				$imported_template_data['content'] = $content;
			}

			return $this->format_success( $imported_template_data );
		} catch ( \Exception $e ) {
			return $this->format_error(
				'importSingleTemplate',
				'generic_api_error',
				$e->getMessage()
			);
		}
	}

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function import_free_block( $request ) {

		$block_id     = $request->get_param( 'blockId' );
		$json_url     = $request->get_param( 'jsonUrl' );
		$import_again = $request->get_param( 'importAgain' ) === 'true';

		$imported_template_data = [];

		Limits::get_instance()->raise_limits();

		$downloaded_template_id = false;
		if ( ! $import_again ) {
			// We check if we've previously inserted this free block template before.
			// We do this by checking for the "block_id" in the downloads variable.
			$already_downloaded_id = Downloaded_Items::get_instance()->find_downloaded_id( $block_id );
			if ( $already_downloaded_id ) {
				// check if this post still exists in the database
				$existing_post = get_post( $already_downloaded_id );
				if ( $existing_post && $existing_post->ID == $already_downloaded_id ) {
					// It's been removed from the download, remove downloaded_id so below code will pull it down again
					$downloaded_template_id = $existing_post->ID;
				}
			}
		}

		try {

			// Only download the template if it isn't already downloaded
			if ( ! $downloaded_template_id ) {
				// Core WP image handling classes:
				require_once( ABSPATH . '/wp-admin/includes/file.php' );
				require_once( ABSPATH . '/wp-admin/includes/media.php' );
				require_once( ABSPATH . '/wp-admin/includes/image.php' );

				$temporary_json_file_path = wp_tempnam( 'tk-block-' . $block_id );
				$download_response        = wp_safe_remote_get( $json_url, array(
					'timeout'  => 60,
					'stream'   => true,
					'filename' => $temporary_json_file_path
				) );

				// If we failed to download return an error
				if ( is_wp_error( $download_response ) ) {
					return $this->format_error(
						'importFreeBlock',
						'generic_api_error',
						$download_response->get_error_message()
					);
				}

				$block_data = json_decode( file_get_contents( $temporary_json_file_path ), true );

				$source     = \Elementor\Plugin::$instance->templates_manager->get_source( 'local' );
				// FYI, newer versions of Elementor delete a JSON file after successful import
				$result     = $source->import_template( basename( $temporary_json_file_path ), $temporary_json_file_path );

				if ( file_exists( $temporary_json_file_path ) ) {
					unlink( $temporary_json_file_path );
				}

				if ( is_wp_error( $result ) ) {
					return $this->format_error(
						'importFreeBlock',
						'generic_api_error',
						$result->get_error_message()
					);
				}

				if ( $result && $result[0] && $result[0]['template_id'] ) {
					$downloaded_template_id = $result[0]['template_id'];
				}
			}

			if ( $downloaded_template_id ) {
				$imported_template_data['imported_template_id'] = $downloaded_template_id;
				// Check if we need to add any custom CSS for this free block to operate.
				if ( $block_data && ! empty( $block_data['custom_css'] ) ) {
					$existing_customizer_css             = '';
					$css_separator                       = 'Block Kit CSS: ' . esc_html( $block_data['kit_id'] );
					$custom_css_post                     = wp_get_custom_css_post();
					$has_this_css_been_installed_already = false;
					if ( $custom_css_post && $custom_css_post->ID ) {
						// The user has customizer data on their website.
						$existing_customizer_css = $custom_css_post->post_content;
						if ( false !== strpos( $existing_customizer_css, $css_separator ) ) {
							// We have found this separator in the users customizer data.
							// Set the flag to true so we won't install this css snippet.
							$has_this_css_been_installed_already = true;
						}
					}
					if ( ! $has_this_css_been_installed_already ) {
						// The user hasn't installed this css before (or it's a fresh site with no customzer data yet)
						$existing_customizer_css .= "\n\n/** Start $css_separator **/\n\n" . $block_data['custom_css'] . "\n\n/** End $css_separator **/\n\n";
						wp_update_custom_css_post( $existing_customizer_css );
					}
				}

				Downloaded_Items::get_instance()->record_download_event( $block_id, $imported_template_data['imported_template_id'] );
			} else {
				return $this->format_error(
					'importFreeBlock',
					'generic_api_error',
					'Failed to import free block, sorry.'
				);
			}

			if ( $request->get_param( 'insertToPage' ) === 'true' ) {
				// we have to return additional JSON data so Elementor can insert these widgets on the page.
				\Elementor\Plugin::$instance->editor->set_edit_mode( true );
				$db      = \Elementor\Plugin::$instance->db;
				$content = $db->get_builder( $imported_template_data['imported_template_id'] );
				if ( ! empty( $content ) ) {
					$content = \Elementor\Plugin::$instance->db->iterate_data( $content, function ( $element ) {
						$element['id'] = \Elementor\Utils::generate_random_string();

						return $element;
					} );
				}
				$imported_template_data['content'] = $content;
			}

			return $this->format_success( $imported_template_data );
		} catch ( \Exception $e ) {
			return $this->format_error(
				'importFreeBlock',
				'generic_api_error',
				$e->getMessage()
			);
		}
	}

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function get_single_template_for_import( $request ) {

		$template_kit_id = (int) $request->get_param( 'templateKitId' );
		$template_id     = (int) $request->get_param( 'templateId' );

		try {
			$template_kit_data = Template_Kits::get_instance()->get_installed_template_kit( $template_kit_id );
			$template_data     = Template_Kits::get_instance()->get_single_template_data( $template_kit_id, $template_id );
			if ( is_wp_error( $template_data ) ) {
				return $this->format_error(
					'getSingleTemplateImportData',
					'generic_api_error',
					$template_data->get_error_message()
				);
			}

			$template_data_to_return = $template_data['template_json'];
			if ( is_array( $template_data_to_return ) && ! empty( $template_kit_data['title'] ) ) {
				$template_data_to_return['template_kit_name'] = $template_kit_data['title'];
			}

			return $this->format_success( [
				'template_data' => $template_data_to_return
			] );
		} catch ( \Exception $e ) {
			return $this->format_error(
				'getSingleTemplateImportData',
				'generic_api_error',
				$e->getMessage()
			);
		}
	}

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function import_elementor_template_image( $request ) {

		Limits::get_instance()->raise_limits();

		$image_id     = (int) $request->get_param( 'id' );
		$provided_url = $request->get_param( 'url' );
		$kit_name     = $request->get_param( 'templateKitName' );

		// Explode the URL into bits so we can urlencode each segment
		// without actually encoding the / characters.
		$path         = parse_url( $provided_url, PHP_URL_PATH );
		$encoded_path = implode( '/', array_map( 'urlencode', explode( '/', $path ) ) );
		$provided_url = str_replace( $path, $encoded_path, $provided_url );

		$image_url = filter_var( $provided_url, FILTER_VALIDATE_URL );

		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return $this->format_error(
				'importElementorTemplateImage',
				'generic_api_error',
				'Missing required plugin: Elementor'
			);
		}

		if ( empty( $image_id ) || empty( $image_url ) ) {
			return $this->format_error(
				'importElementorTemplateImage',
				'generic_api_error',
				'Image parameter error'
			);
		}

		try {

			// Core WP image handling classes:
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			require_once( ABSPATH . '/wp-admin/includes/media.php' );
			require_once( ABSPATH . '/wp-admin/includes/image.php' );

			// Elementor doesn't check if an image exists, it just imports
			// HTML as an image from a 404 page. Lets try to avoid that.
			$does_image_exist = wp_safe_remote_head( $image_url );

			if ( is_wp_error( $does_image_exist ) || (
			    $does_image_exist['response']['code'] !== 200 && $does_image_exist['response']['code'] !== 301
			) ) {
				// The author provided image no longer exists!
				// Import a placeholder instead.
				$image_url       = 'https://assets.wp.envatoextensions.com/template-kits/placeholder/placeholder.png';
			}

			// Reach into the Elementor plugin to use their image handling code.
			$attachment = \Elementor\Plugin::$instance->templates_manager->get_import_images_instance()->import( [
				'id'  => $image_id,
				'url' => $image_url,
			] );

			if ( $attachment && ! is_wp_error( $attachment ) ) {
				return $this->format_success( $attachment );
			} else {
				return $this->format_success(
					[
						'id'      => 1,
						'message' => 'Failed to import the image: ' . $image_url
					]
				);
			}

		} catch ( \Exception $e ) {
			return $this->format_error(
				'importElementorTemplateImage',
				'generic_api_error',
				$e->getMessage()
			);
		}
	}

	public function register_api_endpoints() {
		$this->register_endpoint( 'fetchInstalledTemplateKits', [ $this, 'fetch_all_installed_template_kits' ] );
		$this->register_endpoint( 'fetchIndividualTemplates', [ $this, 'fetch_individual_templates' ] );
		$this->register_endpoint( 'importSingleTemplate', [ $this, 'import_single_template' ] );
		$this->register_endpoint( 'importFreeBlock', [ $this, 'import_free_block' ] );
		$this->register_endpoint( 'getSingleTemplateImportData', [ $this, 'get_single_template_for_import' ] );
		$this->register_endpoint( 'importElementorTemplateImage', [ $this, 'import_elementor_template_image' ] );
	}
}
