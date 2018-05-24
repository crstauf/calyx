<?php
/**
 * Abstract object for image tag data.
 *
 * @version 0.0.7.0
 * @link https://gist.github.com/crstauf/030df6bd6c436620e96cb92a44c9772f
 */

/**
 * Abstract object for image tag data.
 */
abstract class image_tag {

	const VERSION = '0.0.7.0';
	const DATAURI = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

	/** @var mixed $_source Image source. **/
	protected $_source;

	/** @var bool $_noscript Switch to enable noscript tag. **/
	protected $_noscript = false;

	/** @var bool $_lazyload Switch to enable lazy load. **/
	protected $_lazyload = true;
	
	/** @var null|string $_orientation Orientation of the image. **/
	protected $_orientation = null;

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

	function __construct( $source, array $attributes = array(), array $args = array() ) {

		$this->_source = $source;
		
		array_key_exists( 'noscript', $args ) && $this->_noscript = $args['noscript'];
		array_key_exists( 'lazyload', $args ) && $this->_lazyload = $args['lazyload'];
		
		foreach ( array( 'id', 'alt', 'src', 'class', 'sizes', 'style', 'title', 'width', 'height', 'srcset' ) as $attribute )
			if ( array_key_exists( $attribute, $attributes ) )
				$this->$attribute = $attributes[$attribute];
				
		if ( array_key_exists( 'data', $attributes ) )
			$this->data = wp_parse_args( $attributes['data'], $this->data );
		
		if ( $attributes['width'] > $attributes['height'] )
			$this->_orientation = 'landscape';
			
		else if ( $attributes['width'] < $attributes['height'] )
			$this->_orientation = 'portrait';
			
		else if ( $attribtues['width'] == $attributes['height'] )
			$this->_orientation = 'square';

	}

	/**
	 * Print the HTML for the `img` tag.
	 */
    function __toString() {
		return $this->get_html();
    }
	
	function get_prop( string $prop ) {
		return property_exists( $this, $prop )
			? $this->$prop
			: null;
	}

	function get_html() {
		$output = '<img src="' . $this->_source . '" />';

		$output .= $this->maybe_get_noscript_html();

		return $output;
	}

	function maybe_get_noscript_html() {
		if ( !$this->_noscript )
			return null;

		$output = '<noscript>';

		return $output . '</noscript>';
	}

}

/**
 * Image tag for WordPress attachments.
 */
class image_tag_wp_attachment extends image_tag {
	
	/** @var int $_source Attachment object ID. **/
	protected $_source = null;
	
	/** @var WP_Post $_post Post object of the attachment. **/
	protected $_post = null;

	/** @var array $sizes List of registered image sizes. **/
	protected $_sizes = array();

	/** @var array $sizes_data Array of _image_tag_wp_attachment_image_size objects. **/
	protected $_sizes_data = array(
		'__largest'  => null,
		'__smallest' => null,
	);

	function __construct( int $source_id, array $attributes = array(), array $args = array() ) {
		parent::__construct( $source_id, $attributes, $args );
		
		$this->_post  = get_post( $source_id );
		$this->_sizes = !empty( $args['image_sizes'] ) 
			? $args['image_sizes'] 
			: array_merge( get_intermediate_image_sizes(), array( 'full' ) );

		foreach ( $this->_sizes as $size )
			$this->_add_size_data( $size );

	}

	protected function _add_size_data( string $size ) {
		static $_class = null;

		if ( is_null( $_class ) )
			$_class = apply_filters( 'image_tag/_image_tag_wp_attachment_image_size', '_image_tag_wp_attachment_image_size' );
			
		if ( is_null( $this->_sizes_data['__largest'] ) ) {
			$this->_sizes_data['__largest']  = new _image_tag_wp_attachment_image_size__largest;
			$this->_sizes_data['__smallest'] = new _image_tag_wp_attachment_image_size__smallest;
		}

		$this->_sizes_data[$size] = new $_class( $this->_source, $size );
		
		if ( $this->_sizes_data[$size]->get( 'width' ) > $this->_sizes_data['__largest']->get( 'width' ) )
			$this->_sizes_data['__largest'] = $this->_sizes_data[$size];
			
		if ( $this->_sizes_data[$size]->get( 'width' ) < $this->_sizes_data['__smallest']->get( 'width' ) )
			$this->_sizes_data['__smallest'] = $this->_sizes_data[$size];
	}

	function get_sizes_data() {
		return array_filter( 
			$this->_sizes_data, 
			function ( $k ) { 
				return '__' !== substr( $k, 0, 2 ); 
			}, 
			ARRAY_FILTER_USE_KEY 
		);
	}

    function has_size( string $size ) {
        return array_key_exists( $size, $this->get_sizes_data() );
    }

    function get_size( string $size ) {
        return $this->has_size( $size )
            ? $this->get_sizes_data()[$size]
            : null;
    }

	function get_attachment_id() {
		return $this->_source;
	}

	function get_html() {
		do_action_ref_array( 'image_tag/' . $this->_source . '/before_output', array( &$this ) );
		do_action_ref_array( 'image_tag/before_output', array( $this->_source, &$this ) );

		// return parent::get_html();

		return '<img src="' . $this->get_size( 'medium_large' )->get( 'src' ) . '" />';
	}

}

/**
 * Object for WordPress attachment image size.
 */
class _image_tag_wp_attachment_image_size {

	protected $src         = null;
	protected $width       = null;
	protected $height      = null;
	protected $orientation = null;

    /**
     * Get the properties of the image size.
     *
     * @param int    $source_id Source ID.
     * @param string $size      Size name.
     */
	function __construct( int $source_id, string $size ) {
		$attachment = wp_get_attachment_image_src( $source_id, $size );

        if ( empty( $attachment ) )
            return;

        list(
            $this->src,
            $this->width,
            $this->height,
            ,
        ) = $attachment;
		
		if ( $this->width > $this->height )
			$this->orientation = 'landscape';
			
		else if ( $this->width < $this->height )
			$this->orientation = 'portrait';
			
		else if ( $this->width == $this->height )
			$this->orientation = 'square';
	}

	function get( string $prop ) {
		return property_exists( $this, $prop )
			? $this->$prop
			: null;
	}

    function exists() {
        return !empty( $this->get( 'src' ) );
    }

}

class _image_tag_wp_attachment_image_size__largest  extends _image_tag_wp_attachment_image_size { protected $width = 0;    function __construct() {} }
class _image_tag_wp_attachment_image_size__smallest extends _image_tag_wp_attachment_image_size { protected $width = 9999; function __construct() {} }

class image_tag_placeholder extends image_tag {

	function __construct( string $source, array $attributes = array(), array $args = array() ) {

		$details = array(
			'width'  => null,
			'height' => null,
			'text'   => null,
		);
		
		preg_match( '/^http:\/\/via\.placeholder.com\/([0-9]*)(?:x([0-9]*))?.*(?:\/?\?text=(.*))?$/', $source, $matches );
		
		if ( empty( $matches ) ) {
			if (
				   !empty( $attributes['width']  )
				&& !empty( $attributes['height'] )
			) {
				$source = 'http://via.placeholder.com/' . $attributes['width'] . 'x' . $attributes['height'];
				empty( $attributes['alt']   ) || add_query_arg( 'text', $attributes['alt'],   $source );
				empty( $attributes['title'] ) || add_query_arg( 'text', $attributes['title'], $source );
			} else
				return;
		}

		parent::__construct( $source, $attributes, $args );   

	}

}

class image_tag_picsum extends image_tag {
	
	function __construct( string $source, array $attributes = array(), array $args = array() ) {
		preg_match( '/^https:\/\/picsum.photos\/(?:g\/)?([0-9]*)(?:\/([0-9]*))?.*$/', $source, $matches );
		
		if ( empty( $matches ) ) {
			if ( 
				   !empty( $attributes['width']  )
				&& !empty( $attributes['height'] )
			)
				$source = 'https://picsum.photos/' . $attributes['width'] . '/' . $attributes['height'] . '/?random';
			else
				return;
		}
		
		parent::__construct( $source, $attributes, $args );
		
	}
	
}

function get_image_tag_object( $source, array $args = array(), bool $skip_cache = false ) {
	static $_class_names = array();

	$cache_key = md5( $source . '_' . json_encode( $args ) );
	$skip_cache || $cache = wp_cache_get( $cache_key, __FUNCTION__, false, $has_cache );

	if ( !empty( $has_cache ) )
		return $cache;

	if (
		is_numeric( $source )
		&&  intval( $source ) == $source
	)
		$class = 'image_tag_wp_attachment';

	else if (
		'placeholder' === $source
		|| false !== stripos( $source, 'via.placeholder.com' )
	)
		$class = 'image_tag_placeholder';
		
	else if (
		'picsum' === $source
		|| false !== stripos( $source, 'picsum.photos' )
	)
		$class = 'image_tag_picsum';

	if ( !array_key_exists( $class, $_class_names ) )
		$_class_names[$class] = apply_filters( 'image_tag/' . $class, $class );

	$_image_tag = new $_class_names[$class]( $source, $args );

    wp_cache_add( $cache_key, $_image_tag, __FUNCTION__ );

    return $_image_tag;
}

?>
