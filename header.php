<?php Calyx()->benchmark( basename( __FILE__ ) . ':' . __LINE__, 1 ); ?>

<!DOCTYPE html>
<html <?php language_attributes() ?> class="no-js wf-inactive">
<head>

	<meta charset="<?php bloginfo( 'charset' ) ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ) ?>" />

    <?php
	wp_enqueue_style( THEME_PREFIX . '/copy' );
	wp_enqueue_style( THEME_PREFIX . '/above-fold' );
	wp_enqueue_style( THEME_PREFIX . '/style' );
	wp_enqueue_style( THEME_PREFIX . '/responsive' );

	wp_enqueue_script( THEME_PREFIX . '/modernizr' );
	wp_enqueue_script( 'webfontloader' );
	wp_enqueue_script( 'lazysizes' );

    wp_head();
    ?>

</head>
<body <?php body_class() ?>>
<?php wc_print_notices() ?>
