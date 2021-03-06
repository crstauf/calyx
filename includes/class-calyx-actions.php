<?php
/**
 * Container for global actions.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Global actions.
 */
class Calyx_Actions {
	use Calyx_Singleton;

	/**
	 * Construct.
	 */
	protected function __construct() {
		do_action( 'qm/start', __METHOD__ . '()' );

		add_action( 'init',               array( &$this, 'init'               ) );
		add_action( 'pre_ping',           array( &$this, 'pre_ping'           ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'wp_enqueue_scripts' ) );
		add_action( 'admin_bar_menu',     array( &$this, 'admin_bar_menu'     ), 50 );

		add_action( THEME_PREFIX . '/compatibility_monitor/version',     array( &$this, '_compatibility_monitor__version'   ), 10, 4 );
		add_action( THEME_PREFIX . '/compatibility_monitor/internal',    array( &$this, '_compatibility_monitor__hash'      ), 10, 4 );
		add_action( THEME_PREFIX . '/compatibility_monitor/__wordpress', array( &$this, '_compatibility_monitor__wordpress' ), 10, 2 );

		do_action( 'qm/stop', __METHOD__ . '()' );
	}

	/**
	 * Action: init
	 *
	 * - register theme script object
	 * - register theme copy stylesheet
	 * - register vendor assets
	 *
	 * @uses Calyx::_register_vendor_assets()
	 */
	function init() {

		wp_register_style( THEME_PREFIX . '/copy', get_theme_file_url( 'assets/critical/copy.min.css' ), null, 'init' );

		Calyx()->_register_vendor_assets();

		wp_register_script( THEME_PREFIX . '/script_object', get_theme_file_url( 'assets/js/calyx.min.js' ), null, 'init' );

			$localize_args = array(
				'_site' => home_url(),
				'_rest' => home_url( 'wp-json' ),
				'_ajax' => admin_url( 'admin-ajax.php' ),

				   '_server_high_load' => json_encode( Calyx()->server()->is_high_load()     ),
				'_server_extreme_load' => json_encode( Calyx()->server()->is_extreme_load()  ),
				      '_webfontloader' => json_encode( Calyx()->get_webfontloader_settings() ),
			);

			is_admin() && $localize_args['_admin'] = json_encode( true );

			wp_localize_script( THEME_PREFIX . '/script_object', '_' . THEME_PREFIX . '_data', $localize_args );

	}

	/**
	 * Action: pre_ping
	 *
	 * Prevent self-pinging.
	 *
	 * @param array &$links
	 */
	function pre_ping( &$links ) {
		foreach ( $links as $i => $link )
			if ( 0 === strpos( $link, home_url() ) )
				unset( $links[$i] );
	}

	/**
	 * Action: wp_enqueue_scripts
	 *
	 * - enqueue theme script object
	 * - remove built-in emoji styles
	 */
	function wp_enqueue_scripts() {

		wp_enqueue_script( THEME_PREFIX . '/script_object' );

		remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles',     'print_emoji_styles' );
		remove_action( 'admin_print_styles',  'print_emoji_styles' );

	}

	/**
	 * Action: admin_bar_menu
	 *
	 * - if WP_LOCAL_DEV or WP_DEVELOP constants are true, add link to documentation.
	 *
	 * @param WP_Admin_Bar $bar
	 */
	function admin_bar_menu( $bar ) {

		if ( WP_LOCAL_DEV || WP_DEVELOP )
			$bar->add_node( array(
				'id' => THEME_PREFIX . '-docs',
				'title' => 'View Docs',
				'parent' => 'site-name',
				'href' => get_template_directory_uri() . '/includes/_dev/docs',
				'meta' => array(
					'target' => '_blank',
				),
			) );

	}

	/**
	 * Check version of components for compatibility.
	 *
	 * @param string $dependency_name           Name of dependency, for example: WordPress, WooCommerce, etc.
	 * @param string $dependency_version        Version of dependency, for example: 2.0.1.
	 * @param string $dependent_name            Name of customization.
	 * @param string $tested_dependency_version Last tested version of dependency.
	 */
	function _compatibility_monitor__version( $dependency_name, $dependency_version, $dependent_name, $tested_dependency_version ) {
		if ( version_compare( $dependency_version, $tested_dependency_version ) > 0 )
			trigger_error( $dependency_name . ' version ' . $dependency_version . ' is not tested with ' . $dependent_name . ' (last tested with ' . $tested_dependency_version . ').' );
	}

	/**
	 * Check hash of components for compatibility.
	 *
	 * @param string $dependency_name        Name of dependency, for example: WordPress, WooCommerce, etc.
	 * @param string $dependency_hash        Latest commit hash of dependency.
	 * @param string $dependent_name         Name of customization.
	 * @param string $tested_dependency_hash Last tested commit hash of dependency.
	 */
	function _compatibility_monitor__hash( $dependency_name, $dependency_hash, $dependent_name, $tested_dependency_hash ) {
		if ( $dependency_hash !== $tested_dependency_hash )
			trigger_error( $dependency_name . ' ' . ' has not been tested with ' . $dependent_name . ' (' . $dependency_hash . ' => ' . $tested_dependency_hash . ').' );
	}

	/**
	 * Check version of WordPress and component for compatibility.
	 *
	 * @param string $dependent_name    Name of customization.
	 * @param string $tested_wp_version Last tested version of WordPress dependency.
	 */
	function _compatibility_monitor__wordpress( $dependent_name, $tested_wp_version ) {
		global $wp_version;
		do_action( THEME_PREFIX . '/compatibility_monitor/version', 'WordPress', $wp_version, $depdenent_name, $tested_wp_version );
	}

}

?>