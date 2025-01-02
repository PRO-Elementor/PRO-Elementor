<?php
/**
 * Envato Elements:
 *
 * Elements Welcome Page UI.
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
 * Handles the photos embed feature.
 *
 * @since 2.0.0
 */
class Photos_Embed extends Base {

	/**
	 * Deep constructor.
	 */
	public function __construct() {
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'load_custom_wp_admin_scripts' ] );
		add_action( 'elementor/preview/enqueue_styles', [ $this, 'load_custom_wp_admin_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'load_custom_wp_admin_scripts' ], 100 );
		add_action( 'delete_attachment', array( $this, 'delete_attachment' ) );
	}

	public function delete_attachment( $post_to_be_deleted ) {
		$post = get_post( $post_to_be_deleted );
		if ( $post && 'attachment' === $post->post_type ) {
			// We're deleting an attachment, check if it's an Envato Elements photo.
			$recorded_download_event_id = get_post_meta( $post->ID, 'envato_elements_download_event', true );
			if ( $recorded_download_event_id ) {
				Downloaded_Items::get_instance()->remove_download_event( $recorded_download_event_id );
			}
		}
	}


	public function load_custom_wp_admin_scripts() {
		Welcome::get_instance()->admin_page_assets();
		wp_enqueue_script( 'elements-deep', ENVATO_ELEMENTS_URI . 'assets/elements_deep.js', [
			'envato-elements-admin',
			'jquery'
		], ENVATO_ELEMENTS_VER, true );
		wp_enqueue_style( 'elements-deep', ENVATO_ELEMENTS_URI . 'assets/elements_deep.css', [], ENVATO_ELEMENTS_VER );
	}
}
