<?php
/**
 * Helper for the WordPress Customizer.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Add fields, sections, and settings to the Customizer.
 */
class Calyx_Customizer {
	use Calyx_Singleton;

	/**
	 * @var array Storage for inline scripts.
	 */
	protected $_inline_scripts = array();

	/**
	 * Construct.
	 */
	function __construct() {

		add_action( 'customize_register',     array( &$this, 'action__customize_register'     ) );
		add_action( 'customize_preview_init', array( &$this, 'action__customize_preview_init' ) );

	}

	/**
	 * Manage settings and controls.
	 *
	 * @param WP_Customize_Manager $customizer
	 *
	 * @uses WP_Customize_Manager::add_setting()
	 * @uses WP_Customize_Manager::add_control()
	 * @uses WP_Customize_Manager::add_section()
	 * @uses WP_Customize_Manager::remove_section()
	 * @uses WP_Customize_Manager::remove_control()
	 * @uses Calyx_Customizer::set_transport()
	 */
	function action__customize_register( $customizer ) {
		$customizer->add_setting( 'copyright', array(
			'default'    => '',
			'type'       => 'theme_mod',
			'capability' => 'edit_theme_options',
		) );

		$customizer->add_control( 'copyright', array(
			'settings' => 'copyright',
			'section'  => 'title_tagline',
			'type'     => 'text',
			'label'    => __( 'Copyright' ),
		) );

		$customizer->add_section( THEME_PREFIX . '_contact_info', array(
			'title' => 'Contact Info',
		) );

		$customizer->add_setting( 'phone', array(
			'default' => '',
			'type' => 'theme_mod',
			'capability' => 'edit_theme_options',
		) );

		$customizer->add_control( 'phone', array(
			'settings' => 'phone',
			'section' => THEME_PREFIX . '_contact_info',
			'type' => 'tel',
			'label' => __( 'Phone' ),
		) );

		$customizer->remove_section( 'custom_css' );
		$customizer->remove_control( 'site_icon' );

		$this->set_transport( 'copyright', $customizer );
		$this->set_transport( 'phone',     $customizer );
	}

	/**
	 * Hook: customize_preview_init
	 *
	 * - enqueue 'cusotmize-preview' script
	 * - add inline scripts for updating fields
	 */
	function action__customize_preview_init() {
		wp_enqueue_script( 'customize-preview' );

		wp_add_inline_script(
			'customize-preview',
			implode( ' ', $this->_inline_scripts )
		);

	}

	/**
	 * Set field to update in Customizer preview.
	 *
	 * @param string               $mod_name   Name of the theme mod.
	 * @param WP_Customize_Manager $customizer By reference.
	 */
	protected function set_transport( $mod_name, &$customizer ) {
		$customizer->get_setting( $mod_name )->transport = 'postMessage';

		$this->_inline_scripts[] = 'wp.customize( "' . $mod_name . '", function( value ) {' .
			'value.bind( function( to ) {' .
				'jQuery( ".mod-' . $mod_name . '").html( window.calyx.nl2br( to ) );' .
			'} );' .
		'} );';
	}

	/**
	 * Print the specified theme mod.
	 *
	 * @param string $mod_name Theme mod name.
	 *
	 * @uses $this;:get_the_mod()
	 */
	function print_theme_mod( $mod_name ) {
		echo $this->get_the_mod( $mod_name );
	}

	/**
	 * Return the specified theme mod.
	 *
	 * If in Customizer preview, wrap it so text can be updated.
	 *
	 * @param string $mod_name Theme mod name.
	 */
	function get_theme_mod( $mod_name ) {
		return is_customize_preview()
			? '<span class="mod-' . $mod_name . '">' . get_theme_mod( $mod_name ) . '</span>'
			: get_theme_mod( $mod_name );
	}

}

?>