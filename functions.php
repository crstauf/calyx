<?php
/**
 * Load the theme.
 */

namespace Calyx;

defined( 'WPINC' ) || die();


/*
 *  ######  ######## ######## ##     ## ########
 * ##    ## ##          ##    ##     ## ##     ##
 * ##       ##          ##    ##     ## ##     ##
 *  ######  ######      ##    ##     ## ########
 *       ## ##          ##    ##     ## ##
 * ##    ## ##          ##    ##     ## ##
 *  ######  ########    ##     #######  ##
 */

defined( 'THEME_PREFIX' ) || define( 'THEME_PREFIX', 'calyx' );
defined( 'THEME_ABSPATH' ) || define( 'THEME_ABSPATH', trailingslashit( __DIR__ ) );
defined( 'THEME_INCLUDES' ) || define( 'THEME_INCLUDES', THEME_ABSPATH . 'includes/' );

add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script' ) );
add_theme_support( 'title-tag' );
add_theme_support( 'post-thumbnails' );

register_nav_menus( array(
) );


/*
 * ######## ##     ## ##    ##  ######  ######## ####  #######  ##    ##  ######
 * ##       ##     ## ###   ## ##    ##    ##     ##  ##     ## ###   ## ##    ##
 * ##       ##     ## ####  ## ##          ##     ##  ##     ## ####  ## ##
 * ######   ##     ## ## ## ## ##          ##     ##  ##     ## ## ## ##  ######
 * ##       ##     ## ##  #### ##          ##     ##  ##     ## ##  ####       ##
 * ##       ##     ## ##   ### ##    ##    ##     ##  ##     ## ##   ### ##    ##
 * ##        #######  ##    ##  ######     ##    ####  #######  ##    ##  ######
 */

include_once THEME_INCLUDES . 'fallback-functions.php';


/*
 * #### ##    ##  ######  ##       ##     ## ########  ########  ######
 *  ##  ###   ## ##    ## ##       ##     ## ##     ## ##       ##    ##
 *  ##  ####  ## ##       ##       ##     ## ##     ## ##       ##
 *  ##  ## ## ## ##       ##       ##     ## ##     ## ######    ######
 *  ##  ##  #### ##       ##       ##     ## ##     ## ##             ##
 *  ##  ##   ### ##    ## ##       ##     ## ##     ## ##       ##    ##
 * #### ##    ##  ######  ########  #######  ########  ########  ######
 */

require_once THEME_INCLUDES . 'class-calyx.php';


/*
 * #### ##    ## #### ########
 *  ##  ###   ##  ##     ##
 *  ##  ####  ##  ##     ##
 *  ##  ## ## ##  ##     ##
 *  ##  ##  ####  ##     ##
 *  ##  ##   ###  ##     ##
 * #### ##    ## ####    ##
 */

Calyx::instance();
do_action( THEME_PREFIX . '/after_init' );
