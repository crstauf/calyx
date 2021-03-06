<?php
/**
 * Temporary snippets for admin.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}


/**
 * Register temporary admin assets.
 */
function _temporary_action__init() {
	wp_register_script( THEME_PREFIX . '/_temporary/admin', get_theme_file_url( 'temporary/admin.js'  ) );
	wp_register_style(  THEME_PREFIX . '/_temporary/admin', get_theme_file_url( 'temporary/admin.css' ) );
}
add_action( 'init', '_temporary_action__init' );

/**
 * Enqeuue temporary admin assets.
 */
function _temporary_action__admin_enqueue_scripts() {
	wp_enqueue_script( THEME_PREFIX . '/_temporary/admin' );
	wp_enqueue_style(  THEME_PREFIX . '/_temporary/admin' );
}
// add_action( 'admin_enqueue_scripts', '_temporary_action__admin_enqueue_scripts' );

?>