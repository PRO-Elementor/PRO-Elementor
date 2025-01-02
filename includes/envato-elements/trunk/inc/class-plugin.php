<?php
/**
 * Envato Elements:
 *
 * This starts things up. Registers the SPL and starts up some classes.
 *
 * @package Envato/Envato_Elements
 * @since 0.0.2
 */

namespace Envato_Elements;

use Envato_Elements\Backend\Photos_Embed;
use Envato_Elements\Backend\Template_Kits;
use Envato_Elements\Backend\Welcome;
use Envato_Elements\Utils\Base;

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
class Plugin extends Base {

	/**
	 * Initializing Envato Elements plugin.
	 *
	 * @since 0.0.2
	 * @access private
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_init', [ $this, 'admin_init' ] );
		add_action( 'plugins_loaded', [ $this, 'db_upgrade_check' ] );
	}

	/**
	 * Sets up the admin menu options.
	 *
	 * @since 0.0.2
	 * @access public
	 */
	public function admin_menu() {
		Welcome::get_instance()->admin_menu();
	}

	/**
	 * Sets up the admin menu options.
	 *
	 * @since 0.0.2
	 * @access public
	 */
	public function admin_init() {
		Photos_Embed::get_instance();
		Template_Kits::get_instance();
	}

	public function db_upgrade_check() {
		if ( is_admin() && get_option( 'envato_elements_version' ) !== ENVATO_ELEMENTS_VER ) {
			$this->activation();
		}
	}

	public function activation() {
		update_option( 'envato_elements_version', ENVATO_ELEMENTS_VER );
		if ( ! get_option( 'envato_elements_install_time' ) ) {
			update_option( 'envato_elements_install_time', time() );
		}
	}

}
