<?php
/**
 * Container for global filters.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Global filters.
 */
class Calyx_Filters {
	use Calyx_Singleton;

	/**
	 * Construct.
	 */
	protected function __construct() {
		do_action( 'qm/start', __METHOD__ . '()' );

		add_filter( 'http_request_args',      array( &$this, 'http_request_args'      ), 10, 2 );
		add_filter( 'schedule_event',         array( &$this, 'schedule_event'         ) );
		add_filter( 'acf/settings/save_json', array( &$this, 'acf_settings_save_json' ) );
		add_filter( 'acf/settings/load_json', array( &$this, 'acf_settings_load_json' ) );

		do_action( 'qm/stop', __METHOD__ . '()' );
	}

	/**
	 * Filter: http_request_args
	 *
	 * Prevent update checks for theme.
	 *
	 * @param array  $args
	 * @param string $url
	 *
	 * @return array
	 */
	function http_request_args( $args, $url ) {
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

	/**
	 * Filter: schedule_event; Load: high
	 *
	 * Prevent pings, trackbacks, and enclosures during high load.
	 *
	 * @param object $event Properties of event.
	 *
	 * @return object|bool
	 */
	function schedule_event( $event ) {
		if (
			!Calyx()->server()->is_high_load()
			|| 'do_pings' !== $event->hook
		)
			return $event;

		Calyx()->server()->add_notice( 'Prevented scheduling of ping' );

		return false;
	}

	/**
	 * Filter: acf/settings/save_json
	 *
	 * Specify directory to save ACF JSON to.
	 *
	 * @link https://www.advancedcustomfields.com/resources/local-json/ Documentation.
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	function acf_settings_save_json( $path = '' ) {
		return trailingslashit( __DIR__ ) . 'acf';
	}

	/**
	 * Filter: acf/settings/load_json
	 *
	 * Specify directories to look for ACF JSON.
	 *
	 * @param array $paths
	 *
	 * @uses $this::acf_settings_save_json()
	 *
	 * @return array
	 */
	function acf_settings_load_json( $paths ) {
		return array( $this->acf_settings_save_json() );
	}

}

?>