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
 * CPT.
 *
 * Class to handle everything Custom Post Type related.
 *
 * @since 0.0.2
 */
abstract class CPT extends Base {


	/**
	 * Core custom post name for these templates.
	 *
	 * @var string
	 */
	public $cpt_name = '';


	/**
	 * Core custom post name for these templates.
	 *
	 * @var string
	 */
	public $cpt_slug = '';


	/**
	 * Core custom post name for these templates.
	 *
	 * @var string
	 */
	public $menu_name = '';


	/**
	 * Core custom post name for these templates.
	 *
	 * @var string
	 */
	public $menu_parent = ENVATO_TEMPLATE_KIT_IMPORT_SLUG;


	/**
	 * Do we allow editing
	 *
	 * @var array
	 */
	public $supports = array( 'title', 'author', 'thumbnail', 'elementor', 'page-attributes' );


	/**
	 * Initializing Elements Content App plugin.
	 *
	 * @since 0.0.2
	 * @access private
	 */
	public function __construct() {

		parent::__construct();

		add_action( 'init', array( $this, 'register_custom_post_type' ) );
		add_filter( 'parent_file', array( $this, 'override_wordpress_submenu' ) );
	}

	/**
	 * We override the "submenu_file" WordPress global so that the correct submenu is highlighted when on our custom admin page.
	 *
	 * @param string $this_parent_file Current parent file for menu rendering.
	 *
	 * @return string
	 */
	public function override_wordpress_submenu( $this_parent_file ) {
		global $post, $submenu_file;
		if ( is_admin() && $post && $post->ID && $this->cpt_slug === $post->post_type ) {
			$submenu_file     = 'edit.php?post_type=' . $this->cpt_slug; // WPCS: override ok.
			$this_parent_file = $this->menu_parent;
		}

		return $this_parent_file;
	}


	/**
	 * Adds our custom submenu page.
	 */
	public function admin_menu() {

		add_submenu_page(
			$this->menu_parent,
			$this->menu_name,
			$this->menu_name,
			'manage_options',
			'edit.php?post_type=' . $this->cpt_slug
		);

	}

	/**
	 * Here is our magical custom post type that stores all our Elementor site wide styles.
	 *
	 * @since 0.0.2
	 */
	public function register_custom_post_type() {

		register_post_type( $this->cpt_slug, $this->get_cpt_args() );

	}

	/**
	 * These are the args used to register our CPT.
	 *
	 * @return array args
	 */
	public function get_cpt_args() {

		$labels = array(
			'name'               => $this->cpt_name . 's',
			'singular_name'      => $this->cpt_name,
			'menu_name'          => $this->cpt_name . 's',
			'parent_item_colon'  => 'Parent ' . $this->cpt_name . ':',
			'all_items'          => 'All ' . $this->cpt_name . 's',
			'view_item'          => 'View ' . $this->cpt_name,
			'add_new_item'       => 'Add New ' . $this->cpt_name,
			'add_new'            => 'New ' . $this->cpt_name,
			'edit_item'          => 'Edit ' . $this->cpt_name,
			'update_item'        => 'Update ' . $this->cpt_name,
			'search_items'       => 'Search ' . $this->cpt_name . 's',
			'not_found'          => 'No ' . $this->cpt_name . 's found',
			'not_found_in_trash' => 'No ' . $this->cpt_name . 's found in Trash',
		);

		return array(
			'description'         => $this->cpt_name . 's',
			'labels'              => $labels,
			'supports'            => $this->supports,
			'taxonomies'          => array(),
			'hierarchical'        => true,
			'public'              => defined( 'ENVATO_TEMPLATE_KIT_IMPORT_DEV' ) && ENVATO_TEMPLATE_KIT_IMPORT_DEV,
			'show_in_menu'        => defined( 'ENVATO_TEMPLATE_KIT_IMPORT_DEV' ) && ENVATO_TEMPLATE_KIT_IMPORT_DEV,
			'show_in_nav_menus'   => false,
			'exclude_from_search' => true,
			'menu_position'       => 36,
			'menu_icon'           => 'dashicons-download',
			'can_export'          => false,
			'has_archive'         => false,
			'publicly_queryable'  => false,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'capabilities'        => array(
				'create_posts' => 'do_not_allow',
			),
			'map_meta_cap'        => true,
		);
	}


}
