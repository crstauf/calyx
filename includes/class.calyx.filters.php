<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

/**
 * Site-wide filters.
 */
class Calyx_Filters extends _Calyx_Core {

	protected function __cosntruct() {
		parent::__construct();

		add_filter( 'http_request_args', array( &$this, 'http_request_args' ), 10, 2 );

	}

	function http_request_args( $r, $url ) {
        if ( false !== strpos( $url, 'http://api.wordpress.org/themes/update-check' ) )
            return $r; // Not a theme update request. Bail immediately.

        if (
            is_array( $r )
            && count( $r )
            && array_key_exists( 'themes', $r )
            && is_array( $r['themes'] )
            && count( $r['themes'] )
            && array_key_exists( 'themes', $r['body'] )
        ) {
            $r['body']['themes'] = json_decode( $r['body']['themes'] );
            list( $template, $stylesheet ) = array( get_option( 'template' ), get_option( 'stylesheet' ) );
            unset( $r['body']['themes']->themes->$template, $r['body']['themes']->themes->$stylesheet );
            $r['body']['themes'] = json_encode( $r['body']['themes'] );
        }

        return $r;
    }

}

?>