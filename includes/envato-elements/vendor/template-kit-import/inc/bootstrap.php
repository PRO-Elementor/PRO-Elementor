<?php
/**
 * Template Kit Import: Bootstrap File
 *
 * This starts things up. Registers the SPL and starts up some classes.
 *
 * @package Envato/Template_Kit_Import
 * @since 0.0.2
 */

namespace Envato_Template_Kit_Import;

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
		$relative_class = strtolower( substr( $class, $len + 1 ) );
		$relative_class = 'class-' . $relative_class;
		$file           = $base_dir . DIRECTORY_SEPARATOR . str_replace( array( '\\', '_' ), array( '/', '-' ), $relative_class ) . '.php';
		if ( file_exists( $file ) ) {
			require $file;
		} else {
			die( esc_html( basename( $file ) . ' missing.' ) );
		}
	}
);

require_once __DIR__ . '/helper.php';

Delete::get_instance();
CPT_Kits::get_instance();
Builder_Elementor::get_instance();
