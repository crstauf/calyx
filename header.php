<?php
/**
 * Default site header.
 */

defined( 'ABSPATH' ) || die();
?>

<!DOCTYPE html>
<html <?php language_attributes() ?> class="no-js">
<head>

	<script type="text/javascript">document.documentElement.className = document.documentElement.className.replace( 'no-js', 'js' );</script>

	<?php
	wp_enqueue_style( THEME_PREFIX . '/copy' );

	wp_head();
	?>

</head>
<body <?php body_class() ?>>
