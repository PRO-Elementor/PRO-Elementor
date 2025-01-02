<?php
/**
 * Envato Elements: Bootstrap File
 *
 * This starts things up. Registers the SPL and starts up some classes.
 *
 * @package Envato/Envato_Elements
 * @since 0.0.2
 */

namespace Envato_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


spl_autoload_register(
	function ( $class ) {
		$prefix   = __NAMESPACE__;
		$base_dir = __DIR__;
		$len      = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 || $class === $prefix ) {
			return;
		}

		$class        = strtolower( $class );
		$class        = str_replace([ '\\', '_' ], [ '/', '-' ], $class );
		$class_path   = strtolower( substr( $class, $len + 1 ) );
		$class_name   = basename( $class_path );
		$class_folder = dirname( $class_path );
		if ( ! $class_folder || $class_folder === '.' ) {
			$class_folder = '';
		} else {
			$class_folder .= DIRECTORY_SEPARATOR;
		}
		$file = $base_dir . DIRECTORY_SEPARATOR . $class_folder . 'class-' . $class_name . '.php';
		if ( file_exists( $file ) ) {
			require $file;
		} else {
			die( esc_html( basename( $file ) . ' not found.' ) );
		}
	}
);

Plugin::get_instance();
Backend\Options::get_instance();
Backend\REST::get_instance();
Backend\Elementor_Modal::get_instance();
