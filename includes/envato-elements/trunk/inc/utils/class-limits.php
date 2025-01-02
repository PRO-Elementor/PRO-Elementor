<?php
/**
 * Envato Elements: Limits
 *
 * Limits
 *
 * @package Envato/Envato_Elements
 * @since 2.0.0
 */

namespace Envato_Elements\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Limits
 *
 * @since 2.0.0
 */
class Limits extends Base {

	/**
	 * We raise our memory, timeout limits and image threshold during import because
	 * some operations take a lot of processing (i.e. large images in Template Kits).
	 */
	public function raise_limits() {

		// WordPress added a size threshold when uploading images in 5.3.0. Adding This filter
		// will remove that threshold. Reference - https://developer.wordpress.org/reference/hooks/big_image_size_threshold/.
		add_filter( 'big_image_size_threshold', '__return_false' );

		// WordPress has a built in way to raise the memory limit thankfully:
		wp_raise_memory_limit( 'admin' );

		if ( wp_is_ini_value_changeable( 'max_execution_time' ) ) {
			ini_set( 'max_execution_time', 0 );
		}

		@ set_time_limit( 0 );
	}

	public function get_current_ini_bytes( $ini_setting ) {
		return wp_convert_hr_to_bytes( ini_get( $ini_setting ) );
	}

	public function get_max_execution_time() {
		return ini_get( 'max_execution_time' );
	}

	public function get_max_input_vars() {
		return ini_get( 'max_input_vars' );
	}
}
