<?php
/**
 * Envato Elements: Extensions API
 *
 * Extensions API
 *
 * @package Envato/Envato_Elements
 * @since 2.0.0
 */

namespace Envato_Elements\Utils;

use Envato_Elements\Backend\Subscription;
use Envato_Elements\Backend\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Extensions API
 *
 * @since 2.0.0
 */
class Extensions_API extends Base {

	/**
	 * @var string
	 *
	 * @since 0.0.9
	 */
	private $api_endpoint = 'https://api.extensions.envato.com';

	/**
	 * @var string
	 *
	 * @since 0.0.9
	 */
	private $token = '';

	public function __construct() {
		if ( defined( 'ELEMENTS_API_HOSTNAME' ) ) {
			$this->api_endpoint = ELEMENTS_API_HOSTNAME;
		}
	}

	public function set_token( $token = false ) {
		if ( ! $token ) {
			$elements_token = Options::get_instance()->get( Subscription::SUBSCRIPTION_TOKEN_OPTION );
			if ( $elements_token && ! empty( $elements_token['token'] ) ) {
				$token = $elements_token['token'];
			}
		}
		$this->token = $token;
	}

	public function get_token() {
		return $this->token;
	}

	public function get_extension_id() {
		// Our legacy extension ID was the license code, check if we've got one prior to v2
		$legacy_extension_id = Options::get_instance()->get( 'license_code' );
		if ( $legacy_extension_id ) {
			return $legacy_extension_id;
		}

		// Default to a hash of the site url for the extension ID
		return md5( get_site_url() );
	}

	private function encode_url_parameter( $parameter ) {
		$parameter = html_entity_decode( $parameter, ENT_QUOTES | ENT_XML1, 'UTF-8' );
		$parameter = str_replace( '#', '', $parameter );

		return urlencode( $parameter );
	}

	public function get_token_url() {
		$extension_description = trim( Options::get_instance()->get( 'project_name', get_bloginfo( 'name' ) ) );
		if ( strlen( $extension_description ) > 0 ) {
			$extension_description .= ' (' . get_home_url() . ')';
		} else {
			$extension_description = get_home_url();
		}
		$extension_description = substr( $extension_description, 0, 254 );

		return $this->api_endpoint . "/extensions/begin_activation?extension_id=" . $this->get_extension_id() . "&extension_type=envato-wordpress&extension_description=" . $this->encode_url_parameter( $extension_description );
	}

	/**
	 *
	 * @param $endpoint
	 * @param string $method
	 * @param array $body_args
	 *
	 * @return \stdClass|\WP_Error
	 */
	public function api_call( $endpoint, $method = 'GET', $body_args = [] ) {

		if ( ! $this->token ) {
			$this->set_token();
		}
		$http_args = [
			'user-agent' => 'Mozilla/5.0 (Envato Elements ' . ENVATO_ELEMENTS_VER . ';) ' . home_url(),
			'timeout'    => 15,
			'headers'    => [ 'Extensions-Extension-Id' => $this->get_extension_id() ]
		];
		if ( $this->token ) {
			$http_args['headers']['Extensions-Token'] = $this->token;
		}

		foreach ( [ true, false, ] as $sslverify ) {
			// Unfortunately some hosts ONLY work with sslverify true, and some ONLY work with sslverify false.
			// So we cannot just hard code it to false, we have to try both. SSL first, then broken SSL if that fails.
			$http_args['sslverify'] = $sslverify;
			if ( $method == 'GET' ) {
				$response = wp_safe_remote_get( $this->api_endpoint . $endpoint, $http_args );
			} else {
				$http_args['headers']['Content-Type'] = 'application/json';
				$http_args['body']                    = json_encode( $body_args );
				$http_args['data_format']             = 'body';
				$response                             = wp_safe_remote_post( $this->api_endpoint . $endpoint, $http_args );
			}
			if ( $response && ! is_wp_error( $response ) ) {
				break;
			}
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$raw_response = wp_remote_retrieve_body( $response );
		$data         = json_decode( $raw_response, true );

		$response_code = wp_remote_retrieve_response_code( $response );

//		$response_code = 401;$data = [ 'error' => [ 'code'=>'token_expired', 'message'=>'errormsg' ] ];
//		$response_code = 403;$data = [ 'error' => [ 'code'=>'download_forbidden', 'message'=>'errormsg' ] ];
//		$response_code = 404;$data = [ 'error' => [ 'code'=>'item_not_found', 'message'=>'errormsg' ] ];
//		$response_code = 503;$data = 'Unavailable';

		$error_message_to_display = 'Unknown error';

		if ( empty( $data ) || ! is_array( $data ) ) {
			// Did the response contain HTML instead of json?
			if ( strlen( $raw_response ) && ! $data ) {
				// we failed to decode the response into JSON
				$error_message_to_display = 'The API did not respond with valid JSON data.';
			}

			return new \WP_Error( 'no_json', $error_message_to_display, [
				__( 'An error occurred, please try again', 'envato-elements' ),
				var_export( wp_remote_retrieve_body( $response ), true )
			] );
		}

		if ( 200 !== (int) $response_code && 201 !== (int) $response_code ) {
			$error_message_to_display = __( 'HTTP Error', 'envato-elements' );
			if ( $data && ! empty( $data['message'] ) ) {
				$error_message_to_display = $data['message'];
			}
			if ( $data && ! empty( $data['error'] ) && ! empty( $data['error']['message'] ) ) {
				$error_message_to_display = $data['error']['message'];
			}

			// format our error response data into something easier to parse
			return new \WP_Error( $response_code, $error_message_to_display, $data && ! empty( $data['error'] ) ? $data['error'] : $data );
		}

		return $data;
	}

}
