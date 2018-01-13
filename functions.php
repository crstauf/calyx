<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

add_theme_support( 'html5' );
add_theme_support( 'title-tag' );
add_theme_support( 'post-thumbnails' );

/*
 ######   #######  ##    ##  ######  ########    ###    ##    ## ########  ######
##    ## ##     ## ###   ## ##    ##    ##      ## ##   ###   ##    ##    ##    ##
##       ##     ## ####  ## ##          ##     ##   ##  ####  ##    ##    ##
##       ##     ## ## ## ##  ######     ##    ##     ## ## ## ##    ##     ######
##       ##     ## ##  ####       ##    ##    ######### ##  ####    ##          ##
##    ## ##     ## ##   ### ##    ##    ##    ##     ## ##   ###    ##    ##    ##
 ######   #######  ##    ##  ######     ##    ##     ## ##    ##    ##     ######
*/

define( 'THEME_PREFIX', 'Calyx' );
define( 'THEME_IMG_DIR_PATH', trailingslashit( STYLESHEETPATH ) . 'img/' );
define( 'THEME_IMG_DIR_URI',  trailingslashit( get_stylesheet_directory_uri() ) . 'img/' );


/*
######## #### ##       ########  ######
##        ##  ##       ##       ##    ##
##        ##  ##       ##       ##
######    ##  ##       ######    ######
##        ##  ##       ##             ##
##        ##  ##       ##       ##    ##
##       #### ######## ########  ######
*/

require_once 'inc/class.calyx.php';
require_once 'inc/class.calyx.' . ( is_admin() ? 'admin' : 'front' ) . '.php';

require_once 'inc/class.cpt.php';

require_once 'inc/class.customizer.php';
require_once 'inc/class.image-tag.php';

if ( WP_DEBUG )
	require_once '_dev.php';

Calyx()->benchmark( basename( __FILE__ ) . ':' . __LINE__, 1 );

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

    function __construct() {

		add_action( 'init', array( &$this, 'init' ) );

    }

	function init() {

        if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
            return;

		wp_register_script( 'lazysizes',     get_theme_asset_uri( 'js/lazysizes.min.js' ), array(), '2.0.2' );
		wp_register_script( 'webfontloader', get_theme_asset_uri( 'js/webfontloader.js' ), array(), '1.6.26' );

        wp_register_style( THEME_PREFIX . '/copy',            get_theme_asset_uri( 'css/copy/copy.min.css'       ), array( THEME_PREFIX . '/fonts' ), 'init' );
		wp_register_style( THEME_PREFIX . '/copy/responsive', get_theme_asset_uri( 'css/copy/responsive.min.css' ), array( THEME_PREFIX . '/copy' ), 'init', '(max-width: 809px)' );
		wp_register_style( THEME_PREFIX . '/customizer',      get_theme_asset_uri( 'css/customizer.min.css'      ) );
		wp_register_style( THEME_PREFIX . '/fonts',           get_theme_asset_uri( 'fonts/fonts.min.css'         ), array(), 'init' );

    }

}

Calyx()->benchmark( basename( __FILE__ ) . ':' . __LINE__, 1 );


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

    function __construct() {

        add_filter( 'http_request_args', array( &$this, 'http_request_args' ) );

    }

    function http_request_args( $r, $url ) {
		Calyx()->benchmark( $url, 1 );
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

Calyx()->benchmark( basename( __FILE__ ) . ':' . __LINE__, 1 );


/*
######## ##     ## ##    ##  ######  ######## ####  #######  ##    ##  ######
##       ##     ## ###   ## ##    ##    ##     ##  ##     ## ###   ## ##    ##
##       ##     ## ####  ## ##          ##     ##  ##     ## ####  ## ##
######   ##     ## ## ## ## ##          ##     ##  ##     ## ## ## ##  ######
##       ##     ## ##  #### ##          ##     ##  ##     ## ##  ####       ##
##       ##     ## ##   ### ##    ##    ##     ##  ##     ## ##   ### ##    ##
##        #######  ##    ##  ######     ##    ####  #######  ##    ##  ######
*/

function get_theme_asset_uri( $theme_file_path ) {

    if ( false === stripos( $theme_file_path, '.min.' ) )
        return trailingslashit( get_stylesheet_directory_uri() ) . $theme_file_path;

    if ( false !== strpos( $theme_file_path, '.js' ) )
        $debug = defined( 'COMPRESS_SCRIPTS' ) ? !COMPRESS_SCRIPTS : false;
    else
        $debug = defined( 'COMPRESS_CSS' ) ? !COMPRESS_CSS : false;

	if ( false === $debug )
		$debug = SCRIPT_DEBUG;

    if (
        false === $debug
        && file_exists( trailingslashit( STYLESHEETPATH ) . $theme_file_path )
    )
        return trailingslashit( get_stylesheet_directory_uri() ) . $theme_file_path;

    if ( file_exists( trailingslashit( STYLESHEETPATH ) . str_replace( '.min.', '.', $theme_file_path ) ) )
        return trailingslashit( get_stylesheet_directory_uri() ) . str_replace( '.min.', '.', $theme_file_path );

    return trailingslashit( get_stylesheet_directory_uri() ) . $theme_file_path;

}

function get_image_prominent_color( $attachment_id, $image_path = false ) {
	if ( !class_exists( 'GetMostCommonColors' ) )
		return 'FFF';

    if ( false !== $attachment_id ) {
        $image = wp_get_attachment_image_src( $attachment_id, 'medium' );
        $filename = basename( $image[0] );
        $image_path = dirname( get_attached_file( $attachment_id ) ) . '/' . $filename;
    }

    if ( !file_exists( $image_path ) )
        return false;

    require_once STYLESHEETPATH . '/inc/class.get-most-common-colors.php';

    $class = new GetMostCommonColors();
    $colors = array_keys( $class->Get_Color( $image_path, 1, true, true, 8 ) );

    return $colors[0];
}

?>
