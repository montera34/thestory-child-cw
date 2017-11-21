<?php
// theme setup main function
add_action( 'after_setup_theme', 'cw_theme_setup' );
function cw_theme_setup() {

	/* Add your nav menus function to the 'init' action hook. */
	add_action( 'init', 'cw_register_menus' );

	/* Load JavaScript files on the 'wp_enqueue_scripts' action hook. */
	add_action( 'wp_enqueue_scripts', 'cw_load_scripts' );

	// load text domain for child theme
	load_theme_textdomain( 'cw', get_stylesheet_directory_uri() . '/lang' );

} // end montera34 theme setup function

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
