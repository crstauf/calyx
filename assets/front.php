<?php
/**
 * Register assets used on the frontend.
 */

/*
######## ##     ## ######## ##     ## ########
   ##    ##     ## ##       ###   ### ##
   ##    ##     ## ##       #### #### ##
   ##    ######### ######   ## ### ## ######
   ##    ##     ## ##       ##     ## ##
   ##    ##     ## ##       ##     ## ##
   ##    ##     ## ######## ##     ## ########
*/

# Theme header
calyx_register_style( THEME_PREFIX . '/site/copy',    'assets/css/site-copy.css', null, 'init' );
calyx_register_style( THEME_PREFIX . '/site/desktop', 'assets/css/site-desktop.css', array( THEME_PREFIX . '/site/copy' ), 'init' );
calyx_register_style( THEME_PREFIX . '/site/mobile',  'assets/css/site-mobile.css', array( THEME_PREFIX . '/site/desktop' ), 'init', 'only screen and ( max-width: 767px )' );

	wp_register_style( THEME_PREFIX . '/site/header', null, array(
		THEME_PREFIX . '/site/copy',
		THEME_PREFIX . '/site/desktop',
		THEME_PREFIX . '/site/mobile',
	) );

# Theme footer
calyx_register_style( THEME_PREFIX . '/site/styles', 'style.css', array( THEME_PREFIX . '/site/header' ), 'init' );

	wp_register_style( THEME_PREFIX . '/site/footer', null, array( THEME_PREFIX . '/site/styles', ) );


/*
######## ######## ##     ## ########  ##          ###    ######## ########  ######
   ##    ##       ###   ### ##     ## ##         ## ##      ##    ##       ##    ##
   ##    ##       #### #### ##     ## ##        ##   ##     ##    ##       ##
   ##    ######   ## ### ## ########  ##       ##     ##    ##    ######    ######
   ##    ##       ##     ## ##        ##       #########    ##    ##             ##
   ##    ##       ##     ## ##        ##       ##     ##    ##    ##       ##    ##
   ##    ######## ##     ## ##        ######## ##     ##    ##    ########  ######
*/




/*
##     ## ######## ##    ## ########   #######  ########   ######
##     ## ##       ###   ## ##     ## ##     ## ##     ## ##    ##
##     ## ##       ####  ## ##     ## ##     ## ##     ## ##
##     ## ######   ## ## ## ##     ## ##     ## ########   ######
 ##   ##  ##       ##  #### ##     ## ##     ## ##   ##         ##
  ## ##   ##       ##   ### ##     ## ##     ## ##    ##  ##    ##
   ###    ######## ##    ## ########   #######  ##     ##  ######
*/




do_action( THEME_PREFIX . '/registered_assets/front' );

?>
