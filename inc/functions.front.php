<?php

require_once('class.image-tag.php');
require_once('class.enhance-wp-enqueues.php');


/*
   ###     ######  ######## ####  #######  ##    ##  ######
  ## ##   ##    ##    ##     ##  ##     ## ###   ## ##    ##
 ##   ##  ##          ##     ##  ##     ## ####  ## ##
##     ## ##          ##     ##  ##     ## ## ## ##  ######
######### ##          ##     ##  ##     ## ##  ####       ##
##     ## ##    ##    ##     ##  ##     ## ##   ### ##    ##
##     ##  ######     ##    ####  #######  ##    ##  ######
*/


class Calyx_ActionsFront {

	static function init() {
		wp_register_script( 'lazysizes',		get_stylesheet_directory_uri() . '/js/lazysizes.min.js', array(),'1.5.0-1.5.0-1.5.0');
		wp_register_script( 'calyx/front',		get_stylesheet_directory_uri() . '/js/scripts.js', array( 'jquery', 'lazysizes', 'webfontloader' ), 'init' );
		wp_register_script( 'webfontloader',	get_stylesheet_directory_uri() . '/js/webfontloader.min.js', array(), '1.6.24' );

			wp_scripts()->add_data( 'lazysizes',     'async', true );
			wp_scripts()->add_data( 'webfontloader', 'async', true );

		wp_register_style( 'calyx/home',			get_theme_asset_uri( 'css/home.min.css' ), array(), 'init' );
		wp_register_style( 'calyx/front',			get_theme_asset_uri( 'style.min.css' ), array( 'calyx/copy' ), 'init' );
		wp_register_style( 'calyx/responsive',		get_theme_asset_uri( 'css/responsive.min.css' ), array( 'calyx/front' ), 'init', '(max-width: 809px)' );
		wp_register_style( 'calyx/ie',				get_theme_asset_uri( 'css/ie.min.css' ), array( 'calyx/front' ), 'init' );

		wp_register_style( 'calyx/front', false, array( 'calyx/copy', 'calyx/style', 'calyx/responsive' ) );

			wp_styles()->add_data( 'calyx/google-fonts', 'noscript', true );

			wp_styles()->add_data( 'calyx/copy',         'inline', true );
			wp_styles()->add_data( 'calyx/responsive',   'inline', true );

			wp_styles()->add_data( 'calyx/ie', 'conditional', 'lt IE 11' );
			wp_styles()->add_data( 'calyx/ie' ,'noscript', true);
			wp_styles()->add_data( 'calyx/ie', 'inline', true );
	}

}


/*
######## #### ##       ######## ######## ########   ######
##        ##  ##          ##    ##       ##     ## ##    ##
##        ##  ##          ##    ##       ##     ## ##
######    ##  ##          ##    ######   ########   ######
##        ##  ##          ##    ##       ##   ##         ##
##        ##  ##          ##    ##       ##    ##  ##    ##
##       #### ########    ##    ######## ##     ##  ######
*/


class Calyx_FiltersFront {

}

/*
######## ##     ## ##    ##  ######  ######## ####  #######  ##    ##  ######
##       ##     ## ###   ## ##    ##    ##     ##  ##     ## ###   ## ##    ##
##       ##     ## ####  ## ##          ##     ##  ##     ## ####  ## ##
######   ##     ## ## ## ## ##          ##     ##  ##     ## ## ## ##  ######
##       ##     ## ##  #### ##          ##     ##  ##     ## ##  ####       ##
##       ##     ## ##   ### ##    ##    ##     ##  ##     ## ##   ### ##    ##
##        #######  ##    ##  ######     ##    ####  #######  ##    ##  ######
*/


function calyx_shutdown_function() {
	$error = error_get_last();
	if (!current_user_can('administrator') && NULL !== $error && is_array($error) && array_key_exists('type',$error) && E_ERROR === $error['type'] && file_exists(ABSPATH . 'error.php'))
		echo '<script type="text/javascript">window.location="' . get_bloginfo('url') . '/error.php";</script>';
}

?>
