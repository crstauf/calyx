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
	include_once __DIR__ . '/class-calyx-server-state.php';
	include_once __DIR__ . '/samples/class-calyx-cpt-sample.php';
}
add_action( THEME_PREFIX . '/include_files', '_develop_action__theme__include_files' );

function _develop_action__admin_bar_menu( $bar ) {
	$bar->add_node( array(
		'id' => THEME_PREFIX . '-style-guide',
		'title' => 'View Style Guide',
		'parent' => 'site-name',
		'href' => add_query_arg( 'style-guide', 1, home_url() ),
		'meta' => array(
			'target' => '_blank',
		),
	) );
}
add_action( 'admin_bar_menu', '_develop_action__admin_bar_menu', 50 );

function _develop_filter__template_include( $template ) {
	if (
		!is_front_page()
		|| !array_key_exists( 'style-guide', $_GET )
		|| !file_exists( __DIR__ . '/style-guide.php' )
	)
		return $template;

	return __DIR__ . '/style-guide.php';
}
add_filter( 'template_include', '_develop_filter__template_include' );

?>