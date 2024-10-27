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
 * Modal that pops up when in the back end Elementor editor
 *
 * @since 2.0.0
 */
class Elementor_Modal extends Base {

	public function __construct() {

		// This is for the outer Elementor editor, we need JS to add our magic button and register onclick events etc..
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'enqueue_editor_scripts' ) );
		// This is for the inner iframe, we only need CSS in this inner iframe:
		add_action( 'elementor/preview/enqueue_styles', [ $this, 'enqueue_embedded_iframe_styles' ] );
	}

	/**
	 * Load JS for our custom Elementor modal.
	 */
	public function enqueue_editor_scripts() {

		// First load our main react bundle so we've got access to the modal code from within 'elementor_modal.js';
		Welcome::get_instance()->admin_page_assets();
		// Now load our custom elementor_modal.js code and css:
		wp_enqueue_script( 'elements-elementor-modal', ENVATO_ELEMENTS_URI . 'assets/elementor_modal.js', array( 'jquery' ), ENVATO_ELEMENTS_VER );
	}

	public function enqueue_embedded_iframe_styles(){
		wp_enqueue_style( 'envato-elements-admin', ENVATO_ELEMENTS_URI . 'assets/main.css', [], filemtime( ENVATO_ELEMENTS_DIR . 'assets/main.css' ) );
		wp_enqueue_style( 'elements-elementor-modal', ENVATO_ELEMENTS_URI . 'assets/elementor_modal.css', [], ENVATO_ELEMENTS_VER );
	}

}
