<?php
/**
 * Envato Elements: REST API controller
 *
 * REST API controller.
 *
 * @package Envato/Envato_Elements
 * @since 0.0.2
 */

namespace Envato_Elements\Backend;

use Envato_Elements\API\Banners;
use Envato_Elements\API\Photos_Import;
use Envato_Elements\API\Photos_Search;
use Envato_Elements\API\Project_Name;
use Envato_Elements\API\Requirements;
use Envato_Elements\API\Settings;
use Envato_Elements\API\Subscription_API;
use Envato_Elements\API\Template_Kit_Install;
use Envato_Elements\API\Template_Kit_Search;
use Envato_Elements\API\Template_Kit_Import;
use Envato_Elements\Utils\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * REST API controller.
 *
 * @since 0.0.2
 */
class REST extends Base {


	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since 0.0.2
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
		// We also add admin-ajax because the REST API is unsuitable for a lot of hosts.
		add_action( 'wp_ajax_envato_elements', [ $this, 'ajax_handler' ] );
	}

	/**
	 * Update: We want to use the old ajax endpoint because the REST API is unsuitable on a lot of hosts.
	 *
	 * Revisit the REST API after Gutenberg becomes stable because that will iron our REST API issues.
	 *
	 * @since 0.0.9
	 */
	public function ajax_handler() {

		$nonce = null;
		if ( isset( $_REQUEST['_wpnonce'] ) ) {
			$nonce = $_REQUEST['_wpnonce'];
		} elseif ( isset( $_SERVER['HTTP_X_WP_NONCE'] ) ) {
			$nonce = $_SERVER['HTTP_X_WP_NONCE'];
		}
		if ( $nonce && wp_verify_nonce( $nonce, 'wp_rest' ) && isset( $_GET['endpoint'] ) ) {
			$namespace = ENVATO_ELEMENTS_API_NAMESPACE;
			$endpoint  = $_GET['endpoint'];
			$server    = rest_get_server();
			$routes    = $server->get_routes();
			$rest_key  = '/' . $namespace . '/' . $endpoint;
			if ( isset( $routes[ $rest_key ] ) && isset( $routes[ $rest_key ][0] ) ) {
				$request = new \WP_REST_Request( 'PUT' );
				$request->set_headers( $server->get_headers( wp_unslash( $_SERVER ) ) );
				$request->set_body( $server->get_raw_data() );
				$check_required = $request->has_valid_params();
				if ( is_wp_error( $check_required ) ) {
					wp_send_json_error( '-1' );
				} else {
					$check_sanitized = $request->sanitize_params();
					if ( is_wp_error( $check_sanitized ) ) {
						wp_send_json_error( '-2' );
					}
				}

				if ( call_user_func( $routes[ $rest_key ][0]['permission_callback'], $request ) ) {
					$rest_response = call_user_func( $routes[ $rest_key ][0]['callback'], $request );
					if ( ! is_wp_error( $rest_response ) && ! empty( $rest_response->data ) ) {
						wp_send_json( $rest_response->data, $rest_response->status );
					}
				}
			}
		}
		wp_die();
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		Template_Kit_Search::get_instance();
		Photos_Search::get_instance();
		Template_Kit_Import::get_instance();
		Template_Kit_Install::get_instance();
		Subscription_API::get_instance();
		Banners::get_instance();
		Project_Name::get_instance();
		Settings::get_instance();
		Photos_Import::get_instance();
		Requirements::get_instance();
	}

}

