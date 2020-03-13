<?php
/**
 * Helper definitions.
 */

defined( 'ABSPATH' ) || die();

if ( !function_exists( 'maybe_define_constant' ) ) {

	/**
	 * Maybe define a constant, if it doesn't already exist.
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return bool True if constant defined, false if not.
	 */
	function maybe_define_constant( string $name, $value ) {
		return defined( $name ) ? true : define( $name, $value );
	}
}

if ( !function_exists( 'trim_sha' ) ) {

	/**
	 * Trim full git commit hash to first seven characters.
	 *
	 * @param string $hash
	 * @return string
	 */
	function trim_sha( string $hash ) {
		return substr( $hash, 0, 7 );
	}

}

if ( !function_exists( 'calyx_register_style' ) ) {

	/**
	 * Register stylesheet from Calyx theme.
	 *
	 * @param string $handle
	 * @param string $relative_src
	 * @param string[] $deps
	 * @param mixed $ver
	 * @param string $media
	 *
	 * @uses get_template_directory_uri()
	 * @uses wp_register_style()
	 */
	function calyx_register_style( $handle, $relative_src, $deps = array(), $ver = false, $media = 'all' ) {
		$suffix = ( COMPRESS_SCRIPTS ? '.min' : '' ) . '.css';
		$relative_src = preg_replace( '/(?:\.min)?\.css$/', $suffix, $relative_src );
		$src = get_template_directory_uri() . '/' . $relative_src;

		wp_register_style( $handle, $src, $deps, $ver, $media );
	}

}

if ( !function_exists( 'calyx_register_script' ) ) {

	/**
	 * Register stylesheet from Calyx theme.
	 *
	 * @param string $handle
	 * @param string $relative_src
	 * @param string[] $deps
	 * @param mixed $ver
	 * @param bool $in_footer
	 *
	 * @uses get_template_directory_uri()
	 * @uses wp_register_script()
	 */
	function calyx_register_script( $handle, $relative_src, $deps = array(), $ver = false, $in_footer = false ) {
		$suffix = ( COMPRESS_SCRIPTS ? '.min' : '' ) . '.js';
		$relative_src = preg_replace( '/(?:\.min)?\.js/', $suffix, $relative_src );
		$src = get_template_directory_uri() . '/' . $relative_src;

		wp_register_script( $handle, $src, $deps, $ver, $in_footer );
	}

}

if ( !function_exists( 'current_user_is' ) ) {

	/**
	 * Check if current user is specified role.
	 *
	 * @param string $role
	 * @return bool
	 */
	function current_user_is( string $role ) {
		if ( !is_user_logged_in() )
			return 'guest' === $role;

		$user = wp_get_current_user();
		return in_array( $role, ( array ) $user->roles );
	}

}

if ( !function_exists( 'hex2rgba' ) ) {

	/**
	 * Convert hexadecimal color to RGBa.
	 *
	 * @param string $_hex
	 * @param float $opacity
	 * @return string
	 */
	function hex2rgba( string $_hex, float $opacity = 1.0 ) {
		# Remove "#" prefix.
		$_hex = ltrim( $_hex, '#' );

		# If not enough places, fallback to black.
		if ( !in_array( strlen( $_hex ), array( 3, 6 ) ) )
			$_hex = '000';

		# Create string from shorthand.
		if ( 3 === strlen( $_hex ) )
			$_hex = $_hex[0] . $_hex[0] . $_hex[1] . $_hex[1] . $_hex[2] . $_hex[2];

		# Trim to six places.
		$_hex = substr( $_hex, 0, 6 );

		# Break into pairs.
		$_hex = str_split( $_hex, 2 );

		# Convert hexadecimal to decimal.
		$rgb = array_map( 'hexdec', $_hex );

		# Because there's no such thing as negative opacity.
		$opacity = abs( $opacity );

		# If 100% opacity, return rgb.
		if ( 1 == $opacity )
			return 'rgb( ' . implode( ', ', $rgb ) . ' )';

		# If opacity greater than 1, assume percentage.
		if ( 1 < $opacity )
			$opacity /= 100;

		return 'rgba( ' . implode( ', ', $rgb ) . ', ' . $opacity . ' )';
	}

}

if ( !function_exists( 'array_filter_deep' ) ) {

	/**
	 * Filter multidimensional arrays.
	 *
	 * @param array $array
	 * @param callable $callback
	 * @param int $flag
	 * @return array
	 */
	function array_filter_deep( array $array ) {
		$array = array_filter( $array );

		foreach ( $array as &$item ) {
			if ( !is_array( $item ) )
				continue;

			$item = array_filter_deep( $item );
		}

		return array_filter( $array );
	}

}

?>
