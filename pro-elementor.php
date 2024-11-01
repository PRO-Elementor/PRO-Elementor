<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://pro-elementor.com
 * @since             1.0.0
 * @package           Pro_Elementor
 *
 * @wordpress-plugin
 * Plugin Name:       PRO Elementor
 * Plugin URI:        https://pro-elementor.com
 * Description:       Enable PRO features in the Elementor page builder. Create professional websites faster than ever with hundreds of premium Elementor Template Kits. Fully flexible and ready to use with zero code. Note: Please deactivate Elementor Pro, PRO Elements, or Envato Elements plugins first if you have any of them activated.
 * Version:           2.2.0
 * Author:            PRO-Elementor.com
 * Author URI:        https://pro-elementor.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pro-elementor
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PRO_ELEMENTOR_VERSION', '2.2.0' );

/**
 * The URL our updater / license checker pings.
 */
define( 'PRO_ELEMENTOR_URL', 'https://pro-elementor.com' );

/**
 * The PRO Elementor download ID.
 */
define( 'PRO_ELEMENTOR_ITEM_ID', 15 );

/**
 * The the main plugin file.
 */
define( 'PRO_ELEMENTOR_FILE', __FILE__ );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pro-elementor-activator.php
 */
function activate_pro_elementor() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pro-elementor-activator.php';
	Pro_Elementor_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pro-elementor-deactivator.php
 */
function deactivate_pro_elementor() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pro-elementor-deactivator.php';
	Pro_Elementor_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_pro_elementor' );
register_deactivation_hook( __FILE__, 'deactivate_pro_elementor' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pro-elementor.php';

/**
 * Load the custom updater if it doesn't already exist.
 */
if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	include plugin_dir_path( __FILE__ ) . 'includes/EDD_SL_Plugin_Updater.php';
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_pro_elementor() {

	$plugin = new Pro_Elementor();
	$plugin->run();

}
run_pro_elementor();
