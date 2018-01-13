<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}

// Calyx()->front->enhance_enqueues = new enhance_enqueues;

class enhance_enqueues {

	// const VERSION = '0.0.2.3';

	function __construct() {

		add_filter( 'style_loader_tag',        array( __CLASS__, 'filter_style_loader_tag' ), 9999999, 4 );

		if ( !defined( 'SCRIPT_DEBUG' ) || !SCRIPT_DEBUG ) {
			add_filter( 'script_loader_src',   array( __CLASS__, 'filter_script_loader_src' ), 10, 2 );
			add_filter( 'print_scripts_array', array( __CLASS__, 'filter_print_scripts_array' ) );
		}

		add_filter( 'script_loader_tag',       array( __CLASS__, 'filter_script_loader_tag' ), 9999999, 3 );

        add_action( 'wp_print_footer_scripts', array( __CLASS__, 'action_wp_print_footer_scripts' ) );

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

	static function action_wp_print_footer_scripts() {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$after_scripts = apply_filters( THEME_PREFIX . '/enhance-enqueues/after-page-load/after-scripts', '' );
			if ( empty( $after_scripts ) )
				return;
			?>

			<script id="everythingLoaded" type="text/javascript">
				var everythingLoaded = setInterval(function() {
					if (/loaded|complete/.test(document.readyState)) {
						clearInterval(everythingLoaded);
						<?php echo $after_scripts  ?>
					}
				});
			</script>

			<?php
			return;
		}

		if (
			!isset( wp_scripts()->after_page_load )
			|| !is_array( wp_scripts()->after_page_load )
			|| !count( wp_scripts()->after_page_load )
		)
			return;
		?>

		<script id="everythingLoaded" type="text/javascript">
			var everythingLoaded = setInterval(function() {
				if (/loaded|complete/.test(document.readyState)) {
					clearInterval(everythingLoaded);
					(function(d) {
						var js = d.getElementsByTagName('script')[0];

						<?php
						foreach ( array_keys( wp_scripts()->after_page_load ) as $handle ) {
							?>
							if (!d.getElementById("<?php echo esc_js( $handle ) ?>-js")) {
		                        var load_js = d.createElement('script');
		                        load_js.id = "<?php echo esc_js( $handle ) ?>-js";
		                        load_js.src = "<?php echo wp_scripts()->registered[$handle]->src ?>";
		                        js.parentNode.insertBefore(load_js, js);
		                    }
							<?php
							wp_scripts()->done[] = $handle;
						}
						?>

					}(document));

					<?php echo apply_filters( 'enhance-wp-enqueues/after-page-load/after-scripts', '' ); ?>

				}
			},1000);
		</script>

		<?php

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

		if (
			( !defined( 'WP_DEBUG' ) || !WP_DEBUG )
			&& false !== wp_styles()->get_data( $handle, 'inline' )
		) {
			$print = self::print_inline_asset( $obj, $handle, $media );
			if ( false !== $print )
				return $print;
		}

		if ( false !== wp_styles()->get_data( $handle, 'noscript' ) )
			$tag = '<noscript>' . $tag . '</noscript>' . "\n";

		return $tag;
	}

	static function filter_script_loader_src( $src, $handle ) {
		if ( false !== wp_scripts()->get_data( $handle, 'after-load' ) ) {
			if ( !isset( wp_scripts()->after_page_load ) )
				wp_scripts()->after_page_load = array();
			wp_scripts()->after_page_load[$handle] = 1;
			return false;
		}

		return $src;
	}

	static function filter_print_scripts_array( $to_do ) {
		if ( is_array( $to_do ) && count( $to_do ) )
			foreach ( $to_do as $i => $handle )
				if ( false !== wp_scripts()->get_data( $handle, 'after-load' ) ) {
					if ( !isset( wp_scripts()->after_page_load ) )
						wp_scripts()->after_page_load = array();
					wp_scripts()->after_page_load[$handle] = 1;
					unset( $to_do[$i] );
				}

		return $to_do;
	}

	static function filter_script_loader_tag( $tag, $handle, $src ) {
		$obj = wp_scripts()->registered[$handle];

		if ( false !== wp_scripts()->get_data( $handle, 'inline' ) ) {
			$print = self::print_inline_asset( $obj, $handle, $media );
			if ( false !== $print )
				return $print;
		}

		if ( false !== wp_scripts()->get_data( $handle, 'after-load' ) )
			$tag .= '<!-- set to load after page load -->';

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
			'<style id="' . esc_attr( $handle ) . ( $rtl ? '-rtl' : '' ) . '-css" type="text/css">' .
				( !defined( 'COMPRESS_CSS' ) || COMPRESS_CSS
					? self::minify_css( $contents )
					: $contents
				) .
			'</style>' .
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
		$css = preg_replace('/([^\d\.]0)(px|em|pt)/ims', '$1', $css);

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
