<?php
/**
 * Load the theme.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

add_theme_support( 'html5' );
add_theme_support( 'title-tag' );
add_theme_support( 'post-thumbnails' );
add_theme_support( 'enhanced-enqueues' );

define( 'THEME_PREFIX', 'calyx' );
define( 'CALYX_ABSPATH', __DIR__ . '/' );

!trait_exists( 'Calyx_Features' ) && require_once CALYX_ABSPATH . 'includes/traits.php';
!class_exists( 'Calyx'          ) && require_once CALYX_ABSPATH . 'includes/class-calyx.php';

/**
 * Function to access theme helper singleton.
 */
function Calyx() {
	return Calyx::get_instance();
}

// Initialize!
Calyx();

do_action( THEME_PREFIX . '/initialized' );

?>
