<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Main theme class.
 */
class Calyx {

	private $_apis = null;
	private $_cpts = null;
	private $_data = null;
	private $_admin = null;
	private $_front = null;
	private $_actions = null;
	private $_filters = null;
	private $_utilities = null;
	private $_woocommerce = null;

	private $_features = array();

	public static function get_instance() {
		static $_instance = null;

		if ( is_null( $_instance ) )
			$_instance = new self();

		return $_instance;
	}

	protected function __construct() {
		do_action( 'qm/start', THEME_PREFIX . ':init' );

		$this->_data      = Calyx_Data::initialize();
		$this->_actions   = Calyx_Actions::initialize();
		$this->_filters   = Calyx_Filters::initialize();
		$this->_utilities = Calyx_Utilities::initialize();

		class_exists( 'Calyx_WooCommerce' ) && $this->_woocommerce = Calyx_WooCommerce::initialize();

		class_exists( 'Calyx_Admin' ) && $this->_admin = Calyx_Admin::initialize();
		class_exists( 'Calyx_Front' ) && $this->_front = Calyx_Front::initialize();

		do_action( 'init_theme_' . THEME_PREFIX );

		add_action( THEME_PREFIX . '/add/api',     array( &$this, 'add_api'     ), 10, 2 );
		add_action( THEME_PREFIX . '/add/cpt',     array( &$this, 'add_cpt'     ) );
		add_action( THEME_PREFIX . '/add/feature', array( &$this, 'add_feature' ), 10, 2 );

		do_action( 'after_init_theme_' . THEME_PREFIX );

		do_action( 'qm/stop', THEME_PREFIX . ':init' );

	}

	function admin()    { return $this->_admin;       }
	function front()    { return $this->_front;       }
	function actions()  { return $this->_actions;     }
	function filters()  { return $this->_filters;     }
	function utils()    { return $this->_utilities;   }
	function wc()       { return $this->_woocommerce; }

	/**
	 * Manage theme data.
	 *
	 * @uses Calyx_Data::__data()
	 *
	 * @return mixed
	 */
	function data( string $key = null, $value = null, bool $force = false ) {
		return $this->_data->__data( $key, $value, $force );
	}


	/*
	######## ########    ###    ######## ##     ## ########  ########  ######
	##       ##         ## ##      ##    ##     ## ##     ## ##       ##    ##
	##       ##        ##   ##     ##    ##     ## ##     ## ##       ##
	######   ######   ##     ##    ##    ##     ## ########  ######    ######
	##       ##       #########    ##    ##     ## ##   ##   ##             ##
	##       ##       ##     ##    ##    ##     ## ##    ##  ##       ##    ##
	##       ######## ##     ##    ##     #######  ##     ## ########  ######
	*/

	/**
	 * Check if feature exists.
	 *
	 * @param string $name Feature name.
	 *
	 * @return bool
	 */
	 function has_feature( string $name ) {
		return array_key_exists( $name, $this->_features );
	}

	/**
	 * Add theme feature.
	 *
	 * @param string $name    Feature name.
	 * @param object $feature Feature object.
	 *
	 * @return bool Theme feature exists or was registered.
	 */
	function add_feature( string $name, object $feature ) {
		if (
			 doing_action( 'setup_theme_' . THEME_PREFIX )
			|| did_action( 'setup_theme_' . THEME_PREFIX )
		) {
			      !$this->has_feature( $name ) && $this->_features[$name] = $feature;
			return $this->has_feature( $name );
		} else
			_doing_it_wrong( __METHOD__ . '()', 'Features should not be added before \'setup_theme_' . THEME_PREFIX . '\' action.', '1.0' );

		return false;
	}

	/**
	 * Get theme feature.
	 *
	 * @param string $name Feature name.
	 *
	 * @uses $this::has_feature()
	 *
	 * @return false|object
	 */
	function get_feature( string $name ) {
		return $this->has_feature( $name )
			? $this->_features[$name]
			: false;
	}

		/**
		 * Wrapper for get_feature().
		 *
		 * @param string $name Feature name.
		 *
		 * @uses $this::get_feature()
		 */
		function get( string $name ) {
			return $this->get_feature( $name );
		}


	/*
	 ######  ########  ########  ######
	##    ## ##     ##    ##    ##    ##
	##       ##     ##    ##    ##
	##       ########     ##     ######
	##       ##           ##          ##
	##    ## ##           ##    ##    ##
	 ######  ##           ##     ######
	*/

	/**
	 * Check if theme CPT exists.
	 *
	 * @param string $type Post type.
	 *
	 * @return bool
	 */
	function has_cpt( string $type ) {
		return array_key_exists( $type, $this->_cpts );
	}

	/**
	 * Add theme CPT.
	 *
	 * @param _Calyx_CPT $object Post type object.
	 *
	 * @return bool Theme CPT exists or was registered.
	 */
	function add_cpt( _Calyx_CPT $object ) {
		      !$this->has_cpt( $object->get_type() ) && $this->_cpts[$object->get_type()] = $object;
		return $this->has_cpt( $object->get_type() );
	}

	/**
	 * Check if theme CPT exists.
	 *
	 * @param string $type Post type.
	 *
	 * @uses $this::has_cpt()
	 *
	 * @return null|_Calyx_Cpt
	 */
	function get_cpt( string $type ) {
		return $this->has_cpt( $type )
			? $this->_cpts[$type]
			: null;
	}


	/*
	   ###    ########  ####  ######
	  ## ##   ##     ##  ##  ##    ##
	 ##   ##  ##     ##  ##  ##
	##     ## ########   ##   ######
	######### ##         ##        ##
	##     ## ##         ##  ##    ##
	##     ## ##        ####  ######
	*/

	function has_api( $api_id ) {
		return array_key_exists( $api_id, $this->_apis );
	}

	function add_api( $api_id, _Calyx_HTTP $object ) {
		      !$this->has_api( $api_id ) && $this->_apis[$api_id] = $object;
		return $this->has_api( $api_id );
	}

	function get_api( $api_id ) {
		return $this->has_api( $api_id )
			? $this->_apis[$api_id]
			: null;
	}


	/*
	 ######  ######## ########  ##     ## ######## ########
	##    ## ##       ##     ## ##     ## ##       ##     ##
	##       ##       ##     ## ##     ## ##       ##     ##
	 ######  ######   ########  ##     ## ######   ########
	      ## ##       ##   ##    ##   ##  ##       ##   ##
	##    ## ##       ##    ##    ## ##   ##       ##    ##
	 ######  ######## ##     ##    ###    ######## ##     ##
	*/

	/**
	 * Get server info.
	 *
	 * 1 => Apache|nginx
	 * 2 => version
	 *
	 * @return array
	 */
	function get_server_info() {
		preg_match( '/^(nginx|Apache)\/([0-9\.]*).*$/', $_SERVER['SERVER_SOFTWARE'], $matches );
		return $matches;
	}

	/**
	 * Retrieve indication of server under high load.
	 *
	 * @uses $this::is_extreme_load()
	 */
	function is_server_high_load() {
		return (
			(
				       defined( THEME_PREFIX . '_HIGH_LOAD' )
				   && constant( THEME_PREFIX . '_HIGH_LOAD' )
			)
			|| !!get_transient( THEME_PREFIX . '_HIGH_LOAD' )
			|| $this->is_server_extreme_load()
		);
	}

	/**
	 * Retrieve indication of server under extreme load.
	 */
	 function is_server_extreme_load() {
		 return (
			(
				       defined( THEME_PREFIX . '_EXTREME_LOAD' )
				   && constant( THEME_PREFIX . '_EXTREME_LOAD' )
			)
			|| !!get_transient( THEME_PREFIX . '_EXTREME_LOAD' )
		);
	}

	function is_woocommerce_active() {
		return is_plugin_active( 'woocommerce/woocommerce.php' );
	}

		function is_wc_active() {
			return $this->is_woocommerce_active();
		}


	/*
	######## ##     ## ######## ##    ## ########  ######
	##       ##     ## ##       ###   ##    ##    ##    ##
	##       ##     ## ##       ####  ##    ##    ##
	######   ##     ## ######   ## ## ##    ##     ######
	##        ##   ##  ##       ##  ####    ##          ##
	##         ## ##   ##       ##   ###    ##    ##    ##
	########    ###    ######## ##    ##    ##     ######
	*/

	/**
	 * Check if doing AJAX.
	 */
	function doing_ajax() {
		return !!(
			wp_doing_ajax()
			|| (
				defined( 'WC_DOING_AJAX' )
				       && WC_DOING_AJAX
			)
			|| (
				isset( $_GET )
				&& array_key_exists( 'wc-ajax', $_GET )
			)
		);
	}

	/** Check if doing cron. */
	function doing_cron()     { return !!( defined( 'DOING_CRON'     ) && DOING_CRON     ); }

	/** Check if doing autosave. */
	function doing_autosave() { return !!( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ); }

	/** Check if Query Monitor disabled. */
	function QM_disabled()    { return !!( defined( 'QM_DISABLED'    ) && QM_DISABLED    ); }

	/**
	 * Check if is REST request.
	 */
	 function doing_rest() {
		return !!(
			(
				defined( 'REST_REQUEST' )
				       && REST_REQUEST
			)
			|| (
				defined( 'WC_API_REQUEST' )
				       && WC_API_REQUEST
			)
		);
	}

}

function Calyx() {
	return Calyx::get_instance();
}

?>
