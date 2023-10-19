<?php
/**
 * Default template.
 */

defined( 'WPINC' ) || die();

global $wp;
trigger_error( sprintf( 'Unable to find a template for <code>%s</code>.', site_url( $wp->request ) ), E_USER_WARNING );

get_header();

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		?>

		<div id="post-<?php the_ID() ?>" <?php post_class() ?>>

			<h1><?php the_title() ?></h1>

			<div class="entry">
				<?php the_content() ?>
			</div>

		</div>

		<?php
	}
}

get_footer();

?>
