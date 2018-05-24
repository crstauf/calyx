<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

define( 'CALYX_IMAGE_PATH', CALYX_ABSPATH . 'assets/img' );

defined( 'CALYX_HIGH_LOAD'     ) || define( 'CALYX_HIGH_LOAD',      false );
defined( 'CALYX_EXTREME_LOAD'  ) || define( 'CALYX_EXTREME_LOAD',   false );

defined( 'WP_LOCAL_DEV'        ) || define( 'WP_LOCAL_DEV',         false );
defined( 'WP_DEVELOP'          ) || define( 'WP_DEVELOP',           false );
defined( 'WP_DEBUG'            ) || define( 'WP_DEBUG',             WP_DEVELOP );
defined( 'WP_DEBUG_LOG'        ) || define( 'WP_DEBUG_LOG',         WP_DEVELOP );
defined( 'WP_DEBUG_DISPLAY'    ) || define( 'WP_DEBUG_DISPLAY',     false );
defined( 'SCRIPT_DEBUG'        ) || define( 'SCRIPT_DEBUG',         WP_DEVELOP );
defined( 'CONCATENATE_SCRIPTS' ) || define( 'CONCATENATE_SCRIPTS', !WP_DEVELOP || !SCRIPT_DEBUG );
defined( 'COMPRESS_SCRIPTS'    ) || define( 'COMPRESS_SCRIPTS',    !WP_DEVELOP || !SCRIPT_DEBUG );
defined( 'COMPRESS_CSS'        ) || define( 'COMPRESS_CSS',        !WP_DEVELOP || !SCRIPT_DEBUG );
defined( 'QM_DISABLED'         ) || define( 'QM_DISABLED',         !WP_DEBUG );
defined( 'ACF_LITE'            ) || define( 'ACF_LITE',            !WP_DEVELOP );

?>