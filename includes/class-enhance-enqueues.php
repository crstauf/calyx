<?php
/**
 * Enhance Wordpress enqueues.
 */

/**
 * Class to enhance WordPress enqueues.
 *
 * Possible properties: critical, noscript, preload.
 */
class CSSLLC_EnhanceEnqueues {

	/**
	 * @var array $_enhancements Reference list of enhancements by asset type.
	 */
	private $_enhancements = array(
		'script' => array(
			'preload',
			'critical',
		),
		'style' => array(
			'preload',
			'critical',
			'noscript',
		),
	);

	function __construct() {

		if ( !defined( 'SCRIPT_DEBUG' ) || !SCRIPT_DEBUG ) {

			add_filter(  'style_loader_tag', array( &$this, 'maybe_enhance_stylesheet__critical' ), 999, 4 );
			add_filter( 'script_loader_tag', array( &$this, 'maybe_enhance_script__critical'     ), 999, 3 );

			add_filter(  'style_loader_tag', array( &$this, 'maybe_enhance__preload' ), 999, 3 );
			add_filter( 'script_loader_tag', array( &$this, 'maybe_enhance__preload' ), 999, 3 );

		}

		add_filter( 'style_loader_tag', array( &$this, 'maybe_enhance_stylesheet__noscript' ), 1000, 2 );

		add_action( 'wp_enqueue_scripts', array( &$this, '_debug_action__wp_enqueue_scripts' ) );

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

	function maybe_enhance_stylesheet__noscript( $tag, $handle ) {
		if ( empty( wp_styles()->get_data( $handle, 'noscript' ) ) )
			return $tag;

		return '<noscript>' . $tag . '</noscript>';
	}

	function maybe_enhance_stylesheet__critical( $tag, $handle, $href, $media ) {
		if ( empty( wp_styles()->get_data( $handle, 'critical' ) ) )
			return $tag;

		if ( !$this->server_supports_http2_push() )
			return $this->_maybe_print_stylesheet_inline( $tag, $handle, $href, $media );
	}

	function maybe_enhance_script__critical( $tag, $handle, $src ) {
		if ( empty( wp_styles()->get_data( $handle, 'critical' ) ) )
			return $tag;

		if ( !$this->server_supports_http2_push() )
			return $this->_maybe_print_script_inline( $tag, $handle, $src );
	}

	function maybe_enhance__preload( $tag, $handle, $src ) {
		static $once = false;

		$as = 'style_loader_tag' === current_filter() ? 'style' : 'script';

		if ( 'style' === $as ) {
			if (    empty(  wp_styles()->get_data( $handle, 'preload' ) ) )
				return $tag;
		} else if ( empty( wp_scripts()->get_data( $handle, 'preload' ) ) )
			return $tag;

		if ( false === $once ) {
			$this->_maybe_enqueue_cssrelpreload();
			$once = true;
		}

		$preload_tag = '<link rel="preload" href="' . $src . '" as="' . $as . '" />';
		$search = $tag;

		(
			'script' === $as
			&& $search = "<script type='text/javascript' src='$src'></script>"
		)
		|| $preload_tag .= "\n";

		return str_replace( $search, $preload_tag, $tag );
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

	/**
	 * Print stylesheet contents inline, if found.
	 */
	protected function _maybe_print_stylesheet_inline( $tag, $handle, $href, $media ) {
		$contents = $this->_get_asset_contents( $href );

		if ( false === $contents )
			return $tag;

		if ( 'all' !== $media )
			$contents = '@media ' . $media . ' { ' . $contents . ' }';

		return '<style type="text/css" id="' . esc_attr( $handle ) . '-css">' . "\n" .
			$contents . "\n" .
		'</style>' . "\n";
	}

	/**
	 * Print stylesheet contents inline, if found.
	 */
	protected function _maybe_print_script_inline( $tag, $handle, $src ) {
		$contents = $this->_get_asset_contents( $src );

		if ( false === $contents )
			return $tag;

		return str_replace(
			"<script type='text/javascript' src='$src'></script>",
			'<script type="text/javascript" id="' . esc_attr( $handle ) . '-js">' . "\n" .
				$contents . "\n" .
			'</script>',
			$tag
		);

		return '<script type="text/javascript" id="' . esc_attr( $handle ) . '-js">' . "\n" .
			$contents . "\n" .
		'</script>';
	}

	/**
	 * Get theme asset's contents.
	 *
	 * @param string $href URI of the asset.
	 *
	 * @return bool|string
	 */
	protected function _get_asset_contents( $href ) {
		foreach ( array(
			get_stylesheet_directory_uri() => get_stylesheet_directory(),
			  get_template_directory_uri() => get_template_directory(),
		) as $uri => $directory )
			if ( false !== stripos( $href, $uri ) ) {
				$path = trailingslashit( $directory ) . str_replace( trailingslashit( $uri ), '', $href );
				break;
			}

		$path = preg_replace( "/^(.+?)\?.*$/", "$1", $path );

		if (
			          empty( $path )
			|| !file_exists( $path )
			||     !is_file( $path )
		)
			return false;

		return apply_filters( 'enhance-enqueues/asset/content', file_get_contents( $path ), $path, $href );
	}

	function _maybe_enqueue_cssrelpreload() {
		if ( wp_script_is( 'cssrelpreload' ) )
			return;

		if (
			WP_DEVELOP
			&& !wp_script_is( 'cssrelpreload', 'registered' )
		) {
			trigger_error( 'When using <code>rel="preload"</code>, it is recommended to register https://github.com/filamentgroup/loadCSS as "<code>cssrelpreload</code>."' );
			return;
		}

		if ( did_action( 'wp_print_footer_scripts' ) ) {
			wp_print_scripts( 'cssrelpreload' );
			return;
		}

		wp_enqueue_script( 'cssrelpreload' );
	}

	/**
	 * Check if server version supports http2 push.
	 * @return bool
	 */
	function server_supports_http2_push() {
		return false;
	}

	static function enhance_script__critical( $handle ) {
		if ( !array_key_exists( $handle, wp_scripts()->registered ) )
			return;

		wp_scripts()->add_data( $handle, 'critical', true );
	}

	static function enhance_stylesheet__critical( $handle ) {
		if ( !array_key_exists( $handle, wp_styles()->registered ) )
			return;

		wp_styles()->add_data( $handle, 'critical', true );
	}

	static function enhance_stylesheet__noscript( $handle ) {
		if ( !array_key_exists( $handle, wp_styles()->registered ) )
			return;

		wp_styles()->add_data( $handle, 'noscript', true );
	}

	static function enhance_stylesheet__preload( $handle ) {
		if ( !array_key_exists( $handle, wp_styles()->registered ) )
			return;

		wp_styles()->add_data( $handle, 'preload', true );
	}

	static function enhance_script__preload( $handle ) {
		if ( !array_key_exists( $handle, wp_scripts()->registered ) )
			return;

		wp_scripts()->add_data( $handle, 'preload', true );
	}

	function _debug_action__wp_enqueue_scripts() {
		$handle = 'enhance-enqueues-test';

		wp_enqueue_style( $handle, get_theme_file_url( 'includes/_dev/samples/style.css' ) );

			wp_add_inline_style( $handle, '.' . $handle . '::after { content: "after"; }' );
			// enhance_stylesheet__critical( $handle );
			// self::enhance_stylesheet__noscript( $handle );
			self::enhance_stylesheet__preload(  $handle );

		wp_enqueue_script( $handle, get_theme_file_url( 'includes/_dev/samples/scripts.js' ) );

			wp_add_inline_script( $handle, 'var before = 1;', 'before' );
			wp_add_inline_script( $handle, 'var  after = 1;' );
			// self::enhance_script__critical( $handle );
			self::enhance_script__preload(  $handle );
	}

}

new CSSLLC_EnhanceEnqueues;

?>