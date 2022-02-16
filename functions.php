<?php
/**
 * Load the theme.
 *
 * @package calyx
 */

defined( 'ABSPATH' ) || die();


/*
 ######  ######## ######## ##     ## ########
##    ## ##          ##    ##     ## ##     ##
##       ##          ##    ##     ## ##     ##
 ######  ######      ##    ##     ## ########
      ## ##          ##    ##     ## ##
##    ## ##          ##    ##     ## ##
 ######  ########    ##     #######  ##
*/

defined( 'THEME_PREFIX'   ) || define( 'THEME_PREFIX', 'calyx' );
defined( 'THEME_ABSPATH'  ) || define( 'THEME_ABSPATH',  trailingslashit( __DIR__ ) );
defined( 'THEME_INCLUDES' ) || define( 'THEME_INCLUDES', THEME_ABSPATH . 'includes/' );

add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script' ) );
add_theme_support( 'title-tag' );
add_theme_support( 'post-thumbnails' );

register_nav_menus( array(
	'primary' => __( 'Primary', 'calyx' ),
) );

if ( !function_exists( 'is_production' ) ) {

	function is_production() : bool {
		return 'production' === wp_get_environment_type();
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

/**
 * Define empty helpers to gracefully handle missing plugins.
 *
 * @link https://github.com/crstauf/enhance-assets Enhance Assets
 * @link https://gist.github.com/crstauf/9a2f412e48c6630e6de945bd1d0e9e53 CSSLLC Subresource Integrity
 * @link https://gist.github.com/crstauf/46a29f046cfffcaf2829401ae0773c90 CSSLLC WordPress Helpers
 */
if ( !function_exists( 'wp_enhance_asset'  ) ) { function wp_enhance_asset(  string $handle, string $enhancement, $args = array(), bool $is_script = true ) {} }
if ( !function_exists( 'wp_enhance_script' ) ) { function wp_enhance_script( string $handle, string $enhancement, array $args = array() ) {} }
if ( !function_exists( 'wp_dehance_script' ) ) { function wp_dehance_script( string $handle, string $enhancement = null ) {} }
if ( !function_exists( 'wp_enhance_style'  ) ) { function wp_enhance_style(  string $handle, string $enhancement, array $args = array() ) {} }
if ( !function_exists( 'wp_dehance_style'  ) ) { function wp_dehance_style(  string $handle, string $enhancement = null ) {} }
if ( !function_exists( 'wp_set_script_sri' ) ) { function wp_set_script_sri( string $handle, string $hash ) {} }
if ( !function_exists( 'wp_set_style_sri'  ) ) { function wp_set_style_sri(  string $handle, string $hash ) {} }
if ( !function_exists( 'prerender' ) ) { function prerender( $prerender_urls ) {} }


/*
#### ##    ##  ######  ##       ##     ## ########  ########  ######
 ##  ###   ## ##    ## ##       ##     ## ##     ## ##       ##    ##
 ##  ####  ## ##       ##       ##     ## ##     ## ##       ##
 ##  ## ## ## ##       ##       ##     ## ##     ## ######    ######
 ##  ##  #### ##       ##       ##     ## ##     ## ##             ##
 ##  ##   ### ##    ## ##       ##     ## ##     ## ##       ##    ##
#### ##    ##  ######  ########  #######  ########  ########  ######
*/

require_once THEME_INCLUDES . 'class-calyx.php';


/*
#### ##    ## #### ########
 ##  ###   ##  ##     ##
 ##  ####  ##  ##     ##
 ##  ## ## ##  ##     ##
 ##  ##  ####  ##     ##
 ##  ##   ###  ##     ##
#### ##    ## ####    ##
*/

Calyx::instance();
do_action( THEME_PREFIX . '/after_init' );

?>
