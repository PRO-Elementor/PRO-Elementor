<?php
/**
 * Envato Elements: Search API
 *
 * Search API
 *
 * @package Envato/Envato_Elements
 * @since 2.0.0
 */

namespace Envato_Elements\API;

use Envato_Elements\Backend\Downloaded_Items;
use Envato_Elements\Backend\Options;
use Envato_Elements\Backend\Subscription;
use Envato_Elements\Utils\Extensions_API;
use Envato_Elements\Utils\Limits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Search API
 *
 * @since 2.0.0
 */
class Photos_Import extends API {

	const IMPORT_WIDTH = 2000;

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function import_photo( $request ) {

		$photo_id    = $request->get_param( 'photoId' );
		$photo_title = sanitize_text_field( $request->get_param( 'photoTitle' ) );

		// If the user doesn't have a paid subscription we return an invalid subscription message
		if ( Subscription::get_instance()->get_subscription_status() !== Subscription::SUBSCRIPTION_PAID ) {
			return $this->format_error(
				'importPhoto',
				'invalid_subscription',
				'A valid subscription is required to install kits'
			);
		}

		// Core WP image handling classes:
		require_once( ABSPATH . '/wp-admin/includes/file.php' );
		require_once( ABSPATH . '/wp-admin/includes/media.php' );
		require_once( ABSPATH . '/wp-admin/includes/image.php' );

		// todo: check we haven't already downloaded this photo, don't download it a second time if it still exists in media library
		$already_downloaded_id = Downloaded_Items::get_instance()->find_downloaded_id( $photo_id );
		if ( $already_downloaded_id ) {
			$already_downloaded_item = get_post( $already_downloaded_id );
			if ( $already_downloaded_item && ! is_wp_error( $already_downloaded_item ) && $already_downloaded_item->ID ) {
				$fullsize_path = get_attached_file( $already_downloaded_item->ID );
				if ( is_file( $fullsize_path ) ) {
					// still exists! don't download this item again.
					$data = [
						'success'           => true,
						'imported_photo_id' => $already_downloaded_item->ID,
						'attachment_data'   => wp_prepare_attachment_for_js( $already_downloaded_item->ID ),
					];

					return $this->format_success( $data );
				}
			}
		}

		Limits::get_instance()->raise_limits();

		// Reach out to Extensions API for a download request of this item
		$api_license_response = Extensions_API::get_instance()->api_call( '/extensions/item/' . $photo_id . '/download', 'POST', [
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
					'importPhoto',
					'generic_api_error',
					'Failed to download photo: ' . $extensions_api_error_data['message']
				);
			}

			return $this->format_error(
				'importPhoto',
				'generic_api_error',
				'Failed to download photo: ' . $api_license_response->get_error_message()
			);
		}

		$file_name = '';
		if ( $photo_title ) {
			$file_name = preg_replace( '#[^a-z0-9]+#', '-', basename( strtolower( $photo_title ) ) ) . '.jpg';
		}
		if ( ! $file_name ) {
			$file_name = 'elements-' . $photo_id . '.jpg';
		}
		$file_description = $photo_title;

		if ( $api_license_response && ! is_wp_error( $api_license_response ) && ! empty( $api_license_response['download_urls'][ 'max' . self::IMPORT_WIDTH ] ) ) {

			// Download our remote ZIP file to a local temporary file:
			$temporary_image_name = wp_tempnam( $file_name );
			$download_response    = wp_safe_remote_get( $api_license_response['download_urls'][ 'max' . self::IMPORT_WIDTH ], array(
				'timeout'  => 20,
				'stream'   => true,
				'filename' => $temporary_image_name
			) );

			// If we failed to download return an error
			if ( is_wp_error( $download_response ) ) {
				return $this->format_error(
					'importPhoto',
					'generic_api_error',
					$download_response->get_error_message()
				);
			}

			$file_data = file_get_contents( $temporary_image_name );
			$upload    = wp_upload_bits( $file_name, 0, $file_data );
			if ( $upload && ! is_wp_error( $upload ) && empty( $upload['error'] ) && ! empty( $upload['file'] ) ) {
				$info      = wp_check_filetype( $upload['file'] );
				$post_data = [
					'post_title'   => $photo_title,
					'post_excerpt' => $file_description,
					'post_content' => $file_description,
				];
				if ( $info ) {
					$post_data['post_mime_type'] = $info['type'];
				}
				$attachment_id = wp_insert_attachment( $post_data, $upload['file'] );
				if ( $attachment_id && ! is_wp_error( $attachment_id ) ) {
					$attachment_meta = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
					wp_update_attachment_metadata( $attachment_id, $attachment_meta );
					$result['success']       = true;
					$result['attachment_id'] = $attachment_id;
					// Update list of imported images.
					update_post_meta( $attachment_id, 'envato_elements', $photo_id );
					update_post_meta( $attachment_id, '_wp_attachment_image_alt', $photo_title );

					@unlink( $temporary_image_name );

					// If we get here we've got a successful license event from Elements. Lets flag that in our database so
					// we can update the UI on future page loads.
					Downloaded_Items::get_instance()->record_download_event( $photo_id, $attachment_id );

					$data = [
						'success'           => true,
						'imported_photo_id' => $attachment_id,
						'attachment_data'   => wp_prepare_attachment_for_js( $attachment_id ),
					];

					return $this->format_success( $data );
				}
			}
			@unlink( $temporary_image_name );
		}

		return $this->format_error(
			'importPhoto',
			'generic_api_error',
			'Failed to import photo. '
		);
	}

	public function register_api_endpoints() {
		$this->register_endpoint( 'importPhoto', [ $this, 'import_photo' ] );
	}
}
