<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

get_header();

if ( have_posts() )
    while ( have_posts() ) {
        the_post();
        the_content();
    }

get_footer();
?>
