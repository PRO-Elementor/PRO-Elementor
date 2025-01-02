<?php
/**
 * Template Kit Import: Elementor
 *
 * Elementor template display/import.
 *
 * @package Envato/Envato_Template_Kit_Import
 * @since 0.0.2
 */

namespace Envato_Template_Kit_Import;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * New Elementor K it builder code.
 *
 * @since 0.0.2
 */
class Builder_Elementor_Kit extends Builder {

	/*
	 * Which builder this is for (e.g. 'elementor' or 'gutenberg')
	 *
	 * @var string
	 */
	public $builder = ENVATO_TEMPLATE_KIT_IMPORT_TYPE_ELEMENTOR;

	/**
	 * Gets url for entire kit screenshot
	 *
	 * @return string|false
	 */
	public function get_screenshot_url() {
		$template_kit_templates = $this->get_manifest_data();

		if ( ! empty( $template_kit_templates['thumbnail'] ) ) {
			return $template_kit_templates['thumbnail'];
		}


		return false;
	}

	/**
	 * Gets the list of required plugins from metadata
	 *
	 * @return array
	 */
	public function get_requirements() {
		return [
			'plugins'      => $this->get_required_plugins(),
		];
	}

	/**
	 * Gets the list of required plugins.
	 * For now this is just hard coded to "Elementor".
	 * We will update this to support WooCommerce etc.. when those features are made available.
	 *
	 * @return array
	 */
	public function get_required_plugins() {
		$required = [
			[
				"name"    => "Elementor",
				"slug"    => "elementor",
				"version" => "3.5.0",
				"file"    => "elementor/elementor.php",
				"author"  => "Elementor.com",
			]
		];

		return Required_Plugin::get_instance()->check_for_required_plugins( $required );
	}
}
