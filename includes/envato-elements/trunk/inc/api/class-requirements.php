<?php
/**
 * Envato Elements: Template Kits Import
 *
 * API for handling template kit imports
 *
 * @package Envato/Envato_Elements
 * @since 2.0.0
 */

namespace Envato_Elements\API;

use Envato_Elements\Backend\Template_Kits;
use Envato_Elements\Utils\Limits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * API for handling template kit imports
 *
 * @since 2.0.0
 */
class Requirements extends API {

	/**
	 * @return \WP_REST_Response
	 */
	public function install_requirement( $request ) {

		Limits::get_instance()->raise_limits();

		$requirement = json_decode( $request->get_param( 'requirement' ), true );
		if ( ! empty( $requirement ) && ! empty( $requirement['setting'] ) ) {

			// The front end has sent an API request to set any default settings.
			if ( ! current_user_can( 'manage_options' ) ) {
				return $this->format_error(
					'installRequirement',
					'install_failed',
					'Please contact your administrator to setup settings correctly.'
				);
			}

			$settings_allowlist = [
				'elementor_disable_color_schemes'      => 'yes',
				'elementor_disable_typography_schemes' => 'yes',
			];

			$setting_name = $requirement['setting']['setting_name'];

			// Check if our setting is in the allowlist
			if ( isset( $settings_allowlist[ $setting_name ] ) ) {
				// Set our setting to the permitted value in the allowlist
				update_option( $setting_name, $settings_allowlist[ $setting_name ] );

				// Tell our front end it worked:
				return $this->format_success( [
					'success' => $setting_name
				] );
			} else {
				// If our front end tries to set a not allowed setting we return an error.
				return $this->format_error(
					'installRequirement',
					'install_failed',
					'This setting is not allowed.'
				);
			}

		}

		if ( ! empty( $requirement ) && ! empty( $requirement['plugin'] ) ) {

			if ( ! current_user_can( 'install_plugins' ) ) {
				return $this->format_error(
					'installRequirement',
					'install_failed',
					'Please contact your administrator to install plugins.'
				);
			}

			$plugin_slug = $requirement['plugin']['slug'];

			if ( $plugin_slug === 'elementor-pro' ) {
				return $this->format_error(
					'installRequirement',
					'install_failed',
					'Please purchase Elementor Pro from here first',
					[
						'url' => 'https://elementor.com/pro/?ref=2837'
					]
				);
			}

			$install_status = $this->install_plugin( $requirement['plugin'] );
			if ( ! $install_status['success'] ) {
				return $this->format_error(
					'installRequirement',
					'install_failed',
					'Plugin install failed: ' . $install_status['errorMessage']
				);
			}

			return $this->format_success( [
				'success' => $plugin_slug
			] );
		}

		if ( ! empty( $requirement ) && ! empty( $requirement['theme'] ) ) {

			if ( ! current_user_can( 'install_themes' ) ) {
				return $this->format_error(
					'installRequirement',
					'install_failed',
					'Please contact your administrator to install themes.'
				);
			}

			if ( is_multisite() ) {
				return $this->format_error(
					'installRequirement',
					'install_failed',
					'Please contact your administrator to activate this theme in the global network settings.'
				);
			}

			// current_user_can( 'switch_themes' )
			$theme_slug = $requirement['theme']['slug'];
			$theme      = wp_get_theme( $theme_slug );
			if ( ! $theme->exists() ) {
				// install !
				$install_status = $this->install_theme( $theme_slug );
				if ( ! $install_status['success'] ) {
					return $this->format_error(
						'installRequirement',
						'install_failed',
						'Theme install failed: ' . $install_status['errorMessage']
					);
				}
			}

			$theme = wp_get_theme( $theme_slug );
			if ( ! $theme->exists() || ! $theme->is_allowed() ) {
				return $this->format_error(
					'installRequirement',
					'install_failed',
					'Unable to activate this theme, please install manually.'
				);
			}

			switch_theme( $theme->get_stylesheet() );


			return $this->format_success( [
				'success' => 'theme'
			] );
		}

		if ( ! empty( $requirement ) && ! empty( $requirement['requiredCss'] ) ) {

			// The front end has sent an API request to set some default CSS
			if ( ! current_user_can( 'manage_options' ) ) {
				return $this->format_error(
					'installRequirement',
					'install_failed',
					'Please contact your administrator to install Custom CSS correctly.'
				);
			}

			$template_kit_id = $requirement['requiredCss']['templateKitId'];
			$css_filename    = $requirement['requiredCss']['file'];

			try {
				Template_Kits::get_instance()->install_custom_css_into_customizer( $template_kit_id, $css_filename );
			}catch (\Exception $e){

			}

			return $this->format_success( [
				'success' => 'customCss'
			] );
		}

		return $this->format_error(
			'installRequirement',
			'generic_api_error',
			'Requirement installation failed'
		);
	}

	private function install_plugin( $plugin_details ) {

		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

		$all_plugins = get_plugins();

		$is_plugin_already_installed = false;
		foreach ( array_keys( $all_plugins ) as $plugin ) {
			if ( strpos( $plugin, $plugin_details['file'] ) !== false ) {
				$is_plugin_already_installed = true;
			}
		}

		if ( $is_plugin_already_installed ) {
			// just activate it:
			$activate_status = $this->activate_plugin( $plugin_details['file'] );

		} else {

			$status = [
				'success' => false,
			];

			$api = plugins_api(
				'plugin_information',
				array(
					'slug'   => sanitize_key( wp_unslash( $plugin_details['slug'] ) ),
					'fields' => array(
						'sections' => false,
					),
				)
			);

			if ( is_wp_error( $api ) ) {
				$status['errorMessage'] = $api->get_error_message();

				return $status;
			}

			$status['pluginName'] = $api->name;

			$skin     = new \WP_Ajax_Upgrader_Skin();
			$upgrader = new \Plugin_Upgrader( $skin );
			$result   = $upgrader->install( $api->download_link );

			if ( is_wp_error( $result ) ) {
				$status['errorCode']    = $result->get_error_code();
				$status['errorMessage'] = $result->get_error_message();

				return $status;
			} elseif ( is_wp_error( $skin->result ) ) {
				$status['errorCode']    = $skin->result->get_error_code();
				$status['errorMessage'] = $skin->result->get_error_message();

				return $status;
			} elseif ( $skin->get_errors()->has_errors() ) {
				$status['errorMessage'] = $skin->get_error_messages();

				return $status;
			} elseif ( is_null( $result ) ) {
				global $wp_filesystem;

				$status['errorCode']    = 'unable_to_connect_to_filesystem';
				$status['errorMessage'] = __( 'Unable to connect to the filesystem. Please confirm your credentials.' );

				// Pass through the error from WP_Filesystem if one was raised.
				if ( $wp_filesystem instanceof \WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->has_errors() ) {
					$status['errorMessage'] = esc_html( $wp_filesystem->errors->get_error_message() );
				}

				return $status;
			}

			$install_status = install_plugin_install_status( $api );

			$activate_status = $this->activate_plugin( $install_status['file'] );
		}

		if ( $activate_status && ! is_wp_error( $activate_status ) ) {
			$status['success'] = true;
		}

		return $status;
	}

	private function activate_plugin( $file ) {
		if ( current_user_can( 'activate_plugin', $file ) && is_plugin_inactive( $file ) ) {
			$result = activate_plugin( $file, false, false );
			if ( is_wp_error( $result ) ) {
				return $result;
			} else {
				return true;
			}
		}

		return false;
	}

	private function install_theme( $slug ) {
		$status = array(
			'success' => false,
		);

		if ( ! current_user_can( 'install_themes' ) ) {
			$status['errorMessage'] = __( 'Sorry, you are not allowed to install themes on this site.' );

			return $status;
		}

		require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		include_once( ABSPATH . 'wp-admin/includes/theme.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		include_once( ABSPATH . 'wp-admin/includes/theme-install.php' );

		$api = themes_api(
			'theme_information',
			array(
				'slug'   => $slug,
				'fields' => array( 'sections' => false ),
			)
		);

		if ( is_wp_error( $api ) ) {
			$status['errorMessage'] = $api->get_error_message();

			return $status;
		}

		$skin     = new \WP_Ajax_Upgrader_Skin();
		$upgrader = new \Theme_Upgrader( $skin );
		$result   = $upgrader->install( $api->download_link );

		if ( is_wp_error( $result ) ) {
			$status['errorCode']    = $result->get_error_code();
			$status['errorMessage'] = $result->get_error_message();

			return $status;
		} elseif ( is_wp_error( $skin->result ) ) {
			$status['errorCode']    = $skin->result->get_error_code();
			$status['errorMessage'] = $skin->result->get_error_message();

			return $status;
		} elseif ( $skin->get_errors()->has_errors() ) {
			$status['errorMessage'] = $skin->get_error_messages();

			return $status;
		} elseif ( is_null( $result ) ) {
			global $wp_filesystem;

			$status['errorCode']    = 'unable_to_connect_to_filesystem';
			$status['errorMessage'] = __( 'Unable to connect to the filesystem. Please confirm your credentials.' );

			// Pass through the error from WP_Filesystem if one was raised.
			if ( $wp_filesystem instanceof \WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->has_errors() ) {
				$status['errorMessage'] = esc_html( $wp_filesystem->errors->get_error_message() );
			}

			return $status;
		}

		$status['themeName'] = wp_get_theme( $slug )->get( 'Name' );

		$status['success'] = true;

		return $status;
	}

	public function register_api_endpoints() {
		$this->register_endpoint( 'installRequirement', [ $this, 'install_requirement' ] );
	}
}
