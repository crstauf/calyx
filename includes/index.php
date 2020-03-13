<?php
/**
 * Load theme includes.
 */

defined( 'ABSPATH' ) || die();

# Pre-req files.
require_once THEME_ABSPATH . 'includes/constants.php';
require_once THEME_ABSPATH . 'includes/helpers.php';

	do_action( 'qm/lap', THEME_PREFIX . '/setup', 'files/pre-req' );

# Drop-ins.
// None

	do_action( 'qm/lap', THEME_PREFIX . '/setup', 'files/drop-ins' );

# Core files.
require_once THEME_INCLUDES . 'core/class-calyx.php';
require_once THEME_INCLUDES . 'core/class-calyx-' . ( is_admin() ? 'admin' : 'front' ) . '-hooks.php';

	do_action( 'qm/lap', THEME_PREFIX . '/setup', 'files/core' );

# Integration files.
// None

	do_action( 'qm/lap', THEME_PREFIX . '/setup', 'files/integrations' );

# Feature files.
// None

	do_action( 'qm/lap', THEME_PREFIX . '/setup', 'files/features' );

?>
