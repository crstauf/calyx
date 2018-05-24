<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

class Calyx_CPT_Sample extends _Calyx_CPT {

	protected $_type = 'sample';
	protected $_plural = 'Sample Posts';
	protected $_singular = 'Sample Post';
	protected $_dashicon_code = '\f468';
	protected $_args = array(
		'menu_icon' => 'dashicons-sos',
	);

}

do_action( THEME_PREFIX . '/cpts/add', Calyx_CPT_Sample::create_instance() );

?>