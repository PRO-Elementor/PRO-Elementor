<?php
/**
 * Envato Elements: Settings API
 *
 * Settings API
 *
 * @package Envato/Envato_Elements
 * @since 2.0.0
 */

namespace Envato_Elements\API;

use Envato_Elements\Backend\Options;
use Envato_Elements\Utils\Limits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Settings API
 *
 * @since 2.0.0
 */
class Settings extends API {

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function reset_users_settings( $request ) {

		Options::get_instance()->reset_user();

		// Return some success to react:
		return $this->format_success( [
			'reset' => true,
		] );
	}

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function save_preferred_start_page( $request ) {

		$start_page = $request->get_param( 'startPage' );

		$start_page_allow_list = [
			'welcome',
			'premium-kits',
			'free-kits',
			'installed-kits',
			'photos',
		];

		if ( in_array( $start_page, $start_page_allow_list ) ) {
			Options::get_instance()->set( 'start_page', $start_page );

			// Return some success to react:
			return $this->format_success( [
				'saved' => true,
			] );
		}

		return $this->format_error(
			'savePreferredStartPage',
			'generic_api_error',
			'Failed to save start page preference'
		);
	}

	/**
	 * @return \WP_REST_Response
	 */
	public function get_server_limits() {
		$limits = [];

		// Check memory limit is > 256 M
		try {
			$memory_limit         = Limits::get_instance()->get_current_ini_bytes( 'memory_limit' );
			$memory_limit_desired = 256;
			$memory_limit_ok      = $memory_limit < 0 || $memory_limit >= $memory_limit_desired * 1024 * 1024;
			$memory_limit_in_mb   = $memory_limit < 0 ? 'Unlimited' : floor( $memory_limit / ( 1024 * 1024 ) ) . 'M';

			$limits['memory_limit'] = [
				'title'   => 'PHP Memory Limit',
				'ok'      => $memory_limit_ok,
				'message' => $memory_limit_ok ? "is ok at {$memory_limit_in_mb}." : "{$memory_limit_in_mb} is too small. Please set your PHP memory limit to at least 256MB - or ask your hosting provider to do this if you're unsure."
			];
		} catch ( \Exception $e ) {
			$limits['memory_limit'] = [
				'title'   => 'PHP Memory Limit',
				'ok'      => false,
				'message' => 'Failed to check memory limit. Please ask hosting provider to raise the memory limit for you.'
			];
		}

		// Check upload size.
		try {
			$upload_size_desired = 40;

			$upload_max_filesize       = wp_max_upload_size();
			$upload_max_filesize_ok    = $upload_max_filesize < 0 || $upload_max_filesize >= $upload_size_desired * 1024 * 1024;
			$upload_max_filesize_in_mb = $upload_max_filesize < 0 ? 'Unlimited' : floor( $upload_max_filesize / ( 1024 * 1024 ) ) . 'M';

			$limits['upload'] = [
				'ok'      => $upload_max_filesize_ok,
				'title'   => 'PHP Upload Limits',
				'message' => $upload_max_filesize_ok ? "is ok at '$upload_max_filesize_in_mb'." : "$upload_max_filesize_in_mb is too small. Please set your PHP upload limits to at least {$upload_size_desired}M - or ask your hosting provider to do this if you're unsure.",
			];
		} catch ( \Exception $e ) {
			$limits['upload'] = [
				'title'   => 'PHP Upload Limits',
				'ok'      => false,
				'message' => 'Failed to check upload limit. Please ask hosting provider to raise the upload limit for you.'
			];
		}

		// Check max_input_vars.
		try {
			$max_input_vars         = Limits::get_instance()->get_max_input_vars();
			$max_input_vars_desired = 100;
			$max_input_vars_ok      = $max_input_vars < 0 || $max_input_vars >= $max_input_vars_desired;

			$limits['max_input_vars'] = [
				'ok'      => $max_input_vars_ok,
				'title'   => 'PHP Max Input Vars',
				'message' => $max_input_vars_ok ? "is ok at '$max_input_vars'." : "$max_input_vars is too small. Please set your PHP max input vars to at least $max_input_vars_desired - or ask your hosting provider to do this if you're unsure.",
			];
		} catch ( \Exception $e ) {
			$limits['max_input_vars'] = [
				'title'   => 'PHP Max Input Vars',
				'ok'      => false,
				'message' => 'Failed to check input vars limit. Please ask hosting provider to raise the input vars limit for you.'
			];
		}

		// Check max_execution_time.
		try {
			$max_execution_time         = Limits::get_instance()->get_max_execution_time();
			$max_execution_time_desired = 30;
			$max_execution_time_ok      = $max_execution_time <= 0 || $max_execution_time >= $max_execution_time_desired;

			$limits['max_execution_time'] = [
				'ok'      => $max_execution_time_ok,
				'title'   => 'PHP Execution Time',
				'message' => $max_execution_time_ok ? "PHP execution time limit is ok at {$max_execution_time}." : "$max_execution_time is too small. Please set your PHP max execution time to at least $max_execution_time_desired - or ask your hosting provider to do this if you're unsure.",
			];
		} catch ( \Exception $e ) {
			$limits['max_execution_time'] = [
				'title'   => 'PHP Execution Time',
				'ok'      => false,
				'message' => 'Failed to check PHP execution time limit. Please ask hosting provider to raise this limit for you.'
			];
		}

		// Check API connectivity.
		try {
			$response      = wp_safe_remote_get( 'https://api.extensions.envato.com', [
				'user-agent' => 'Mozilla/5.0 (Envato Elements ' . ENVATO_ELEMENTS_VER . ';) ' . home_url(),
				'timeout'    => 5,
			] );
			$response_code = wp_remote_retrieve_response_code( $response );
			if ( $response && ! is_wp_error( $response ) && $response_code === 200 ) {
				$limits['extensions_api'] = [
					'ok'      => true,
					'title'   => 'Envato Extensions Auth API',
					'message' => 'Connected ok.',
				];
			} else {
				$limits['extensions_api'] = [
					'ok'      => false,
					'title'   => 'Envato Extensions Search API',
					'message' => "Extensions API failed. Status '$response_code'. Please ensure PHP is allowed to connect to the host 'api.extensions.envato.com' - or ask your hosting provider to do this if you’re unsure. " . ( is_wp_error( $response ) ? $response->get_error_message() : '' ),
				];
			}
		} catch ( \Exception $e ) {
			$limits['extensions_api'] = [
				'ok'      => true,
				'title'   => 'Envato Extensions Auth API',
				'message' => "Extensions API failed. Please contact the hosting provider and ensure PHP is allowed to connect to the host 'api.extensions.envato.com'. " . $e->getMessage(),
			];
		}

		// Check content API connectivity.
		try {
			$response      = wp_safe_remote_get( 'https://assets.wp.envatoextensions.com/template-kits/ping.json', [
				'user-agent' => 'Mozilla/5.0 (Envato Elements ' . ENVATO_ELEMENTS_VER . ';) ' . home_url(),
				'timeout'    => 5,
			] );
			$response_code = wp_remote_retrieve_response_code( $response );
			if ( $response && ! is_wp_error( $response ) && $response_code === 200 ) {
				$limits['content_api'] = [
					'ok'      => true,
					'title'   => 'Envato Extensions Content API',
					'message' => 'Connected ok.',
				];
			} else {
				$limits['content_api'] = [
					'ok'      => false,
					'title'   => 'Envato Extensions Content API',
					'message' => "Content API failed. Status '$response_code'. Please ensure PHP is allowed to connect to the host 'assets.wp.envatoextensions.com' - or ask your hosting provider to do this if you’re unsure. " . ( is_wp_error( $response ) ? $response->get_error_message() : '' ),
				];
			}
		} catch ( \Exception $e ) {
			$limits['content_api'] = [
				'ok'      => true,
				'title'   => 'Envato Extensions Content API',
				'message' => "Content API failed. Please contact the hosting provider and ensure PHP is allowed to connect to the host 'assets.wp.envatoextensions.com'. " . $e->getMessage(),
			];
		}

		$debug_enabled      = defined( 'WP_DEBUG' ) && WP_DEBUG;
		$limits['wp_debug'] = [
			'ok'      => ! $debug_enabled,
			'title'   => 'WP Debug',
			'message' => $debug_enabled ? 'If you’re on a production website, it’s best to set WP_DEBUG to false, please ask your hosting provider to do this if you’re unsure.' : 'WP Debug is disabled, all ok.',
		];

		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		$current_filesystem_method = get_filesystem_method();
		if ( $current_filesystem_method !== 'direct' ) {
			$limits['filesystem_method'] = [
				'ok'      => false,
				'title'   => 'WordPress Filesystem',
				'message' => 'Please enable WordPress FS_METHOD direct - or ask your hosting provider to do this if you’re unsure.',
			];
		}

		$wp_upload_dir                 = wp_upload_dir();
		$upload_base_dir               = $wp_upload_dir['basedir'];
		$upload_base_dir_writable      = is_writable( $upload_base_dir );
		$limits['wp_content_writable'] = [
			'ok'      => $upload_base_dir_writable,
			'title'   => 'WordPress File Permissions',
			'message' => $upload_base_dir_writable ? 'is ok.' : 'Please set correct WordPress PHP write permissions for the wp-content directory - or ask your hosting provider to do this if you’re unsure.',
		];

		$elementor_pro_installed = class_exists( '\ElementorPro\Plugin' );
		$limits['elementor_pro'] = [
			'ok'      => $elementor_pro_installed,
			'title'   => 'Elementor Pro',
			'message' => $elementor_pro_installed ? 'Elementor Pro is installed and active.' : 'Some of our Free and Premium Templates will fail to install without Elementor Pro.',
		];

		$active_plugins    = get_option( 'active_plugins' );
		$active_plugins_ok = count( $active_plugins ) < 15;
		if ( ! $active_plugins_ok ) {
			$limits['active_plugins'] = [
				'ok'      => false,
				'title'   => 'Active Plugins',
				'message' => 'Please try to reduce the number of active plugins on your WordPress site, as this will slow things down.',
			];
		}

		$structure = get_option( 'permalink_structure' );
		if ( $structure !== '/%postname%/' ) {
			$limits['permalinks'] = [
				'ok'      => false,
				'title'   => 'Permalinks',
				'message' => 'If you are having trouble importing and editing Template Kits, please try changing the WordPress permalinks to "Post name". This can be done by going to Settings > Permalinks.',
			];
		}

		// Elementor sets an option variable if FA5 upgrade is required in wp-content/plugins/elementor/core/upgrade/upgrades.php:507
		$elementor_font_awesome_5_upgrade_needed = get_option( 'elementor_icon_manager_needs_update', null );
		if ( $elementor_font_awesome_5_upgrade_needed === 'yes' ) {
			$limits['elementor_fa5'] = [
				'ok'      => false,
				'title'   => 'Elementor Font Awesome 5',
				'message' => 'We noticed that Elementor needs the Font Awesome 5 upgrade applied. Please do this via the Elementor -> Tools menu.',
			];
		}

        if ( ! class_exists( '\ZipArchive' ) ) {
            $limits['zip_archive'] = [
                'ok'      => false,
                'title'   => 'PHP ZipArchive Extension',
                'message' => 'ZipArchive extension not enabled. Please ask hosting provider or Google search "Enable ZipArchive for PHP".'
            ];
        }

		return $this->format_success(
			[
				'limits' => $limits,
			]
		);
	}

	/**
	 * @return \WP_REST_Response
	 */
	public function get_elementor_global_style_templates() {

		$templates = [];

		$existing_global_styles_query = array(
			'meta_query'     => array(
				array(
					'key'   => '_elementor_template_type',
					'value' => 'kit',
				),
			),
			'post_type'      => 'elementor_library',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
		);
		$current_active_global_style  = get_option( 'elementor_active_kit' );
		$existing_global_styles       = get_posts( $existing_global_styles_query );
		if ( $existing_global_styles && ! is_wp_error( $existing_global_styles ) ) {
			foreach ( $existing_global_styles as $post ) {
				$templates[] = [
					'id'      => $post->ID,
					'default' => $current_active_global_style == $post->ID,
					'title'   => $post->post_title,
				];
			}
		}

		// Return some success to react:
		return $this->format_success( $templates );
	}

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return \WP_REST_Response
	 */
	public function save_elementor_global_style_template( $request ) {

		$global_style_template_id = (int) $request->get_param( 'globalStyleTemplateId' );

		update_option( 'elementor_active_kit', $global_style_template_id );

		// Return some success to react:
		return $this->format_success( [
			'saved' => true,
		] );
	}

	public function register_api_endpoints() {
		$this->register_endpoint( 'resetUserSettings', [ $this, 'reset_users_settings' ] );
		$this->register_endpoint( 'savePreferredStartPage', [ $this, 'save_preferred_start_page' ] );
		$this->register_endpoint( 'getServerLimits', [ $this, 'get_server_limits' ] );
		$this->register_endpoint( 'getElementorGlobalStyleTemplates', [ $this, 'get_elementor_global_style_templates' ] );
		$this->register_endpoint( 'saveElementorGlobalStyleTemplate', [ $this, 'save_elementor_global_style_template' ] );
	}
}
