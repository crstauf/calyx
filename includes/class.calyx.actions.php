<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

/**
 * Site-wide actions.
 */
class Calyx_Actions extends _Calyx_Core {

    protected function __construct() {
		parent::__construct();

        add_action( 'init',               array( &$this, 'init'               ) );
        add_action( 'wp_enqueue_scripts', array( &$this, 'wp_enqueue_scripts' ) );

    }

    function init() {

        wp_register_script( THEME_PREFIX . '/script_object', get_theme_asset_url( 'assets/js/calyx.min.js' ), null, 'init' );

            $localize_args = array(
                '_site' => home_url(),
                '_rest' => home_url( 'wp-json' ),
                '_ajax' => admin_url( 'admin-ajax.php' ),

                   '_server_high_load' => json_encode( Calyx()->is_server_high_load()    ),
                '_server_extreme_load' => json_encode( Calyx()->is_server_extreme_load() ),
            );

            is_admin() && $localize_args['_admin'] = json_encode( true );

            wp_localize_script( THEME_PREFIX . '/script_object', '_calyx_data', $localize_args );

        wp_register_style( THEME_PREFIX . '/copy', get_theme_asset_url( 'assets/critical/copy.min.js' ), null, 'init' );

    }

    function wp_enqueue_scripts() {

        wp_enqueue_script( THEME_PREFIX . '/script_object' );

    }

}

?>