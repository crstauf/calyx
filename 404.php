<?php
/**
 * 404 template.
 */

namespace Calyx;

defined( 'WPINC' ) || die();

get_header();

echo '<main>' .
	'<div class="container" style="text-align: center;">' .
		'<h1 class="h2">There\'s nothing here!</h1>' .
	'</div>' .
'</main>';

get_footer();