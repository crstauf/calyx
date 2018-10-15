<?php
/**
 * Helper for managing data.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Data management class.
 */
class Calyx_Data {
	use Calyx_Singleton;

	/**
	 * Construct.
	 */
	protected function __construct() {
		do_action( 'qm/start', __METHOD__ . '()' );

		do_action( 'qm/stop', __METHOD__ . '()' );
	}

	/**
	 * Add persistent cache data.
	 *
	 * @param string $key
	 * @param mixed  $data
	 * @param int    $life Data life in seconds.
	 *
	 * @uses $this::get_persistent_transient()
	 * @uses $this::set_persistent_transient()
	 */
	function add_persistent_transient( $key, $data, $life = 0 ) {
		if ( empty( $this->get_persistent_transient( $key ) ) )
			return;

		$this->set_persistent_transient( $key, $data, $life );
	}

	/**
	 * Set persistent cache data.
	 *
	 * @param string $key
	 * @param mixed  $data
	 * @param int    $life Data life in seconds.
	 */
	function set_persistent_transient( $key, $data, $life = 0 ) {
		if ( !wp_using_ext_object_cache() ) {
			set_transient( $key, $data, $life );
			return;
		}

		$expiration = !empty( $life )
			? time() + $life
			: 0;

		update_option( '_persistent_transient_' . $key, $data );
		!empty( $expiration ) && update_option( '_persistent_transient_timeout_' . $key, $expiration );

		wp_cache_set( $key, $data, 'transient/persistent', $expiration );
	}

	/**
	 * Get persistent cache data.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	function get_persistent_transient( $key ) {
		if ( !wp_using_ext_object_cache() )
			return get_transient( $key );

		$option  = get_option( '_persistent_transient_' . $key );
		$timeout = get_option( '_persistent_transient_timeout_' . $key );

		if (
			!empty( $timeout )
			&& time() > $timeout
		) {
			delete_option( '_persistent_transient_' . $key );
			delete_option( '_persistent_transient_timeout_' . $key );
			wp_cache_delete( $key, 'transient/persistent' );

			return false;
		}

		return $option;
	}

	/**
	 * Log data values (on event).
	 *
	 * @param string|int $message Log message.
	 * @param null|int   $init    Timestamp to evaluate against last log's timestamp.
	 */
	function log( $message, $init = null ) {
	}

}

?>
