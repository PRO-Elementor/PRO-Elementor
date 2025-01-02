<?php
/**
 * Envato Elements: Content API
 *
 * Content API
 *
 * @package Envato/Envato_Elements
 * @since 2.0.0
 */

namespace Envato_Elements\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Content API
 *
 * @since 2.0.0
 */
class Content_API extends Base {

	/**
	 * This is the API endpoint for all our static template kit data:
	 *
	 * @var string
	 *
	 * @since 2.0.0
	 */
	private $api_endpoint = 'https://assets.wp.envatoextensions.com/template-kits/';

	/**
	 * Fetch a file from our static endpoint:
	 *
	 * @param $file - the file to get
	 * @param bool $force - if we should skip local transient cache
	 *
	 * @return string|\WP_Error
	 */
	public function fetch_file_content( $file, $force = false ) {
		$url = $this->api_endpoint . $file;

		$cache_key = 'envato_elements_' . md5( $url );

		if ( ! $force ) {
			$cached_response = get_transient( $cache_key );
			if ( $cached_response ) {
				return $cached_response;
			}
		}

		$response = wp_safe_remote_get(
			$url, [
				'user-agent' => 'Mozilla/5.0 (Envato Elements ' . ENVATO_ELEMENTS_VER . ';) ' . home_url(),
				'timeout'    => 10,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== (int) $response_code ) {
			return new \WP_Error( $response_code, 'Failed to fetch file' );
		}

		// Get the response string from our WP response object
		$cached_response = wp_remote_retrieve_body( $response );

		// Cache this response string in WordPress locally for an hour:
		set_transient( $cache_key, $cached_response, 3600 );

		return $cached_response;
	}

	/**
	 * Fetch our template listing, filter based on search parameters, and return an array.
	 *
	 * @param array $search
	 *
	 * @return array|\WP_Error
	 */
	public function get_free_template_kits( $search = [] ) {
		$manifest_content = $this->fetch_file_content( 'manifest.json' );
		if ( is_wp_error( $manifest_content ) ) {
			return $manifest_content;
		}

		$all_free_template_kits = json_decode( $manifest_content, true );

		$all_available_industries = [];
		foreach ( $all_free_template_kits as $free_template_kit_id => $free_template_kit ) {
			if ( ! empty( $free_template_kit['industry'] ) && is_array( $free_template_kit['industry'] ) ) {
				foreach ( $free_template_kit['industry'] as $industry_key => $industry_value ) {
					// we fix up the &amp; encoding ready for display in the UI:
					$fixed_industry_encoding                                                      = htmlspecialchars_decode( $industry_value );
					$all_free_template_kits[ $free_template_kit_id ]['industry'][ $industry_key ] = $fixed_industry_encoding;

					// Add this industry to the list of available industries
					$all_available_industries[ $industry_key ] = [
						'key'   => $industry_key,
						'value' => $fixed_industry_encoding,
					];
				}
			}
		}

		$page     = ! empty( $search['page'] ) ? $search['page'] : 1;
		$per_page = 12;

		// Filter based on provided industry
		$filter_by_industry = ! empty( $search['industry'] ) ? $search['industry'] : false;
		if ( $filter_by_industry ) {
			$all_free_template_kits = array_filter( $all_free_template_kits, function ( $free_template_kit ) use ( $filter_by_industry ) {
				return isset( $free_template_kit['industry'][ $filter_by_industry ] );
			} );
		}

		// Filter based on provided text string
		$filter_by_text = ! empty( $search['text'] ) ? $search['text'] : false;
		if ( $filter_by_text ) {
			$all_free_template_kits = array_filter( $all_free_template_kits, function ( $free_template_kit ) use ( $filter_by_text ) {
				$has_title_match     = stripos( $free_template_kit['name'], $filter_by_text ) !== false;
				$matching_categories = array_filter( $free_template_kit['industry'], function ( $industry ) use ( $filter_by_text ) {
					return stripos( $industry, $filter_by_text ) !== false;
				} );

				return $has_title_match || $matching_categories;
			} );
		}

		// We count the total of all nested 'thumbnail' entries as this gives us an indication of how many free templates are available.
		$total_template_count = array_sum( array_map( "count", array_column( $all_free_template_kits, 'thumbnails' ) ) );

		$items = array_slice( $all_free_template_kits, ( ( $page - 1 ) * $per_page ), $per_page );

		$result = [
			'items' => $items,
			'meta'  => [
				'industries'           => array_values( $all_available_industries ), // so we can populate the drop down.
				'total_items'          => count( $all_free_template_kits ), // for pagination buttons to work
				'per_page'             => $per_page,  // for pagination buttons to work
				'current_page'         => $page,  // for pagination buttons to work
				'total_template_count' => $total_template_count, // for our browse sub text
			],
		];

		return $result;
	}

	/**
	 * Fetch our template listing, filter based on search parameters, and return an array.
	 *
	 * @param array $search
	 *
	 * @return array|\WP_Error
	 */
	public function get_free_blocks( $search = [] ) {
		$manifest_content = $this->fetch_file_content( 'blocks.json' );
		if ( is_wp_error( $manifest_content ) ) {
			return $manifest_content;
		}

		$free_block_data    = json_decode( $manifest_content, true );
		$matched_block_data = [];

		$all_available_categories = [];
		foreach ( $free_block_data['categories'] as $category_id => $category_data ) {
			$all_available_categories[ $category_data['id'] ] = [
				'key'   => $category_data['id'],
				'value' => htmlspecialchars_decode( $category_data['name'] ),
			];
		}

		ksort( $all_available_categories );

		// Filter based on provided industry
		$filter_by_category = ! empty( $search['category'] ) ? $search['category'] : false;
		if ( $filter_by_category && isset( $all_available_categories[ $filter_by_category ] ) ) {
			$matched_block_data = $free_block_data['categories'][ $filter_by_category ]['blocks'];

			// Let's decode some special characters in the block name key.
			foreach ( $matched_block_data as $matched_block_id => $matched_block ) {
				$matched_block_data[$matched_block_id]['name']    = htmlspecialchars_decode( $matched_block['name'] );
			}
		}

		// We count the total of all nested 'thumbnail' entries as this gives us an indication of how many free templates are available.
		$total_template_count = array_sum( array_map( 'count', array_column( $free_block_data['categories'], 'blocks' ) ) );

		$result = [
			'items' => $matched_block_data,
			'meta'  => [
				'categories'           => array_values( $all_available_categories ), // so we can populate the drop down.
				'total_template_count' => $total_template_count, // for our browse sub text
			],
		];

		return $result;
	}

	/**
	 * @param $zip_url
	 *
	 * @return string|\WP_Error
	 */
	public function download_zip( $zip_url ) {
		require_once( ABSPATH . '/wp-admin/includes/file.php' );
		$temporary_zip_file_path = wp_tempnam( 'content-api-dl' );
		$download_response       = wp_safe_remote_get( $zip_url, array(
			'timeout'    => 60,
			'user-agent' => 'Mozilla/5.0 (Envato Elements ' . ENVATO_ELEMENTS_VER . ';) ' . home_url(),
			'stream'     => true,
			'filename'   => $temporary_zip_file_path
		) );
		if ( is_wp_error( $download_response ) ) {
			return $download_response;
		}

		// We successfully downloaded the zip file, return the path
		return $temporary_zip_file_path;
	}

}
