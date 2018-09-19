<?php
/**
 * Container for admin filters.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Filters for admin.
 */
class Calyx_Admin_Filters {
	use Calyx_Singleton;

	/**
	 * Construct.
	 */
	function __construct() {

	}

	/**
	 * Limits the number of items displayed in list tables.
	 *
	 * @param int $per_page User setting for number of items per page.
	 *
	 * @see Calyx_Admin_Actions::maybe_cap_num_list_table_items()
	 * @see WP_List_Table::get_items_per_page()
	 *
	 * @uses Calyx_Server::add_notices()
	 *
	 * @return int
	 */
	function user_option_per_page( $per_page ) {
		static $_once = false;

		if ( !$_once ) {
			Calyx()->server()->add_notices( 'Set max number of table items: ' . current_filter() );
			$_once = true;
		}

		return $per_page <= 20
			? $per_page
			: 20;
	}

}