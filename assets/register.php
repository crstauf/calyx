<?php declare( strict_types=1 );
/**
 * Register assets.
 *
 * @see Calyx::action__init()
 *
 * Handles for styles and scripts that are local to the theme should
 * be prefixed with the THEME_ABSPATH constant.
 */

namespace Calyx;

defined( 'WPINC' ) || die();

# Only register assets inside of `init` action.
if ( ! doing_action( 'init' ) ) {
	trigger_error( 'Do not load <code>' . __FILE__ . '</code> outside of <code>init</code> action.' );
	return;
}

/**
 * If AJAX, cron, or JSON request, don't register assets.
 *
 * @see https://core.trac.wordpress.org/ticket/42061
 */
if ( ! apply_filters( THEME_PREFIX . '/register_assets', ! (
	   wp_is_json_request()
	|| wp_doing_ajax()
	|| wp_doing_cron()
) ) ) {
	return;
}

$css_suffix = COMPRESS_CSS ? '.min' : '';
 $js_suffix = COMPRESS_SCRIPTS ? '.min' : '';


/*
##        #######   ######     ###    ##
##       ##     ## ##    ##   ## ##   ##
##       ##     ## ##        ##   ##  ##
##       ##     ## ##       ##     ## ##
##       ##     ## ##       ######### ##
##       ##     ## ##    ## ##     ## ##
########  #######   ######  ##     ## ########
*/

wp_register_style( THEME_PREFIX . '/styles', get_stylesheet_directory_uri() . '/style' . $css_suffix . '.css', array(), 'init' );
 wp_enhance_style( THEME_PREFIX . '/styles', 'async' );


/*
##     ## ######## ##    ## ########   #######  ########   ######
##     ## ##       ###   ## ##     ## ##     ## ##     ## ##    ##
##     ## ##       ####  ## ##     ## ##     ## ##     ## ##
##     ## ######   ## ## ## ##     ## ##     ## ########   ######
 ##   ##  ##       ##  #### ##     ## ##     ## ##   ##         ##
  ## ##   ##       ##   ### ##     ## ##     ## ##    ##  ##    ##
   ###    ######## ##    ## ########   #######  ##     ##  ######
*/

require_once 'register-lazysizes.php';


do_action( THEME_PREFIX . '/registered_assets', $css_suffix, $js_suffix );
