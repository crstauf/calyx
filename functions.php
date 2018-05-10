<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

require_once 'includes/constants.php';

do_action( 'qm/start', THEME_PREFIX . ':load' );

// core files
require_once 'includes/class.calyx.php';
require_once 'includes/trait.calyx.singleton.php';
require_once 'includes/abstract.calyx._core.php';
require_once 'includes/class.calyx.utilities.php';

do_action( 'qm/lap', THEME_PREFIX . ':load', 'core' );

if (
	WP_DEVELOP
	&& file_exists( get_theme_file_path( 'includes/_dev/dev.php' ) )
) {
	require_once get_theme_file_path( 'includes/_dev/dev.php' );
	do_action( 'qm/lap', THEME_PREFIX . ':load', 'development' );
}

// additional core files
require_once get_theme_file_path( 'includes/class.calyx.data.php'     );
require_once get_theme_file_path( 'includes/class.calyx.actions.php'  );
require_once get_theme_file_path( 'includes/class.calyx.filters.php'  );

// core abstracts
require_once get_theme_file_path( 'includes/abstract.cpt.php' );

do_action( 'qm/lap', THEME_PREFIX . ':load', 'additional core' );

if ( is_admin() ) {

	// admin-only files
	require_once get_theme_file_path( 'includes/class.calyx.admin.php' );

	do_action( 'qm/lap', THEME_PREFIX . ':load', 'back-end' );

} else {

	// front-only files
	require_once get_theme_file_path( 'includes/class.calyx.front.php' );
	require_once get_theme_file_path( 'includes/class.image-tag.php' );

	do_action( 'qm/lap', THEME_PREFIX . ':load', 'front-end' );

}

include_once get_theme_file_path( 'includes/_temporary.php' );

do_action( 'qm/stop', THEME_PREFIX . ':load' );

// let's initialize!
Calyx();

do_action( 'qm/start', THEME_PREFIX . ':setup' );
do_action( 'setup_theme_' . THEME_PREFIX );
do_action( 'qm/stop', THEME_PREFIX . ':setup' );

do_action( 'qm/start', THEME_PREFIX . ':after_setup' );
do_action( 'after_setup_theme_' . THEME_PREFIX );
do_action( 'qm/stop', THEME_PREFIX . ':after_setup' );

?>
