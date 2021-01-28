<?php
/**
 * 404 template.
 *
 * @package Calyx
 */

defined( 'ABSPATH' ) || die();

get_header();

echo '<main>' .
	'<div class="container" style="text-align: center;">' .
		'<h1 class="h2">There\'s nothing here!</h1>' .
		'<p>Start <a href="' . esc_attr( home_url() ) . '">here</a>.</p>' .
	'</div>' .
'</main>';

get_footer();
?>