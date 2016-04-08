<?php
header("Content-type: text/css; charset: UTF-8");

echo 'body#tinymce {';

	if (array_key_exists('bgimage',$_GET))
		echo 'background-image: url(' . $_GET['bgimage'] . ');';

echo '}';
?>
