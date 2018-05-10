<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Actions, filters, and functions for frontend.
 */
class Calyx_Front extends _Calyx_Core {

	protected function __construct() {
		parent::__construct();


	}

}

?>