<?php

if ( function_exists( 'QMX_Benchmark' ))
	QMX_Benchmark();

add_theme_support( 'html5' );
add_theme_support( 'title-tag' );
add_theme_support( 'post-thumbnails' );

require_once 'inc/class.base-cpt.php';
require_once 'inc/functions.' . ( is_admin() ? 'admin' : 'front' ) . '.php';

add_action( 'init', array( 'Calyx_Actions', 'init' ) );
// add_filter( 'http_request_args', array( 'Calyx_Filters', 'http_request_args' ), 5, 2 );

if ( is_admin() ) {

	add_action( 'init',						array( 'Calyx_ActionsAdmin', 'init' ) );
	add_filter( 'mce_buttons_2',			array( 'Calyx_FiltersAdmin', 'mce_buttons_2' ) );
	add_filter( 'tiny_mce_before_init',		array( 'Calyx_FiltersAdmin', 'tiny_mce_before_init' ) );
	add_filter( 'tiny_mce_before_init',		array( 'Calyx_FiltersAdmin', 'tiny_mce_before_init_999999' ), 999999 );

} else {

	add_action( 'init',					array( 'Calyx_ActionsFront', 'init' ) );
	// add_filter('script_loader_tag',		array('enhance_wp_enqueues','tag'),10,2);
	// add_filter('style_loader_tag',		array('enhance_wp_enqueues','tag'),10,2);
	// add_action('wp_head',				array('enhance_wp_enqueues','tags'),8);
	// add_action('wp_head',				array('enhance_wp_enqueues','tags'),9);
	// add_action('wp_footer',				array('enhance_wp_enqueues','tags'),8);
	// add_action('wp_footer',				array('enhance_wp_enqueues','tags'),9);
	register_shutdown_function('calyx_shutdown_function');

}

if ( file_exists( get_stylesheet_directory() . '/_dev.php' ) )
	require_once '_dev.php';


/*
   ###     ######  ######## ####  #######  ##    ##  ######
  ## ##   ##    ##    ##     ##  ##     ## ###   ## ##    ##
 ##   ##  ##          ##     ##  ##     ## ####  ## ##
##     ## ##          ##     ##  ##     ## ## ## ##  ######
######### ##          ##     ##  ##     ## ##  ####       ##
##     ## ##    ##    ##     ##  ##     ## ##   ### ##    ##
##     ##  ######     ##    ####  #######  ##    ##  ######
*/


class Calyx_Actions {

	static function init() {
		wp_register_style( 'calyx/copy', get_theme_asset_uri( 'css/copy.min.css' ), array(), 'init' );
		wp_register_style( 'calyx/google-fonts','https://fonts.googleapis.com/css?family=Roboto' );

		wp_styles()->registered['calyx/copy']->deps[] = 'calyx/google-fonts';
	}

}


/*
######## #### ##       ######## ######## ########   ######
##        ##  ##          ##    ##       ##     ## ##    ##
##        ##  ##          ##    ##       ##     ## ##
######    ##  ##          ##    ######   ########   ######
##        ##  ##          ##    ##       ##   ##         ##
##        ##  ##          ##    ##       ##    ##  ##    ##
##       #### ########    ##    ######## ##     ##  ######
*/


class Calyx_Filters {

	static function http_request_args( $r, $url ) {

		if ( false !== strpos( $url, 'http://api.wordpress.org/themes/update-check' ) )
			return $r; // Not a theme update request. Bail immediately.

		if (
			is_array( $r )
			&& count( $r )
			&& array_key_exists( 'themes', $r )
			&& is_array( $r['themes'] )
			&& count( $r['themes'] )
			&& array_key_exists( 'themes', $r['body'] )
		) {
			$r['body']['themes'] = json_decode( $r['body']['themes'] );
			list( $template, $stylesheet ) = array( get_option( 'template' ), get_option( 'stylesheet' ) );
			unset( $r['body']['themes']->themes->$template, $r['body']['themes']->themes->$stylesheet );
			$r['body']['themes'] = json_encode( $r['body']['themes'] );
		}

		return $r;
	}

}


/*
######## ##     ## ##    ##  ######  ######## ####  #######  ##    ##  ######
##       ##     ## ###   ## ##    ##    ##     ##  ##     ## ###   ## ##    ##
##       ##     ## ####  ## ##          ##     ##  ##     ## ####  ## ##
######   ##     ## ## ## ## ##          ##     ##  ##     ## ## ## ##  ######
##       ##     ## ##  #### ##          ##     ##  ##     ## ##  ####       ##
##       ##     ## ##   ### ##    ##    ##     ##  ##     ## ##   ### ##    ##
##        #######  ##    ##  ######     ##    ####  #######  ##    ##  ######
*/


function get_theme_asset_uri( $theme_path ) {
	// if SCRIPT_DEBUG is true, and file exists, serve un-minified assets
    if (
        ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG )
		|| !file_exists( trailingslashit( get_stylesheet_directory() ) . $theme_path )
    ) {
        $unminified_theme_path = str_replace( '.min', '', $theme_path );
        if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $unminified_theme_path ) )
            $theme_path = $unminified_theme_path;
    }

    return trailingslashit( get_stylesheet_directory_uri() ) . $theme_path;
}

?>
