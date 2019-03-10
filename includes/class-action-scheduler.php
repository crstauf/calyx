<?php
/**
 * Helper for integration with Action Scheduler.
 * @link https://github.com/Prospress/action-scheduler Action Scheduler repository.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Class.
 */
class Calyx_ActionScheduler {
	use Calyx_Singleton;

	/**
	 * Construct.
	 */
	function __construct() {

	}

}

if ( did_action( 'action_scheduler_pre_init' ) )
	do_action( THEME_PREFIX . '/features/add', 'action-scheduler', array( 'Calyx_ActionScheduler', 'create_instance' ) );

?>
