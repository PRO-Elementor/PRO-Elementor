<?php
/**
 * Envato Elements: Options
 *
 * Making option management a bit easier for us.
 *
 * @package Envato/Envato_Elements
 * @since 2.0.0
 */

namespace Envato_Elements\API;

use Envato_Elements\Utils\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * API base class
 *
 * @since 2.0.0
 */
abstract class API extends Base {

	public function __construct() {
		$this->register_api_endpoints();
	}

	public function rest_permission_check( $request ) {
		return current_user_can( 'edit_posts' ) && current_user_can( 'upload_files' );
	}

	public function register_endpoint( $endpoint, $callback ){
		register_rest_route(
			ENVATO_ELEMENTS_API_NAMESPACE,
			$endpoint,
			[
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => $callback,
					'permission_callback' => [ $this, 'rest_permission_check' ],
					'args'                => [],
				],
			]
		);
	}

	/**
	 * @param array $data
	 *
	 * @return \WP_REST_Response
	 */
	public function format_success($data) {
		return new \WP_REST_Response( $data, 200 );
	}

	/**
	 * @param $endpoint
	 * @param $error_code
	 * @param $error_message
	 * @param array $additional_data
	 *
	 * @return \WP_REST_Response
	 */
	public function format_error($endpoint, $error_code, $error_message, $additional_data = []){
		return new \WP_REST_Response( [
			'error' => [
				'context' => $endpoint,
				'code' => $error_code,
				'message' => $error_message,
				'data' => $additional_data,
			]
		], 500 );
	}
}
