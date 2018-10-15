<?php
/**
 * Container for admin actions.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Actions for admin.
 */
class Calyx_Admin_Actions {
	use Calyx_Singleton;

	/**
	 * Construct.
	 */
	protected function __construct() {
		do_action( 'qm/start', __METHOD__ . '()' );

		add_action( 'admin_init',         array( &$this, 'admin_init' ) );
		add_action( 'wp_dashboard_setup', array( &$this, 'wp_dashboard_setup' ) );

		do_action( 'qm/stop', __METHOD__ . '()' );
	}

	/**
	 * Action: admin_init
	 *
	 * - add editor style
	 * - set maximum number of table items if high load
	 *
	 * @uses Calyx_Admin::_maybe_cap_num_list_table_items()
	 */
	function admin_init() {
		add_editor_style( 'assets/critical/copy.min.css' );
		add_editor_style( 'assets/css/tinymce.min.css' );

		Calyx()->admin()->_maybe_cap_num_list_table_items();
	}

	/**
	 * Action: wp_dashboard_setup
	 *
	 * - disable specific Dashboard widgets if high load
	 *
	 * @uses Calyx_Server::is_high_load()
	 * @uses Calyx_Server::add_notices()
	 */
	function wp_dashboard_setup() {
		if ( !Calyx()->server()->is_high_load() )
			return;

		remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'normal' );
		remove_meta_box( 'rg_forms_dashboard',       'dashboard', 'normal' );
		remove_meta_box( 'wpe_dify_news_feed',       'dashboard', 'normal' );

		Calyx()->server()->add_notices( array(
			'Disabled WordPress SEO widget',
			'Disabled Gravity Forms widget',
			'Disabled WP Engine news feed widget',
		) );
	}

}