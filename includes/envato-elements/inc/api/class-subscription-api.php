<?php
/**
 * Envato Elements: Subscription API
 *
 * Search API
 *
 * @package Envato/Envato_Elements
 * @since 2.0.0
 */

namespace Envato_Elements\API;

use Envato_Elements\Backend\Subscription;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Subscription API
 *
 * @since 2.0.0
 */
class Subscription_API extends API {

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function verify_extensions_token( $request ) {

		// This is the user provided token that comes from the front end UI:
		$extensions_token = trim( $request->get_param('token') );

		if(!$extensions_token){
			return $this->format_error(
				'verifyElementsToken',
				'missing_data',
				'Please provide a token'
			);
		}

		$subscriptionClass = Subscription::get_instance();
		$token_response = $subscriptionClass->verify_token_and_cache_user_info($extensions_token);

		if($token_response['valid']){
			// we received a valid token from Extensions API
			if($token_response['status'] === $subscriptionClass::SUBSCRIPTION_PAID) {
				// return success if they have a paid subscription:
				return $this->format_success( $token_response );
			}else{
				// return failure if they haven't an active paid subscription
				return $this->format_error(
					'verifyElementsToken',
					'no_paid_account',
					'Verification Failed - you need a paid, Envato Elements subscription to continue'
				);
			}
		}else{
			// token was invalid, see if we have an 'error' code from Extensions API:
			if(!empty($token_response['error']) && !empty($token_response['error']['code'])){
				return $this->format_error(
					'verifyElementsToken',
					$token_response['error']['code'],
					$token_response['error']['message']
				);
			}
			// Unknown error from verification process:
			return $this->format_error(
				'verifyElementsToken',
				'invalid_token',
				'Invalid token provided',
				$token_response
			);
		}
	}

	public function register_api_endpoints() {
		$this->register_endpoint( 'verifyExtensionsToken', [ $this, 'verify_extensions_token' ] );
	}
}
