<?php
/**
 * Template part: default content.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}
?>

<article id="post-<?php the_ID() ?>" <?php post_class() ?>>

	<div class="entry">
		<h1><?php the_title() ?></h1>
		<?php the_content() ?>
	</div>

</article>