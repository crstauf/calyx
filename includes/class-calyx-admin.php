<?php
/**
 * Helper for admin.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Admin initialization, includes, and functions.
 */
class Calyx_Admin {
	use Calyx_Singleton;

	/** @var null|Calyx_Admin_Actions Admin actions container. */
	protected $_actions = null;

	/** @var null|Calyx_Admin_Filters Admin filters container. */
	protected $_filters = null;

	/**
	 * Construct.
	 */
	protected function __construct() {
		do_action( 'qm/start', __METHOD__ . '()' );

		$this->_actions = Calyx_Admin_Actions::create_instance();
		$this->_filters = Calyx_Admin_Filters::create_instance();

		do_action( 'qm/stop', __METHOD__ . '()' );
	}

	/**
	 * Include files.
	 */
	public static function include_files() {
		require_once CALYX_ABSPATH . 'includes/class-calyx-admin-actions.php';
		require_once CALYX_ABSPATH . 'includes/class-calyx-admin-filters.php';
		include_once CALYX_ABSPATH . 'temporary/admin.php';

		do_action( 'qm/lap', THEME_PREFIX . '/' . __FUNCTION__ . '()', 'admin' );
	}

	/** Alias for $_actions property. */
	function actions() { return $this->_actions; }

	/** Alias for $_filter property. */
	function filters() { return $this->_filters; }

	/**
	 * Load all registered ACF files.
	 *
	 * @uses Calyx::has_acf_files()
	 * @uses Calyx::get_acf_files()
	 * @uses Calyx::load_acf_file()
	 */
	function maybe_load_acf_files() {
		if ( !Calyx()->has_acf_files() )
			return;

		do_action( THEME_PREFIX . '/acfs/loading_all' );

		remove_all_filters( 'acf/get_field_groups' );

		// add fields registered by PHP files
		add_filter( 'acf/get_field_groups', 'api_acf_get_field_groups', 2, 1 );

		foreach ( array_keys( Calyx()->get_acf_files() ) as $handle )
			Calyx()->load_acf_file( $handle );

		do_action( THEME_PREFIX . '/acfs/loaded_all' );
	}

}

add_action( THEME_PREFIX . '/include_files/after_core', array( 'Calyx_Admin', 'include_files' ) );

?>