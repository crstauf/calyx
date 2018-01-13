<?php

class base_cpt {

	var $type = '',
		$nonce = '',
		$plural = '',
		$singular = '',
		$args = array();

	public static $object = false;

	function __construct() {
		add_action('init',						array(&$this,'action_init'));
		add_filter('dashboard_glance_items',	array(&$this,'filter_dashboard_glance_items'),15);
		add_filter('post_updated_messages',		array(&$this,'filter_post_updated_messages'));

		if (method_exists($this,'hooks')) $this->hooks();
	}

	function action_init() {
		$labels = array(
			'name'					=> $this->plural,
			'singular_name'			=> $this->singular,
			'menu_name'				=> $this->plural,
			'name_admin_bar'		=> $this->singular,
			'add_new'				=> 'Add ' . ucfirst($this->singular),
			'add_new_item'			=> 'Add New ' . $this->singular,
			'new_item'				=> 'New ' . $this->singular,
			'edit_item'				=> 'Edit ' . $this->singular,
			'view_item'				=> 'View ' . $this->singular,
			'all_items'				=> 'All ' . $this->plural,
			'search_items'			=> 'Search ' . $this->plural,
			'parent_item_colon'		=> 'Parent ' . $this->plural . ':',
			'not_found'				=> 'No ' . strtolower($this->plural) . ' found',
			'not_found_in_trash'	=> 'No ' . strtolower($this->plural) . ' found in Trash',
		);

		register_post_type($this->type,array_merge(
			$this->args,
			array(
				'labels'				=> $labels,
				'public'				=> true,
				'publicly_queryable'	=> true,
				'show_ui'				=> true,
				'show_in_menu'			=> true,
				'query_var'				=> true,
				'rewrite'				=> array('slug' => ''),
				'has_archive'			=> true,
				'hierarchical'			=> false,
				'supports'				=> array(
											'title',
											'editor',
											'author',
											'thumbnail'
										),
			)
		));

		do_action('cpt_init');
	}

	function filter_dashboard_glance_items($items) {
		$count = wp_count_posts($this->type);
		$items['count_' . $this->type] = '<a class="icon-' . $this->type . '" href="' . admin_url(add_query_arg($this->type,'post_type','edit.php')) . '">' . $count->publish . _n(' ' . $this->singular,' ' . $this->plural,$count->publish) . '</a>';

		return $items;
	}

	function filter_post_updated_messages($notices) {
		global $post_ID,$post;

		$notices[$this->type] = array(
			0	=> '', // Unused. Messages start at index 1.
			1	=> sprintf( __($this->singular . ' updated. <a href="%s">View ' . strtolower($this->singular) . '</a>'), esc_url( get_permalink($post_ID) ) ),
			2	=> __('Custom field updated.'),
			3	=> __('Custom field deleted.'),
			4	=> __($this->singular . ' updated.'),
			5	=> isset($_GET['revision']) ? sprintf( __($this->singular . ' restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6	=> sprintf( __($this->singular . ' published. <a href="%s">View ' . $this->singular . '</a>'), esc_url( get_permalink($post_ID) ) ),
			7	=> __($this->singular . ' saved.'),
			8	=> sprintf( __($this->singular . ' submitted. <a target="_blank" href="%s">Preview ' . $this->singular . '</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			9	=> sprintf( __($this->singular . ' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview ' . $this->singular . '</a>'),date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
			10	=> sprintf( __($this->singular . ' draft updated. <a target="_blank" href="%s">Preview ' . $this->singular . '</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		);

		return $notices;
	}

}

?>
