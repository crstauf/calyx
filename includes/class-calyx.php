<?php
/**
 * Theme manager.
 *
 * @package calyx
 */

defined( 'ABSPATH' ) || die();

/**
 * Class: Calyx
 */
class Calyx {

	/**
	 * Get singleton instance.
	 *
	 * @return self
	 */
	static function instance() : self {
		static $instance = null;

		if ( is_null( $instance ) )
			$instance = new self;

		return $instance;
	}

	/**
	 * Construct.
	 */
	protected function __construct() {

		add_action( 'init', array( $this, 'action__init' ) );
		add_action( 'wp_default_scripts', array( $this, 'action__wp_default_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'action__wp_enqueue_scripts' ) );

		add_filter( 'http_request_args', array( $this, 'filter__http_request_args' ), 10, 2 );

	}

	/**
	 * Action: init
	 *
	 * - register assets
	 */
	function action__init() : void {
		require_once THEME_ABSPATH . 'assets/register.php';
	}
	
	/**
	 * Action: wp_default_scripts
	 *
	 * Don't include jQuery Migrate.
	 *
	 * @param \WP_Scripts $scripts
	 * @return void
	 */
	function action__wp_default_scripts( \WP_Scripts $scripts ) : void {
		if (
			is_admin()
			|| !isset( $scripts->registered['jquery'] )
		)
			return;

		$key = array_search( 'jquery-migrate', $scripts->registered['jquery']->deps );

		if ( empty( $key ) )
			return;

		unset( $scripts->registered['jquery']->deps[ $key ] );
	}

	/**
	 * Action: wp_enqueue_scripts
	 *
	 * Dequeue block library styles.
	 *
	 * @return void
	 */
	function action__wp_enqueue_scripts() : void {

		wp_dequeue_style( 'wp-block-library' );
		wp_enhance_script( 'wp-embed', 'async' );

	}

	/**
	 * Filter: http_request_args
	 *
	 * Prevent check for theme update.
	 *
	 * @param array $args
	 * @param string $url
	 * @return array
	 */
	function filter__http_request_args( $args, string $url ) : array {
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