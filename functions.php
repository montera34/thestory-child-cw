<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// template tags
include_once "inc/templates-tags.php";

// the story theme functions redefinition
include_once "inc/thestory-redefinitions.php";


// theme setup main function
function cw_theme_setup() {

	/* Add your nav menus function to the 'init' action hook. */
	add_action( 'init', 'cw_register_menus' );

	// load text domain for child theme
	load_theme_textdomain( 'cw', get_stylesheet_directory_uri() . '/lang' );

} // end montera34 theme setup function
add_action( 'after_setup_theme', 'cw_theme_setup' );

// register custom menus
function cw_register_menus() {
	if ( function_exists( 'register_nav_menus' ) ) {
		register_nav_menus(
		array(
			'cw-pre-center-menu' => __('Header centered menu','cw'),
			'cw-pre-right-menu' => __('Header right menu','cw'),
		)
		);
	}
} // end register custom menus

// load js scripts to avoid conflicts
function cw_load_scripts() {
	wp_enqueue_script(
		'cw-js',
		get_stylesheet_directory_uri() . '/js/cw.js',
		array( 'jquery' ),
		'0.1',
		TRUE
	);
	wp_enqueue_style( 'fontawesome', get_stylesheet_directory_uri().'/fontawesome/css/font-awesome.min.css',NULL,'4.7.0' );
}
add_action( 'wp_enqueue_scripts', 'cw_load_scripts' );

// custom args for loops
function cw_custom_args_for_loops( $query ) {

	if ( is_page_template('template-portfolio-gallery.php') && array_key_exists('post_type', $query->query_vars ) && $query->query_vars['post_type'] == PEXETO_PORTFOLIO_POST_TYPE ) { 
		$query->set( 'order','DESC');
		$query->set( 'orderby','meta_value_num date');
		$query->set( 'meta_key','_pj_date_begin');

	}

	return $query;
}
add_filter( 'pre_get_posts', 'cw_custom_args_for_loops' );
