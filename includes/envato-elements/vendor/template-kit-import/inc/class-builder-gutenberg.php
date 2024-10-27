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
 * Gutenberg builder code.
 *
 * @since 0.0.2
 */
class Builder_Gutenberg extends Builder {

	/*
	 * Which builder this is for (e.g. 'elementor' or 'gutenberg')
	 *
	 * @var string
	 */
	public $builder = 'gutenberg';

}
