<?php

add_theme_support('html5');
add_theme_support('title-tag');
add_theme_support('post-thumbnails');

require_once('inc/class.base_cpt.php');
require_once('inc/functions.' . (is_admin() ? 'admin' : 'public') . '.php');

add_filter('http_request_args',		'theme_filter_http_request_args',5,2);

if (is_admin()) {

	add_action('admin_enqueue_scripts',	'theme_action_admin_enqueue_scripts');
	add_filter('mce_buttons_2',			'theme_filter_mce_buttons_2');
	add_filter('tiny_mce_before_init',	'theme_filter_tiny_mce_before_init');

} else {

	add_action('wp_enqueue_scripts',	'theme_action_wp_enqueue_scripts');
	add_filter('script_loader_tag',		array('enhance_wp_enqueues','tag'),10,2);
	add_filter('style_loader_tag',		array('enhance_wp_enqueues','tag'),10,2);
	add_action('wp_head',				array('enhance_wp_enqueues','tags'),8);
	add_action('wp_head',				array('enhance_wp_enqueues','tags'),9);
	add_action('wp_footer',				array('enhance_wp_enqueues','tags'),8);
	add_action('wp_footer',				array('enhance_wp_enqueues','tags'),9);

}

if (file_exists(get_stylesheet_directory() . '/_dev.php'))
	require_once('_dev.php');


/** ======================================================================================= **/


function theme_filter_http_request_args($r,$url) {
	if (false !== strpos($url,'http://api.wordpress.org/themes/update-check'))
		return $r; // Not a theme update request. Bail immediately.

	if (
		is_array($r) &&
		count($r) &&
		array_key_exists('themes',$r) &&
		is_array($r['themes']) &&
		count($r['themes']) &&
		array_key_exists('themes',$r['body'])
	) {
		$r['body']['themes'] = json_decode($r['body']['themes']);
		list($template,$stylesheet) = array(get_option('template'),get_option('stylesheet'));
		unset($r['body']['themes']->themes->$template);
		unset($r['body']['themes']->themes->$stylesheet);
		$r['body']['themes'] = json_encode($r['body']['themes']);
	}

	return $r;
}

?>
