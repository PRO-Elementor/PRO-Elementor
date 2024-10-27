<?php
/**
 * Envato Elements: Banners API
 *
 * Banners API
 *
 * @package Envato/Envato_Elements
 * @since 2.0.0
 */

namespace Envato_Elements\API;

use Envato_Elements\Backend\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Search API
 *
 * @since 2.0.0
 */
class Project_Name extends API {

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function set_project_name( $request ) {
		// Pull in the project name from user input:
		$project_name = trim( sanitize_text_field( $request->get_param( 'projectName' ) ) );

		if ( ! strlen( $project_name ) ) {
			return $this->format_error(
				'setProjectName',
				'invalid_project_name',
				'Please enter a valid project name'
			);
		}

		// Save user provided project name to the database:
		Options::get_instance()->set( 'project_name', $project_name );

		// Return some success to react:
		return $this->format_success( [
			'saved' => true,
		] );
	}

	public function register_api_endpoints() {
		$this->register_endpoint( 'setProjectName', [ $this, 'set_project_name' ] );
	}
}
