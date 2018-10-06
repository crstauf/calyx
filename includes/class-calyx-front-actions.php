<?php
/**
 * Class to manage frontend actions.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Actions for front-end.
 */
class Calyx_Front_Actions {
	use Calyx_Singleton;

	/**
	 * Construct.
	 */
	protected function __construct() {
		do_action( 'qm/start', __METHOD__ . '()' );

		add_action( 'init',                    array( &$this, 'init'                    ) );
		add_action( 'wp_head',                 array( &$this, 'wp_head'                 ) );
		add_action( 'wp_enqueue_scripts',      array( &$this, 'wp_enqueue_scripts__0'   ), 0 );
		add_action( 'wp_enqueue_scripts',      array( &$this, 'wp_enqueue_scripts__5'   ), 5 );
		add_action( 'login_enqueue_scripts',   array( &$this, 'login_enqueue_scripts'   ) );
		add_action( 'wp_footer',               array( &$this, 'wp_footer'               ) );
		add_action( 'wp_print_footer_scripts', array( &$this, 'wp_print_footer_scripts' ) );

		do_action( 'qm/stop', __METHOD__ . '()' );
	}

	/**
	 * Action: init
	 *
	 * @uses Calyx_Front::_register_vendor_assets() to register vendor assets.
	 */
	function init() {

		wp_register_style( THEME_PREFIX . '/critical/site',   get_theme_file_url( 'assets/critical/site.min.css'   ), array( THEME_PREFIX . '/copy'          ), 'init' );
		wp_register_style( THEME_PREFIX . '/critical/mobile', get_theme_file_url( 'assets/critical/mobile.min.css' ), array( THEME_PREFIX . '/critical/site' ), 'init' );
		wp_register_style( THEME_PREFIX . '/styles',          get_theme_file_uri( 'style.min.css'                  ), array( THEME_PREFIX . '/critical/site' ), 'init' );
		wp_register_style( THEME_PREFIX . '/login',           get_theme_file_uri( 'assets/critical/login.min.css'  ), array( 'login'                         ), 'init' );

			wp_style_add_data( THEME_PREFIX . '/critical/site',   'critical', true );
			wp_style_add_data( THEME_PREFIX . '/critical/mobile', 'critical', true );
			wp_style_add_data( THEME_PREFIX . '/login',           'critical', true );

		wp_register_script( THEME_PREFIX . '/scripts', get_theme_file_uri( 'assets/js/scripts.min.js' ), array(), 'init' );

		Calyx()->front()->_register_vendor_assets();

	}

	/**
	 * Action: wp_head
	 *
	 * - add head meta and link tags.
	 */
	function wp_head() {
		?>

		<meta charset="<?php bloginfo( 'charset' ) ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
		<link rel="profile" href="http://gmpg.org/xfn/11" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ) ?>" />

		<?php
	}

	/**
	 * Action: wp_enqueue_scripts, priority 0
	 *
	 * - enqueue modernizr script
	 * - enqueue lazysizes script
	 */
	function wp_enqueue_scripts__0() {

		wp_enqueue_script( 'modernizr' );
		wp_enqueue_script( 'lazysizes' );

		wp_add_inline_script( 'webfontloader', Calyx()->front()->_inlineScript_webfontloader( 'all' ) );

	}

	/**
	 * Action: wp_enqueue_scripts, priority 5
	 *
	 * - enqueue copy stylesheet
	 */
	function wp_enqueue_scripts__5() {

		 wp_enqueue_style( THEME_PREFIX . '/copy' );
		wp_style_add_data( THEME_PREFIX . '/copy', 'critical', true );

	}

	/**
	 * Action: login_enqueue_scripts
	 *
	 * - enqueue login page stylesheet
	 */
	function login_enqueue_scripts() {

		wp_enqueue_style( THEME_PREFIX . '/login' );

	}

	/**
	 * Action: wp_footer
	 *
	 * - enqueue webfontloader script
	 */
	function wp_footer() {

		wp_enqueue_script( 'webfontloader' );

	}

	/**
	 * Action: wp_print_footer_scripts
	 *
	 * - label broken links
	 */
	function wp_print_footer_scripts() {
		if ( !current_user_can( 'edit_posts' ) )
			return;
		?>

		<style type="text/css">
			a[href="#"] {
				position: relative;
				counter-increment: calyx-broken-link;
			}

			a[href="#"]::before {
				content: 'Broken';
				position: absolute;
				left: 0;
				top: 0;
				z-index: 2;
				padding: 8px 15px;
				background-color: #f00;
				transform: rotate( -20deg ) translate( -10%, -10% );
				text-transform: uppercase;
				font-family: sans-serif;
				pointer-events: none;
				white-space: nowrap;
				letter-spacing: 2px;
				font-weight: 600;
				font-size: 0.5em;
				color: #FFF;
				opacity: 1;

				-webkit-transition: opacity 0.2s;
				        transition: opacity 0.2s;

				-webkit-box-shadow: 2px 2px 5px 0px rgba( 0, 0, 0, 0.5 );
				   -moz-box-shadow: 2px 2px 5px 0px rgba( 0, 0, 0, 0.5 );
				        box-shadow: 2px 2px 5px 0px rgba( 0, 0, 0, 0.5 );
			}

				a[href="#"]:hover::before {
					opacity: 0;
				}
		</style>

		<?php
	}

}

?>