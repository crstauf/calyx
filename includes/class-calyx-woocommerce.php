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

	/**
	 * Construct.
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
		is_admin()
			? self::include_files__admin()
			: self::include_files__front();

		if ( !empty( WC_Data_Store::load( 'webhook' )->get_webhooks_ids() ) )
			require_once CALYX_ABSPATH . 'includes/class-woocommerce-monitor-webhooks.php';

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

		add_action( 'admin_enqueue_scripts', array( &$this, 'action__admin_enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts',    array( &$this, 'action__wp_enqueue_scripts'    ) );
		add_action( 'admin_menu',            array( &$this, 'action__admin_menu'            ), 21 );
		add_action( 'wp_dashboard_setup',    array( &$this, 'action__wp_dashboard_setup'    ) );
		add_action( 'admin_bar_menu',        array( &$this, 'action__admin_bar_menu'        ), 65 );

		add_filter( 'request',                  array( &$this, 'filter__request'                  ) );
		add_filter( 'dashboard_glance_items',   array( &$this, 'filter__dashboard_glance_items'   ) );
		add_filter( 'edit_shop_order_per_page', array( &$this, 'filter__edit_shop_order_per_page' ) );
		add_filter( 'schedule_event',           array( &$this, 'filter__schedule_event'           ) );

	}

	/**
	 * Initialize theme's WooCommerce manager.
	 *
	 * Final step in construct.
	 */
	protected function initialize() {
		do_action( THEME_PREFIX . '/compatibility_monitor/__woocommerce', __CLASS__, '3.4.0' );
		do_action( THEME_PREFIX . '/woocommerce/before_init' );

		if ( class_exists( 'Calyx_WooCommerce_MonitorWebhooks' ) )
			$this->add_feature( 'monitor-webhooks', new Calyx_WooCommerce_MonitorWebhooks );

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
	 * Action: admin_enqueue_scripts
	 *
	 * @uses $this::_enqueue_assets()
	 */
	function action__admin_enqueue_scripts() {
		$this->_enqueue_assets();
	}

	/**
	 * Action: wp_enqueue_scripts
	 *
	 * @uses $this::_enqueue_assets()
	 */
	function action__wp_enqueue_scripts() {
		$this->_enqueue_assets();
	}

	/**
	 * Action: admin_menu
	 *
	 * @uses $this::_maybe_remove_reports_page()
	 */
	function action__admin_menu() {
		$this->_maybe_remove_reports_page();
	}

	/**
	 * Hook: wp_dashboard_setup
	 *
	 * - add inline styles for WooCommerce glance items.
	 *
	 * @see $this::filter__dashboard_glance_items()
	 */
	function action__wp_dashboard_setup() {
		wp_add_inline_style( 'dashboard', $this->_inlineStyle_dashboard() );
	}

	/**
	 * Add orders count and search field to admin bar.
	 *
	 * @param WP_Admin_Bar $bar
	 *
	 * @uses $this::get_orders_count__today()
	 */
	function action__admin_bar_menu( $bar ) {
		if ( Calyx()->server()->is_extreme_load() ) {
			$count = 'NA';
			$text = 'Count for orders today currently not available';
			$title = '<span class="ab-label count-na" aria-hidden="true">NA</span>';

			Calyx()->server()->add_notices( 'Disabled count of today\'s orders' );
		} else {
			$count = $this->get_orders_count__today();
			$text = sprintf( _n( '%s order today', '%s orders today', $count ), number_format_i18n( $count ) );
			$title = '<span class="ab-label count-' . esc_attr( $count ) . '" aria-hidden="true">' . number_format_i18n( $count ) . '</span>';
		}

		$icon  = '<span class="ab-icon"></span>';
		$title .= '<span class="screen-reader-text">' . $text . '</span>';

		$menu_id = THEME_PREFIX . '-orders';

		$bar->add_menu( array(
			'id'    => $menu_id,
			'title' => $icon . $title,
			'href'  => add_query_arg( 'post_type', 'shop_order', admin_url( 'edit.php' ) ),
		) );

		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();

			if (
				'post' === $screen->base
				&& 'shop_order' === $screen->id
			)
				$current_id = $_GET['post'];
		}

		$bar->add_menu( array(
			'parent' => $menu_id,
			'id' => THEME_PREFIX . '-orders-search',
			'title' => '<input type="text" id="' . THEME_PREFIX . '-admin-bar-orders-search" placeholder="Order ID/Email"' . ( !empty( $current_id ) ? ' value="' . esc_attr( $current_id ) . '"' : '' ) . ' />',
		) );
	}

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
	 * Filter: request
	 *
	 * - add support for searching orders by billing email
	 *
	 * @param array $request Query arguments.
	 *
	 * @return array
	 */
	function filter__request( $request ) {
		global $typenow;

		if (
			!current_user_can( 'view_shop_orders' )
			|| !in_array( $typenow, wc_get_order_types( 'order-meta-boxes' ) )
			|| !array_key_exists( '_billing_email', $_GET )
			|| empty( $_GET['_billing_email'] )
		)
			return $request;

		$request['meta_query'] = array(
			array(
				'key'   => '_billing_email',
				'value' => esc_attr( $_GET['_billing_email'] ),
				'compare' => 'LIKE'
			),
		);

		return $request;
	}

	/**
	 * Filter: dashboard_glance_items
	 *
	 * - add count of CPTs to 'At a Glance' dashboard widget.
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	function filter__dashboard_glance_items( $items ) {
		foreach ( array( 'product', 'shop_order', 'shop_coupon' ) as $post_type ) {
			$object = get_post_type_object( $post_type );
			$count = wp_count_posts( $post_type );

			$items['count_' . $post_type] =
				'<a class="icon-wc-' . $post_type . '" href="' . admin_url( add_query_arg( 'post_type', $post_type, 'edit.php' ) ) . '">' .
					$count->publish . ' ' . _n( $object->labels->singular_name, $object->labels->name, $count->publish ) .
				'</a>';
		}

		return $items;
	}

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

	/**
	 * Filter: schedule_event
	 *
	 * - prevent scheduling of subscription report caching event
	 *
	 * @return false|object
	 */
	function filter__schedule_event( $event ) {
		return 'wcs_report_update_cache' === $event->hook
			? false
			: $event;
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
	 * Add inline styles for admin bar menu item.
	 *
	 * @uses $this::_inlineStyle_adminBar()
	 * @uses $this::_inlineScript_adminBar()
	 */
	function _enqueue_assets() {
		wp_add_inline_style(  'admin-bar', $this->_inlineStyle_adminBar()  );
		wp_add_inline_script( 'admin-bar', $this->_inlineScript_adminBar() );
	}

	/**
	 * Get styles for dashboard widget 'At a Glance' items.
	 *
	 * @return string CSS.
	 */
	function _inlineStyle_dashboard() {
		ob_start();
		?>

		.icon-wc-product::before {
			font-family: 'WooCommerce' !important;
			content: '\e006' !important;
		}
		.icon-wc-shop_order::before {
			font-family: 'WooCommerce' !important;
			content: '\e03d' !important;
		}
		.icon-wc-shop_coupon::before {
			font-family: 'WooCommerce' !important;
			content: '\e600' !important;
		}

		<?php
		return ob_get_clean();
	}

	/**
	 * Get styles for admin bar menu item.
	 *
	 * @return string CSS.
	 */
	function _inlineStyle_adminBar() {
		ob_start();
		?>

		#wp-admin-bar-<?php echo THEME_PREFIX ?>-orders .ab-item {
			height: auto;
		}

		#wp-admin-bar-<?php echo THEME_PREFIX ?>-orders .ab-label.count-0,
		#wp-admin-bar-<?php echo THEME_PREFIX ?>-orders .ab-label.count-na { opacity: 0.5; }

		#wp-admin-bar-<?php echo THEME_PREFIX ?>-orders .ab-icon::before {
			top: 2px;
			font-family: 'WooCommerce' !important;
			content: '\e03d' !important;
		}

		#wp-admin-bar-calyx-orders-default {
			padding: 0 !important;
		}

		#wp-admin-bar-<?php echo THEME_PREFIX ?>-orders-search .ab-item {
			height: auto !important;
		}

		#<?php echo THEME_PREFIX ?>-admin-bar-orders-search {
			padding: 0 5px;
			background-color: inherit;
			box-sizing: border-box;
			text-align: center;
			border: none;
			color: inherit;
		}

		#<?php echo THEME_PREFIX ?>-admin-bar-orders-search::-webkit-input-placeholder {
			color: inherit;
			opacity: 0.5;
		}

		<?php
		return ob_get_clean();
	}

	/**
	 * Get JavaScript for admin bar menu item.
	 *
	 * @return string JavaScript.
	 */
	function _inlineScript_adminBar() {
		ob_start();
		?>

		jQuery( document ).ready( function() {

			jQuery( '#wp-admin-bar-<?php echo THEME_PREFIX ?>-orders' ).hoverIntent( {
				over: function() {
					jQuery( this ).addClass( 'hover' );
					jQuery( '#calyx-admin-bar-orders-search' ).focus();
				},
				out: function() {
					jQuery( this ).removeClass( 'hover' );
					jQuery( '#calyx-admin-bar-orders-search' ).val( document.getElementById( '<?php echo THEME_PREFIX ?>-admin-bar-orders-search' ).getAttribute( 'value' ) );
				},
				timeout: 180,
				sensitivity: 7,
				interval: 100
			} );

			jQuery( '#calyx-admin-bar-orders-search' ).on( 'keydown', function( ev ) {
				if ( 13 !== ev.keyCode )
					return true;

				var search_val = jQuery( this ).val();

				if ( parseFloat( search_val ) == search_val ) { /* then object ID */
					window.location = "<?php echo esc_js( esc_url( add_query_arg( 'action', 'edit', admin_url( 'post.php' ) ) ) ) ?>&post=" + parseFloat( search_val );
				} else {
					window.location = "<?php echo esc_js( esc_url( add_query_arg( 'post_type', 'shop_order', admin_url( 'edit.php' ) ) ) ) ?>&_billing_email=" + search_val;
				}
			} );

		} );

		<?php
		return ob_get_clean();
	}

	/**
	 * Remove WooCommerce Reports page during high load.
	 *
	 * @see WC_Admin_Menus::reports_menu()
	 *
	 * @uses Calyx_Server::is_high_load()
	 * @uses Calyx_Server::add_notices()
	 */
	function _maybe_remove_reports_page() {
		if (
			!is_current_action( 'admin_menu' )
			|| !Calyx()->server()->is_high_load()
		)
			return;

		Calyx()->server()->add_notices( 'Disabled WooCommerce reports screen' );

		current_user_can( 'manage_woocommerce' )
			? remove_submenu_page( 'woocommerce', 'wc-reports' )
			: remove_menu_page( 'wc-reports' );
	}

	/**
	 * Check whether to include the mini cart.
	 *
	 * @return bool
	 */
	function include_mini_cart() {
		return true;
	}

	/**
	 * Alias for include_mini_cart().
	 *
	 * @uses $this::include_mini_cart()
	 *
	 * @return bool
	 */
	function has_mini_cart() {
		return $this->include_mini_cart();
	}

	/**
	 * Get number of orders for today (five minute interval).
	 *
	 * @uses $this::_get_orders_count__today()
	 *
	 * @return int
	 */
	function get_orders_count__today() {
		$count = get_transient( THEME_PREFIX . '_orders_count__recent_today' );

		if ( !empty( $count ) )
			return $count;

		$count = $this->_get_orders_count__today();

		set_transient( THEME_PREFIX . '_orders_count__recent_today', $count, MINUTE_IN_SECONDS * 5 );

		return intval( $count );
	}

	/**
	 * Get number of orders for today (real-time).
	 *
	 * @return int
	 */
	function _get_orders_count__today() {
		global $wpdb;

		$values = wc_get_order_types( 'order-count' );
		$placeholders = implode( ', ', array_fill( 0, count( $values ), '%s' ) );

		$start = strtotime( 'midnight', time() + ( HOUR_IN_SECONDS * get_option( 'gmt_offset' ) ) );
		$values[] = date( 'Y-m-d H:i:s', $start );

		$query = $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->posts WHERE `post_type` IN ( $placeholders ) AND `post_date` >= %s", $values );

		return intval( $wpdb->get_var( $query ) );
	}

}

add_action( THEME_PREFIX . '/include_files/after_core', array( 'Calyx_WooCommerce', 'include_files' ) );

?>
