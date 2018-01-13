<?php

define( 'THEME_PREFIX', 'Calyx' );
define( 'THEME_IMG_DIR', 'assets/img' );
defined( 'Calyx_HIGH_LOAD' ) || define( 'Calyx_HIGH_LOAD', false );
defined( 'Calyx_EXTREME_LOAD' ) || define( 'Calyx_EXTREME_LOAD', false );

defined( 'WP_LOCAL_DEV' ) || define( 'WP_LOCAL_DEV', false );
defined( 'WP_DEVELOP' ) || define( 'WP_DEVELOP', false );
defined( 'WP_DEBUG' ) || define( 'WP_DEBUG', WP_DEVELOP );
defined( 'WP_DEBUG_LOG' ) || define( 'WP_DEBUG_LOG', true );
defined( 'WP_DEBUG_DISPLAY' ) || define( 'WP_DEBUG_DISPLAY', false );
defined( 'SCRIPT_DEBUG' ) || define( 'SCRIPT_DEBUG', WP_DEVELOP );
defined( 'CONCATENATE_SCRIPTS' ) || define( 'CONCATENATE_SCRIPTS', !WP_DEVELOP );
defined( 'COMPRESS_SCRIPTS' ) || define( 'COMPRESS_SCRIPTS', !WP_DEVELOP || !SCRIPT_DEBUG );
defined( 'COMPRESS_CSS' ) || define( 'COMPRESS_CSS', !WP_DEVELOP || !SCRIPT_DEBUG );
defined( 'ACF_LITE' ) || define( 'ACF_LITE', !WP_DEVELOP );

?>