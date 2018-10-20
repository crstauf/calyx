<?php
/**
 * Post template.
 */

get_header();
?>

<main class="container">

	<?php
	if ( have_posts() )
		while ( have_posts() ) {
			the_post()
			?>

			<article id="post-<?php the_ID() ?>"<?php post_class() ?>>

				<h1><?php the_title() ?></h1>

				<div class="entry">
					<?php the_content() ?>
				</div>

			</article>

			<?php
		}
	?>

</main>

<?php
get_footer()
?>