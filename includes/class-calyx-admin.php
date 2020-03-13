<?php
/**
 * Actions, filters, and functions for backend.
 */

defined( 'ABSPATH' ) || die();

/**
 * Class: Calyx_Admin
 */
abstract class Calyx_Admin {

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
	 */
	protected function __construct() {

	}

}

add_action( THEME_PREFIX . '/init', function() {
	Calyx_Admin::init();
} );

?>
