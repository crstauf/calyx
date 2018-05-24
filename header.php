<?php
/**
 * Default site header.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}
?>

<!DOCTYPE html>
<html <?php language_attributes() ?>>
<head>

	<?php wp_head() ?>

</head>
<body <?php body_class() ?>>