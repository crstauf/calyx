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
	 * Limits the number of items displayed in list tables.
	 *
	 * @param int $per_page User setting for number of items per page.
	 *
	 * @see Calyx_Admin_Actions::maybe_cap_num_list_table_items()
	 * @see WP_List_Table::get_items_per_page()
	 *
	 * @return int
	 */
	function user_option_per_page( $per_page ) {
		return $per_page <= 50
			? $per_page
			: 50;
	}

}