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
	* Set long life transient data.
	*
	* @param string $transient  Transient name.
	* @param mixed  $value      Transient value.
	* @param int    $expiration Transient expiration.
	*/
	function set_long_life_transient( $transient, $value, $expiration ) {
		if ( !wp_using_ext_object_cache() )
			return set_transient( $transient, $value, $expiration );

		return (
			   update_option( '_longlife_' . $transient, $value )
			&& update_option( '_longlife_timeout_' . $transient, time() + $expiration )
		);
	}

	/**
	 * Get long life transient data.
	 *
	 * @param string $transient Transient name.
	 *
	 * @return bool|mixed
	 */
	function get_long_life_transient( $transient ) {
		if ( !wp_using_ext_object_cache() )
			return get_transient( $transient );

		$option = get_option( '_longlife_' . $transient );
		$timeout = get_option( '_longlife_timeout_' . $transient );

		if (
			false !== $timeout
			&& time() > $option['expiration']
		) {
			$this->delete_long_life_transient( $transient );
			return false;
		}

		return $option;
	}

	/**
	 * Delete long life transient.
	 *
	 * @param string $transient Transient name.
	 */
	function delete_long_life_transient( $transient ) {
		if ( !wp_using_ext_object_cache() )
			return delete_transient( $tranient );

		delete_option( '_longlife_timeout_' . $transient );
		delete_option( '_longlife_' . $transient );
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
