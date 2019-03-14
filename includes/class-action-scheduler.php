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

		add_action( 'init', array( $this, 'action__init' ) );

	}

	function action__init() {
		if ( Calyx()->server()->has_valid_low_traffic_hours() )
			$this->server__low_traffic_hours();
	}


	/*
	##        #######  ##      ##         ######## ########     ###    ######## ######## ####  ######     ##     ##  #######  ##     ## ########   ######
	##       ##     ## ##  ##  ##            ##    ##     ##   ## ##   ##       ##        ##  ##    ##    ##     ## ##     ## ##     ## ##     ## ##    ##
	##       ##     ## ##  ##  ##            ##    ##     ##  ##   ##  ##       ##        ##  ##          ##     ## ##     ## ##     ## ##     ## ##
	##       ##     ## ##  ##  ## #######    ##    ########  ##     ## ######   ######    ##  ##          ######### ##     ## ##     ## ########   ######
	##       ##     ## ##  ##  ##            ##    ##   ##   ######### ##       ##        ##  ##          ##     ## ##     ## ##     ## ##   ##         ##
	##       ##     ## ##  ##  ##            ##    ##    ##  ##     ## ##       ##        ##  ##    ##    ##     ## ##     ## ##     ## ##    ##  ##    ##
	########  #######   ###  ###             ##    ##     ## ##     ## ##       ##       ####  ######     ##     ##  #######   #######  ##     ##  ######
	*/

	function server__low_traffic_hours() {
		add_action( THEME_PREFIX . '/low-traffic/begin', array( &$this, 'server__low_traffic_hours__begin' ) );
		add_action( THEME_PREFIX . '/low-traffic/end',   array( &$this, 'server__low_traffic_hours__end'   ) );

		$this->server__low_traffic_hours__schedule();
	}

	function server__low_traffic_hours__schedule() {
		$next_begin_occurrence = as_next_scheduled_action( THEME_PREFIX . '/server/low-traffic/begin', null, THEME_PREFIX );
		  $next_end_occurrence = as_next_scheduled_action( THEME_PREFIX . '/server/low-traffic/end',   null, THEME_PREFIX );

		if (
			 $next_begin_occurrence
			&& $next_end_occurrence
		)
			return;

		# Schedule beginning action of low-traffic hours.
		if ( !$next_begin_occurrence )
			$next_begin_occurrence = $this->_server__low_traffic_hours__schedule_begin();

		# Schedule ending action of low-traffic hours.
		if (
			    !$next_end_occurrence
			&& $next_begin_occurrence
		)
			$this->_server__low_traffic_hours__schedule_end();
	}

	protected function _server__low_traffic_hours__schedule_begin() {
		$seconds = Calyx()->server()->get_low_traffic_hours_begin();
		$hours = floor( $seconds / HOUR_IN_SECONDS );
		$minutes = ( $seconds - ( $hours * HOUR_IN_SECONDS ) ) / MINUTE_IN_SECONDS;

		$schedule = sprintf( '%d %d * * * *', $minutes, $hours );

		as_schedule_cron_action( time(), $schedule, THEME_PREFIX . '/server/low-traffic/begin', array(), THEME_PREFIX );
	}

	protected function _server__low_traffic_hours__schedule_end() {
		$seconds = Calyx()->server()->get_low_traffic_hours_end();
		$hours = floor( $seconds / HOUR_IN_SECONDS );
		$minutes = ( $seconds - ( $hours * HOUR_IN_SECONDS ) ) / MINUTE_IN_SECONDS;

		$schedule = sprintf( '%d %d * * * *', $minutes, $hours );

		as_schedule_cron_action( time(), $schedule, THEME_PREFIX . '/server/low-traffic/end', array(), THEME_PREFIX );
	}

	function server__low_traffic_hours__begin() {
		update_option( THEME_PREFIX . '/server/low-traffic/within-hours', true );
	}

	function server__low_traffic_hours__end() {
		delete_option( THEME_PREFIX . '/server/low-traffic/within-hours' );
	}

}

if ( did_action( 'action_scheduler_pre_init' ) )
	do_action( THEME_PREFIX . '/features/add', 'action-scheduler', array( 'Calyx_ActionScheduler', 'create_instance' ) );

?>
