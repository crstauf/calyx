<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

define( 'THEME_PREFIX', 'Calyx' );
define( 'THEME_IMG_DIR', 'assets/img' );

defined( THEME_PREFIX . '_HIGH_LOAD'    ) || define( THEME_PREFIX . '_HIGH_LOAD',    false );
defined( THEME_PREFIX . '_EXTREME_LOAD' ) || define( THEME_PREFIX . '_EXTREME_LOAD', false );

defined( 'WP_LOCAL_DEV' )        || define( 'WP_LOCAL_DEV',         false );
defined( 'WP_DEVELOP' )          || define( 'WP_DEVELOP',           false );
defined( 'WP_DEBUG' )            || define( 'WP_DEBUG',             WP_DEVELOP );
defined( 'WP_DEBUG_LOG' )        || define( 'WP_DEBUG_LOG',         WP_DEVELOP );
defined( 'WP_DEBUG_DISPLAY' )    || define( 'WP_DEBUG_DISPLAY',     false );
defined( 'SCRIPT_DEBUG' )        || define( 'SCRIPT_DEBUG',         WP_DEVELOP );
defined( 'CONCATENATE_SCRIPTS' ) || define( 'CONCATENATE_SCRIPTS', !WP_DEVELOP );
defined( 'COMPRESS_SCRIPTS' )    || define( 'COMPRESS_SCRIPTS',    !WP_DEVELOP || !SCRIPT_DEBUG );
defined( 'COMPRESS_CSS' )        || define( 'COMPRESS_CSS',        !WP_DEVELOP || !SCRIPT_DEBUG );
defined( 'ACF_LITE' )            || define( 'ACF_LITE',            !WP_DEVELOP );

?>