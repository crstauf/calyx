<?php

require_once 'includes/class.calyx.php';
require_once 'includes/class.calyx.utilities.php';

Calyx();

require_once 'includes/class.calyx.actions.php';
require_once 'includes/class.calyx.filters.php';

if ( is_admin() ) {

    require_once Calyx()->utils()->get_file_path( 'includes/class.calyx.admin.php' );

} else {

    require_once Calyx()->utils()->get_file_path( 'includes/class.calyx.front.php' );

}

if ( )

Calyx()->utils()->benchmark( 'Calyx files included' );

do_action(       'setup_theme_' . THEME_PREFIX );
do_action( 'after_setup_theme_' . THEME_PREFIX );

?>
