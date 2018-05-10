<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Abstract class for HTTP helpers.
 */
abstract class _Calyx_HTTP {
	use Calyx_Singleton;

}

?>