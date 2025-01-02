<?php
/**
 * Envato Elements: Options
 *
 * Making option management a bit easier for us.
 *
 * @package Envato/Envato_Elements
 * @since 2.0.0
 */

namespace Envato_Elements\Backend;

use Envato_Elements\Utils\Base;
use Envato_Elements\Utils\Extensions_API;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Option saving / getting
 *
 * @since 2.0.0
 */
class Options extends Base {

	const OPTION_KEY = 'envato_elements_options';

	public function __construct() {
		add_action( 'admin_head', array( $this, 'print_admin_env_vars' ) );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'print_admin_env_vars' ] );
		add_action( 'elementor/preview/enqueue_styles', [ $this, 'print_admin_env_vars' ] );
	}

	/**
	 * Prints some variables we can access from within React
	 */
	public function print_admin_env_vars() {
		$admin_options = array(
			'api_nonce'           => wp_create_nonce( 'wp_rest' ),
			//'api_url'   => admin_url( 'admin-ajax.php?action=envato_elements&endpoint=' ),
			'api_url'             => get_rest_url() . 'envato-elements/v2/',
			'elements_token_url'  => Extensions_API::get_instance()->get_token_url(),
			'subscription_status' => Subscription::get_instance()->get_subscription_status(),
			'downloaded_items'    => Downloaded_Items::get_instance()->get_downloaded_items(),
			'dismissed_banners'   => Options::get_instance()->get( 'dismissed_banners', [] ),
			'project_name'        => Options::get_instance()->get( 'project_name', get_bloginfo( 'name' ) ),
			'start_page'          => Options::get_instance()->get( 'start_page', 'welcome' ),
		);
		?>
		<script>
      var envato_elements = <?php echo json_encode( $admin_options ); ?>;
		</script>
		<?php
	}

	/**
	 * @param bool $key
	 * @param bool $default
	 *
	 * @return array|bool|mixed|string|void
	 */
	public function get( $key = false, $default = false ) {

		// Default our project name setting value to the home url
		if ( $key === 'project_name' && ! $default ) {
			$default = get_home_url();
		}

		// We ran into issues with cached option values happening when downloading multiple items quickly one after the other.
		// The fix for this is to grab a fresh entry out of the WordPress DB each time the user requests an option.
		// We don't do this too often in our plugin so it should be safe
		wp_cache_delete( self::OPTION_KEY, 'option' );

		$options = get_option( self::OPTION_KEY, array() );
		if ( ! $options || ! is_array( $options ) ) {
			$options = array();
		}
		$user_id = get_current_user_id();
		if ( $user_id ) {
			$user_options = isset( $options[ $user_id ] ) ? $options[ $user_id ] : array();
			if ( $key !== false ) {
				return isset( $user_options[ $key ] ) ? $user_options[ $key ] : $default;
			}

			return $user_options;
		} else {
			return $default;
		}
	}

	public function set( $key, $value ) {
		$options = get_option( self::OPTION_KEY, array() );
		if ( ! is_array( $options ) ) {
			$options = array();
		}
		$user_id = get_current_user_id();
		if ( $user_id ) {
			if ( ! isset( $options[ $user_id ] ) ) {
				$options[ $user_id ] = array();
			}
			$options[ $user_id ][ $key ] = $value;
			update_option( self::OPTION_KEY, $options, false );
		}
	}

	public function reset_user(){
		$options = get_option( self::OPTION_KEY, array() );
		$user_id = get_current_user_id();
		if ( $user_id ) {
			$options[ $user_id ] = [];
			update_option( self::OPTION_KEY, $options, false );
		}
	}

}
