<?php
/**
 * Envato Elements: Template Kits installs
 *
 * API for handling template kit installs
 *
 * @package Envato/Envato_Elements
 * @since 2.0.0
 */

namespace Envato_Elements\API;

use Envato_Elements\Backend\Downloaded_Items;
use Envato_Elements\Backend\Options;
use Envato_Elements\Backend\Subscription;
use Envato_Elements\Backend\Template_Kits;
use Envato_Elements\Utils\Extensions_API;
use Envato_Elements\Utils\Content_API;
use Envato_Elements\Utils\Limits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * API for handling template kit installs
 *
 * @since 2.0.0
 */
class Template_Kit_Install extends API {

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function install_template_kit_from_elements( $request ) {

        if ( ! current_user_can( 'install_themes' ) ) {
            return $this->format_error(
                'installTemplateKit',
                'missing_permissions',
                'Please contact your administrator to perform this action.'
            );
        }

		$template_kit_humane_id = $request->get_param( 'templateKitId' );

		// If the user doesn't have a paid subscription we return an invalid subscription message
		if ( Subscription::get_instance()->get_subscription_status() !== Subscription::SUBSCRIPTION_PAID ) {
			return $this->format_error(
				'installTemplateKit',
				'invalid_subscription',
				'A valid subscription is required to install kits'
			);
		}


		Limits::get_instance()->raise_limits();

		// Reach out to Extensions API for a download request of this item
		$api_license_response = Extensions_API::get_instance()->api_call( '/extensions/item/' . $template_kit_humane_id . '/download', 'POST', [
			'project_name'   => Options::get_instance()->get( 'project_name', get_bloginfo( 'name' ) ),
			'extension_type' => 'envato-wp',
		] );

		if ( is_wp_error( $api_license_response ) ) {
			$extensions_api_error_data = $api_license_response->get_error_data();
			if ( $extensions_api_error_data && ! empty( $extensions_api_error_data['message'] ) ) {
				// e.g :
				/**
				 * [code] => invalid_token
				 * [message] => No token exists with the specified token code
				 */
				return $this->format_error(
					'installTemplateKit',
					'zip_failure',
					'Failed to download item: ' . $extensions_api_error_data['message']
				);
			}

			return $this->format_error(
				'installTemplateKit',
				'zip_failure',
				'Failed to download item: ' . $api_license_response->get_error_message()
			);
		}

		// Check if we get a successful API response with a download URL we can work with:
		if ( $api_license_response && ! is_wp_error( $api_license_response ) && ! empty( $api_license_response['download_urls']['original'] ) ) {

			// Download our remote ZIP file to a local temporary file:
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			$temporary_zip_file_path = wp_tempnam( 'tk-' . $template_kit_humane_id );
			$download_response       = wp_safe_remote_get( $api_license_response['download_urls']['original'], array(
				'timeout'  => 60,
				'stream'   => true,
				'filename' => $temporary_zip_file_path
			) );

			// If we failed to download return an error
			if ( is_wp_error( $download_response ) ) {
				return $this->format_error(
					'installTemplateKit',
					'zip_failure',
					$download_response->get_error_message()
				);
			}

			// Assume we downloaded successfully:
			$error_or_template_kit_id = Template_Kits::get_instance()->process_zip_file( $temporary_zip_file_path );
			unlink( $temporary_zip_file_path );

			if ( is_wp_error( $error_or_template_kit_id ) ) {
				return $this->format_error(
					'installTemplateKit',
					'zip_failure',
					$error_or_template_kit_id->get_error_message()
				);
			}

			// If we get here we've got a successful license event from Elements. Lets flag that in our database so
			// we can update the UI on future page loads.
			Downloaded_Items::get_instance()->record_download_event( $template_kit_humane_id, $error_or_template_kit_id );

			$data = [
				'success'         => true,
				'template_kit_id' => $error_or_template_kit_id,
			];

			return $this->format_success( $data );
		}

		return $this->format_error(
			'installTemplateKit',
			'zip_failure',
			'Failed to download item, please try again.'
		);

	}

	/**
	 * Installs free template kits from our s3 bucket.
	 *
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function install_free_template_kit( $request ) {

        if ( ! current_user_can( 'install_themes' ) ) {
            return $this->format_error(
                'installTemplateKit',
                'missing_permissions',
                'Please contact your administrator to perform this action.'
            );
        }

		$template_kit_id      = $request->get_param( 'templateKitId' );
		$template_kit_zip_url = $request->get_param( 'zipUrl' );

		$temporary_zip_file_path = Content_API::get_instance()->download_zip( $template_kit_zip_url );

		// We didn't give the request a valid url so we will error.
		if ( is_wp_error( $temporary_zip_file_path ) ) {
			return $this->format_error(
				'installTemplateKit',
				'zip_failure',
				$temporary_zip_file_path->get_error_message()
			);
		}

		// Assume we downloaded successfully:
		$error_or_template_kit_id = Template_Kits::get_instance()->process_zip_file( $temporary_zip_file_path );
		unlink( $temporary_zip_file_path );

		if ( is_wp_error( $error_or_template_kit_id ) ) {
			return $this->format_error(
				'installTemplateKit',
				'zip_failure',
				$error_or_template_kit_id->get_error_message()
			);
		}

		// If we get here we've got a successful license event from Elements. Lets flag that in our database so
		// we can update the UI on future page loads.
		Downloaded_Items::get_instance()->record_download_event( $template_kit_id, $error_or_template_kit_id );

		$data = [
			'success'         => true,
			'template_kit_id' => $error_or_template_kit_id,
		];

		return $this->format_success( $data );

		// return $this->format_error(
		// 	'installTemplateKit',
		// 	'zip_failure',
		// 	'Failed to download item. ' . ( is_wp_error( $api_license_response ) ? $api_license_response->get_error_message() : '' )
		// );
	}

	/**
	 * @param $request \WP_REST_Request
	 */
	public function upload_template_kit_zip_file( $request ) {

        if ( ! current_user_can( 'install_themes' ) ) {
            return $this->format_error(
                'installTemplateKit',
                'missing_permissions',
                'Please contact your administrator to perform this action.'
            );
        }

		Limits::get_instance()->raise_limits();

		$all_files = $request->get_file_params();
		if ( $all_files && ! empty( $all_files['file'] ) ) {
			if ( is_uploaded_file( $all_files['file']['tmp_name'] ) && ! $all_files['file']['error'] ) {
				// We've got a successful file upload!
				$temp_file_name           = $all_files['file']['tmp_name'];
				$error_or_template_kit_id = Template_Kits::get_instance()->process_zip_file( $temp_file_name );
				unlink( $temp_file_name );

				if ( is_wp_error( $error_or_template_kit_id ) ) {
					return $this->format_error(
						'uploadTemplateKitZipFile',
						'zip_failure',
						$error_or_template_kit_id->get_error_message()
					);
				}

				// If we get here we assume the kit installed correctly.
				return $this->format_success( [
					'templateKitId' => $error_or_template_kit_id,
					'message'       => 'Zip installed successfully'
				] );
			}
		}

		return $this->format_error(
			'uploadTemplateKitZipFile',
			'zip_failure',
			'Failed to process ZIP file, please ensure the selected file is the correct Template Kit format.'
		);
	}

	/**
	 * Deletes the template kit.
	 *
	 * @param $request \WP_REST_Request
	 */
	public function delete_template_kit( $request ) {

        if ( ! current_user_can( 'install_themes' ) ) {
            return $this->format_error(
                'installTemplateKit',
                'missing_permissions',
                'Please contact your administrator to perform this action.'
            );
        }

		$template_kit_id = $request->get_param( 'templateKitId' );
		Template_Kits::get_instance()->delete_template_kit( $template_kit_id );

		return $this->format_success(
			array(
				'message' => 'Kit deleted successfully',
			)
		);
	}

	public function register_api_endpoints() {
		$this->register_endpoint( 'installPremiumTemplateKit', [ $this, 'install_template_kit_from_elements' ] );
		$this->register_endpoint( 'installFreeTemplateKit', [ $this, 'install_free_template_kit' ] );
		$this->register_endpoint( 'uploadTemplateKitZipFile', [ $this, 'upload_template_kit_zip_file' ] );
		$this->register_endpoint( 'deleteTemplateKit', [ $this, 'delete_template_kit' ] );
	}
}
