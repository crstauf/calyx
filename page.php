<?php
/**
 * Default page tmeplate.
 */

get_header();
?>

<div class="container">

	<?php
	if ( have_posts() )
		while ( have_posts() ) {
			the_post()
			?>

			<main id="post-<?php the_ID() ?>"<?php post_class() ?>>

				<h1><?php the_title() ?></h1>

				<div class="entry">
					<?php the_content() ?>
				</div>

			</main>

			<?php
		}
	?>

</div>

<?php
get_footer()
?>