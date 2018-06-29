<?php
/**
 * Trait definitions.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}


/*
 ######  #### ##    ##  ######   ##       ######## ########  #######  ##    ##
##    ##  ##  ###   ## ##    ##  ##       ##          ##    ##     ## ###   ##
##        ##  ####  ## ##        ##       ##          ##    ##     ## ####  ##
 ######   ##  ## ## ## ##   #### ##       ######      ##    ##     ## ## ## ##
      ##  ##  ##  #### ##    ##  ##       ##          ##    ##     ## ##  ####
##    ##  ##  ##   ### ##    ##  ##       ##          ##    ##     ## ##   ###
 ######  #### ##    ##  ######   ######## ########    ##     #######  ##    ##
*/

/**
 * Trait for singleton objects.
 */
trait Calyx_Singleton {

	/**
	 * Create instance.
	 */
	public static function create_instance() {
		static $_instance = null;

		if ( is_null( $_instance ) )
			$_instance = new static;
		else {
			_doing_it_wrong( get_called_class() . '::' . __FUNCTION__ . '()', __( 'Singletons should be accessed via the theme\'s main handler class.' ), '1.0' );
			return;
		}

		return $_instance;
	}

}


/*
##     ##    ###    ##    ##    ###     ######   ########  ##        ###    ########  ####  ######
###   ###   ## ##   ###   ##   ## ##   ##    ##  ##       ####      ## ##   ##     ##  ##  ##    ##
#### ####  ##   ##  ####  ##  ##   ##  ##        ##        ##      ##   ##  ##     ##  ##  ##
## ### ## ##     ## ## ## ## ##     ## ##   #### ######           ##     ## ########   ##   ######
##     ## ######### ##  #### ######### ##    ##  ##        ##     ######### ##         ##        ##
##     ## ##     ## ##   ### ##     ## ##    ##  ##       ####    ##     ## ##         ##  ##    ##
##     ## ##     ## ##    ## ##     ##  ######   ########  ##     ##     ## ##        ####  ######
*/

/**
 * Trait ot add API management.
 */
trait Calyx_ManageAPIs {

	/** @var null|array Array of API helpers. **/
	private $_apis = null;

	/**
	 * Check if API exists.
	 *
	 * @param string $api_id API ID.
	 *
	 * @return bool
	 */
	function has_api( $api_id ) {
		return array_key_exists( $api_id, $this->_apis );
	}

	/**
	 * Add API object.
	 *
	 * @param string      $api_id API ID.
	 * @param _Calyx_HTTP $object API object.
	 */
	function add_api( $api_id, _Calyx_HTTP $object ) {
		      !$this->has_api( $api_id ) && $this->_apis[$api_id] = $object;
		return $this->has_api( $api_id );
	}

	/**
	 * Get API object.
	 *
	 * @param string $api_id API ID.
	 *
	 * @uses Calyx_ManageAPIs::has_api()
	 *
	 * @return false|_Calyx_HTTP
	 */
	function get_api( $api_id ) {
		return $this->has_api( $api_id )
			? $this->_apis[$api_id]
			: null;
	}

}


/*
##     ##    ###    ##    ##    ###     ######   ########  ##     ######## ########    ###    ######## ##     ## ########  ########  ######
###   ###   ## ##   ###   ##   ## ##   ##    ##  ##       ####    ##       ##         ## ##      ##    ##     ## ##     ## ##       ##    ##
#### ####  ##   ##  ####  ##  ##   ##  ##        ##        ##     ##       ##        ##   ##     ##    ##     ## ##     ## ##       ##
## ### ## ##     ## ## ## ## ##     ## ##   #### ######           ######   ######   ##     ##    ##    ##     ## ########  ######    ######
##     ## ######### ##  #### ######### ##    ##  ##        ##     ##       ##       #########    ##    ##     ## ##   ##   ##             ##
##     ## ##     ## ##   ### ##     ## ##    ##  ##       ####    ##       ##       ##     ##    ##    ##     ## ##    ##  ##       ##    ##
##     ## ##     ## ##    ## ##     ##  ######   ########  ##     ##       ######## ##     ##    ##     #######  ##     ## ########  ######
*/

/**
 * Trait to add features management.
 */
trait Calyx_ManageFeatures {

	/** @var null|array Array of features. **/
	private $_features = array();

	/**
	 * Check if feature exists.
	 *
	 * @param string $name Feature name.
	 *
	 * @return bool
	 */
	 function has_feature( $name ) {
		return array_key_exists( $name, $this->_features );
	}

	/**
	 * Add feature.
	 *
	 * @param string $name    Feature name.
	 * @param object $feature Feature object.
	 *
	 * @return bool Feature exists or was registered.
	 */
	function add_feature( $name, $feature ) {
		if ( did_action( THEME_PREFIX . '/before_init' ) ) {
			      !$this->has_feature( $name ) && $this->_features[$name] = $feature;
			return $this->has_feature( $name );
		} else
			_doing_it_wrong( __METHOD__ . '()', 'Features should not be added before \'' . THEME_PREFIX . '/before_init\' action.', '1.0' );

		return false;
	}

	/**
	 * Get feature object.
	 *
	 * @param string $name Feature name.
	 *
	 * @uses Calyx_ManageFeatures::has_feature()
	 *
	 * @return false|object
	 */
	function get_feature( $name ) {
		return $this->has_feature( $name )
			? $this->_features[$name]
			: false;
	}

}

?>