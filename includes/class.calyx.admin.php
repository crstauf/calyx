<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Actions, filters, and functions for admin.
 */
class Calyx_Admin extends _Calyx_Core {

	protected function __construct() {
		parent::__construct();

	}

}

?>