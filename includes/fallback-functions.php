<?php

/**
 * Define empty helpers to gracefully handle missing (non-critical) plugins.
 *
 * @link https://github.com/crstauf/enhance-assets Enhance Assets
 * @link https://github.com/cssllc/mu-plugins/blob/master/cssllc-subresourceintegrity.php CSSLLC Subresource Integrity
 * @link https://github.com/cssllc/mu-plugins/blob/master/cssllc-helpers.php CSSLLC WordPress Helpers
 *
 * phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
 */

defined( 'WPINC' ) || die();

if ( ! function_exists( 'wp_enhance_asset' ) ) {

	/**
	 * @param string $handle
	 * @param string $enhancement
	 * @param array<string, string> $args
	 * @param bool $is_script
	 */
	function wp_enhance_asset( string $handle, string $enhancement, array $args = array(), bool $is_script = true ) : void {}

}

if ( ! function_exists( 'wp_enhance_script' ) ) {

	/**
	 * @param string $handle
	 * @param string $enhancement
	 * @param array<string, string> $args
	 */
	function wp_enhance_script( string $handle, string $enhancement, array $args = array() ) : void {}

}

if ( ! function_exists( 'wp_dehance_script' ) ) {

	function wp_dehance_script( string $handle, string $enhancement = null ) : void {}

}

if ( ! function_exists( 'wp_enhance_style' ) ) {

	/**
	 * @param string $handle
	 * @param string $enhancement
	 * @param array<string, string> $args
	 */
	function wp_enhance_style( string $handle, string $enhancement, array $args = array() ) : void {}

}

if ( ! function_exists( 'wp_dehance_style' ) ) {

	function wp_dehance_style( string $handle, string $enhancement = null ) : void {}

}

if ( ! function_exists( 'wp_set_script_sri' ) ) {

	function wp_set_script_sri( string $handle, string $hash, bool $condition = true ) : void {}

}

if ( ! function_exists( 'wp_set_style_sri' ) ) {

	function wp_set_style_sri(  string $handle, string $hash, bool $condition = true ) : void {}

}

if ( ! function_exists( 'prerender' ) ) {

	/**
	 * @param string[] $prerender_urls
	 */
	function prerender( array $prerender_urls ) : void {}

}

if ( ! function_exists( 'is_production' ) ) {

	function is_production() : bool {
		return 'production' === wp_get_environment_type();
	}

}
