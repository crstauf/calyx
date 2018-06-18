<?php
/**
 * Define dynamic styles helper.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Helper for dynamic styles.
 */
class Calyx_DynamicStyles {

	/** @var array $_styles Store styles until next hook to print. */
	protected $_styles = array();

	/**
	 * Construct.
	 */
	function __construct() {

		add_action( THEME_PREFIX . '/dynamic-styles/add', array( &$this, 'add' ), 10, 3 );

		add_action( 'wp_print_styles',         array( &$this, 'maybe_print_styles' ), 999 );
		add_action( 'wp_print_footer_scripts', array( &$this, 'maybe_print_styles' ), 999 );

	}

	/**
	 * Add dynamic style.
	 *
	 * @param string $handle Handle for the dynamic styles.
	 * @param string $styles Dynamic styles (including media query if needed).
	 * @param bool   $extra  Set dynamic styles as optional, default false.
	 *
	 * @uses Calyx::is_server_high_load()
	 */
	function add( $handle, $styles, $extra = false ) {
		if ( did_action( 'wp_print_footer_scripts' ) ) {
			_doing_it_wrong( __METHOD__, 'Dynamic styles can not be added after <code>wp_print_footer_scripts</code> hook.', '1.0' );
			return;
		}

		if (
			$extra
			&& Calyx()->is_server_high_load()
		)
			return;

		$this->_styles[$handle] = $styles;
	}

	/**
	 * Print dynamic styles.
	 *
	 * Hooked: wp_print_styles
	 * Hooked: wp_print_footer_scripts
	 */
	function maybe_print_styles() {
		if ( empty( $this->_styles ) )
			return;

		$styles = '<style type="text/css">' . "\n" . '/* BEGIN dynamic styles */';

			foreach ( $this->_styles as $handle => $styles )
				$styles .= '// ' . $handle . "\n" . $styles . "\n\n";

		$styles .= '/* END dynamic styles */' . "\n" . '</style>';

		echo $styles;

		$this->_styles = array();
	}

}

do_action( THEME_PREFIX . '/features/add', 'dynamic-styles', new Calyx_DynamicStyles );
?>
