<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

abstract class Calyx_EditTemplate {

    var $template_name,     // front-page
        $template_filename; // front-page.php

    function __construct() {

        add_action( 'current_screen', array( &$this, 'action_current_screen' ), 9 );
        add_action( 'save_post_page', array( &$this, 'action_save_post' ) );

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

    function action_current_screen() {
        if ( !$this->test_screen() )
            return false;

        add_filter( 'admin_body_class', array( &$this, 'filter_admin_body_class' ) );

        return true;
    }

    function action_edit_form_after_editor( $post ) {
        wp_nonce_field( $this->nonce_action, '_edit_' . $this->nonce_name . '_nonce' );
    }

    function action_save_post( $post_id ) {

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return false;

        if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
            return false;

        return $post_id;

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

    function filter_admin_body_class( $classes ) {
        $classes .= ' edit-' . $this->template_name;
        return $classes;
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

    function test_screen() {
        if ( !function_exists( 'get_current_screen' ) )
            return false;

        $screen = get_current_screen();

        if ( !is_object( $screen ) || empty( $screen->base ) )
            return false;

        if (
            'post' !== $screen->base
            || 'page' !== $screen->id
            || 'page' !== $screen->post_type
            || !is_array( $_GET )
            || !count( $_GET )
        )
            return false;

        if ( is_array( $_GET ) && count( $_GET ) && array_key_exists( 'post', $_GET ) )
            $post = get_post( $_GET['post'] );

        if (
            (
                'add' === $screen->action
                && array_key_exists( 'new_template', $_GET )
                && $this->template_name === $_GET['new_template']
            ) || (
                '' === $screen->action
                && array_key_exists( 'post', $_GET )
                && array_key_exists( 'action', $_GET )
                && 'edit' === $_GET['action']
                && (
                    get_page_template_slug( $_GET['post'] ) === $this->template_filename
                    || $post->post_name === $this->template_name
                )
            )
        )
            return true;

        return false;
    }

}

?>
