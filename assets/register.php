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

$css_suffix = constant( 'COMPRESS_CSS' ) ? '.min.css' : '.css';
 $js_suffix = constant( 'COMPRESS_SCRIPTS' ) ? '.min.js' : '.js';

/**
 * Get theme asset URL: minified or not.
 *
 * @param string $theme_asset_relative_path
 * @return string
 */
function get_theme_asset_url( string $theme_asset_relative_path ) : string {
	$theme_abspath = constant( 'THEME_ABSPATH' );

	if ( ! file_exists( $theme_abspath . $theme_asset_relative_path ) ) {
		return '';
	}

	$theme_asset_url         = trailingslashit( get_template_directory_uri() );
	$min_asset_relative_path = $theme_asset_relative_path;

	if ( defined( 'COMPRESS_CSS' ) && constant( 'COMPRESS_CSS' ) ) {
		$min_asset_relative_path = str_replace( '.css', '.min.css', $min_asset_relative_path );
	}

	if ( defined( 'COMPRESS_SCRIPTS' ) && constant( 'COMPRESS_SCRIPTS' ) ) {
		$min_asset_relative_path = str_replace( '.js', '.min.js', $min_asset_relative_path );
	}

	if ( $min_asset_relative_path !== $theme_asset_relative_path && file_exists( $theme_abspath . $min_asset_relative_path) ) {
		return $theme_asset_url . $min_asset_relative_path;
	}

	return $theme_asset_url . $asset_relative_path;
}


/*
##        #######   ######     ###    ##
##       ##     ## ##    ##   ## ##   ##
##       ##     ## ##        ##   ##  ##
##       ##     ## ##       ##     ## ##
##       ##     ## ##       ######### ##
##       ##     ## ##    ## ##     ## ##
########  #######   ######  ##     ## ########
*/

wp_register_style( THEME_PREFIX . '/styles', get_theme_asset_url( 'style.css' ), array(), 'init' );
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
