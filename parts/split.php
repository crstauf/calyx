<?php
/**
 * Template part: Split
 *
 * @todo define
 */

defined( 'WPINC' ) || die();

$default_args = array(
	'container' => 'div'
	'container_id' => false,
	'container_class' => array(),
	'container_attributes' => array(),
	'text' => '',
	'image_id' => 0,
	'text_on_right' => false,
);

$args = wp_parse_args( $args, $default_args );
// array_intersect_keys()
$args = ( object ) $args;

if ( empty( $args->text ) )
	return;

$image = create_image_tag( $args->image_id );

if ( !$image->is_valid() )
	return;

$args->container_class[] = 'template-part-split';

if ( $image->is_type( 'attachment' ) )
	$args->container_class[] = 'image-id-' . $image->attachment_id;

if ( $args->text_on_right )
	$args->container_class[] = 'text-right';

echo '<' . esc_attr( $args->container )
	
	. ( 
		$args->container_id 
		? ' id="' . esc_attr( $args->container_id ) . '"' 
		: '' 
	)
	. ' class="' . esc_attr( implode( ' ', $args->container_class ) ) . '"';

	foreach ( $args->container_attributes as $attribute => $value )
		echo ' ' . esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';

echo '>';

	echo '<div class="entry">'
		. wp_kses_post( $args->text )
	. '</div>';
	
	echo '<div class="image">'
		. '<div class="container--ratio">'
			$image
		. '</div>'
	. '</div>';
	
echo '</' . esc_attr( $args->container ) . '>';

?>