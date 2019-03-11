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

		add_action( 'init', array( &$this, 'server__low_traffic_hours__schedule' ) );
	}

	function server__low_traffic_hours__schedule() {
		$next_begin_occurrence = as_next_scheduled_action( THEME_PREFIX . '/low-traffic/begin', null, THEME_PREFIX );
		  $next_end_occurrence = as_next_scheduled_action( THEME_PREFIX . '/low-traffic/end',   null, THEME_PREFIX );

		if (
			 $next_begin_occurrence
			&& $next_end_occurrence
		)
			return;

		$now      = as_get_datetime_object()->format( 'U' );
		$today    = as_get_datetime_object( 'today'    )->format( 'U' );
		$tomorrow = as_get_datetime_object( 'tomorrow' )->format( 'U' );

		# Schedule beginning action of low-traffic hours.
		if ( !$next_begin_occurrence )
			$next_begin_occurrence = $this->_server__low_traffic_hours__schedule_begin();

		# Schedule ending action of low-traffic hours.
		if (
			    !$next_end_occurrence
			&& $next_begin_occurrence
		)
			$this->_server__low_traffic_hours__schedule_end( $next_begin_occurrence, $today, $now, $tomorrow );
	}

	protected function _server__low_traffic_hours__schedule_begin( $today = null, $now = null, $tomorrow = null ) {
		is_null( $now      ) && $now      = as_get_datetime_object()->format( 'U' );
		is_null( $today    ) && $today    = as_get_datetime_object( 'today'    )->format( 'U' );
		is_null( $tomorrow ) && $tomorrow = as_get_datetime_object( 'tomorrow' )->format( 'U' );

		$begin = Calyx()->server()->get_low_traffic_hours_begin();
		$todays_occurrence = $today + $begin;

		if ( $todays_occurrence > $now )
			$next_begin_occurrence = $todays_occurrence;

		if ( empty( $next_occurrence ) )
			$next_begin_occurrence = $tomorrow + $begin;

		as_schedule_single_action( $next_begin_occurrence, THEME_PREFIX . '/low-traffic/begin', array(), THEME_PREFIX );
	}

	protected function _server__low_traffic_hours__schedule_end( $today = null, $tomorrow = null ) {
		$next_begin_occurrence = as_next_scheduled_action( THEME_PREFIX . '/low-traffic/begin', null, THEME_PREFIX );

		is_null( $today    ) && $today    = as_get_datetime_object( 'today'    )->format( 'U' );
		is_null( $tomorrow ) && $tomorrow = as_get_datetime_object( 'tomorrow' )->format( 'U' );

		$end = Calyx()->server()->get_low_traffic_hours_end();
		$todays_occurrence = $today + $end;

		if ( $todays_occurrence > $next_begin_occurrence )
			$next_end_occurrence = $todays_occurrence;

		if ( empty( $next_end_occurrence ) )
			$next_end_occurrence = $tomorrow + $end;
;
		as_schedule_single_action( $next_end_occurrence, THEME_PREFIX . '/low-traffic/end', array(), THEME_PREFIX );
	}

	function server__low_traffic_hours__begin() {
		update_option( THEME_PREFIX . '/server/low-traffic/within-hours', true );

		if ( !as_next_scheduled_action( THEME_PREFIX . '/low-traffic/begin', array(), THEME_PREFIX ) )
			$this->_server__low_traffic_hours__schedule_begin();
	}

	function server__low_traffic_hours__end() {
		delete_option( THEME_PREFIX . '/server/low-traffic/within-hours' );

		if ( !as_next_scheduled_action( THEME_PREFIX . '/low-traffic/end', array(), THEME_PREFIX ) )
			$this->_server__low_traffic_hours__schedule_end();
	}

}

if ( did_action( 'action_scheduler_pre_init' ) )
	do_action( THEME_PREFIX . '/features/add', 'action-scheduler', array( 'Calyx_ActionScheduler', 'create_instance' ) );

?>
