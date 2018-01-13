<?php

new enhance_wp_enqueues;

class enhance_wp_enqueues {

	const VERSION = '0.0.1';

	function __construct() {
		add_filter( 'style_loader_tag', array( __CLASS__, 'filter_style_loader_tag' ), 9999999, 4 );
		add_filter( 'script_loader_tag', array( __CLASS__, 'filter_script_loader_tag' ), 9999999, 3 );
	}


	/*
	   ###     ######  ######## ####  #######  ##    ##  ######
	  ## ##   ##    ##    ##     ##  ##     ## ###   ## ##    ##
	 ##   ##  ##          ##     ##  ##     ## ####  ## ##
	##     ## ##          ##     ##  ##     ## ## ## ##  ######
	######### ##          ##     ##  ##     ## ##  ####       ##
	##     ## ##    ##    ##     ##  ##     ## ##   ### ##    ##
	##     ##  ######     ##    ####  #######  ##    ##  ######
	*/


	static function action_wp_head() {
		echo 'wp_head';
	}

	static function action_wp_print_styles() {
		echo 'wp_print_styles';
	}

	static function action_wp_print_scripts() {
		echo 'wp_print_scripts';
	}

	/*
	######## #### ##       ######## ######## ########   ######
	##        ##  ##          ##    ##       ##     ## ##    ##
	##        ##  ##          ##    ##       ##     ## ##
	######    ##  ##          ##    ######   ########   ######
	##        ##  ##          ##    ##       ##   ##         ##
	##        ##  ##          ##    ##       ##    ##  ##    ##
	##       #### ########    ##    ######## ##     ##  ######
	*/


	static function filter_style_loader_tag( $tag, $handle, $href, $media = 'all' ) {
		$obj = wp_styles()->registered[$handle];

		if ( false !== wp_styles()->get_data( $handle, 'inline' ) ) {
			$print = self::print_inline_asset( $obj, $handle, $media );
			if ( false !== $print )
				return $print;
		}

		if ( false !== wp_styles()->get_data( $handle, 'noscript' ) )
			$tag = '<noscript>' . $tag . '</noscript>' . "\n";

		return $tag;
	}

	static function filter_script_loader_tag( $tag, $handle, $src ) {
		$obj = wp_scripts()->registered[$handle];

		if ( false !== wp_scripts()->get_data( $handle, 'inline' ) ) {
			$print = self::print_inline_asset( $obj, $handle, $media );
			if ( false !== $print )
				return $print;
		}

		if ( false !== wp_scripts()->get_data( $handle, 'async' ) )
			$tag = str_replace( '<script ', '<script async ', $tag );
		else if ( false !== wp_scripts()->get_data( $handle, 'defer' ) )
			$tag = str_replace( '<script ', '<script defer ', $tag );

		return $tag;
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


	static function print_inline_asset( $obj, $handle, $media = 'all' ) {
		$obj->path = trailingslashit( get_stylesheet_directory() ) . str_replace( trailingslashit( get_stylesheet_directory_uri() ), '', $obj->src );

		if ( !file_exists( $obj->path ) || !is_file( $obj->path ) )
			return false;

		$contents = file_get_contents( $obj->path );

		$rtl = ( 'rtl' === wp_styles()->text_direction && isset($obj->extra['rtl']) && $obj->extra['rtl'] );

		if ( 'all' !== $media )
			$contents = '@media ' . $media . ' { ' . $contents . ' } ';

		return ( isset( $obj->extra['noscript'] ) ? '<noscript>' : '' ) .
			'<style id="' . esc_attr( $handle ) . ( $rtl ? '-rtl' : '' ) . '-css" type="text/css">' . self::minify_css( $contents ) . '</style>' .
		( isset( $obj->extra['noscript'] ) ? '</noscript>' : '' ) . "\n";
	}

	static function minify_css( $css ) {
		// remove comments
		$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);

		// backup values within single or double quotes
		preg_match_all('/(\'[^\']*?\'|"[^"]*?")/ims', $css, $hit, PREG_PATTERN_ORDER);
		for ($i=0; $i < count($hit[1]); $i++) {
			$css = str_replace($hit[1][$i], '##########' . $i . '##########', $css);
		}

		// remove trailing semicolon of selector's last property
		$css = preg_replace('/;[\s\r\n\t]*?}[\s\r\n\t]*/ims', "}\r\n", $css);

		// remove space between empty definition and next definition
		$css = preg_replace('/}[\s\r\n\t]*/ims', "}\r\n", $css);

		// remove space around plus sign
		$css = preg_replace('/[\s\r\n\t]*\+[\s\r\n\t]*?([^\s\r\n\t])/ims', '+$1', $css);

		// remove any whitespace between semicolon and property-name
		$css = preg_replace('/;[\s\r\n\t]*?([\r\n]?[^\s\r\n\t])/ims', ';$1', $css);

		// remove any whitespace surrounding property-colon
		$css = preg_replace('/[\s\r\n\t]*:[\s\r\n\t]*?([^\s\r\n\t])/ims', ':$1', $css);

		// remove any whitespace surrounding selector-comma
		$css = preg_replace('/[\s\r\n\t]*,[\s\r\n\t]*?([^\s\r\n\t])/ims', ',$1', $css);

		// remove any whitespace surrounding opening parenthesis
		$css = preg_replace('/[\s\r\n\t]*{[\s\r\n\t]*?([^\s\r\n\t])/ims', '{$1', $css);

		// remove any whitespace between numbers and units
		$css = preg_replace('/([\d\.]+)[\s\r\n\t]+(px|em|pt|%)/ims', '$1$2', $css);

		// shorten zero-values
		$css = preg_replace('/([^\d\.]0)(px|em|pt|%)/ims', '$1', $css);

		// remove tabs
		$css = str_replace("\t",' ',$css);

		// constrain multiple whitespaces
		$css = preg_replace('/\p{Zs}+/ims',' ', $css);

		// remove newlines
		$css = str_replace(array("\r\n", "\r", "\n"), '', $css);

		// shorten #aabbcc to #abc
		$css = preg_replace("/#([0-9a-fA-F])\\1([0-9a-fA-F])\\2([0-9a-fA-F])\\3/", "#$1$2$3", $css);

		// Restore backupped values within single or double quotes
		for ($i=0; $i < count($hit[1]); $i++) {
			$css = str_replace('##########' . $i . '##########', $hit[1][$i], $css);
		}

		return $css;
	}

}

?>
