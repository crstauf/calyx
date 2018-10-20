<?php
/**
 * Post template.
 */

get_header();

echo '<main class="container">';

	if ( have_posts() )
		while ( have_posts() ) {
			the_post();
			Calyx()->get_template_part( 'parts/content.php' );
		}

echo '</main>';

get_footer()
?>