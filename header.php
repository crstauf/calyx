<!DOCTYPE html>
<html <?php language_attributes() ?> class="no-js">
<head>

	<meta charset="<?php bloginfo( 'charset' ) ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ) ?>" />

	<?php
	// wp_enqueue_script( 'modernizr' );
	wp_enqueue_script( 'webfontloader' );
	wp_enqueue_script( 'lazysizes' );

	wp_enqueue_style( 'calyx/copy' );
	wp_enqueue_style( 'calyx/responsive' );
	wp_enqueue_style( 'calyx/ie' );

	wp_head();
	?>

	<script type="text/javascript">
		WebFontConfig = {};
		window.lazySizesConfig = window.lazySizesConfig || {};
		document.documentElement.className = document.documentElement.className.replace('no-js','js');
	</script>

</head>

<body <?php body_class() ?>>
