<?php
/**
 * Default site header.
 *
 * @package calyx
 */

defined( 'ABSPATH' ) || die();
?>

<!DOCTYPE html>
<html <?php language_attributes() ?> class="no-js">
<head>

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
	<script>document.documentElement.className = document.documentElement.className.replace( 'no-js', 'js' );</script>

	<?php
	wp_enqueue_style( THEME_PREFIX . '/critical/copy' );
	wp_enqueue_style( THEME_PREFIX . '/critical/site' );

	wp_head();
	?>

</head>
<body <?php body_class() ?>>
