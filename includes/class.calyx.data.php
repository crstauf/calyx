<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Data management class.
 */
class Calyx_Data extends _Calyx_Core {

	/**
	 * @var $_storeroom Array of stored data.
	 */
	private $_storeroom = array();

	/**
	 * Generate cache key.
	 *
	 * @param mixed  $unhashed_key Unhashed key data for cache value.
	 * @param string $prefix       A string to label the data's use or source.
	 *
	 * @return string md5 hash of JSON encoded key data.
	 */
	function get_key( $unhashed_key, $prefix = null ) {
		empty( $prefix ) || $prefix .= '_';
		return md5( $prefix . json_encode( $unhashed_key ) );
	}

	/**
	 * Check if cache key exists.
	 *
	 * @param string $key Hashed data identifier.
	 *
	 * @return bool
	 */
	function exists( string $key ) {
		return array_key_exists( $key, $this->_storeroom );
	}

	/**
	 * Get stored data.
	 *
	 * @param string|int $key Hashed data identifier.
	 *
	 * @return mixed|null Stored data value.
	 */
	function get( string $key ) {
		return $this->exists( $key )
			? $this->_storeroom[$key]
			: null;
	}

	function add( string $key, $value ) {
		!$this->exists( $key ) && $this->_storeroom[$key] = $value;
		return $this->exists( $key );
	}

	/**
	 * Add or update data.
	 *
	 * @param string|int $key   Hashed data identifier.
	 * @param mixed      $value Data value.
	 *
	 * @return mixed Data value.
	 */
	function set( string $key, $value ) {
		return $this->_storeroom[$key] = $value;
	}

	/**
	 * Purge stored data.
	 *
	 * @param string $key Hashed data identifier.
	 */
	function purge( string $key ) {
		unset( $this->_storeroom[$key] );
	}

	/**
	 * Purge all stored data.
	 */
	function purge_all() {
		$this->_storeroom = array();
		do_action( THEME_PREFIX . '/data/purged' );
	}

	/**
	 * Wrapper for getting and setting of data.
	 *
	 * @param mixed      $key   Hashed data identifier, or data to generate key.
	 * @param null|mixed $value Data value.
	 * @param bool       $force Force set data value, default false.
	 *
	 * @uses $this::get()
	 * @uses $this::set()
	 *
	 * @return mixed (Stored) data value.
	 */
	function __data( $key, $value = null, bool $force = false ) {
		if ( !is_string( $key ) )
			return $this->get_key( $key, $value );

		else if ( is_null( $value ) )
			return $this->get( $key );

		if ( true === $force )
			return $this->set( $key, $value );

		return $this->has( $key )
			 ? $this->get( $key )
			 : $this->set( $key, $value );
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