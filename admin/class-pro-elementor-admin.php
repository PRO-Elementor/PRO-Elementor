<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://pro-elementor.com
 * @since      1.0.0
 *
 * @package    Pro_Elementor
 * @subpackage Pro_Elementor/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pro_Elementor
 * @subpackage Pro_Elementor/admin
 * @author     PRO-Elementor.com <hi@pro-elementor.com>
 */
class Pro_Elementor_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since     1.0.0
	 * @access    private
	 * @var       string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since     1.0.0
	 * @access    private
	 * @var       string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name    The name of this plugin.
	 * @param    string    $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pro_Elementor_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pro_Elementor_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pro-elementor-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pro_Elementor_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pro_Elementor_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pro-elementor-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Filters text with its translation.
	 *
	 * @since 1.0.0
	 *
	 * @param    string    $translation    Translated text.
	 * @param    string    $text           Text to translate.
	 * @param    string    $domain         Text domain. Unique identifier for retrieving translated strings.
	 */
	public function text_strings( $translated_text, $text, $domain ) {
		switch ( $translated_text ) {
			case 'Pro Elements':
				$translated_text = __( 'PRO Elementor', 'elementor-pro' );
				break;

			case 'Pro Elements plugin requires installing the Elementor plugin':
				$translated_text = __( 'PRO Elementor plugin requires installing the Elementor plugin', 'elementor-pro' );
				break;

			case 'You\'re not using Pro Elements yet!':
				$translated_text = __( 'You\'re not using PRO Elementor yet!', 'elementor-pro' );
				break;

			case 'Activate the Elementor plugin to start using all of Pro Elements plugin’s features.':
				$translated_text = __( 'Activate the Elementor plugin to start using all of PRO Elementor plugin’s features.', 'elementor-pro' );
				break;

			case '%1$sPro Elements  requires newer version of the Elementor plugin%2$s Update the Elementor plugin to reactivate the Pro Elements plugin.':
				$translated_text = __( '%1$sPRO Elementor requires newer version of the Elementor plugin%2$s Update the Elementor plugin to reactivate the PRO Elementor plugin.', 'elementor-pro' );
				break;
		}

		return $translated_text;
	}

	/**
	 * Add a settings page link to a menu.
	 *
	 * @link      https://codex.wordpress.org/Administration_Menus
	 * @since     1.0.0
	 * @return    void
	 */
	public function add_menu() {
		add_menu_page(
			esc_html__( 'PRO Elementor Settings', 'pro-elementor' ),
			'PRO Elementor',
			'manage_options',
			'pro-elementor-settings',
			array( $this, 'page_options' ),
			'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMSIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHdpZHRoPSI1MTIiIGhlaWdodD0iNTEyIiB4PSIwIiB5PSIwIiB2aWV3Qm94PSIwIDAgMjQgMjQiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTIiIHhtbDpzcGFjZT0icHJlc2VydmUiIGNsYXNzPSIiPjxnPjxwYXRoIGQ9Ik0yNCAxOWMwIDIuMjEtMS43OSA0LTQgNEg0Yy0yLjIxIDAtNC0xLjc5LTQtNCAwLS41NS40NS0xIDEtMXMxIC40NSAxIDFjMCAxLjEuOSAyIDIgMmgxNmMxLjEgMCAyLS45IDItMiAwLS41NS40NS0xIDEtMXMxIC40NSAxIDFaTTEgN2MuNTUgMCAxLS40NSAxLTEgMC0xLjEuOS0yIDItMmgxNmMxLjEgMCAyIC45IDIgMiAwIC41NS40NSAxIDEgMXMxLS40NSAxLTFjMC0yLjIxLTEuNzktNC00LTRINEMxLjc5IDIgMCAzLjc5IDAgNmMwIC41NS40NSAxIDEgMVptLTEgOXYtNS41QTIuNSAyLjUgMCAwIDEgMi41IDhINGMxLjY1IDAgMyAxLjM1IDMgM3MtMS4zNSAzLTMgM0gydjJjMCAuNTUtLjQ1IDEtMSAxcy0xLS40NS0xLTFabTItNGgyYy41NSAwIDEtLjQ1IDEtMXMtLjQ1LTEtMS0xSDIuNWMtLjI4IDAtLjUuMjItLjUuNVYxMlptMTEuNTcgMS41NSAxLjI2IDEuODljLjMxLjQ2LjE4IDEuMDgtLjI4IDEuMzktLjE3LjExLS4zNi4xNy0uNTUuMTctLjMyIDAtLjY0LS4xNi0uODMtLjQ1TDExLjQ3IDE0aC0xLjQ2djJjMCAuNTUtLjQ1IDEtMSAxcy0xLS40NS0xLTF2LTUuNWEyLjUgMi41IDAgMCAxIDIuNS0yLjVoMS41YzEuNjUgMCAzIDEuMzUgMyAzIDAgMS4wOC0uNTcgMi4wMy0xLjQzIDIuNTVaTTEwIDEyaDIuMDJjLjU1IDAgLjk5LS40NS45OS0xcy0uNDUtMS0xLTFoLTEuNWMtLjI4IDAtLjUuMjItLjUuNVYxMlptMTQtLjV2MmMwIDEuOTMtMS41NyAzLjUtMy41IDMuNVMxNyAxNS40MyAxNyAxMy41di0yQzE3IDkuNTcgMTguNTcgOCAyMC41IDhTMjQgOS41NyAyNCAxMS41Wm0tMiAwYzAtLjgzLS42Ny0xLjUtMS41LTEuNXMtMS41LjY3LTEuNSAxLjV2MmMwIC44My42NyAxLjUgMS41IDEuNXMxLjUtLjY3IDEuNS0xLjV2LTJaIiBmaWxsPSIjZmZmZmZmIiBvcGFjaXR5PSIxIiBkYXRhLW9yaWdpbmFsPSIjMDAwMDAwIj48L3BhdGg+PC9nPjwvc3ZnPg=='
		);

		add_submenu_page(
			'pro-elementor-settings',
			esc_html__( 'PRO Elementor Settings', 'pro-elementor' ),
			esc_html__( 'Settings', 'pro-elementor' ),
			'manage_options',
			'pro-elementor-settings'
		);

		add_submenu_page(
			'pro-elementor-settings',
			esc_html__( 'Template Kits', 'pro-elementor' ),
			esc_html__( 'Template Kits', 'pro-elementor' ),
			'edit_posts',
			'pro-elementor-template-kits',
			array( \Envato_Elements\Backend\Welcome::get_instance(), 'admin_page_open' )
		);
	}

	/**
	 * Create the options page.
	 *
	 * @since     1.0.0
	 * @return    void
	 */
	public function page_options() {
		add_settings_section(
			'pro_elementor_license',
			__( 'PRO Elementor License', 'pro-elementor' ),
			array( $this, 'pro_elementor_license_key_settings_section' ),
			'pro-elementor-settings',
			array(
				'before_section' => '<div class="pro-elementor-license">',
				'after_section'  => '</div>',
			)
		);

		add_settings_field(
			'pro_elementor_license_key',
			'<label for="pro_elementor_license_key">' . __( 'License Key', 'pro-elementor' ) . '</label>',
			array( $this, 'pro_elementor_license_key_settings_field' ),
			'pro-elementor-settings',
			'pro_elementor_license',
		);
		?>

		<div class="wrap">
			<h2><?php esc_html_e( 'PRO Elementor Options', 'pro-elementor' ); ?></h2>
			<form method="post" action="options.php">
			<?php
				do_settings_sections( 'pro-elementor-settings' );
				settings_fields( 'pro_elementor_license' );
				submit_button();
			?>
			</form>
		<?php
	}

	/**
	 * Add content to the settings section.
	 *
	 * @return    void
	 */
	public function pro_elementor_license_key_settings_section() {
		if ( empty( get_option( 'pro_elementor_license_key' ) ) || 'free' === get_option( 'pro_elementor_license_type' ) ) {
			printf( '<div class="notice notice-info">%s. <a href="%s" target="_blank">Grab it now</a>!</div>',
				__( 'Use discount code <b>WELCOME</b> and get up to 50% off', 'pro-elementor' ),
				'https://pro-elementor.com/pricing/'
			);
		}

		echo '<p>' . esc_html__( 'Activate your license to enjoy the benefits of automatic updates and access to hundreds of premium Elementor Template Kits.', 'pro-elementor' ) . '</p>';
	}

	/**
	 * Output the license key settings field.
	 *
	 * @return    void
	 */
	public function pro_elementor_license_key_settings_field() {
		$license = get_option( 'pro_elementor_license_key' );
		$type    = get_option( 'pro_elementor_license_type' );
		$status  = get_option( 'pro_elementor_license_status' );
		$expires = get_option( 'pro_elementor_license_expires' );

		if ( 32 === strlen( $license ) ) {
			$license = str_replace( substr( $license, 4, 24 ), str_repeat( '*', 24 ), $license );
		}

		printf(
			'<input type="text" class="regular-text" id="pro_elementor_license_key" name="pro_elementor_license_key" value="%s" />',
			esc_attr( $license )
		);

		if ( 'valid' !== $status ) {
			$buttons = array(
				array(
					'name'  => 'edd_license_activate',
					'label' => __( 'Activate License', 'pro-elementor' )
				)
			);
		} else {
			$buttons = array(
				array(
					'name'  => 'edd_license_deactivate',
					'label' => __( 'Deactivate License', 'pro-elementor' )
				),
				array(
					'name'  => 'edd_refresh_license',
					'label' => __( 'Refresh License', 'pro-elementor' )
				)
			);
		}

		wp_nonce_field( 'pro_elementor_nonce', 'pro_elementor_nonce' );

		foreach( $buttons as $button ) {
			printf(
				'<input type="submit" class="button-secondary" name="%s" value="%s"/>',
				$button['name'],
				$button['label']
			);
		}

		if ( 'free' === $type ) {
			printf( '<p class="description">%s</p>',
				wp_sprintf(
					esc_html__( '%sUpgrade your license%s to access to hundreds of premium Elementor Template Kits.', 'pro-elementor' ),
					'<a href="https://pro-elementor.com/profile/" target="_blank">',
					'</a>'
				)
			);
		}

		if ( $expires && 'valid' === $status && 'paid' === $type ) {
			if ( 'lifetime' === $expires ) {
				printf( '<p class="description">%s</p>',
					esc_html__( 'You have a lifetime license that will never expire!', 'pro-elementor' )
				);
			} else {
				$current_time = current_time( 'timestamp' );
				$expired      = strtotime( $expires, $current_time ) < $current_time ? true : false;

				printf( '<p class="description">%s</p>',
					wp_sprintf(
						$expired ?
						esc_html__( 'Your license key expired on %1$s. Please %2$srenew your license key%3$s.', 'pro-elementor' ) :
						esc_html__( 'Your license key expires on %1$s.', 'pro-elementor' ),
						date_i18n( get_option( 'date_format' ), strtotime( $expires, $current_time ) ),
						'<a href="https://pro-elementor.com/profile/" target="_blank">',
						'</a>'
					)
				);
			}
		}

		if ( empty( $license ) ) {
			printf( '<p class="description">%s</p>',
				esc_html__( 'Enter your license key.', 'pro-elementor' )
			);
		} elseif ( 'valid' !== $status ) {
			printf( '<p class="description">%s</p>',
				esc_html__( 'Please ensure you click the "Save" button to store the license key first, then click the "Activate License" button to activate it.', 'pro-elementor' )
			);
		}
	}

	/**
	 * Register the license key setting in the options table.
	 *
	 * @since     1.0.0
	 * @return    void
	 */
	public function register_option() {
		register_setting( 'pro_elementor_license', 'pro_elementor_license_key', array( $this, 'sanitize_license' ) );
	}

	/**
	 * Sanitize the license key.
	 *
	 * @since    1.0.0
	 *
	 * @param    string    $new    The value of license key.
	 */
	public function sanitize_license( $new ) {
		$old = get_option( 'pro_elementor_license_key' );

		if ( $old && $old !== $new ) {
			delete_option( 'pro_elementor_license_status' );
		}

		return sanitize_text_field( $new );
	}

	/**
	 * Activate the license key.
	 *
	 * @since     1.0.0
	 * @return    void
	 */
	public function activate_license() {
		if ( ! isset( $_POST['edd_license_activate'] ) && ! isset( $_POST['edd_refresh_license'] ) ) {
			return;
		}

		if ( ! check_admin_referer( 'pro_elementor_nonce', 'pro_elementor_nonce' ) ) {
			return;
		}

		$license = trim( get_option( 'pro_elementor_license_key' ) );

		if ( ! $license ) {
			$license = ! empty( $_POST['pro_elementor_license_key'] ) ? sanitize_text_field( $_POST['pro_elementor_license_key'] ) : '';
		}

		if ( ! $license ) {
			return;
		}

		$api_params = array(
			'edd_action'  => 'activate_license',
			'license'     => $license,
			'item_id'     => PRO_ELEMENTOR_ITEM_ID,
			'item_name'   => 'PRO Elementor',
			'url'         => home_url(),
			'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production'
		);

		$response = wp_remote_post(
			PRO_ELEMENTOR_URL,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params
			)
		);

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'pro-elementor' );
			}
		} else {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {
				switch ( $license_data->error ) {
					case 'expired':
						$message = wp_sprintf(
							esc_html__( 'Your license key expired on %s.', 'pro-elementor' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'disabled':
					case 'revoked':
						$message = esc_html__( 'Your license key has been disabled.', 'pro-elementor' );
						break;

					case 'missing':
						$message = esc_html__( 'Invalid license.', 'pro-elementor' );
						break;

					case 'invalid':
					case 'site_inactive':
						$message = esc_html__( 'Your license is not active for this URL.', 'pro-elementor' );
						break;

					case 'item_name_mismatch':
						$message = esc_html__( 'This appears to be an invalid license key for PRO Elementor.', 'pro-elementor' );
						break;

					case 'no_activations_left':
						$message = esc_html__( 'Your license key has reached its activation limit.', 'pro-elementor' );
						break;

					default:
						$message = esc_html__( 'An error occurred, please try again.', 'pro-elementor' );
						break;
				}
			}
		}

		if ( ! empty( $message ) ) {
			$redirect = add_query_arg(
				array(
					'page'          => 'pro-elementor-settings',
					'sl_activation' => 'false',
					'message'       => rawurlencode( $message )
				),

				admin_url( 'admin.php' )
			);

			wp_safe_redirect( $redirect );

			exit();
		}

		if ( 'valid' === $license_data->license ) {
			update_option( 'pro_elementor_license_key', $license );
			update_option( 'pro_elementor_license_expires', $license_data->expires );

			if ( '1' === $license_data->price_id ) {
				update_option( 'pro_elementor_license_type', 'free' );
			} else {
				update_option( 'pro_elementor_license_type', 'paid' );
			}
		}

		update_option( 'pro_elementor_license_status', $license_data->license );
		wp_safe_redirect( admin_url( 'admin.php?page=pro-elementor-settings' ) );

		exit();
	}

	/**
	 * Deactivate the license key.
	 * This will decrease the site count.
	 *
	 * @return    void
	 */
	public function deactivate_license() {
		if ( isset( $_POST['edd_license_deactivate'] ) ) {
			if ( ! check_admin_referer( 'pro_elementor_nonce', 'pro_elementor_nonce' ) ) {
				return;
			}

			$license = trim( get_option( 'pro_elementor_license_key' ) );

			$api_params = array(
				'edd_action'  => 'deactivate_license',
				'license'     => $license,
				'item_id'     => PRO_ELEMENTOR_ITEM_ID,
				'item_name'   => 'PRO Elementor',
				'url'         => home_url(),
				'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production'
			);

			$response = wp_remote_post(
				PRO_ELEMENTOR_URL,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params
				)
			);

			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
				if ( is_wp_error( $response ) ) {
					$message = $response->get_error_message();
				} else {
					$message = __( 'An error occurred, please try again.', 'pro-elementor' );
				}

				$redirect = add_query_arg(
					array(
						'page'          => 'pro-elementor-settings',
						'sl_activation' => 'false',
						'message'       => rawurlencode( $message )
					),
					admin_url( 'admin.php' )
				);

				wp_safe_redirect( $redirect );

				exit();
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( 'deactivated' === $license_data->license || 'failed' === $license_data->license ) {
				delete_option( 'pro_elementor_license_status' );
			}

			wp_safe_redirect( admin_url( 'admin.php?page=pro-elementor-settings' ) );

			exit();
		}
	}

	/**
	 * Initialize the updater.
	 *
	 * @since     1.0.0
	 * @return    void
	 */
	public function updater() {
		$doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;

		if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
			return;
		}

		$license_key = trim( get_option( 'pro_elementor_license_key' ) );

		$edd_updater = new EDD_SL_Plugin_Updater(
			PRO_ELEMENTOR_URL,
			PRO_ELEMENTOR_FILE,
			array(
				'version' => PRO_ELEMENTOR_VERSION,
				'license' => $license_key,
				'item_id' => PRO_ELEMENTOR_ITEM_ID,
				'author'  => 'PRO-Elementor.com',
				'beta'    => false
		) );
	}

	/**
	 * Catche errors from the activation and displaying it to the customer.
	 */
	public function edd_admin_notices() {
		if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {
			switch ( $_GET['sl_activation'] ) {
				case 'false':
					$message = urldecode( $_GET['message'] );
					?>
					<div class="error">
						<p><?php echo wp_kses_post( $message ); ?></p>
					</div>
					<?php
					break;
				case 'true':
				default:
					break;
			}
		}
	}

	/**
	 * Filter whether to show the Elementor top bar.
	 *
	 * @since    1.0.0
	 *
	 * @param    bool      $is_top_bar_active Whether to show the Elementor top bar.
	 * @param    WP_Screen $current_screen    WordPress current screen object.
	 */
	public function remove_admin_top_bar( $is_top_bar_active, $current_screen ) {
		if ( false !== strpos( $current_screen->id ?? '', 'pro-elementor' ) ) {
			$is_top_bar_active = false;
		}

		return $is_top_bar_active;
	}

	/**
	 * Remove text displayed in the admin footer.
	 *
	 * @since    1.0.0
	 *
	 * @param     string    $footer_text The original text.
	 * @return    string    $footer_text The new text.
	 */
	public function admin_footer_text( $footer_text ) {
		$current_screen = get_current_screen();

		if ( false !== strpos( $current_screen->id ?? '', 'pro-elementor' ) ) {
			$footer_text = '';
		}

		return $footer_text;
	}

}
