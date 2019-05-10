<?php
/**
 * Helper for low-traffic server times.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Class.
 */
class Calyx_Server_Low_Traffic {
	use Calyx_Singleton;

	/** @var string Timestamp of start of low-traffic hours. Use 1970-01-01 00:00 as the starting point. */
	const START = '1970-01-01 2:00';

	/** @var string Timestamp of end of low-traffic hours. Use 1970-01-01 00:00 as the starting point. */
	const END = '1970-01-01 6:00';

	/**
	 * Construct.
	 */
	protected function __construct() {

		add_action( 'init', array( &$this, 'action__init' ) );
		add_action( THEME_PREFIX . '/server/low-traffic/start', array( &$this, 'action__start' ) );
		add_action( THEME_PREFIX . '/server/low-traffic/end',   array( &$this, 'action__end'   ) );

	}

	/**
	 * Action: init
	 *
	 * - schedule Action Scheduler tasks if not schedule
	 */
	function action__init() {
		if ( did_action( 'action_scheduler_pre_init' ) )
			$this->maybe_schedule_as_actions();
	}

	/**
	 * Action: THEME_PREFIX/low-traffic/start
	 */
	function action__start() {
		error_log( __METHOD__ );
		update_option( THEME_PREFIX . '/server/low-traffic/active', true );
	}

	/**
	 * Action: THEME_PREFIX/low-traffic/end
	 */
	function action__end() {
		delete_option( THEME_PREFIX . '/server/low-traffic/active' );
	}

	/**
	 * Get start time of low-traffic hours.
	 * @return int
	 */
	function get_start_datetime() {
		static $_cache = null;

		if ( !is_null( $_cache ) )
			return $_cache;

		return $_cache = new DateTime( apply_filters( THEME_PREFIX . '/server/low-traffic/time/start', $this::START ) );
	}

	/**
	 * Get number of seconds from midnight of first day for start of low-traffic.
	 * @return int
	 */
	function get_start_seconds() {
		return ( int ) $this->get_start_datetime()->getTimestamp();
	}

	/**
	 * Get ending time of low-traffic hours.
	 * @return DateTime
	 */
	function get_end_datetime() {
		static $_cache = null;

		if ( !is_null( $_cache ) )
			return $_cache;

		return $_cache = new DateTime( apply_filters( THEME_PREFIX . '/server/low-traffic/time/end', $this::END ) );
	}

	/**
	 * Get number of seconds from midnight of first day for end of low-traffic.
	 * @return int
	 */
	function get_end_seconds() {
		return ( int ) $this->get_end_datetime()->getTimestamp();
	}

	/**
	 * Check if valid low-traffic hours are set.
	 *
	 * @uses $this::get_low_traffic_hours_begin()
	 * @uses $this::get_low_traffic_hours_end()
	 * @return bool
	 */
	function has_valid_hours() {
		$start = $this->get_start_seconds();
		  $end = $this->get_end_seconds();

		return (
			   !empty( $start )
			&& !empty( $end   )
			&& ( $end - $start ) >= 0
		);
	}

	/**
	 * Check if in low-traffic hours.
	 *
	 * @uses $this::has_valid_low_traffic_hours()
	 * @return bool
	 * @todo Test.
	 */
	function is_active() {
		if ( has_filter( THEME_PREFIX . '/server/low-traffic/is-active' ) )
			return apply_filters( THEME_PREFIX . '/server/low-traffic/is-active', null );

		if ( get_option( THEME_PREFIX . '/server/low-traffic/is-active', false ) )
			return true;

		if ( !$this->has_valid_hours() )
			return false;

		$today = new DateTime( 'today', new DateTimezone( 'UTC' ) );
		$start = $today->format( 'U' ) + $this->get_start_seconds();
		 $diff = $this->get_end_seconds() - $this->get_start_seconds();
		  $end = $start + $diff;

		return (
			   time() >= $start
			&& time() <= $end
		);
	}

	/**
	 * Schedule actions (via Action Scheduler) to start and end low-traffic hours.
	 *
	 * @uses as_next_scheduled_action()
	 * @uses as_schedule_cron_action()
	 */
	function maybe_schedule_as_actions() {
		$scheduled = array();

		foreach ( array( 'start', 'end' ) as $point ) {
			$scheduled[$point] = as_next_scheduled_action( THEME_PREFIX . '/server/low-traffic/' . $point, array(), THEME_PREFIX );

			if ( $scheduled[$point] )
				continue;

			$func = 'get_' . $point . '_datetime';

			list( $hours, $minutes ) = explode( ':', $this->$func()->format( 'H:i' ) );
			$schedule = sprintf( '%d %d * * * *', $minutes, $hours );

			$time = time();

			if ( 'end' === $point )
				$time = $scheduled['start'];

			as_schedule_cron_action( $time, $schedule, THEME_PREFIX . '/server/low-traffic/' . $point, array(), THEME_PREFIX );

			if ( empty( $scheduled[$point] ) )
				$scheduled[$point] = as_next_scheduled_action( THEME_PREFIX . '/server/low-traffic/' . $point, array(), THEME_PREFIX );;
		}
	}

}
