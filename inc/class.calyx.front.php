<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}


/*
######## #### ##       ########  ######
##        ##  ##       ##       ##    ##
##        ##  ##       ##       ##
######    ##  ##       ######    ######
##        ##  ##       ##             ##
##        ##  ##       ##       ##    ##
##       #### ######## ########  ######
*/

require_once STYLESHEETPATH . '/inc/class.enhance-enqueues.php';


/*
 ######  ##          ###     ######   ######
##    ## ##         ## ##   ##    ## ##    ##
##       ##        ##   ##  ##       ##
##       ##       ##     ##  ######   ######
##       ##       #########       ##       ##
##    ## ##       ##     ## ##    ## ##    ##
 ######  ######## ##     ##  ######   ######
*/

Calyx()->front = new Calyx_Front;

class Calyx_Front {

    var $actions = null,
        $filters = null;

    function __construct() {

        $this->actions = new Calyx_FrontActions;
        $this->filters = new Calyx_FrontFilters;

    }

    function webfontloader( $position = 'head' ) {

    }

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

class Calyx_FrontActions {

    function __construct() {

        add_action( 'init',                    array( &$this, 'init' ) );
    	add_action( 'login_enqueue_scripts',   array( &$this, 'login_enqueue_scripts' ) );
    	add_action( 'wp_enqueue_scripts',      array( &$this, 'wp_enqueue_scripts_11' ), 11 );
    	add_action( 'wp_print_footer_scripts', array( &$this, 'wp_print_footer_scripts' ) );

    }

    function init() {

		wp_register_script( THEME_PREFIX . '/modernizr',       get_theme_asset_uri( 'js/modernizr.min.js' ), array(), '3.3.1-init' );
		wp_register_script( THEME_PREFIX . '/scripts',         get_theme_asset_uri( 'js/scripts.js' ), array( 'lazysizes', 'webfontloader' ), 'init' );

            wp_add_inline_script( 'webfontloader',             Calyx()->front->webfontloader() );
            wp_add_inline_script( THEME_PREFIX . '/scripts',   Calyx()->front->webfontloader( 'footer' ) );

		wp_register_style( THEME_PREFIX . '/above-fold',       get_theme_asset_uri( 'css/above-fold/above-fold.min.css' ), array(), 'init' );
		wp_register_style( THEME_PREFIX . '/login',            get_theme_asset_uri( 'css/login.min.css' ), array( 'login' ), 'init' );
        wp_register_style( THEME_PREFIX . '/responsive',       get_theme_asset_uri( 'css/responsive.min.css' ), array( THEME_PREFIX . '/style' ), 'init', '(max-width: 809px)' );
		wp_register_style( THEME_PREFIX . '/style',            get_theme_asset_uri( 'style.min.css' ), array( THEME_PREFIX . '/copy' ), 'init' );

			wp_styles()->add_data( THEME_PREFIX . '/fonts', 'noscript', true );

			foreach ( preg_grep(
                '/^' . THEME_PREFIX . '\/(?:above-fold|copy)(?:\/.*)?/',
                array_keys( wp_styles()->registered )
            ) as $handle )
                wp_styles()->registered[$handle]->extra['inline'] = true;

            wp_styles()->registered[THEME_PREFIX . '/login']->extra['inline'] = true;

	}

	function login_enqueue_scripts() {
        wp_enqueue_style( THEME_PREFIX . '/login' );
    }

	function wp_enqueue_scripts_11() {
		remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles',     'print_emoji_styles' );
		remove_action( 'admin_print_styles',  'print_emoji_styles' );
	}

	function wp_print_footer_scripts() {
        if ( is_404() )
            wp_dequeue_script( 'wp-embed' );
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

class Calyx_FrontFilters {

    var $template_path = '';

    function __construct() {

        add_filter( 'template_include', array( &$this, 'template_include' ) );
        add_filter( 'body_class',       array( &$this, 'body_class' ) );
    	add_filter( 'post_class',       array( &$this, 'post_class' ), 10, 3 );

    }

    function template_include( $template ) {
        $this->template_path = $template;
        return $template;
    }

	function body_class( $classes ) {
        if ( is_page() )
            foreach ( array_keys( $classes, 'page-template-default' ) as $k )
                $classes[$k] = 'page-template-' . sanitize_title_with_dashes( substr(
                    str_replace(
                        array(
                            get_stylesheet_directory(),
                            get_template_directory(),
                            ABSPATH,
                            '.php'
                        ),
                        '',
                        $this->template_path
                    ),
                    1
                ) );

        if ( has_post_thumbnail() )
            $classes = array_merge( $classes, array(
                'has-post-thumbnail',
                'post-thumbnail-id-' . get_post_thumbnail_id(),
            ) );
        else
            $classes[] = 'no-post-thumbnail';

		if ( is_singular() ) {
			$object = get_queried_object();
			if ( !empty( $object->post_password ) )
				$classes[] = 'post-password-' . ( post_password_required() ? 'required' : 'accepted' );
		}

        return $classes;
    }

    function post_class( $classes, $class, $post_id ) {
        if ( has_post_thumbnail( $post_id ) )
            $classes[] = 'post-thumbnail-id-' . get_post_thumbnail_id();
        else
            $classes[] = 'no-post-thumbnail';

        return $classes;
    }


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




?>
