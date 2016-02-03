<?php
// theme setup main function
add_action( 'after_setup_theme', 'cw_theme_setup' );
function cw_theme_setup() {

	/* Add your nav menus function to the 'init' action hook. */
	add_action( 'init', 'cw_register_menus' );

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


