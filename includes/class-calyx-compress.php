<?php
/**
 * Class: Calyx_Minify
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Helper to minify code.
 */
class Calyx_Minify {
	use Calyx_Singleton;

	/*
	      ##    ###    ##     ##    ###     ######   ######  ########  #### ########  ########
	      ##   ## ##   ##     ##   ## ##   ##    ## ##    ## ##     ##  ##  ##     ##    ##
	      ##  ##   ##  ##     ##  ##   ##  ##       ##       ##     ##  ##  ##     ##    ##
	      ## ##     ## ##     ## ##     ##  ######  ##       ########   ##  ########     ##
	##    ## #########  ##   ##  #########       ## ##       ##   ##    ##  ##           ##
	##    ## ##     ##   ## ##   ##     ## ##    ## ##    ## ##    ##   ##  ##           ##
	 ######  ##     ##    ###    ##     ##  ######   ######  ##     ## #### ##           ##
	*/

	/**
	 * Minify JavaScript.
	 *
	 * @param string $js JavaScript code.
	 *
	 * @link https://datayze.com/howto/minify-javascript-with-php.php Reference.
	 *
	 * @return string JavaScript code.
	 */
	function js( $js ) {
		$buffer = '';

		while ( list( $idx_start, $keyElement ) = $this->_js__getNextKeyElement( $js ) )
			switch ( $keyElement ) {
				case '//':
					$idx_start = strpos( $js, '//' );
					$idx_end = strpos( $js, "\n", $idx_start );
					if ( false !== $idx_end ) {
						$js = substr( $js, 0, $idx_start ) . substr( $js, $idx_end );
					} else {
						$js = substr( $js, 0, $idx_start );
					}
					break;

				case '/*':
					$idx_start = strpos( $js, '/*' );
					$idx_end = strpos( $js, '*/', $idx_start ) + 2;
					$js = substr( $js, 0, $idx_start ) . substr( $js, $idx_end );
					break;

				default: // must be handle like string case
					$idx_start = $this->_js__getNonEscapedQuoteIndex( $js, $keyElement );
					if ( 1 === strlen( $keyElement ) ) {
						// quote!  Either ' or "
						if ( '\'' === substr( $js, $idx_start, 1 ) ) {
							$idx_end = $this->_js__getNonEscapedQuoteIndex( $js, '\'', $idx_start + 1 ) + 1;
						} else{
							$idx_end = $this->_js__getNonEscapedQuoteIndex( $js, '"', $idx_start + 1 ) + 1;
						}
					} else {
						// regex!
						$idx_end = $idx_start + strlen( $keyElement );
					}
					$buffer .= $this->_js__spacing( substr( $js, 0, $idx_start ) );
					$quote = substr( $js, $idx_start, ( $idx_end - $idx_start ) );
					$quote = str_replace( "\\\n", ' ', $quote );
					$buffer .= $quote;
					$js = substr( $js, $idx_end );
			}

		$buffer .= $this->_js__spacing( $js );

		return $buffer;
	}

	/**
	 * Used to minify JavaScript; should not be called directly.
	 *
	 * @param string $js JavaScript code.
	 *
	 * @return bool|array
	 */
	protected function _js__getNextKeyElement( $js ) {
		$elements = array();
		$keyMarkers = array( '\'', '"', '//', '/*' );

		foreach ( $keyMarkers as $marker )
			$elements[$marker] = strpos( $js, $marker );

		//regex to detect all regex
		$regex = "/[\k(](\/[\k\S]+\/)/";
		preg_match( $regex, $js, $matches, PREG_OFFSET_CAPTURE, 1 );

		if ( !empty( $matches ) )
			$elements[$matches[1][0]] = $matches[1][1];

		$elements = array_filter( $elements, function( $k ) { return false !== $k; } );

		if ( empty( $elements ) )
			return false;

		$min = min( $elements );

		return array( $min, array_keys( $elements, $min )[0] );
	}

	/**
	 * Used to minify JavaScript; should not be called directly.
	 *
	 * @param string $string JavaScript code.
	 * @param string $char   Quote.
	 * @param int    $start  Offset.
	 *
	 * @return bool|array
	 */
	protected function _js__getNonEscapedQuoteIndex( $string, $char, $start = 0 ) {
		if ( preg_match('/(\\\\*)(' . preg_quote($char) . ')/', $string, $match, PREG_OFFSET_CAPTURE, $start ) ) {
			if (
				!isset( $match[1][0] )
				|| 0 === strlen( $match[1][0] ) % 2
			)
				return $match[2][1];
			else
				return $this->_js__getNonEscapedQuoteIndex( $string, $char, $match[2][1] + 1 );
		}

		return -1;
	}

	/**
	 * Used to minify JavaScript; should not be called directly.
	 *
	 * @param string $js JavaScript code.
	 *
	 * @return string JavaScript code.
	 */
	protected function _js__spacing( $js ) {
		$blocks = array( 'for', 'while', 'if', 'else' );
		$js = preg_replace( '/([-\+])\s+\+([^\s;]*)/', '$1 (+$2)', trim( $js ) );

		// remove new line in statements
		$js = preg_replace( '/\s+\|\|\s+/', ' || ', $js );
		$js = preg_replace( '/\s+\&\&\s+/', ' && ', $js );
		$js = preg_replace( '/\s*([=+-\/\*:?])\s*/', '$1 ', $js );

		// handle missing brackets {}
		foreach ( $blocks as $block )
			$js = preg_replace( '/(\s*\b' . $block . '\b[^{\n]*)\n([^{\n]+)\n/i', '$1{$2}', $js );

		// handle spaces
		$js = preg_replace( array("/\s*\n\s*/", "/\h+/"), array("\n", " "), $js ); // \h+ horizontal white space
		$js = preg_replace( array('/([^a-z0-9\_])\h+/i', '/\h+([^a-z0-9\$\_])/i'), '$1', $js );
		$js = preg_replace( '/\n?([[;{(\.+-\/\*:?&|])\n?/', '$1', $js );
		$js = preg_replace( '/\n?([})\]])/', '$1', $js );
		$js = str_replace( "\nelse", "else", $js );
		$js = preg_replace( "/([^}])\n/", "$1;", $js );
		$js = preg_replace( "/;?\n/", ";", $js );

		return $js;
	}


	/*
	 ######   ######   ######
	##    ## ##    ## ##    ##
	##       ##       ##
	##        ######   ######
	##             ##       ##
	##    ## ##    ## ##    ##
	 ######   ######   ######
	*/

	/**
	 * Minify CSS.
	 *
	 * @param string $css CSS code.
	 *
	 * @return string Minified CSS.
	 */
	function css( $css ) {
		$css = preg_replace('/\/\*((?!\*\/).)*\*\//', '', $css); // negative look ahead
		$css = preg_replace('/\s{2,}/', ' ', $css);
		$css = preg_replace('/\s*([:;{}])\s*/', '$1', $css);
		$css = preg_replace('/;}/', '}', $css);

		return $css;
	}

}

/**
 * Get singleton instance of Calyx_Minify.
 *
 * @uses Calyx_Minify::create_instance()
 *
 * @return Calyx_Minify
 */
function Calyx_Minify() {
	static $_class = null;

	if ( is_null( $_class ) )
		$_class = Calyx_Minify::create_instance();

	return $_class;
}

?>