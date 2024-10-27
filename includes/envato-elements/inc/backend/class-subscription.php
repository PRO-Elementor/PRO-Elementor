<?php
/**
 * Envato Elements: Subscription
 *
 * Subscription management
 *
 * @package Envato/Envato_Elements
 * @since 0.0.2
 */

namespace Envato_Elements\Backend;

use Envato_Elements\Utils\Base;
use Envato_Elements\Utils\Extensions_API;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Subscription management.
 *
 * @since 0.0.2
 */
class Subscription extends Base {

	const SUBSCRIPTION_TOKEN_OPTION = 'elements_token';
	const SUBSCRIPTION_API_CACHE = 3600; // Cache results locally this long.
	const SUBSCRIPTION_INACTIVE = 'inactive';
	const SUBSCRIPTION_FREE = 'free';
	const SUBSCRIPTION_PAID = 'paid';


	/**
	 * Calls the Extensions API to verify if the provided token is correct.
	 * Caches the users subscription_status so our plugin can know what to do for different cases.
	 *
	 * @return array
	 */
	public function verify_token_and_cache_user_info( $token = false ) {

		if ( $token ) {
			// If we've been given an Extensions token value, we pass that through to our API library:
			Extensions_API::get_instance()->set_token( $token );
		}

		// Call the user_into endpoint, this returns the users subscription_status
		$result = Extensions_API::get_instance()->api_call( '/extensions/user_info' );
		if ( ! is_wp_error( $result ) && is_array( $result ) && ! empty( $result['subscription_status'] ) ) {
			// We've got a successful result from the Extensions API, cache the result locally so we don't have to hit this endpoint over and over again.
			$cached_auth_information = [
				'valid'  => true,
				'token'  => Extensions_API::get_instance()->get_token(),
				'time'   => time(),
				'status' => $result['subscription_status'],
			];
		} else {
			// We got a failure back from the Extensions API, this could be a number of reasons:
			// - invalid token
			// - api connectivity issue
			// - upstream connectivity issue
			$cached_auth_information = [
				'valid'  => false,
				'token'  => '',
				'time'   => time(),
				'status' => 'error',
			];

			// We check if our response was a "wp_error" so we can sniff more details about the issue out of the response:
			if ( is_wp_error( $result ) && is_array( $result->errors ) && is_array( $result->error_data ) ) {
				$error_status  = key( $result->errors );
				$error_data    = $result->error_data[ $error_status ];

				$cached_auth_information['error'] = [
					'message' => ! empty( $error_data['message'] ) ? $error_data['message'] : current( $result->errors[ $error_status ] ),
					'code'    => ! empty( $error_data['code'] ) ? $error_data['code'] : false,
				];
			}
		}

		Options::get_instance()->set( self::SUBSCRIPTION_TOKEN_OPTION, $cached_auth_information );

		return $cached_auth_information;
	}

	public function get_subscription_status(){
		$cached_auth_information = Options::get_instance()->get( self::SUBSCRIPTION_TOKEN_OPTION );
		if( $cached_auth_information && !empty($cached_auth_information['status'])){
			return $cached_auth_information['status'];
		}
		return 'unknown';
	}
}
