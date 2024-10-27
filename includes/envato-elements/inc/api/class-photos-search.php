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

use Envato_Elements\Utils\Extensions_API;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Search API
 *
 * @since 2.0.0
 */
class Photos_Search extends API {

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function fetch_photos_search_results( $request ) {

		$search = $request->get_params();

		// Elements API maxes out around page 50, so lets not even attempt to query pages beyond that number:
		$max_pages = 49;

		$api_parameters = [
			'type' => 'photos',
			'page' => empty( $search['page'] ) || (int) $search['page'] < 1 || (int) $search['page'] > $max_pages ? 1 : (int) $search['page'],
		];

		// 'our_query' => 'elements_query'
		$parameter_mapping = [
			'text' => 'search_terms',
			'orientation' => 'orientation',
			'background' => 'background',
			'colors' => 'colors',
			'tag' => 'tags',
		];

		foreach ( $parameter_mapping as $our_query_key => $elements_query_key ){
			if ( ! empty( $search[ $our_query_key ] ) && strlen( trim( $search[ $our_query_key ] ) ) > 0 ) {
				$api_parameters[ $elements_query_key ] = sanitize_text_field( trim( $search[ $our_query_key ] ) );
			}
		}

		$data = Extensions_API::get_instance()->api_call( '/extensions/search?' . http_build_query( $api_parameters ) );

		if ( is_wp_error( $data ) ) {
			return $this->format_error(
				'fetchPhotosSearchResults',
				'generic_api_error',
				'Failed to fetch photo search results: ' . $data->get_error_message()
			);
		}

		return new \WP_REST_Response( $data, 200 );
	}

	public function register_api_endpoints() {
		$this->register_endpoint( 'fetchPhotosSearchResults', [ $this, 'fetch_photos_search_results' ] );
	}
}
