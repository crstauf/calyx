<?php
/**
 * Calyx theme handler.
 */

defined( 'ABSPATH' ) || die();

/**
 * Class: Calyx
 */
class Calyx {

	/**
	 * @var null|self
	 */
	protected static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return self
	 */
	static function instance() {
		if ( is_null( static::$instance ) )
			static::$instance = new self;

		return static::$instance;
	}

	/**
	 * Construct.
	 */
	function __construct() {

		add_action( 'init',               array( $this, 'action__init' ) );
		add_action( 'pre_ping',           array( $this, 'action__pre_ping' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'action__wp_enqueue_scripts' ) );

		add_filter( 'http_request_args',      array( $this, 'filter__http_request_args'      ), 10, 2 );
		add_filter( 'acf/settings/save_json', array( $this, 'filter__acf_settings_save_json' ) );
		add_filter( 'acf/settings/load_json', array( $this, 'filter__acf_settings_load_json' ) );

	}


	/*
	   ###     ######  ######## ####  #######  ##    ##  ######
	  ## ##   ##    ##    ##     ##  ##     ## ###   ## ##    ##
	 ##   ##  ##          ##     ##  ##     ## ####  ## ##
	##     ## ##          ##     ##  ##     ## ## ## ##  ######
	######### ##          ##     ##  ##     ## ##  ####       ##
	##     ## ##    ##    ##     ##  ##     ## ##   ### ##    ##
	##     ##  ######     ##    ####  #######  ##    ##  ######
	*/

	/**
	 * Action: init
	 *
	 * - include file to register assets
	 */
	function action__init() {
		require_once CALYX_ABSPATH . 'assets/index.php';
	}

	/**
	 * Action: pre_ping
	 *
	 * Don't ping yourself.
	 *
	 * @param array &$links
	 */
	function action__pre_ping( &$links ) {
		foreach ( $links as $i => $link )
			if ( 0 === strpos( $link, home_url() ) )
				unset( $links[$i] );
	}

	/**
	 * Action: wp_enqueue_scripts
	 *
	 * - remove built-in emoji styles
	 */
	function action__wp_enqueue_scripts() {
		remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles',     'print_emoji_styles' );
		remove_action( 'admin_print_styles',  'print_emoji_styles' );
	}


	/*
	######## #### ##       ######## ######## ########   ######
	##        ##  ##          ##    ##       ##     ## ##    ##
	##        ##  ##          ##    ##       ##     ## ##
	######    ##  ##          ##    ######   ########   ######
	##        ##  ##          ##    ##       ##   ##         ##
	##        ##  ##          ##    ##       ##    ##  ##    ##
	##       #### ########    ##    ######## ##     ##  ######
	*/

	/**
	 * Filter: http_request_args
	 *
	 * - prevent update checks for theme
	 *
	 * @param array  $args
	 * @param string $url
	 * @return array
	 */
	function filter__http_request_args( $args, $url ) {
		if ( false !== strpos( $url, 'http://api.wordpress.org/themes/update-check' ) )
			return $args; // Not a theme update request. Bail immediately.

		if (
			is_array( $args )
			&& count( $args )
			&& array_key_exists( 'themes', $args )
			&& is_array( $args['themes'] )
			&& count( $args['themes'] )
			&& array_key_exists( 'themes', $args['body'] )
		) {
			$args['body']['themes'] = json_decode( $args['body']['themes'] );
			list( $template, $stylesheet ) = array( get_option( 'template' ), get_option( 'stylesheet' ) );
			unset( $args['body']['themes']->themes->$template, $args['body']['themes']->themes->$stylesheet );
			$args['body']['themes'] = json_encode( $args['body']['themes'] );
		}

		return $args;
	}

}
