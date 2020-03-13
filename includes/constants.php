<?php
/**
 * Theme constants.
 */

defined( 'ABSPATH' ) || die();

# Debugging constants.
maybe_define_constant( 'WP_LOCAL_DEV',         false );
maybe_define_constant( 'WP_DEVELOP',           false );
maybe_define_constant( 'WP_DEBUG',             WP_DEVELOP );
maybe_define_constant( 'WP_DEBUG_LOG',         WP_DEBUG );
maybe_define_constant( 'WP_DEBUG_DISPLAY',     WP_LOCAL_DEV );

# Asset constants.
maybe_define_constant( 'SCRIPT_DEBUG',         WP_DEBUG );
maybe_define_constant( 'CONCATENATE_SCRIPTS', !SCRIPT_DEBUG );
maybe_define_constant( 'COMPRESS_SCRIPTS',    !SCRIPT_DEBUG );
maybe_define_constant( 'COMPRESS_CSS',        !SCRIPT_DEBUG );

# Theme constants.
maybe_define_constant( 'THEME_ABSPATH', dirname( __DIR__ ) );
maybe_define_constant( 'THEME_INCLUDES', THEME_ABSPATH . 'includes' );

# Plugin constants.
maybe_define_constant(  'QM_DISABLED',        !WP_DEVELOP  );
maybe_define_constant( 'QMX_DISABLED',         QM_DISABLED );
maybe_define_constant( 'ACF_LITE',            !WP_DEVELOP  );

do_action( THEME_PREFIX . '.defined_constants' );

?>
