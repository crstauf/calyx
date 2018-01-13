<?php


/*
   ###     ######  ######## ####  #######  ##    ##  ######
  ## ##   ##    ##    ##     ##  ##     ## ###   ## ##    ##
 ##   ##  ##          ##     ##  ##     ## ####  ## ##
##     ## ##          ##     ##  ##     ## ## ## ##  ######
######### ##          ##     ##  ##     ## ##  ####       ##
##     ## ##    ##    ##     ##  ##     ## ##   ### ##    ##
##     ##  ######     ##    ####  #######  ##    ##  ######
*/


class Calyx_ActionsAdmin {

	static function init() {

		wp_register_script( 'calyx/admin', get_theme_asset_uri( 'js/admin.min.js' ), array( 'jquery' ), 'init' );
		wp_register_style( 'calyx/admin', get_theme_asset_uri( 'css/admin.min.css' ), array(), 'init' );

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


class Calyx_FiltersAdmin {

	static function mce_buttons_2( $buttons ) {
		array_unshift( $buttons, 'styleselect' );
		return $buttons;
	}

	static function tiny_mce_before_init( $init ) {

		$init['style_formats'] = array(
			array( 'title' => 'Text Color', 'items' => array(
				array( 'title' => 'Blue',	'inline' => 'span',	'classes' => 'text-blue' ),
			) ),
			array( 'title' => 'Background Color', 'items' => array(
				array( 'title' => 'Blue',	'inline' => 'span',	'classes' => 'bg-blue' ),
			) ),
			array( 'title' => 'Buttons', 'items' => array(
				array( 'title' => 'Blue',	'selector' => 'a',	'classes' => 'btn bg-blue' ),
			) ),
		);

		$editor_style_uri = get_stylesheet_directory_uri() . '/css/editor-style.php';

		$init['content_css'] = array(
			'copy' => get_stylesheet_directory_uri() . '/css/copy.css',
			'editor_style_uri' => $editor_style_uri,
		);

		$init['preview_styles'] .= ' color background-color padding';

		return $init;
	}

	static function tiny_mce_before_init_999999( $init ) {
		$init['style_formats'] = json_encode( array_values( $init['style_formats'] ) );
		$init['content_css'] = json_encode( array_values( $init['content_css'] ) );
		return $init;
	}

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


?>
