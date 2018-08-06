<?php
/**
 * Classes for image tag elements.
 *
 * @version 0.0.7.0
 * @link https://gist.github.com/crstauf/030df6bd6c436620e96cb92a44c9772f
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Abstract object for image tag data.
 *
 * @link https://github.com/aFarkas/lazysizes Preferred lazy loading script.
 * @todo Add image primary color detection and use.
 */
abstract class image_tag {

	const VERSION = '0.0.7.0';
	const DATAURI = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

	/** @var mixed $_source Image source. **/
	protected $_source;

	/** @var bool $_noscript Switch to enable noscript tag. **/
	protected $_noscript = null;

	/** @var bool $_lazyload Switch to enable lazy load. **/
	protected $_lazyload = true;

	/** @var string $_orientation Orientation of the image. **/
	protected $_orientation = 'unknown';

	/** @var float $_ratio Ratio of the image (height divided by width). **/
	protected $_ratio = 0;

	/**
	 * @var string     $id     Image "id" atribute.
	 * @var string     $alt    Image "alt" attribute.
	 * @var string     $src    Image "src" attribute.
	 * @var string     $class  Image "class" attribute.
	 * @var string     $sizes  Image "sizes" attribute.
	 * @var string     $style  Image "style" attribute.
	 * @var string     $title  Image "title" attribute.
	 * @var int|string $width  Image "width" attribute.
	 * @var int|string $height Image "height" attribute.
	 * @var string     $srcet  Image "srcset" attribute.
	 * @var array      $data   Image "data" attributes.
	 * array(
	 *    'src' => string (data-src attribute)
	 *    'sizes' => string (data-sizes attribute)
	 *    'expand' => string (data-expand attribute)
	 *    'srcset' => string (data-srcset attribute)
	 * )
	 */
	var $id,
		$alt,
		$src,
		$class,
		$sizes,
		$style,
		$title,
		$width,
		$height,
		$srcset,

		$data = array(
			'src'    => null,
			'sizes'  => null,
			'expand' => null,
			'srcset' => null,
		);

	/** @var image_tag $noscript Noscript image object. */
	var $noscript;

	/**
	 * Construct.
	 *
	 * @param mixed $source     Image source.
	 * @param array $attributes Image attributes.
	 * @param array $args       Image object arguments.
	 *
	 * @uses image_tag::_maybe_create_noscript_object()
	 * @uses image_tag::get_attributes()
	 */
	function __construct( $source, $attributes = array(), $args = array() ) {
		$this->_source = $this->src = $source;

		array_key_exists( 'noscript', $args ) && $this->_noscript = $args['noscript'];
		array_key_exists( 'lazyload', $args ) && $this->_lazyload = $args['lazyload'];

		if (
			is_null( $this->_noscript )
			&& $this->_lazyload
		)
			$this->_noscript = true;

		$this->_maybe_create_noscript_object( $source, $attributes, $args );

		foreach ( array_keys( $this->get_attributes() ) as $attribute )
			if (
				array_key_exists( $attribute, $attributes )
				&& property_exists( $this, $attribute )
			)
				$this->$attribute = $attributes[$attribute];

		if ( array_key_exists( 'data', $attributes ) )
			$this->data = wp_parse_args( $attributes['data'], $this->data );
	}

	/**
	 * Print the HTML for the `img` tag.
	 *
	 * @uses image_tag::get_html()
	 *
	 * @return string
	 */
	function __toString() {
		return $this->get_html();
	}

	/**
	 * Get image property.
	 *
	 * @param string $property Property name.
	 *
	 * @uses image_tag::get_html()
	 *
	 * @return string
	 */
	function get( $property ) {
		if ( 'html' === $property )
			return $this->get_html();

		return property_exists( $this, $property )
			? $this->$property
			: null;
	}

	/**
	 * Get image attributes.
	 *
	 * @return array
	 */
	protected function get_attributes() {
		return array(
			'id' => $this->id,
			'alt' => $this->alt,
			'src' => $this->src,
			'class' => $this->class,
			'sizes' => $this->sizes,
			'style' => $this->style,
			'title' => $this->title,
			'width' => $this->width,
			'height' => $this->height,
			'srcset' => $this->srcset,
			'data-src' => $this->data['src'],
			'data-sizes' => $this->data['sizes'],
			'data-expand' => $this->data['expand'],
			'data-srcset' => $this->data['srcset'],
		);
	}

	/**
	 * Maybe create noscript object.
	 *
	 * @param string $source     Image source.
	 * @param array  $attributes Image tag attributes.
	 * @param array  $args       Image object arguments.
	 *
	 * @uses get_image_tag_object()
	 */
	protected function _maybe_create_noscript_object( $source, $attributes, $args ) {
		if (
			!$this->_noscript
			|| !empty( $this->noscript )
		)
			return;

		$args['noscript'] = $args['lazyload'] = false;
		$this->noscript = get_image_tag_object( $source, $attributes, $args );
	}

	/**
	 * Get image tag.
	 *
	 * @uses image_tag::set_orientation()
	 * @uses image_tag::maybe_set_lazyload_attributes()
	 * @uses image_tag::get_attributes()
	 *
	 * @return string <img> tag.
	 */
	function get_html() {
		$attributes = array();

		$this->set_orientation();
		$this->set_ratio();
		$this->maybe_set_lazyload_attributes();

		foreach ( array_filter( $this->get_attributes() ) as $attribute => $value )
			$attributes[] = $attribute . '="' . $value . '"';

		return '<img ' . implode( ' ', $attributes ) . ' />' . ( !empty( $this->noscript ) ? '<noscript>' . $this->noscript . '</noscript>' : '' );
	}

	/**
	 * Print image tag.
	 *
	 * @uses image_tag::get_html()
	 */
	function the_html() {
		echo $this->get_html();
	}

	/**
	 * Set attributes if lazyloading.
	 */
	protected function maybe_set_lazyload_attributes() {
		if ( !$this->_lazyload )
			return;

		if ( !empty( $this->srcset ) ) {
			$this->data['srcset'] = $this->srcset;
			$this->srcset = null;
		} else
			$this->data['src'] = $this->src;

		if ( empty( $this->sizes ) )
			$this->data['sizes'] = 'auto';

		$this->src = $this::DATAURI;
		$this->class .= ' lazyload hide-if-no-js';
	}

	/**
	 * Determine and store image orientation.
	 */
	protected function set_orientation() {
		     if ( $this->width  >  $this->height ) $this->_orientation = 'landscape';
		else if ( $this->width  <  $this->height ) $this->_orientation = 'portrait';
		else if ( $this->width === $this->height ) $this->_orientation = 'square';
	}

	/**
	 * Determine and store image ratio (height divided by width).
	 */
	protected function set_ratio() {
		$this->_ratio = $this->height / $this->width;
	}

}


/*
######## ##     ## ######## ######## ########  ##    ##    ###    ##
##        ##   ##     ##    ##       ##     ## ###   ##   ## ##   ##
##         ## ##      ##    ##       ##     ## ####  ##  ##   ##  ##
######      ###       ##    ######   ########  ## ## ## ##     ## ##
##         ## ##      ##    ##       ##   ##   ##  #### ######### ##
##        ##   ##     ##    ##       ##    ##  ##   ### ##     ## ##
######## ##     ##    ##    ######## ##     ## ##    ## ##     ## ########
*/

/**
 * External image handler.
 */
class image_tag__external extends image_tag {}


/*
##      ##  #######  ########  ########  ########  ########  ########  ######   ######
##  ##  ## ##     ## ##     ## ##     ## ##     ## ##     ## ##       ##    ## ##    ##
##  ##  ## ##     ## ##     ## ##     ## ##     ## ##     ## ##       ##       ##
##  ##  ## ##     ## ########  ##     ## ########  ########  ######    ######   ######
##  ##  ## ##     ## ##   ##   ##     ## ##        ##   ##   ##             ##       ##
##  ##  ## ##     ## ##    ##  ##     ## ##        ##    ##  ##       ##    ## ##    ##
 ###  ###   #######  ##     ## ########  ##        ##     ## ########  ######   ######
*/

/**
 * WordPress attachments handler.
 */
class image_tag__wp_attachment extends image_tag {

	/** @var int $_source_id Attachment object ID. */
	protected $_source_id = null;

	/** @var WP_Post $_post Post object of the attachment. **/
	protected $_post = null;

	/** @var array $sizes List of registered image sizes. **/
	protected $_sizes = array();

	/** @var array $sizes_data Array of _image_tag__wp_attachment_image_size objects. **/
	protected $_sizes_data = array(
		'__largest'  => null,
		'__smallest' => null,
	);

	/**
	 * Construct.
	 *
	 * @param int   $source_id  Attachment image object ID.
	 * @param array $attributes Image attributes.
	 * @param array $args Image object arguments.
	 *
	 * @uses image_tag__wp_attachment::_add_size_data()
	 * @uses image_tag::__construct()
	 */
	function __construct( $source_id, $attributes = array(), $args = array() ) {
		$this->_source_id = $source_id;
		$this->_post = get_post( $source_id );

		$this->_sizes = !empty( $args['image_sizes'] )
			? $args['image_sizes']
			: array_merge( get_intermediate_image_sizes(), array( 'full' ) );

		$this->_sizes_data['__largest']  = new _image_tag__wp_attachment_image_size__largest;
		$this->_sizes_data['__smallest'] = new _image_tag__wp_attachment_image_size__smallest;

		foreach ( $this->_sizes as $size )
			$this->_add_size_data( $size );

		uasort( $this->_sizes_data, array( &$this, '_sort_sizes_asc' ) );

		if ( 1 === count( $this->_sizes ) )
			$this->src = $this->_sizes_data['__smallest']->get( 'src' );
		else {
			$srcset = array();

			foreach ( $this->_sizes_data as $size )
				$srcset[] = $size->get( 'src' ) . ' ' . $size->get( 'width' ) . 'w';

			$this->srcset = implode( ', ', $srcset );
		}

		parent::__construct( $this->_sizes_data['__smallest']->get( 'src' ), $attributes, $args );
	}

	/**
	 * Get and store size data.
	 *
	 * @param string $size Image size name.
	 *
	 * @see _image_tag__wp_attachment_image_size
	 */
	protected function _add_size_data( $size ) {
		static $_class = null;

		if ( is_null( $_class ) )
			$_class = apply_filters( 'image_tag/_image_tag__wp_attachment_image_size', '_image_tag__wp_attachment_image_size' );

		$this->_sizes_data[$size] = new $_class( $this, $size );

		if ( $this->_sizes_data[$size]->get( 'width' ) > $this->_sizes_data['__largest']->get( 'width' ) )
			$this->_sizes_data['__largest'] = $this->_sizes_data[$size];

		if ( $this->_sizes_data[$size]->get( 'width' ) < $this->_sizes_data['__smallest']->get( 'width' ) )
			$this->_sizes_data['__smallest'] = $this->_sizes_data[$size];
	}

	/**
	 * Custom sort method to sort sizes by width descending.
	 *
	 * @param _image_tag__wp_attachment_image_size $a First image size object.
	 * @param _image_tag__wp_attachment_image_size $b Second image size object.
	 *
	 * @uses image_tag::get()
	 */
	protected function _sort_sizes_asc( $a, $b ) {
		return $a->get( 'width' ) > $b->get( 'width' );
	}

	/**
	 * Maybe create noscript object.
	 *
	 * @param int   $source     Image attachment object ID.
	 * @param array $attributes Image tag attributes.
	 * @param array $args       Image object arguments.
	 *
	 * @uses imagee_tag::_maybe_create_noscript_object()
	 */
	protected function _maybe_create_noscript_object( $source, $attributes, $args ) {
		parent::_maybe_create_noscript_object( $this->_source_id, $attributes, $args );
	}

	/**
	 * Get image sizes (except magicals).
	 *
	 * @return array of _image_tag__wp_attachment_image_size objects.
	 */
	function get_sizes_data() {
		return array_filter(
			$this->_sizes_data,
			function ( $k ) {
				return '__' !== substr( $k, 0, 2 );
			},
			ARRAY_FILTER_USE_KEY
		);
	}

	/**
	 * Get largest image size.
	 *
	 * @return _image_tag__wp_attachment_image_size__largest
	 */
	function get_largest_size() {
		return $this->_sizes_data['__largest'];
	}

	/**
	 * Get smallest image size.
	 *
	 * @return _image_tag__wp_attachment_image_size__smallest
	 */
	function get_smallest_size() {
		return $this->_sizes_data['__smallest'];
	}

	/**
	 * Check if image has size.
	 *
	 * @param string $size Image size name.
	 *
	 * @return bool
	 */
	function has_size( $size ) {
		return array_key_exists( $size, $this->get_sizes_data() );
	}

	/**
	 * Get image size object.
	 *
	 * @param string $size Image size name.
	 *
	 * @uses image_tag__wp_attachment::has_size()
	 * @uses image_tag__wp_attachment::get_sizes_data()
	 *
	 * @return _image_tag__wp_attachment_image_size|null
	 */
	function get_size( $size ) {
		return $this->has_size( $size )
			? $this->get_sizes_data()[$size]
			: null;
	}

	/**
	 * Get image attachment object ID.
	 */
	function get_attachment_id() {
		return $this->_source_id;
	}

	/**
	 * Get image attachment metdata.
	 */
	function get_metadata() {
		static $_metadata = null;

		if ( is_null( $_metadata ) )
			$_metadata = wp_get_attachment_metadata( $this->get_attachment_id() );

		return $_metadata;
	}

	/**
	 * Get image tag.
	 *
	 * @return string <img> tag.
	 */
	function get_html() {
		do_action_ref_array( 'imadge_tag/before_output', array( $this->_source, &$this ) );

		$this->src = $this->get_smallest_size()->get( 'src' );

		return parent::get_html();
	}

	function get_mode_color() {
		if ( !empty( get_post_meta( $this->get_attachment_id(), '_mode_color', true ) ) )
			  return get_post_meta( $this->get_attachment_id(), '_mode_color', true );

		$class_filename = apply_filters( 'image_tag/get_mode_color/filepath', __DIR__ . '/class-get-image-most-common-colors.php' );
		list( $class_name, $function_name ) = apply_filters( 'image_tag/get_mode_color/function', array( 'GetImageMostCommonColors', 'Get_Colors' ) );

		if ( !file_exists( $class_filename ) )
			return false;

		require_once $class_filename;

		if (
			!empty( $class_name )
			&& class_exists( $class_name )
		) {
			$class = new $class_name;

			if ( !is_callable( array( $class, $function_name ) ) )
				return false;

			$callable = array( $class, $function_name );
		} else if ( empty( $class_name ) )
			$callable = $function_name;

		if ( empty( $callable ) )
			return false;

		$colors = call_user_func( $callable, $this->get_largest_size()->get( 'path' ) );
		$colors = array_keys( $colors );
		$color = '#' . array_shift( $colors );

		add_post_meta( $this->get_attachment_id(), '_mode_color', $color );

		return $color;
	}

}


/*
##      ## ########     #### ##     ##    ###     ######   ########     ######  #### ######## ########
##  ##  ## ##     ##     ##  ###   ###   ## ##   ##    ##  ##          ##    ##  ##       ##  ##
##  ##  ## ##     ##     ##  #### ####  ##   ##  ##        ##          ##        ##      ##   ##
##  ##  ## ########      ##  ## ### ## ##     ## ##   #### ######       ######   ##     ##    ######
##  ##  ## ##            ##  ##     ## ######### ##    ##  ##                ##  ##    ##     ##
##  ##  ## ##            ##  ##     ## ##     ## ##    ##  ##          ##    ##  ##   ##      ##
 ###  ###  ##           #### ##     ## ##     ##  ######   ########     ######  #### ######## ########
*/

/**
 * Object for WordPress attachment image size.
 */
class _image_tag__wp_attachment_image_size {

	/**
	 * @var string $src         Image size URI.
	 * @var int    $width       Image size width.
	 * @var int    $height      Image size height.
	 * @var string $orientation Image size orientation.
	 */
	protected $src         = null,
	          $path        = null,
	          $width       = null,
	          $height      = null,
	          $orientation = null;

	/**
	 * Get the properties of the image size.
	 *
	 * @param int    $source_id Source ID.
	 * @param string $size      Size name.
	 */
	function __construct( image_tag__wp_attachment &$image, $size ) {
		$attachment = wp_get_attachment_image_src( $image->get_attachment_id(), $size );

		if ( empty( $attachment ) )
			return;

		list(
			$this->src,
			$this->width,
			$this->height,
			,
		) = $attachment;

		$metadata = $image->get_metadata();
		$upload_dir = wp_upload_dir();

		if ( 'full' === $size )
			$this->path = $upload_dir['basedir'] . '/' . $metadata['file'];
		else if ( array_key_exists( $size, $metadata['sizes'] ) )
			$this->path = $upload_dir['basedir'] . '/' . $metadata['sizes'][$size]['file'];

		if ( $this->width > $this->height )
			$this->orientation = 'landscape';

		else if ( $this->width < $this->height )
			$this->orientation = 'portrait';

		else if ( $this->width == $this->height )
			$this->orientation = 'square';
	}

	/**
	 * Get image size property.
	 *
	 * @param string $prop Property name.
	 *
	 * @return string|int
	 */
	function get( $prop ) {
		return property_exists( $this, $prop )
			? $this->$prop
			: null;
	}

	/**
	 * Check if size has image URI.
	 */
	function exists() {
		return !empty( $this->get( 'src' ) );
	}

}

/**
 * Special class for largest WordPress image size.
 */
class _image_tag__wp_attachment_image_size__largest extends _image_tag__wp_attachment_image_size {
	/** Construct. */
	function __construct() { $this->width = 0; }
}

/**
 * Special class for smalleest WordPress image size.
 */
class _image_tag__wp_attachment_image_size__smallest extends _image_tag__wp_attachment_image_size {
	/** Construct. */
	function __construct() { $this->width = 9999; }
}


/*
########  ##          ###     ######  ######## ##     ##  #######  ##       ########  ######## ########
##     ## ##         ## ##   ##    ## ##       ##     ## ##     ## ##       ##     ## ##       ##     ##
##     ## ##        ##   ##  ##       ##       ##     ## ##     ## ##       ##     ## ##       ##     ##
########  ##       ##     ## ##       ######   ######### ##     ## ##       ##     ## ######   ########
##        ##       ######### ##       ##       ##     ## ##     ## ##       ##     ## ##       ##   ##
##        ##       ##     ## ##    ## ##       ##     ## ##     ## ##       ##     ## ##       ##    ##
##        ######## ##     ##  ######  ######## ##     ##  #######  ######## ########  ######## ##     ##
*/

/**
 * Placeholder.com (formerly placehold.it) handler.
 * @link https://placeholder.com
 */
class image_tag__placeholder extends image_tag {

	/**
	 * Construct.
	 *
	 * @param string $source     Only 'placeholder'.
	 * @param array  $attributes Image attributes.
	 * @param array  $args       Image object arguments.
	 */
	function __construct( $source = 'placeholder', $attributes = array(), $args = array() ) {
		$source = 'http://via.placeholder.com/';

		// add width dimension
		if ( array_key_exists( 'width', $args ) )
			$source .= $args['width'];
		else if ( array_key_exists( 'width', $attributes ) )
			$source .= $attributes['width'];

		// add height dimension
		if ( array_key_exists( 'height', $args ) )
			$source .= 'x' . $args['height'];
		else if ( array_key_exists( 'height', $attributes ) )
			$source .= 'x' . $attributes['height'];

		// add background color
		if ( array_key_exists( 'color-bg', $args ) ) {
			$source .= '/' . $args['color-bg'];

			// add text color (background color must be specified)
			if ( array_key_exists( 'color-text', $args ) )
				$source .= '/' . $args['color-text'];
		}

		// add image format (gif, jpeg, jpg, png)
		if ( array_key_exists( 'format', $args ) )
			$source .= '.' . $args['format'];

		// add image text
		if ( array_key_exists( 'text', $args ) )
			$source = add_query_arg( 'text', $args['text'], $source );

		unset(
			$args['width'],
			$args['height'],
			$args['color-bg'],
			$args['color-text'],
			$args['format'],
			$args['text']
		);

		parent::__construct( $source, $attributes, $args );

	}

	/**
	 * Maybe create noscript object.
	 *
	 * @param string $source     "Placeholder".
	 * @param array  $attributes Image tag attributes.
	 * @param array  $args       Image object arguments.
	 *
	 * @uses imagee_tag::_maybe_create_noscript_object()
	 */
	protected function _maybe_create_noscript_object( $source = 'placeholder', $attributes, $args ) {
		parent::_maybe_create_noscript_object( 'placeholder', $attributes, $args );
	}

}


/*
########  ####  ######   ######  ##     ## ##     ##
##     ##  ##  ##    ## ##    ## ##     ## ###   ###
##     ##  ##  ##       ##       ##     ## #### ####
########   ##  ##        ######  ##     ## ## ### ##
##         ##  ##             ## ##     ## ##     ##
##         ##  ##    ## ##    ## ##     ## ##     ##
##        ####  ######   ######   #######  ##     ##
*/

/**
 * Picsum.photos handler.
 * @link https://picsum.photos
 */
class image_tag__picsum extends image_tag {

	/**
	 * Construct.
	 *
	 * @param string $source     Only 'picsum'.
	 * @param array  $attributes Image attributes.
	 * @param array  $args       Image object arguments.
	 */
	function __construct( $source, $attributes = array(), $args = array() ) {
		$source = 'https://picsum.photos/';

		if ( !empty( $args['gray'] ) )
			$source .= 'g/';

		if ( array_key_exists( 'width', $args ) )
			$source .= $args['width'];
		else if ( array_key_exists( 'width', $attributes ) )
			$source .= $attributes['width'];

		if ( array_key_exists( 'height', $args ) )
			$source .= '/' . $args['height'];
		else if ( array_key_exists( 'height', $args ) )
			$source .= '/' . $attributes['height'];

		if ( !empty( $args['image'] ) )
			$source = add_query_arg( 'image', $args['image'], $source );
		else if ( !empty( $args['random'] ) )
			$source = add_query_arg( 'random', 1, $source );

		if ( !empty( $args['blur'] ) )
			$source = add_query_arg( 'blur', 1, $source );

		if ( !empty( $args['gravity'] ) )
			$source = add_query_arg( 'gravity', $args['gravity'], $source );

		unset(
			$args['gray'],
			$args['width'],
			$args['height'],
			$args['image'],
			$args['blur'],
			$args['gravity']
		);

		parent::__construct( $source, $attributes, $args );

	}

	/**
	 * Maybe create noscript object.
	 *
	 * @param int   $source     "Picsum".
	 * @param array $attributes Image tag attributes.
	 * @param array $args       Image object arguments.
	 *
	 * @uses imagee_tag::_maybe_create_noscript_object()
	 */
	protected function _maybe_create_noscript_object( $source = 'picsum', $attributes, $args ) {
		parent::_maybe_create_noscript_object( 'picsum', $attributes, $args );
	}

}

/**
 * Get image tag object.
 *
 * @param int|string $source     Image source.
 * @param array      $attributes Image attributes.
 * @param array      $args       Image object arguments.
 * @param bool       $skip_cache Skip the cached object.
 *
 * @return image_tag
 */
function get_image_tag_object( $source, $attributes = array(), $args = array(), $skip_cache = false ) {
	static $_class_names = array();

	$cache_key = md5( serialize( array( $source, $args ) ) );
	$skip_cache || $cache = wp_cache_get( $cache_key, __FUNCTION__, false, $has_cache );

	if ( !empty( $has_cache ) )
		return $cache;

	if ( false !== stripos( $source, 'http' ) )
		$class = 'image_tag__external';

	else if (
		is_numeric( $source )
		&&  intval( $source ) == $source
	)
		$class = 'image_tag__wp_attachment';

	else if ( 'placeholder' === $source )
		$class = 'image_tag__placeholder';

	else if ( 'picsum' === $source )
		$class = 'image_tag__picsum';

	if ( empty( $class ) )
		return;

	if ( !array_key_exists( $class, $_class_names ) )
		$_class_names[$class] = apply_filters( 'image_tag/' . $class, $class );

	$_image_tag = new $_class_names[$class]( $source, $attributes, $args );

	wp_cache_add( $cache_key, $_image_tag, __FUNCTION__ );

	return $_image_tag;
}

/**
 * Print image tag.
 *
 * @param int|string $source     Image source.
 * @param array      $attributes Image attributes.
 * @param array      $args       Image object arguments.
 * @param bool       $skip_cache Skip the cached object.
 *
 * @uses get_image_tag_object()
 */
function image_tag( $source, $attributes = array(), $args = array(), $skip_cache = false ) {
	echo get_image_tag_object( $source, $attributes, $args, $skip_cache );
}

/**
 * Debug elements for image_tag.
 */
function image_tag__debug() {
	image_tag( 'https://images.unsplash.com/photo-1528485683898-7633212b3db6?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=dffe08428a166a76b5b6527aeae128ce&auto=format&fit=crop&w=4500&q=80', array( 'width' => 400 ) );
	image_tag( 'placeholder', array( 'width' => 250, 'height' => 150 ), array( 'text' => 'Hello' ) );
	image_tag( 'picsum', array( 'width' => 500, 'height' => 500 ), array( 'random' => 1 ) );
	echo ( $wp = get_image_tag_object( 11, array( 'width' => 300, 'style' => 'width: auto; height: 500px;' ), array( 'image_sizes' => array( 'thumbnail', 'full' ) ) ) );
	echo $wp->get_mode_color();
}

?>
