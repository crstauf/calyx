<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

add_action( 'init', function() {

	wp_register_script( THEME_PREFIX . '/_temporary', get_theme_asset_url( 'assets/js/_temporary.js'   ) );
	wp_register_style(  THEME_PREFIX . '/_temporary', get_theme_asset_url( 'assets/css/_temporary.css' ) );

	wp_register_script( THEME_PREFIX . '/critical/_temporary', get_theme_asset_url( 'assets/crtiical/_temporary.js'  ) );
	wp_register_style(  THEME_PREFIX . '/critical/_temporary', get_theme_asset_url( 'assets/critical/_temporary.css' ) );

} );

?>