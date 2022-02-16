<?php
/**
 * Register assets.
 *
 * @package calyx
 * @see Calyx::action__init()
 */

defined( 'ABSPATH' ) || die();

# Only register assets inside of `init` action.
if ( !doing_action( 'init' ) ) {
	trigger_error( 'Do not load <code>' . __FILE__ . '</code> outside of <code>init</code> action.' );
	return;
}

/**
 * If AJAX, cron, or JSON request, don't register assets.
 *
 * @todo Add detection for REST request.
 * @see https://core.trac.wordpress.org/ticket/42061
 */
if ( !apply_filters( THEME_PREFIX . '/register_assets', (
	   wp_doing_ajax()
	|| wp_doing_cron()
	|| wp_is_json_request()
) ) )
	return;

$css_suffix = COMPRESS_CSS     ? '.min' : '';
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

wp_register_style( THEME_PREFIX . '/critical/copy', get_stylesheet_directory_uri() . '/assets/critical/copy' . $css_suffix . '.css', null, 'init' );
 wp_enhance_style( THEME_PREFIX . '/critical/copy', 'critical', array( 'always' => true ) );

wp_register_style( THEME_PREFIX . '/critical/site', get_stylesheet_directory_uri() . '/assets/critical/site' . $css_suffix . '.css', array( THEME_PREFIX . '/critical/copy' ), 'init' );
 wp_enhance_style( THEME_PREFIX . '/critical/site', 'critical', array( 'always' => true ) );

wp_register_style( THEME_PREFIX . '/styles', get_stylesheet_directory_uri() . '/style' . $css_suffix . '.css', null, 'init' );
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

/**
 * When registering vendor assets that do not have a specific version, set the dependency version to null.
 */

require_once 'register-lazysizes.php';


do_action( THEME_PREFIX . '/registered_assets', $css_suffix, $js_suffix );

?>
