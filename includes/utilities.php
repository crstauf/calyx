<?php
/**
 * Miscellaneous functions.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

if ( !function_exists( 'get_theme_file_url' ) ) {

	/**
	 * Get un/compressed theme asset's URL.
	 *
	 * @param string $file Path of asset relative to theme root.
	 *
	 * @uses get_theme_file_uri()
	 *
	 * @return string URL of asset.
	 */
	function get_theme_file_url( $file ) {
		if ( SCRIPT_DEBUG || !CONCATENATE_SCRIPTS )
			$file = str_replace( '.min.', '.', $file );

		return get_theme_file_uri( $file );
	}

}

if ( !function_exists( 'is_current_action' ) ) {

	/**
	 * Check if specified action is the current action.
	 *
	 * @param string $test_action Action name.
	 * @return bool
	 */
	function is_current_action( $test_action ) {
		return current_action() === $test_action;
	}

}

if ( !function_exists( 'is_current_filter' ) ) {

	/**
	 * Check if specified filter is the current filter.
	 *
	 * @param string $test_filter Filter name.
	 * @return bool
	 */
	function is_current_filter( $test_filter ) {
		return current_filter() === $test_filter;
	}

}

if ( !function_exists( 'is_woocommerce_active' ) ) {

	/**
	 * Check if WooCommerce is active.
	 */
	function is_woocommerce_active() {
		return (
			function_exists( 'is_plugin_active' )
			&& is_plugin_active( 'woocommerce/woocommerce.php' )
		);
	}

}

if ( !function_exists( 'hex2rgb' ) ) {

	/**
	 * Convert a hexadecimal value to rgb values.
	 *
	 * @param string $hex     #RRGGBB.
	 * @param float  $opacity Opacity/alpha value.
	 *
	 * @link https://css-tricks.com/snippets/php/convert-hex-to-rgb/ Reference.
	 *
	 * @return string rgb() or rgba() value
	 */
	function hex_to_rgb( $hex, $opacity = false ) {
		if ( '#' === $hex[0] )
			$hex = substr( $hex, 1 );

		if ( !in_array( strlen( $hex ), array( 3, 6 ) ) )
			return false;

		$hexes = (
			6 === strlen( $hex )
			? array( $hex[0] . $hex[1], $hex[2] . $hex[3], $hex[4] . $hex[5] )
			: array( $hex[0] . $hex[0], $hex[1] . $hex[1], $hex[2] . $hex[2] )
		);

		$decimals = array_map( 'hexdec', $hexes );

		if ( $opacity )
			$decimals[] = $opacity <= 1
				? $opacity
				: $opacity / 100;

		return $opacity
			? 'rgba( ' . implode( ', ', $decimals ) . ' )'
			: 'rgb( '  . implode( ', ', $decimals ) . ' )';
	}

}

if ( !function_exists( 'maybe_minify_js' ) ) {

	/**
	 * Maybe minify JavaScript.
	 *
	 * @param string $js JavaScript code.
	 *
	 * @uses Calyx_Minify::js()
	 *
	 * @return string
	 */
	function maybe_minify_js( $js ) {
		return !SCRIPT_DEBUG && COMPRESS_SCRIPTS
			? Calyx_Minify()->js( $js )
			: $js;
	}

}

if ( !function_exists( 'maybe_minify_css' ) ) {

	/**
	 * Maybe minify styles.
	 *
	 * @param string $css Styles code.
	 *
	 * @uses Calyx_Minify::css()
	 *
	 * @return string
	 */
	function maybe_minify_css( $css ) {
		return !SCRIPT_DEBUG && COMPRESS_CSS
			? Calyx_Minify()->css( $css )
			: $css;
	}

}

?>