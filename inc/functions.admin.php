<?php

function theme_action_admin_enqueue_scripts($hook) {
	wp_enqueue_script('theme-admin',get_stylesheet_directory_uri() . '/js/admin.js',array('jquery'),'init');
	wp_enqueue_style('theme-admin',get_stylesheet_directory_uri() . '/css/admin.css',array(),'init');
}

function theme_filter_mce_buttons_2($buttons) {
	array_unshift($buttons,'styleselect');
	return $buttons;
}

function theme_filter_tiny_mce_before_init($init) {
	global $post;

	$init['style_formats'] = json_encode(array(
		array('title' => 'Text Color','items' => array(
			array('title' => 'Blue',	'inline' => 'span',	'classes' => 'text-blue'),
		)),
		array('title' => 'Background Color','items' => array(
			array('title' => 'Blue',	'inline' => 'span',	'classes' => 'bg-blue'),
		)),
		array('title' => 'Buttons','items' => array(
			array('title' => 'Blue',	'selector' => 'a',	'classes' => 'btn bg-blue'),
		)),
	));

	$args = array('post_type' => $post->post_type,'ID' => $post->ID);
	$editor_style_uri = get_stylesheet_directory_uri() . '/css/editor-style.php';

	$init['content_css'] = json_encode(array(
		'copy' => get_stylesheet_directory_uri() . '/css/copy.css',
		'editor_style_uri' => $editor_style_uri,
	));

	$init['preview_styles'] .= ' color background-color padding';

	return $init;
}

?>
