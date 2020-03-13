<?php
/**
 * Register assets.
 *
 * @package calyx
 * @see Calyx::action__init()
 */

require_once THEME_ABSPATH . 'assets/acf/index.php';

/**
 * If AJAX, cron, or JSON request, don't register assets.
 *
 * @todo Add detection for REST request.
 * @see https://core.trac.wordpress.org/ticket/42061
 */
if ( !apply_filters( THEME_PREFIX . '/register_assets', (
	   wp_doing_ajax()
	|| wp_doing_cron()
	|| wp_is_json_request()
) ) )
	return;

# Only register assets inside of `init` action.
if ( !doing_action( 'init' ) ) {
	trigger_error( 'Do not load <code>' . __FILE__ . '</code> outside of <code>init</code> action.' );
	return;
}

# Register assets used globally.
require_once 'global.php';

# Register assets for loaded end.
require_once is_admin() ? 'admin.php' : 'front.php';

do_action( THEME_PREFIX . '/registered_assets' );

?>
