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

use Envato_Elements\Utils\Content_API;
use Envato_Elements\Utils\Extensions_API;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Search API
 *
 * @since 2.0.0
 */
class Template_Kit_Search extends API {

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function fetch_premium_search_results( $request ) {

		if ( defined( 'ENVATO_ELEMENTS_MOCK_TK_RESULTS' ) && ENVATO_ELEMENTS_MOCK_TK_RESULTS ) {
			$result = json_decode( file_get_contents( ENVATO_ELEMENTS_DIR . '__mocks__/premiumTemplateKits.json' ), true );

			return new \WP_REST_Response( $result, 200 );
		}

		$search = $request->get_params();

		// Elements API maxes out around page 50, so lets not even attempt to query pages beyond that number:
		$max_pages = 49;

		$api_parameters = [
			'type'                  => 'wordpress',
			'categories'            => 'Template Kits',
			'include_template_kits' => 'true', // todo: remove later, this won't be required after we go live for real real.
			'page'                  => empty( $search['page'] ) || (int) $search['page'] < 1 || (int) $search['page'] > $max_pages ? 1 : (int) $search['page'],
			'industries'            => ! empty( $search['industries'] ) ? $search['industries'] : '',
		];

		// 'our_query' => 'elements_query'
		$parameter_mapping = [
			'text'       => 'search_terms',
			'industries' => 'industries',
			'tag'        => 'tags',
		];

		foreach ( $parameter_mapping as $our_query_key => $elements_query_key ) {
			if ( ! empty( $search[ $our_query_key ] ) && strlen( trim( $search[ $our_query_key ] ) ) > 0 ) {
				$api_parameters[ $elements_query_key ] = sanitize_text_field( urldecode( trim( $search[ $our_query_key ] ) ) );
			}
		}

		$data = Extensions_API::get_instance()->api_call( '/extensions/search?' . http_build_query( $api_parameters ) );

		if ( is_wp_error( $data ) ) {
			return $this->format_error(
				'fetchPremiumTemplateKitSearchResults',
				'generic_api_error',
				'Failed to fetch template kit search results: ' . $data->get_error_message()
			);
		}

		return new \WP_REST_Response( $data, 200 );
	}

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function fetch_free_template_kit_search_results( $request ) {

		// Get all user provided search parameters:
		$user_search_params = $request->get_params();

		// Build an array of sanitized search params that we can pass through to our Content_API class
		$search = [
			'text'     => ! empty( $user_search_params['text'] ) ? sanitize_text_field( $user_search_params['text'] ) : '',
			'industry' => ! empty( $user_search_params['industry'] ) ? sanitize_text_field( $user_search_params['industry'] ) : '',
			'page'     => empty( $user_search_params['page'] ) || (int) $user_search_params['page'] < 1 ? 1 : (int) $user_search_params['page'],
		];

		$result = Content_API::get_instance()->get_free_template_kits( $search );

		if ( is_wp_error( $result ) ) {
			return $this->format_error(
				'fetchFreeTemplateKitSearchResults',
				'generic_api_error',
				'Failed to fetch free template kit search results: ' . $result->get_error_message()
			);
		}

		return new \WP_REST_Response( $result, 200 );
	}

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function fetch_free_block_search_results( $request ) {

		// Get all user provided search parameters:
		$user_search_params = $request->get_params();

		// Build an array of sanitized search params that we can pass through to our Content_API class
		$search = [
			'text'     => ! empty( $user_search_params['text'] ) ? sanitize_text_field( $user_search_params['text'] ) : '',
			'category' => ! empty( $user_search_params['category'] ) ? sanitize_text_field( $user_search_params['category'] ) : '',
			'page'     => empty( $user_search_params['page'] ) || (int) $user_search_params['page'] < 1 ? 1 : (int) $user_search_params['page'],
		];

		$result = Content_API::get_instance()->get_free_blocks( $search );

		if ( is_wp_error( $result ) ) {
			return $this->format_error(
				'fetchFreeBlockSearchResults',
				'generic_api_error',
				'Failed to fetch free block search results: ' . $result->get_error_message()
			);
		}

		return new \WP_REST_Response( $result, 200 );
	}

	public function register_api_endpoints() {
		$this->register_endpoint( 'fetchPremiumTemplateKitSearchResults', [ $this, 'fetch_premium_search_results' ] );
		$this->register_endpoint( 'fetchFreeTemplateKitSearchResults', [ $this, 'fetch_free_template_kit_search_results' ] );
		$this->register_endpoint( 'fetchFreeBlockSearchResults', [ $this, 'fetch_free_block_search_results' ] );
	}
}
