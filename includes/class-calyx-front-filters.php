<?php
/**
 * Class to manage frontend filter.
 */

 if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
 	header( 'Status: 403 Forbidden' );
 	header( 'HTTP/1.1 403 Forbidden' );
 	exit;
 }

/**
 * Filters for front-end.
 */
class Calyx_Front_Filters {
	use Calyx_Singleton;

	protected function __construct() {
		do_action( 'qm/start', __METHOD__ . '()' );

		do_action( 'qm/stop', __METHOD__ . '()' );
	}

}

?>