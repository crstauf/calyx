<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

class Calyx_CPT {

    var $type = '',
        $nonce_name = '',
        $nonce_action = __FILE__,
        $dashicon_content = '',
        $singular = '',
        $plural = '',
        $args = '';

    function __construct() {

        add_action( 'init',						array( &$this, 'action_init' ) );
		add_action( 'admin_init',				array( &$this, 'action_admin_init' ) );
		add_filter( 'dashboard_glance_items',	array( &$this, 'filter_dashboard_glance_items' ), 15 );
		add_filter( 'post_row_actions',			array( &$this, 'filter_post_row_actions' ), 1, 2 );

		if ( array_key_exists( 'supports', $this->args ) && in_array( 'subtitle', $this->args['supports'] ) ) {
			$this->postmeta['_subtitle'] = 'post_subtitle';
			add_action( 'edit_form_before_permalink', array( &$this, 'action_edit_form_before_permalink' ) );
		}

		add_filter( 'post_updated_messages',    array( &$this, 'filter_post_updated_messages' ) );
        
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

    function action_init() {
		$labels = array(
			'name'					=> $this->plural,
			'singular_name'			=> $this->singular,
			'menu_name'				=> $this->plural,
			'name_admin_bar'		=> $this->singular,
			'add_new'				=> 'Add ' . ucfirst( $this->singular ),
			'add_new_item'			=> 'Add New ' . $this->singular,
			'new_item'				=> 'New ' . $this->singular,
			'edit_item'				=> 'Edit ' . $this->singular,
			'view_item'				=> 'View ' . $this->singular,
			'all_items'				=> 'All ' . $this->plural,
			'search_items'			=> 'Search ' . $this->plural,
			'parent_item_colon'		=> 'Parent ' . $this->plural . ':',
			'not_found'				=> 'No ' . strtolower( $this->plural ) . ' found',
			'not_found_in_trash'	=> 'No ' . strtolower( $this->plural ) . ' found in Trash',
		);

		$args = wp_parse_args( $this->args, array(
			'labels'				=> $labels,
			'public'				=> true,
			'publicly_queryable'	=> true,
			'show_ui'				=> true,
			'show_in_menu'			=> true,
			'query_var'				=> true,
			'rewrite'				=> array( 'slug' => '' ),
			'has_archive'			=> true,
			'hierarchical'			=> false,
			'supports'				=> array(
										'title',
										'editor',
										'author',
										'thumbnail'
									),
		) );

		$this->object = register_post_type( $this->type, $args );

		if (
			count( $this->postmeta )
			&& !empty( $this->nonce_name )
			&& !empty( $this->nonce_action )
		)
			add_action( 'save_post_' . $this->type, array( &$this, 'action_save_post' ), 10, 2 );

		do_action( THEME_PREFIX . '_cpt_' . $this->type . '_init' );
		do_action( THEME_PREFIX . '_cpt_init', $this->type );
	}

	function action_admin_init() {
		if ( !empty( $this->dashicon_content ) )
			wp_add_inline_style( 'dashicons', '.icon-cpt-' . $this->type . ':before { content: "' . $this->dashicon_content . '" !important; }' );
	}

    function action_edit_form_before_permalink() {
		// last update WP 4.7
		global $post;

		if ( !is_object( $post ) || get_post_type( $post ) !== $this->type )
			return;

		$post_subtitle = '';
		if ( is_object( $post ) && 'auto-draft' !== $post->post_status )
			$post_subtitle = get_post_meta( $post->ID, '_subtitle', true );
		?>

		<div id="subtitlediv">
			<div id="subtitlewrap">
				<label class="screen-reader-text" id="subtitle-prompt-text" for="subtitle"><?php echo apply_filters( 'enter_subtitle_here_' . $this->type, apply_filters( 'enter_subtitle_here', 'Enter subtitle here', $this->type ) ) ?></label>
				<input type="text" name="post_subtitle" size="30" value="<?php echo esc_attr( $post_subtitle ) ?>" id="subtitle" spellcheck="true" autocomplete="off" />
			</div>
		</div>

		<?php
		wp_nonce_field( $this->nonce_action, $this->nonce_name );
		wp_add_inline_script( 'post', 'jQuery(document).ready(function() { wptitlehint("subtitle") });' );
	}

	function action_save_post( $post_id, $post ) {
		if (
			( defined( 'DOING_AJAX' ) && DOING_AJAX )
			|| ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			|| !wp_verify_nonce( $_POST[$this->nonce_name], $this->nonce_action )
		)
			return false;

		foreach ( $this->postmeta as $postmeta_key => $field_name ) {
			if ( false === $field_name )
				continue;
			update_post_meta( $post_id, $postmeta_key, $_POST[$field_name] );
		}
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

	function filter_dashboard_glance_items( $items ) {
		$count = wp_count_posts( $this->type );
		$items['count_' . $this->type] =
			'<a class="icon-cpt-' . $this->type . '" href="' . admin_url( add_query_arg( 'post_type', $this->type, 'edit.php' ) ) . '">' .
				$count->publish . _n( ' ' . $this->singular, ' ' . $this->plural, $count->publish ) .
			'</a>';

		return $items;
	}

	function filter_post_row_actions( $actions, $post ) {
		$post_type_obj = get_post_type_object( get_post_type( $post ) );

		if ( false === $post_type_obj->public )
			unset( $actions['view'], $actions['w3tc_flush_post'] );

		return $actions;
	}

	function filter_post_updated_messages( $notices ) {
		global $post_ID, $post;

		$notices[$this->type] = array(
			0	=> '', // Unused. Messages start at index 1.
			1	=> sprintf( __( $this->singular . ' updated. <a href="%s">View ' . strtolower( $this->singular ) . '</a>' ), esc_url( get_permalink( $post_ID ) ) ),
			2	=> __( 'Custom field updated.' ),
			3	=> __( 'Custom field deleted.' ),
			4	=> __( $this->singular . ' updated.' ),
			5	=> isset( $_GET['revision'] ) ? sprintf( __( $this->singular . ' restored to revision from %s' ), wp_post_revision_title( ( int ) $_GET['revision'], false ) ) : false,
			6	=> sprintf( __( $this->singular . ' published. <a href="%s">View ' . $this->singular . '</a>' ), esc_url( get_permalink( $post_ID ) ) ),
			7	=> __( $this->singular . ' saved.'),
			8	=> sprintf( __( $this->singular . ' submitted. <a target="_blank" href="%s">Preview ' . $this->singular . '</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			9	=> sprintf( __( $this->singular . ' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview ' . $this->singular . '</a>' ), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
			10	=> sprintf( __( $this->singular . ' draft updated. <a target="_blank" href="%s">Preview ' . $this->singular . '</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);

		return $notices;
	}

}
