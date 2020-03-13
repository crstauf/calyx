<?php
/**
 * Register assets used globally (front and back).
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

calyx_register_script( THEME_PREFIX . '/site/object', 'assets/js/calyx.js', null, 'init' );


/*
########  #######  ##    ## ########  ######
##       ##     ## ###   ##    ##    ##    ##
##       ##     ## ####  ##    ##    ##
######   ##     ## ## ## ##    ##     ######
##       ##     ## ##  ####    ##          ##
##       ##     ## ##   ###    ##    ##    ##
##        #######  ##    ##    ##     ######
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



do_action( THEME_PREFIX . '/registered_assets/global' );

?>
