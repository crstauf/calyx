<?php
get_header();
$image = get_image_tag_object( 13, array( 'width' => 300, 'height' => 200 ) );
echo $image;
get_footer();
?>