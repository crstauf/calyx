<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Trait for singleton objects.
 */
trait Calyx_Singleton {

	public static function initialize() {
		static $_instance = null;

		if ( is_null( $_instance ) )
			$_instance = new static;
		else
			_doing_it_wrong( get_called_class() . '::' . __FUNCTION__ . '()', __( 'Singletons should be accessed via the theme\'s main handler class.' ), '1.0' );

		return $_instance;
	}

}

?>