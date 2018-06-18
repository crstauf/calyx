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

		add_action( 'admin_init',         array( &$this, 'maybe_cap_num_list_table_items' ) );
		add_action( 'wp_dashboard_setup', array( &$this, 'maybe_disable_dashboard_widgets' ) );

		if ( ACF_LITE ) {

			add_action( 'load-post.php',                                array( Calyx()->admin(), 'load_acfs' ) );
			add_action( 'load-post-new.php',                            array( Calyx()->admin(), 'load_acfs' ) );
			add_action( 'wp_ajax_acf/location/match_field_groups_ajax', array( Calyx()->admin(), 'load_acfs' ), 9 );

		}

		do_action( 'qm/stop', __METHOD__ . '()' );
	}

	/**
	 * If server is under high load, cap number of list items.
	 *
	 * Action hook: admin_init
	 *
	 * @uses Calyx::doing_ajax()
	 * @uses Calyx::is_server_high_load()
	 * @uses Calyx_Admin_Filters::user_option_per_page()
	 */
	function maybe_cap_num_list_table_items() {
		if (
			Calyx()->doing_ajax()
			|| !Calyx()->is_server_high_load()
		)
			return;

		$user_options_per_page = array(
			'users',
			'orders',
			'redirection_log',
			'gform_forms',
			'upload',
		);

		foreach ( get_post_types() as $post_type )
			$user_options_per_page[] = 'edit_' . $post_type;

		foreach ( $user_options_per_page as $user_option )
			add_filter( $user_option . '_per_page', array( Calyx()->admin( 'filters' ), 'user_option_per_page' ) );

	}

	/**
	 * If server is under high load, disable dashboard widgets.
	 *
	 * Action hook: wp_dashboard_setup
	 *
	 * @uses Calyx::is_server_high_load()
	 */
	function maybe_disable_dashboard_widgets() {
		if ( Calyx()->is_server_high_load() ) {
			remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'normal' );
			remove_meta_box( 'rg_forms_dashboard',       'dashboard', 'normal' );
			remove_meta_box( 'wpe_dify_news_feed',       'dashboard', 'normal' );
		}
	}

}