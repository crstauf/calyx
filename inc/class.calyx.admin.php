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

require_once STYLESHEETPATH . '/inc/class.edit-template.php';


/*
 ######  ##          ###     ######   ######
##    ## ##         ## ##   ##    ## ##    ##
##       ##        ##   ##  ##       ##
##       ##       ##     ##  ######   ######
##       ##       #########       ##       ##
##    ## ##       ##     ## ##    ## ##    ##
 ######  ######## ##     ##  ######   ######
*/

Calyx()->admin = new Calyx_Admin;

class Calyx_Admin {

    var $actions = null,
        $filters = null,
        $templates = null;

    function __construct() {

        $this->actions = new Calyx_AdminActions;
        $this->filters = new Calyx_AdminFilters;

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

class Calyx_AdminActions {

    function __construct() {



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

class Calyx_AdminFilters {

    var $tinymce_init;

    function __construct() {

        add_filter( 'tiny_mce_plugins',			array( &$this, 'tiny_mce_plugins' ) );
        add_filter( 'mce_external_plugins',		array( &$this, 'mce_external_plugins' ) );
    	add_filter( 'mce_buttons_2',			array( &$this, 'mce_buttons_2' ) );
    	add_filter( 'tiny_mce_before_init',		array( &$this, 'tiny_mce_before_init' ) );
    	add_filter( 'tiny_mce_before_init',		array( &$this, 'tiny_mce_before_init_999999' ), 999999 );

    }

    function tiny_mce_plugins( $plugins ) {
        if ( is_array( $plugins ) )
            $plugins = array_diff( $plugins, array( 'wpemoji' ) );

        return $plugins;
    }

    function mce_external_plugins() {
    	return array( 'webfontloader' => get_theme_asset_uri( 'js/mce-external-plugins.min.js' ) );
    }

    function mce_buttons_2( $buttons ) {
    	array_unshift( $buttons, 'styleselect' );
    	return $buttons;
    }

    function tiny_mce_before_init( $init ) {
        $this->tinymce_init = $init;

        if ( !array_key_exists( 'content_css', $init ) )
            $init['content_css'] = '';

        if ( !array_key_exists( 'style_formats', $init ) )
            $init['style_formats'] = json_encode( array() );

        $init['content_css_array'   ] = explode( ',', $init['content_css']    );
        $init['body_class_array'    ] = explode( ' ', $init['body_class']     );
        $init['preview_styles_array'] = explode( ' ', $init['preview_styles'] );
        $init['style_formats_array' ] = json_decode(  $init['style_formats']  );

        foreach ( array(
            THEME_PREFIX . '/copy',
            THEME_PREFIX . '/copy/responsive',
            THEME_PREFIX . '/editor-style',
        ) as $handle )
            $init['content_css_array'][$handle] = add_query_arg(
                'ver',
                wp_styles()->registered[$handle]->ver,
                wp_styles()->registered[$handle]->src
            );

		$init['style_formats_array'] = array_merge( json_decode( $init['style_formats'] ), array(
    		'fonts' => array( 'title' => 'Fonts', 'items' => array(
    		) ),
    		'text-color' => array( 'title' => 'Text Color', 'items' => array(
                'hovers' => array( 'title' => 'Hovers', 'items' => array(
                    'black' => array( 'title' => 'Black', 'selector' => '*', 'inline' => 'span', 'classes' => 'hover-color-black' ),
                    'white' => array( 'title' => 'White', 'selector' => '*', 'inline' => 'span', 'classes' => 'hover-color-white' ),
                ) ),
                'black' => array( 'title' => 'Black', 'selector' => '*', 'inline' => 'span', 'classes' => 'color-black' ),
                'white' => array( 'title' => 'White', 'selector' => '*', 'inline' => 'span', 'classes' => 'color-white' ),
    		) ),
    		'bg-color' => array( 'title' => 'Background Color', 'items' => array(
                'hovers' => array( 'title' => 'Hovers', 'items' => array(
                    'black' => array( 'title' => 'Black', 'selector' => '*', 'inline' => 'span', 'classes' => 'hover-bg-black' ),
                    'white' => array( 'title' => 'White', 'selector' => '*', 'inline' => 'span', 'classes' => 'hover-bg-white' ),
                ) ),
                'black' => array( 'title' => 'Black', 'selector' => '*', 'inline' => 'span', 'classes' => 'bg-black' ),
                'white' => array( 'title' => 'White', 'selector' => '*', 'inline' => 'span', 'classes' => 'bg-white' ),
    		) ),
            'buttons' => array( 'title' => 'Buttons', 'items' => array(
                'btn' => array( 'title' => 'Button', 'selector' => 'a', 'classes' => 'btn' ),
            ) ),
    	) );

        if ( array_key_exists( 'post', $_GET ) ) {
            $template = get_page_template_slug( $_GET['post'] );
            if ( !empty( $template ) )
                $init['body_class_array'][] = 'page-template-' . basename( $template, '.php' );
        }

        $init['preview_styles_array'] = array_merge( explode( ' ', $init['preview_styles'] ), explode( ' ', 'color font-family background-color padding font-weight' ) );

        return $init;
    }

    function tiny_mce_before_init_999999( $init ) {
        $editor_style = $init['content_css_array'][THEME_PREFIX . '/editor-style'];
        unset( $init['content_css_array'][THEME_PREFIX . '/editor-style'] );

        $init['content_css_array'][THEME_PREFIX . '/editor-style'] = $editor_style;

        $init['content_css'   ] = implode( ',', array_unique( array_merge( explode( ',', $init['content_css'   ] ), array_values( $init['content_css_array'] ) ) ) );
        $init['body_class'    ] = implode( ' ', array_unique( array_merge( explode( ' ', $init['body_class'    ] ), array_unique( $init['body_class_array' ] ) ) ) );
        $init['preview_styles'] = implode( ' ', array_unique( array_merge( explode( ' ', $init['preview_styles'] ), $init['preview_styles_array'] ) ) );
        $init['style_formats' ] = json_encode( array_merge( json_decode( $init['style_formats'] ), $this->encode_tinymce_style_formats( $init['style_formats_array'] ) ) );

        unset(
            $init['content_css_array'],
            $init['body_class_array'],
            $init['style_formats_array'],
            $init['preview_styles_array']
        );

        return $init;
    }

        function encode_tinymce_style_formats( $array ) {
            foreach ( $array as $k => $format ) {
                if ( is_array( $format ) && array_key_exists( 'items', $format ) )
                    $array[$k]['items'] = $this->encode_tinymce_style_formats( $array[$k]['items'] );
            }
            return array_values( $array );
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
