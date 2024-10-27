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
class CPT_Kits extends CPT {

	/**
	 * Core custom post name for these templates.
	 *
	 * @var string
	 */
	public $cpt_name = 'Imported Kit';

	/**
	 * Core custom post name for these templates.
	 *
	 * @var string
	 */
	public $cpt_slug = 'envato_tk_import';

	public function __construct() {
		parent::__construct();

		add_filter( 'wpseo_sitemap_exclude_post_type', array( $this, 'wpseo_sitemap_exclude_post_type' ), 10, 2 );
	}

	/**
	 * We need to manually exclude this post type from Yoast because it doesn't behave nicely.
	 *
	 * @param $exclude
	 * @param $post_type
	 *
	 * @return bool
	 *
	 * @since 0.0.9
	 */
	public function wpseo_sitemap_exclude_post_type( $exclude, $post_type ) {
		if ( $post_type === $this->cpt_slug ) {
			return true;
		}

		return $exclude;
	}

	public function get_all_uploaded_kits() {
		return get_posts(
			array(
				'posts_per_page' => -1,
				'post_type'      => $this->cpt_slug,
			)
		);
	}

}
