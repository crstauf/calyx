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

?>