<?php
/**
 * Helper for WooCommerce.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Files, actions, filters, and functions for WooCommerce.
 */
class Calyx_WooCommerce {
	use Calyx_Singleton, Calyx_ManageFeatures;


	/*
	 ######   #######  ##    ##  ######  ######## ########  ##     ##  ######  ########
	##    ## ##     ## ###   ## ##    ##    ##    ##     ## ##     ## ##    ##    ##
	##       ##     ## ####  ## ##          ##    ##     ## ##     ## ##          ##
	##       ##     ## ## ## ##  ######     ##    ########  ##     ## ##          ##
	##       ##     ## ##  ####       ##    ##    ##   ##   ##     ## ##          ##
	##    ## ##     ## ##   ### ##    ##    ##    ##    ##  ##     ## ##    ##    ##
	 ######   #######  ##    ##  ######     ##    ##     ##  #######   ######     ##
	*/

	protected function __construct() {
		do_action( 'qm/start', __METHOD__ . '()' );

		$this->register_hooks();
		$this->initialize();

		do_action( 'qm/stop', __METHOD__ . '()' );
	}

	/**
	 * Include files used in admin and on the frontend.
	 */
	public static function include_files() {
		( is_admin() && self::include_files__admin() )
		             || self::include_files__front();

		if ( apply_filters( THEME_PREFIX . '/woocommerce/monitor-webhooks', !empty( WC_Data_Store::load( 'webhook' )->get_webhooks_ids() ) ) )
			require_once CALYX_ABSPATH . 'includes/class-calyx-wc-monitor-webhooks.php';

		do_action( 'qm/lap', THEME_PREFIX . '/' . __FUNCTION__ . '()', 'woocommerce' );
	}

		/**
		 * Include required admin files.
		 */
		public static function include_files__admin() {

		}

		/**
		 * Include required frotnend files.
		 */
		public static function include_files__front() {

		}

	/**
	 * Register action and filter hooks.
	 *
	 * Step 1 in construct.
	 * Next: initialize().
	 */
	protected function register_hooks() {

		add_action( THEME_PREFIX . '/compatibility_monitor/__woocommerce', array( &$this, 'action__compatibility_monitor' ), 10, 2 );
		add_action( THEME_PREFIX . '/woocommerce/features/add',            array( &$this, 'add_feature'                   ), 10, 2 );

		add_action( 'admin_menu', array( &$this, '_maybe_remove_reports_page' ), 21 );

		add_filter( 'edit_shop_order_per_page', array( &$this, 'filter__edit_shop_order_per_page' ) );

	}

	/**
	 * Initialize theme's WooCommerce manager.
	 *
	 * Final step in construct.
	 */
	protected function initialize() {
		do_action( THEME_PREFIX . '/compatibility_monitor/__woocommerce', __CLASS__, '3.4.0' );
		do_action( THEME_PREFIX . '/woocommerce/before_init' );

		class_exists( 'Calyx_WC_MonitorWebhooks' ) && $this->add_feature( 'monitor-webhooks', new Calyx_WC_MonitorWebhooks );

		do_action( THEME_PREFIX . '/woocommerce/init' );
		do_action( THEME_PREFIX . '/woocommerce/after_init' );
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

	/**
	 * Check version of WooCommerce and component for compatibility.
	 *
	 * @param string $dependent_name    Name of customization.
	 * @param string $tested_wc_version Last tested version of WooCommerce dependency.
	 */
	function action__compatibility_monitor( $dependent_name, $tested_wc_version ) {
		do_action( THEME_PREFIX . '/compatibility_monitor/version', 'WooCommerce', WC_VERSION, $dependent_name, $tested_wc_version );
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

	/**
	 * Limit number of orders in list table to 50.
	 *
	 * @param int $per_page User setting for number of items per page.
	 * @see WP_List_Table::get_items_per_page()
	 *
	 * @return int
	 */
	function filter__edit_shop_order_per_page( $per_page ) {
		return $per_page <= 50
			? $per_page
			: 50;
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
	 * Remove WooCommerce Reports page during high load.
	 *
	 * @see 'admin_menu' action
	 * @see WC_Admin_Menus::reports_menu()
	 */
	function _maybe_remove_reports_page() {
		if (
			!is_current_action( 'admin_menu' )
			|| !Calyx()->is_server_high_load()
		)
			return;

		current_user_can( 'manage_woocommerce' )
			? remove_submenu_page( 'woocommerce', 'wc-reports' )
			: remove_menu_page( 'wc-reports' );
	}

}

add_action( THEME_PREFIX . '/include_files/after_core', array( 'Calyx_WooCommerce', 'include_files' ) );

?>