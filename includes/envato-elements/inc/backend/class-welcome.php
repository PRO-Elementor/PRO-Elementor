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
	 * Registers our main "Elements" menu in the sidebar
	 */
	public function admin_menu() {
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_page_assets' ], 100 );
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
