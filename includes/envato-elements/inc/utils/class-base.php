<?php
/**
 * Envato Elements:
 *
 * Base class for extending our other classes upon.
 *
 * @package Envato/Envato_Elements
 * @since 0.0.2
 */

namespace Envato_Elements\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Envato Elements plugin.
 *
 * The main plugin handler class is responsible for initializing Envato Elements. The
 * class registers and all the components required to run the plugin.
 *
 * @since 0.0.2
 */
abstract class Base {

	const PAGE_SLUG = ENVATO_ELEMENTS_SLUG;
	/**
	 * Holds the plugin instance.
	 *
	 * @since 0.0.2
	 * @access protected
	 * @static
	 *
	 * @var Base
	 */
	private static $instances = [];

	/**
	 * Disable class cloning and throw an error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object. Therefore, we don't want the object to be cloned.
	 *
	 * @access public
	 * @since 0.0.2
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', 'envato-elements' ), '1.0.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @access public
	 * @since 0.0.2
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', 'envato-elements' ), '1.0.0' );
	}

	/**
	 * Sets up a single instance of the plugin.
	 *
	 * @since 0.0.2
	 * @access public
	 * @static
	 *
	 * @return static An instance of the class.
	 */
	public static function get_instance() {
		$module = get_called_class();
		if ( ! isset( self::$instances[ $module ] ) ) {
			self::$instances[ $module ] = new $module();
		}

		return self::$instances[ $module ];
	}

}
