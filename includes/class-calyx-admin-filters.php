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

		add_filter( 'mce_buttons_2',        array( &$this, 'mce_buttons_2'        ) );
		add_filter( 'tiny_mce_before_init', array( &$this, 'tiny_mce_before_init' ) );

	}

	/**
	 * Filter: mce_buttons_2
	 *
	 * - add 'Formats' dropdown to TinyMCE
	 *
	 * @param array $buttons
	 *
	 * @return array
	 */
	function mce_buttons_2( $buttons ) {
		array_unshift( $buttons, 'styleselect' );
		return $buttons;
	}

	/**
	 * Filter: tiny_mce_before_init
	 *
	 * - add items to 'Formats' dropdown
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	function tiny_mce_before_init( $settings ) {
		$settings['style_formats'] = json_encode( array(
			array(
				'title' => 'Headings',
				'items' => array(
					array(
						'title' => 'Heading 1',
						'inline' => 'span',
						'classes' => 'h1',
					),
					array(
						'title' => 'Heading 2',
						'inline' => 'span',
						'classes' => 'h2',
					),
					array(
						'title' => 'Heading 3',
						'inline' => 'span',
						'classes' => 'h3',
					),
					array(
						'title' => 'Heading 4',
						'inline' => 'span',
						'classes' => 'h4',
					),
					array(
						'title' => 'Heading 5',
						'inline' => 'span',
						'classes' => 'h5',
					),
					array(
						'title' => 'Heading 6',
						'inline' => 'span',
						'classes' => 'h6',
					),
				),
			),
			array(
				'title' => 'Inline',
				'items' => array(
					array(
						'title' => 'Superscript',
						'inline' => 'sup',
					),
					array(
						'title' => 'Subscript',
						'inline' => 'sub',
					),
					array(
						'title' => 'Code',
						'inline' => 'code',
					),
				),
			),
			array(
				'title' => 'Colors',
				'items' => array(),
			),
		) );

		return $settings;
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

?>