<?php

require_once('class.img_tag.php');
require_once('class.enhance_wp_enqueues.php');

function theme_action_wp_enqueue_scripts() {
	wp_register_script('lazysizes',		get_stylesheet_directory_uri() . '/js/lazysizes.min.js',array('jquery'),'1.5.0|1.5.0|1.5.0');
	wp_register_script('theme-public',	get_stylesheet_directory_uri() . '/js/scripts.js',array('jquery','lazysizes','webfontloader'),'init');
	wp_register_script('webfontloader',	get_stylesheet_directory_uri() . '/js/webfontloader.min.js',array('jquery'),'1.6.24');

	wp_register_style('copy',			get_stylesheet_directory_uri() . '/css/copy.css',array(),'init');
	//wp_register_style('fonts','');
	wp_register_style('home',			get_stylesheet_directory_uri() . '/css/home.css',array(),'init');
	wp_register_style('theme-public',	get_stylesheet_uri(),array('copy'),'init');

	global $wp_scripts,$wp_styles;
	$wp_scripts->add_data('webfontloader','async',true);
	$wp_scripts->add_data('webfontloader','defer',true);
	//$wp_styles->add_data('fonts','noscript',true);
}

?>
