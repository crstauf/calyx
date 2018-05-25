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

		add_action( 'init',               array( &$this, 'init'                  ) );
		add_action( 'wp_head',            array( &$this, 'wp_head'               ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'wp_enqueue_scripts__0' ), 0 );
		add_action( 'wp_footer',          array( &$this, 'wp_footer'             ) );

		do_action( 'qm/stop', __METHOD__ . '()' );
	}

	/**
	 * Action: init
	 *
	 * @uses Calyx_Front::_register_vendor_assets() to register vendor assets.
	 */
	function init() {

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
	 * - enqueue modernizr and lazysizes early
	 */
	function wp_enqueue_scripts__0() {

		wp_enqueue_script( 'modernizr' );
		wp_enqueue_script( 'lazysizes' );

	}

	/**
	 * Action: wp_footer
	 *
	 * - enqueue webfontloader
	 */
	function wp_footer() {

		wp_enqueue_script( 'webfontloader' );

	}

}

?>