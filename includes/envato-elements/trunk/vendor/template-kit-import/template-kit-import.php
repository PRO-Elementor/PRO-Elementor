<?php
/**
 * Imported from "Template Kit Import" Version: 1.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ENVATO_TEMPLATE_KIT_IMPORT_SLUG' ) ) {
	define( 'ENVATO_TEMPLATE_KIT_IMPORT_SLUG', 'template-kit-import' );
	define( 'ENVATO_TEMPLATE_KIT_IMPORT_VER', '1.0.2' );
	define( 'ENVATO_TEMPLATE_KIT_IMPORT_FILE', __FILE__ );
	define( 'ENVATO_TEMPLATE_KIT_IMPORT_DIR', plugin_dir_path( ENVATO_TEMPLATE_KIT_IMPORT_FILE ) );
	define( 'ENVATO_TEMPLATE_KIT_IMPORT_URI', plugins_url( '/', ENVATO_TEMPLATE_KIT_IMPORT_FILE ) );
	define( 'ENVATO_TEMPLATE_KIT_IMPORT_PHP_VERSION', '5.6' );

	/**
	 * Our supported import types
	 */
	define( 'ENVATO_TEMPLATE_KIT_IMPORT_TYPE_ENVATO', 'template-kit' );
	define( 'ENVATO_TEMPLATE_KIT_IMPORT_TYPE_ELEMENTOR', 'elementor-kit' );

	require ENVATO_TEMPLATE_KIT_IMPORT_DIR . 'inc/bootstrap.php';
}
