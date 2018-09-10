<?php
/**
 * Functions loaded when WP_DEVELOP is true.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Include files in development environment.
 */
function _develop_action__theme__include_files() {
	include_once __DIR__ . '/samples/class-calyx-cpt-sample.php';
}
add_action( THEME_PREFIX . '/include_files', '_develop_action__theme__include_files' );

?>