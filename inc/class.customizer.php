<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

Calyx()->customizer = new Calyx_Customizer;

class Calyx_Customizer {

    function __construct() {

        add_action( 'customize_register',     array( &$this, 'customize_register' ) );
        add_action( 'customize_preview_init', array( &$this, 'customize_preview_init' ) );
        add_action( 'wp_enqueue_scripts',     array( &$this, 'wp_enqueue_scripts' ) );

    }

    function wp_enqueue_scripts() {

		if ( is_customize_preview() )
			wp_enqueue_style( THEME_PREFIX . '/customize-preview', get_theme_asset_uri( 'css/customize-preview.min.css' ) );

	}

    function customize_register( $customizer ) {

        $customizer->add_section(
			THEME_PREFIX . '-contact',
			array(
				'title' => 'Contact Info',
			)
		);

		$customizer->add_setting(
			THEME_PREFIX . '-phone',
			array(
				'default'			=> '',
				'type'				=> 'theme_mod',
				'capability'		=> 'edit_theme_options',
			)
		);
		$customizer->add_setting(
			THEME_PREFIX . '-email',
			array(
				'default'			=> '',
				'type'				=> 'theme_mod',
				'capability'		=> 'edit_theme_options',
			)
		);
		$customizer->add_setting(
			THEME_PREFIX . '-address',
			array(
				'default'			=> '',
				'type'				=> 'theme_mod',
				'capability'		=> 'edit_theme_options',
			)
		);

		$customizer->add_control(
			THEME_PREFIX . '-phone',
			array(
				'settings'		=> THEME_PREFIX . '-phone',
				'section'		=> THEME_PREFIX . '-contact',
				'type'			=> 'text',
				'label'			=> __( 'Phone' ),
				'input_attrs'	=> array(
					'maxlength' => 16,
				),
			)
		);
		$customizer->add_control(
			THEME_PREFIX . '-email',
			array(
				'settings'		=> THEME_PREFIX . '-email',
				'section'		=> THEME_PREFIX . '-contact',
				'type'			=> 'email',
				'label'			=> __( 'Email' ),
			)
		);
		$customizer->add_control(
			THEME_PREFIX . '-address',
			array(
				'settings'		=> THEME_PREFIX . '-address',
				'section'		=> THEME_PREFIX . '-contact',
				'type'			=> 'textarea',
				'label'			=> __( 'Address' ),
			)
		);

		$customizer->remove_section( 'custom_css' );
		$customizer->remove_control( 'site_icon' );

		$customizer->get_setting(THEME_PREFIX . '-phone')->transport = 'postMessage';
		$customizer->get_setting(THEME_PREFIX . '-email')->transport = 'postMessage';
        $customizer->get_setting(THEME_PREFIX . '-address')->transport = 'postMessage';

	}

		function customize_preview_init() {

			wp_enqueue_script( 'customize-preview' );

            wp_add_inline_script(
				'customize-preview',
                'function nl2br (str, is_xhtml) {' .
                    'var breakTag = (is_xhtml || typeof is_xhtml === "undefined") ? "<br />" : "<br>";' .
                    'return (str + "").replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, "$1" + breakTag + "$2");' .
                '}' .
				'wp.customize( "' . THEME_PREFIX . '-phone", function( value ) { value.bind( function( to ) { jQuery(".option-phone").html( to ); jQuery(".option-phone-link").attr("href","tel:" + to); }); });' .
					'wp.customize( "' . THEME_PREFIX . '-email", function( value ) { value.bind( function( to ) { jQuery(".option-email").html( to ); }); });' .
                    'wp.customize( "' . THEME_PREFIX . '-address", function( value ) { value.bind( function( to ) { jQuery(".option-address").html( nl2br( to ) ); }); });'
			);

		}

}

?>
