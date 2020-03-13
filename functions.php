<?php
/**
 * Load the theme.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

defined( 'THEME_PREFIX'   ) || define( 'THEME_PREFIX', 'calyx' );
defined( 'THEME_ABSPATH'  ) || define( 'THEME_ABSPATH',  __DIR__ . '/' );
defined( 'THEME_INCLUDES' ) || define( 'THEME_INCLUDES', THEME_ABSPATH . 'includes/' );

	do_action( 'qm/start', THEME_PREFIX . '/setup' );

add_theme_support( 'html5' );
add_theme_support( 'title-tag' );
add_theme_support( 'woocommerce' );
add_theme_support( 'post-thumbnails' );
add_theme_support( 'enhanced-enqueues' );

	do_action( 'qm/lap', THEME_PREFIX . '/setup', 'supports' );

# Include files.
require_once THEME_ABSPATH . 'includes/index.php';

do_action( THEME_PREFIX . '/init' );

	do_action( 'qm/lap',  THEME_PREFIX . '/setup', 'init' );
	do_action( 'qm/stop', THEME_PREFIX . '/setup' );

do_action( THEME_PREFIX . '/initialized' );

?>
