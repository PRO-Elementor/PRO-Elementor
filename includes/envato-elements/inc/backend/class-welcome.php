<?php
/**
 * Envato Elements:
 *
 * Elements Welcome Page UI.
 *
 * @package Envato/Envato_Elements
 * @since 2.0.0
 */

namespace Envato_Elements\Backend;

use Envato_Elements\Utils\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Envato Elements Welcome Page UI.
 *
 * @since 2.0.0
 */
class Welcome extends Base{

	/**
	 * constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_page_assets' ], 100 );
	}

	/**
	 * Registers our main "Elements" menu in the sidebar
	 */
	public function admin_menu() {
		$page = add_menu_page(
			__( 'Envato Elements', 'envato-elements' ),
			__( 'Elements', 'envato-elements' ),
			'edit_posts',
			ENVATO_ELEMENTS_SLUG,
			[ $this, 'admin_page_open' ],
			ENVATO_ELEMENTS_URI . 'assets/navigation.svg',
			'58.6'
		);
		add_action( 'admin_print_scripts-' . $page, [ $this, 'admin_page_assets' ] );

		$submenu = add_submenu_page(
			ENVATO_ELEMENTS_SLUG,
			__( 'Envato Elements', 'envato-elements' ),
			__( 'Welcome', 'envato-elements' ),
			'edit_posts',
			ENVATO_ELEMENTS_SLUG,
			[ $this, 'admin_page_open' ]
		);

		$submenu = add_submenu_page(
			ENVATO_ELEMENTS_SLUG,
			__( 'Template Kits', 'envato-elements' ),
			__( 'Template Kits', 'envato-elements' ),
			'edit_posts',
			ENVATO_ELEMENTS_SLUG . '#/template-kits/premium-kits',
			[ $this, 'admin_page_open' ]
		);

		$submenu = add_submenu_page(
			ENVATO_ELEMENTS_SLUG,
			__( 'Free Kits', 'envato-elements' ),
			__( 'Free Kits', 'envato-elements' ),
			'edit_posts',
			ENVATO_ELEMENTS_SLUG . '#/template-kits/free-kits',
			[ $this, 'admin_page_open' ]
		);

		$submenu = add_submenu_page(
			ENVATO_ELEMENTS_SLUG,
			__( 'Free Blocks', 'envato-elements' ),
			__( 'Free Blocks', 'envato-elements' ),
			'edit_posts',
			ENVATO_ELEMENTS_SLUG . '#/template-kits/free-blocks',
			[ $this, 'admin_page_open' ]
		);

		$submenu = add_submenu_page(
			ENVATO_ELEMENTS_SLUG,
			__( 'Installed Kits', 'envato-elements' ),
			__( 'Installed Kits', 'envato-elements' ),
			'edit_posts',
			ENVATO_ELEMENTS_SLUG . '#/template-kits/installed-kits',
			[ $this, 'admin_page_open' ]
		);

		$submenu = add_submenu_page(
			ENVATO_ELEMENTS_SLUG,
			__( 'Photos', 'envato-elements' ),
			__( 'Photos', 'envato-elements' ),
			'edit_posts',
			ENVATO_ELEMENTS_SLUG . '#/photos',
			[ $this, 'admin_page_open' ]
		);

		$submenu = add_submenu_page(
			ENVATO_ELEMENTS_SLUG,
			__( 'Settings', 'envato-elements' ),
			__( 'Settings', 'envato-elements' ),
			'edit_posts',
			ENVATO_ELEMENTS_SLUG . '#/settings',
			[ $this, 'admin_page_open' ]
		);

	}

	/**
	 * Called when the plugin page is opened.
	 */
	public function admin_page_open(){
		?>
		<div id="envato-elements-app-holder"></div>
		<script type="text/javascript">
			jQuery(function(){
        var appHolder = document.getElementById( 'envato-elements-app-holder' );
        if (appHolder && 'undefined' !== typeof window.envatoElements) {
					window.envatoElements.initBackend( appHolder );
        }
      })
		</script>
		<?php
	}

	/**
	 * Assets required for the admin page to render correctly (i.e. all our react stuff)
	 */
	public function admin_page_assets(){
		wp_enqueue_style( 'envato-elements-admin', ENVATO_ELEMENTS_URI . 'assets/main.css', [], filemtime( ENVATO_ELEMENTS_DIR . 'assets/main.css' ) );
		wp_enqueue_script( 'envato-elements-admin', ENVATO_ELEMENTS_URI . 'assets/main.js', [], filemtime( ENVATO_ELEMENTS_DIR . 'assets/main.js' ), true );
	}

}
