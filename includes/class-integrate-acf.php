<?php
/**
 * Integrations for Advanced Custom Fields.
 */

defined( 'ABSPATH' ) || die();

class Calyx_ACF {

	/**
	 * @var null|self
	 */
	protected static $_instance = null;

	/**
	 * Get instance.
	 *
	 * @return self
	 */
	static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self;

		return self::$_instance;
	}

	/**
	 * Initialize.
	 *
	 * @uses static::instance()
	 */
	static function init() {
		static::instance();
	}

	/**
	 * Construct.
	 *
	 * - register hooks
	 */
	protected function __construct() {

		add_filter( 'acf/settings/save_json', array( $this, 'filter__acf_settings_save_json' ) );
		add_filter( 'acf/settings/load_json', array( $this, 'filter__acf_settings_load_json' ) );

	}

	/**
	 * Path to ACF export directory.
	 *
	 * @return string
	 */
	protected static function directory() {
		return THEME_ABSPATH . 'assets/acf';
	}

	/**
	 * Filter: acf/settings/save_json
	 *
	 * - specify directory to save ACF JSON to
	 *
	 * @link https://www.advancedcustomfields.com/resources/local-json/ Documentation.
	 * @param string $path
	 * @uses static::directory()
	 * @return string
	 */
	function filter__acf_settings_save_json( $path = '' ) {
		return static::directory();
	}

	/**
	 * Filter: acf/settings/load_json
	 *
	 * - specify directories to look for ACF JSON
	 *
	 * @param array $paths
	 * @uses static::directory()
	 * @return array
	 */
	function filter__acf_settings_load_json( $paths ) {
		return array( static::directory() );
	}

}

add_action( THEME_PREFIX . '/init', function() {
	Calyx_ACF::init();
} );

?>
