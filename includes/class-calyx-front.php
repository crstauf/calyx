<?php
/**
 * Helper for frontend.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Theme helper for frontend.
 *
 * Hooks
 */
class Calyx_Front {
	use Calyx_Singleton;

	/** @var null|Calyx_Front_Actions **/
	protected $_actions = null;

	/** @var null|Calyx_Front_Filters **/
	protected $_filters = null;

	/**
	 * Construct.
	 */
	protected function __construct() {
		do_action( 'qm/start', __METHOD__ . '()' );

		$this->_actions = Calyx_Front_Actions::create_instance();
		$this->_filters = Calyx_Front_Filters::create_instance();

		do_action( 'qm/stop', __METHOD__ . '()' );
	}

	/**
	 * Include files.
	 */
	public static function include_files() {
		require_once CALYX_ABSPATH . 'includes/class-calyx-front-actions.php';
		require_once CALYX_ABSPATH . 'includes/class-calyx-front-filters.php';
		require_once CALYX_ABSPATH . 'includes/class-enhance-enqueues.php';
		include_once CALYX_ABSPATH . 'temporary/front.php';

		do_action( 'qm/lap', THEME_PREFIX . '/' . __FUNCTION__ . '()', 'front' );
	}

	/** Alias for $_actions property. **/
	function actions() { return $this->_actions; }

	/** Alias for $_filters property. **/
	function filters() { return $this->_filters; }

	/**
	 * Register vendor assets.
	 */
	function _register_vendor_assets() {

		/**
		 * Modernizr script.
		 *
		 * @version 3.6.0
		 * @link https://modernizr.com/download?csspointerevents-touchevents-addtest-setclasses-shiv
		 */
		wp_register_script( 'modernizr', get_theme_file_uri( 'assets/js/modernizr.min.js' ), array(), '3.6.0' );
		class_exists( 'CSSLLC_EnhanceEnqueues' ) && CSSLLC_EnhanceEnqueues::enhance_script__critical( 'modernizr' );

		/**
		 * Polyfill for rel="preload".
		 *
		 * @version 2.0.1
		 *
		 * @link https://github.com/filamentgroup/loadCSS GitHub repository for loadCSS.
		 * @link https://caniuse.com/#feat=link-rel-preload Browser support.
		 */
		wp_register_script( 'cssrelpreload', get_theme_file_uri( 'assets/js/cssrelpreload.min.js' ), array(), '2.0.1' );

		/**
		 * Slider script: slick.
		 *
		 * @version 1.8.0
		 * @link https://github.com/kenwheeler/slick GitHub repository for slick.
		 */
		wp_register_script( 'slick', get_theme_file_uri(  'assets/js/slick.min.js'  ), array(), '1.8.0' );
		wp_register_style(  'slick', get_theme_file_uri( 'assets/css/slick.min.css' ), array(), '1.8.0' );

	}

	/**
	 * Load specified fonts using `webfontloader`.
	 *
	 * Returns the JS for issuing instructions to `webfontloader`.
	 *
	 * @see SAES_Actions::init() for call via `wp_add_inline_script()`
	 * @see SAES::get_webfontloader_object() for info on contexts.
	 *
	 * @param string $context Context/position of the function's call: 'all', 'head', 'footer'.
	 *
	 * @return string Non-/minified JS to load fonts.
	 */
	function _inlineScript_webfontloader( $context = 'all' ) {
		ob_start();

		if ( 'footer' === $context ) {
			?>

			if ( 'undefined' !== typeof WebFont )
				WebFont.load( JSON.parse( window._calyx_data._webfontloader ).footer );

			<?php
		} else {
			?>

			var WebFontConfig = JSON.parse( window._calyx_data._webfontloader ).<?php echo esc_js( $context ) ?>;
			if ( 'undefined' !== typeof WebFont )
				WebFont.load( WebFontConfig );

			<?php
		}

		return maybe_minify_js( ob_get_clean() );
	}

}

add_action( THEME_PREFIX . '/include_files/after_core', array( 'Calyx_Front', 'include_files' ) );

?>