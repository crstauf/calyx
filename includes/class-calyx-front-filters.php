<?php
/**
 * Class to manage frontend filter.
 */

 if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
 	header( 'Status: 403 Forbidden' );
 	header( 'HTTP/1.1 403 Forbidden' );
 	exit;
 }

/**
 * Filters for front-end.
 */
class Calyx_Front_Filters {
	use Calyx_Singleton;

	/**
	 * Construct.
	 */
	protected function __construct() {
		do_action( 'qm/start', __METHOD__ . '()' );

		add_filter( 'body_class', array( &$this, 'body_class' ) );
		add_filter( 'post_class', array( &$this, 'post_class' ), 10, 3 );
		add_filter( 'the_content', array( &$this, 'the_content' ) );

		do_action( 'qm/stop', __METHOD__ . '()' );
	}

	/**
	 * Filter: body_class
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	function body_class( $classes ) {
		if (
			!is_singular()
			|| !has_post_thumbnail( get_queried_object_id() )
		)
			return $classes;

		$classes[] = 'has-post-thumbnail';
		$classes[] = 'post-thumbnail-' . get_post_thumbnail_id( get_queried_object_id() );

		return $classes;
	}

	/**
	 * Filter: post_class
	 *
	 * - add post thumbnail's ID
	 *
	 * @param array $classes
	 * @param array $class
	 * @param int   $post_id
	 *
	 * @return array
	 */
	function post_class( $classes, $class, $post_id ) {
		if ( has_post_thumbnail( $post_id ) )
			$classes[] = 'post-thumbnail-' . get_post_thumbnail_id( $post_id );

		return $classes;
	}

	/**
	 * Filter: the_content
	 *
	 * - add headline anchors
	 *
	 * @param string $content
	 *
	 * @uses Calyx_Front::add_headline_anchors()
	 *
	 * @return string
	 */
	function the_content( $content ) {
		return Calyx()->front()->add_headline_anchors( $content );
	}

}

?>