<?php declare( strict_types=1 );
/**
 * Theme manager.
 */

namespace Calyx;

defined( 'WPINC' ) || die();

/**
 * Class: Calyx
 */
class Calyx {

	/**
	 * Get singleton instance.
	 *
	 * @return self
	 */
	public static function instance() : self {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
		}

		return $instance;
	}

	/**
	 * Construct.
	 */
	protected function __construct() {
		add_action( 'gform_loaded', array( $this, 'action__gform_loaded' ) );
		add_action( 'init', array( $this, 'action__init' ) );
		add_action( 'wp_default_scripts', array( $this, 'action__wp_default_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'action__wp_enqueue_scripts' ) );
		add_action( 'gform_enqueue_scripts', array( $this, 'action__gform_enqueue_scripts' ) );

		add_filter( 'http_request_args', array( $this, 'filter__http_request_args' ), 10, 2 );
	}

	/**
	 * Action: gform_loaded
	 *
	 * - enhance Gravity Forms assets
	 */
	public function action__gform_loaded() : void {
		wp_enhance_style( 'gform_basic', 'preload' );
		wp_enhance_style( 'gform_theme', 'preload' );
	}

	/**
	 * Action: init
	 *
	 * - register assets
	 */
	public function action__init() : void {
		require_once constant( 'THEME_ABSPATH' ) . 'assets/register.php';

		/**
		 * Filter: acf/format_value/type=oembed
		 *
		 * Prevent ACF from retrieving oEmbed data, because
		 * we're going to implement ourselves with caching,
		 * and we don't need multiple HTTP requests.
		 */
		remove_filter( 'acf/format_value/type=oembed', array( acf_get_field_type( 'oembed' ), 'format_value' ), 10 );
	}

	/**
	 * Action: wp_default_scripts
	 *
	 * Don't include jQuery Migrate.
	 *
	 * @param \WP_Scripts $scripts
	 * @return void
	 */
	public function action__wp_default_scripts( \WP_Scripts $scripts ) : void {
		if (
			is_admin()
			|| ! isset( $scripts->registered['jquery'] )
		) {
			return;
		}

		$key = array_search( 'jquery-migrate', $scripts->registered['jquery']->deps );

		if ( empty( $key ) ) {
			return;
		}

		unset( $scripts->registered['jquery']->deps[ $key ] );
	}

	/**
	 * Action: wp_enqueue_scripts
	 *
	 * Dequeue block library styles.
	 * Async and defer Query Monitor assets.
	 *
	 * @return void
	 */
	public function action__wp_enqueue_scripts() : void {
		wp_dequeue_style( 'classic-theme-styles' );
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_script( 'wp-embed' );

		wp_enhance_style( 'query-monitor', 'async' );
		wp_enhance_script( 'query-monitor', 'defer' );
	}

	/**
	 * Action: gform_enqueue_scripts
	 *
	 * Defer, preload, and async load Gravity Form dependencies.
	 *
	 * @return void
	 */
	public function action__gform_enqueue_scripts() : void {
		static $once = false;

		if ( $once ) {
			return;
		}

		if ( 'gform_enqueue_scripts' !== current_action() ) {
			return;
		}

		$once = true;

		wp_enhance_style( 'gform_theme_components', 'async' );
		wp_enhance_style( 'gform_theme_ie11', 'async' );
		wp_enhance_style( 'gravity_forms_theme_reset', 'async' );
		wp_enhance_style( 'gravity_forms_theme_foundation', 'async' );
		wp_enhance_style( 'gravity_forms_theme_framework', 'async' );
		wp_enhance_style( 'gravity_forms_orbital_theme', 'async' );

		wp_enhance_script( 'jquery-core', 'defer' );
		wp_enhance_script( 'regenerator-runtime', 'defer' );
		wp_enhance_script( 'wp-polyfill', 'defer' );
		wp_enhance_script( 'wp-dom-ready', 'defer' );
		wp_enhance_script( 'wp-hooks', 'defer' );
		wp_enhance_script( 'wp-i18n', 'defer' );
		wp_enhance_script( 'wp-a11y', 'defer' );
	}


	/**
	 * Filter: http_request_args
	 *
	 * Prevent check for theme update.
	 *
	 * @param mixed[] $args
	 * @param string $url
	 * @return mixed[]
	 */
	public function filter__http_request_args( $args, string $url ) : array {
		if ( false !== strpos( $url, 'http://api.wordpress.org/themes/update-check' ) ) {
			return $args; // Not a theme update request. Bail immediately.
		}

		if (
			is_array( $args )
			&& count( $args )
			&& array_key_exists( 'themes', $args )
			&& is_array( $args['themes'] )
			&& count( $args['themes'] )
			&& array_key_exists( 'themes', $args['body'] )
		) {
			$args['body']['themes']        = json_decode( $args['body']['themes'] );
			list( $template, $stylesheet ) = array( get_option( 'template' ), get_option( 'stylesheet' ) );

			unset( $args['body']['themes']->themes->$template, $args['body']['themes']->themes->$stylesheet );

			$args['body']['themes'] = json_encode( $args['body']['themes'] );
		}

		return $args;
	}

}
