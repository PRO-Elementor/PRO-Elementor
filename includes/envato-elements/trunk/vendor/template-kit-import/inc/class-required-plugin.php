<?php
/**
 * Template Kit Import:
 *
 * This starts things up. Registers the SPL and starts up some classes.
 *
 * @package Envato/Envato_Template_Kit_Import
 * @since 0.0.2
 */

namespace Envato_Template_Kit_Import;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Collection registration and management.
 *
 * @since 0.0.2
 */
class Required_Plugin extends Base {

	private $current_plugins = array();

	/**
	 * Pass in a list of required plugins to get the status / actions for them.
	 *
	 * @param $plugin_details
	 *
	 * @return string
	 */
	public function get_plugin_status( $plugin_details ) {

		//return 'install'; // uncomment this to test no plugins installed.
		//return 'activated'; // uncomment this to test all plugins installed & up to date.

		if ( empty( $plugin_details['file'] ) ) {
			return 'error';
		}

		$plugin_slug = dirname( $plugin_details['file'] );

		if ( ! $this->current_plugins ) {
			$active_plugins          = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
			$active_sitewide_plugins = get_site_option( 'active_sitewide_plugins' );
			if ( ! is_array( $active_plugins ) ) {
				$active_plugins = array();
			}
			if ( ! is_array( $active_sitewide_plugins ) ) {
				$active_sitewide_plugins = array();
			}
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$active_plugins                  = array_merge( $active_plugins, array_keys( $active_sitewide_plugins ) );
			$this->current_plugins['active'] = $active_plugins;
			$this->current_plugins['all']    = get_plugins();
		}

		// This covers when the user is running Elementor in a custom slug dir (i.e. beta version)
		if ( 'elementor' === $plugin_slug && class_exists( '\Elementor\Plugin' ) && ! in_array( 'elementor/elementor.php', $this->current_plugins['active'], true ) ) {
			foreach ( $this->current_plugins['active'] as $active_plugin_slug ) {
				$all_plugins_details = $this->current_plugins['all'][ $active_plugin_slug ];
				if ( 'elementor' === $all_plugins_details['TextDomain'] ) {
					$this->current_plugins['active'][]                       = 'elementor/elementor.php';
					$this->current_plugins['all']['elementor/elementor.php'] = $all_plugins_details;
				}
			}
		}
		// Same with Elementor Pro
		if ( 'elementor-pro' === $plugin_slug && class_exists( '\ElementorPro\Plugin' ) && ! in_array( 'elementor-pro/elementor-pro.php', $this->current_plugins['active'], true ) ) {
			foreach ( $this->current_plugins['active'] as $active_plugin_slug ) {
				$all_plugins_details = $this->current_plugins['all'][ $active_plugin_slug ];
				if ( 'elementor-pro' === $all_plugins_details['TextDomain'] ) {
					$this->current_plugins['active'][]                               = 'elementor-pro/elementor-pro.php';
					$this->current_plugins['all']['elementor-pro/elementor-pro.php'] = $all_plugins_details;
				}
			}
		}

		if ( in_array( $plugin_details['file'], $this->current_plugins['active'], true ) ) {
			$state = 'activated';
			// check it's the required min version.
			if ( ! empty( $plugin_details['min_version'] ) ) {
				if (
					isset( $this->current_plugins['all'][ $plugin_details['file'] ] ) &&
					! empty( $this->current_plugins['all'][ $plugin_details['file'] ]['Version'] ) &&
					version_compare( $this->current_plugins['all'][ $plugin_details['file'] ]['Version'], $plugin_details['min_version'], '<' )
				) {
					$state = 'update';
				}
			}
		} else {
			$state = 'install';
			foreach ( array_keys( $this->current_plugins['all'] ) as $plugin ) {
				if ( strpos( $plugin, $plugin_details['file'] ) !== false ) {
					$state = 'deactivated';
				}
			}
		}

		return $state;
	}

	/**
	 *
	 * This checks if the requested plugins are available locally.
	 * We check plugin slug and minimum version number.
	 *
	 * @param $required_plugins
	 *
	 * @return array
	 */
	public function check_for_required_plugins( $required_plugins ) {

		foreach ( $required_plugins as $required_plugin_id => $plugin_details ) {
			$plugin_status = $this->get_plugin_status( $plugin_details );
			$plugin_slug   = dirname( $plugin_details['file'] );
			// Padd out data in the array ready for frontend consumption:
			$required_plugins[ $required_plugin_id ]['slug']   = $plugin_slug;
			$required_plugins[ $required_plugin_id ]['status'] = $plugin_status;
			$required_plugins[ $required_plugin_id ]['url']    = '';
			switch ( $plugin_status ) {
				case 'deactivated':
					$required_plugins[ $required_plugin_id ]['url'] = add_query_arg( '_wpnonce', wp_create_nonce( 'activate-plugin_' . $plugin_details['file'] ), admin_url( 'plugins.php?action=activate&plugin=' . $plugin_details['file'] ) );
					break;
				case 'update':
					$required_plugins[ $required_plugin_id ]['url'] = admin_url( 'plugins.php' );
					break;
				case 'install':
					$required_plugins[ $required_plugin_id ]['url'] = add_query_arg( '_wpnonce', wp_create_nonce( 'install-plugin_' . $plugin_slug ), admin_url( 'update.php?action=install-plugin&plugin=' . $plugin_slug ) );
					break;
				case 'activated':
					break;
			}
			if ( 'elementor-pro' === $plugin_slug ) {
				$required_plugins[ $required_plugin_id ]['url'] = 'https://elementor.com/pro/?ref=2837';
			}
		}

		return $required_plugins;

	}


}
