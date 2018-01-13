<?php

// https://gist.github.com/crstauf/030df6bd6c436620e96cb92a44c9772f

if (!defined('ABSPATH') || !function_exists('add_filter')) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

class image_tag {

    const VERSION = '0.0.4';
    const DATAURI = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

    protected $args = array();

    var $attributes = array(
                'id' => '',
               'alt' => '',
               'src' => '',
             'sizes' => '',
             'title' => '',
             'width' =>  0,
            'height' =>  0,
            'srcset' => array(),
             'class' => array(),
        ),

        $echo = true,
        $noscript = true,
        $lazyload = true,
        $lazypreload = false,
        $noscript_sizes = array();

    protected $files = array(
        '__smallest' => array(
            'width'  => 9999999,
            'height' => 9999999,
            'url'    => ''
        ),
        '__largest' => array(
            'width'  => 0,
            'height' => 0,
            'url'    => '',
        ),
    );

    protected $orientation = ''; // portrait|landscape

    function __construct() {
        if ( !is_subclass_of( $this, 'image_tag' ) ) {

            $args = func_get_arg( 0 );
            if ( array_key_exists( 'attachment_id', $args ) )
                $source = $args['attachment_id'];
            else if ( array_key_exists( 'source', $args ) )
                $source = $args['source'];
            else if ( array_key_exists( 'theme_image', $args ) )
                $source = $args['theme_image'];
            else
                return false;

            $args['attributes']['class']['old-generated'] = 'old-generated-image-tag';

            unset(
                $args['attachment_id'],
                $args['source'],
                $args['filename']
            );

            if (
                array_key_exists( 'echo', $args )
                && false === $args['echo']
            )
                return image_tag( $source, $args );
            else
                echo image_tag( $source, $args );

            exit();
        }

        add_filter( 'image-tag/noscript/attributes/glue', array( &$this, 'filter_image_tag_noscript_attributes_glue' ) );

        if ( is_array( $this->args ) && count( $this->args ) ) {

			if ( array_key_exists( 'attributes', $this->args ) ) {
				foreach ( $this->args['attributes'] as $key => $value )
					$this->attributes[$key] = $value;
				unset( $this->args['attributes'] );
			}

			foreach ( $this->args as $key => $value )
                if ( isset( $this->$key ) )
                    $this->$key = $value;
		}

        $this->attributes['class']['generated'] = 'generated-image-tag';

        $this->gather_files();
        $this->determine_orientation();
        $this->being_lazy();

    }

    function __toString() {
        if ( !$this->echo )
            return '';

        ksort( $this->attributes );

        $image_output = $this->image_output();

        if ( !$this->noscript )
            return implode( "\n", $image_output );

        $this->noscript_prep();

        return implode( "\n", array_filter( $image_output ) ) . implode( "\n", array_filter( $this->noscript_image_output() ) );
    }

        function image_output() {
            $output = array( 'open' => "\n<img " );

            foreach ( $this->attributes as $attr_name => $attr_value ) {

                $attr_value = apply_filters( 'image-tag/attributes/value', $attr_value, $attr_name, $this );
                $attr_value = apply_filters( 'image-tag/attributes/' . $attr_name . '/value', $attr_value, $this );

                $output[$attr_name] = $this->construct_attribute( $attr_name, $attr_value );

                if ( '' === trim( $output[$attr_name] ) )
                    unset( $output[$attr_name] );

            }

            $output['close'] = "/>\n";

            return array_filter( $output );
        }

        function noscript_prep() {

            $this->attributes['class']['noscript'] = 'noscript';

            if ( !empty( $this->attributes['id'] ) )
                $this->attributes['id'] .= '-noscript';

            if ( array_key_exists( 'data-sizes', $this->attributes ) )
                $this->attributes['sizes'] = $this->attributes['data-sizes'];

            if ( array_key_exists( 'data-src', $this->attributes ) && self::DATAURI !== $this->attributes['data-src'] )
                $this->attributes['src'] = $this->attributes['data-src'];

            if ( 'auto' === $this->attributes['sizes'] ) {

                if ( !empty( $this->noscript_sizes ) && is_array( $this->noscript_sizes ) && count( $this->noscript_sizes ) ) {

                    $this->attributes['sizes'] = $this->noscript_sizes;

                    if ( array_key_exists( 'data-srcset', $this->attributes ) )
                        $this->attributes['srcset'] = $this->attributes['data-srcset'];

                } else {
                    $this->attributes['src'] = $this->files['__largest']['url'];
                    unset( $this->attributes['sizes'] );
                }

            }

            unset(
                $this->attributes['class']['lazyload'],
                $this->attributes['class']['lazypreload'],
                $this->attribtues['data-src'],
                $this->attributes['data-sizes'],
                $this->attributes['data-srcset']
            );
        }

        function noscript_image_output() {
            $output = array(
                'open_noscript' => '<noscript>',
                'open_img' => "\t<img",
            );

            foreach ( $this->attributes as $attr_name => $attr_value ) {

                $attr_value = apply_filters( 'image-tag/noscript/attributes/value', $attr_value, $attr_name, $this );
                $attr_value = apply_filters( 'image-tag/noscript/attributes/' . $attr_name . '/value', $attr_value, $this );

                $output[$attr_name] = "\t" . $this->construct_attribute( $attr_name, $attr_value, true );

                if ( '' === trim( $output[$attr_name] ) )
                    unset( $output[$attr_name] );

            }

            $output['close_img'] = "\n\t/>";
            $output['close_noscript'] = "</noscript>\n";

            return array_filter( $output );
        }

            function construct_attribute( $attr_name, $attr_value, $noscript = false ) {
                if ( !is_array( $attr_value ) ) {
                    if ( !empty( $attr_value ) && ( '' !== trim( $attr_value ) || 'alt' === $attr_name ) )
                        return "\t" . $attr_name . '="' . esc_attr( $attr_value ) . '" ';
                    return false;
                }

                if ( false !== strpos( $attr_name, 'srcset' ) && count( $attr_value ) )
                    foreach ( $attr_value as $w => $url )
                        $attr_value[$w] = $url . ' ' . $w;

                switch ( $attr_name ) {
                    case 'sizes':
                    case 'srcset':
                    case 'data-srcset':
                        $glue = ",\n\t\t";
                        break;
                    case 'class':
                    default:
                        $glue = ' ';
                        break;
                }

                $glue = apply_filters( 'image-tag/' . ( $noscript ? 'noscript/' : '' ) . 'attributes/glue', $glue, $attr_name, $this );
                $glue = apply_filters( 'image-tag/' . ( $noscript ? 'noscript/' : '' ) . 'attributes/' . $attr_name . '/glue', $glue, $this );

                foreach ( $attr_value as $i => $maybe_array )
                    if ( is_array( $maybe_array ) )
                        $attr_value[$i] = implode( $glue, $maybe_array );

                return "\t" . $attr_name . '="' . implode( $glue, $attr_value ) . '" ';
            }

                function filter_image_tag_noscript_attributes_glue( $glue ) {
                    if ( ' ' === $glue )
                        return ' ';
                    return $glue . "\t";
                }

    function determine_orientation() {
        $largest = &$this->files['__largest'];

        if ( $largest['width'] > $largest['height'] )
            $this->orientation = 'landscape';
        else if ( $largest['width'] < $largest['height'] )
            $this->orientation = 'portrait';
        else if ( $largest['width'] === $largest['height'] )
            $this->orientation = 'square';
        else
            $this->orientation = false;

        $this->attributes['class']['orientation'] = 'orientation-' .
            ( false === $this->orientation
                ? 'unknown'
                : $this->orientation
            );
    }

    function gather_files() {}

    function being_lazy() {
        if ( !$this->lazyload && !$this->lazypreload )
            return false;

        if ( $this->lazypreload )
            $this->attributes['class']['lazypreload'] = 'lazypreload';

        if ( !$this->lazyload )
            return false;

        $this->attributes['data-sizes'] = $this->attributes['sizes'];

        $this->attributes['class']['lazyload'] = 'lazyload';

		if ( 3 < count( $this->files ) )
			$this->attributes['data-srcset'] = $this->attributes['srcset'];
		else
			$this->attributes['data-src'] = $this->attributes['src'];

		$this->attributes['src'] = self::DATAURI;

        unset( $this->attributes['srcset'] );

        return true;
    }

}


/*
##      ##  #######  ########  ########  ########  ########  ########  ######   ######
##  ##  ## ##     ## ##     ## ##     ## ##     ## ##     ## ##       ##    ## ##    ##
##  ##  ## ##     ## ##     ## ##     ## ##     ## ##     ## ##       ##       ##
##  ##  ## ##     ## ########  ##     ## ########  ########  ######    ######   ######
##  ##  ## ##     ## ##   ##   ##     ## ##        ##   ##   ##             ##       ##
##  ##  ## ##     ## ##    ##  ##     ## ##        ##    ##  ##       ##    ## ##    ##
 ###  ###   #######  ##     ## ########  ##        ##     ## ########  ######   ######
*/

class image_tag_wp_attachment extends image_tag {

    var $id = 0,
        $post = null,
        $image_sizes = array( 'thumbnail', 'medium', 'medium_large', 'large', 'full' );

    function __construct() {
        $this->args = func_get_arg( 0 );

        parent::__construct();

        $this->get_post();

        if (
            false === $this->post
            || is_wp_error( $this->post )
        )
            return;

        $this->attributes['class']['post'] = 'post-' . $this->id;
		$this->attributes['class']['generated-attachment'] = 'generated-image-attachment-tag';

    }

    function noscript_prep() {
        parent::noscript_prep();

		$this->attributes['class']['post-noscript'] = $this->attributes['class']['post'] . '-noscript';

        if ( !empty( $this->attributes['sizes'] ) || !empty( $this->noscript_sizes ) )
            return;

        $largest = $this->files['__largest'];

        foreach ( $this->files as $size => $file ) {
            if ( in_array( $size, array( '__largest', '__smallest' ) ) )
                continue;
            if ( $file['url'] === $largest['url'] ) {
                $this->attributes['class']['WP-size'] = 'size-' . $size;
                break;
            }
        }
    }

    function get_post() {
        $this->post = get_post( $this->id );

        if ( '' === $this->attributes['alt'] )
            $this->attributes['alt'] = esc_attr( get_the_title( $this->post ) );

        if ( '' === $this->attributes['title'] )
            $this->attributes['title'] = esc_attr( get_the_title( $this->post ) );
    }

    function gather_files() {

        foreach ( $this->image_sizes as $i => $image_size ) {

            $this->attributes['class']['WP-size'][$image_size] = 'size-' . $image_size;

            $img = wp_get_attachment_image_src( $this->id, $image_size );

            if ( 0 === $i ) {
                $this->attributes['src']    = $img[0];
                $this->attributes['width']  = $img[1];
                $this->attributes['height'] = $img[2];
            }

            $this->files[$image_size] = array(
                'width'  => $img[1],
                'height' => $img[2],
                'url'    => $img[0],
            );

            $this->attributes['srcset'][$img[1] . 'w'] = $img[0];

            if ( $this->files['__largest']['width'] < $img[1] )
                $this->files['__largest'] = &$this->files[$image_size];

            if ( $this->files['__smallest']['width'] > $img[1] )
                $this->files['__smallest'] = &$this->files[$image_size];
        }

        $this->files['__largest']['ratio'] = ( $this->files['__largest']['height'] / $this->files['__largest']['width'] ) * 100;

    }

}


/*
##      ## ########     ######## ##     ## ######## ##     ## ########
##  ##  ## ##     ##       ##    ##     ## ##       ###   ### ##
##  ##  ## ##     ##       ##    ##     ## ##       #### #### ##
##  ##  ## ########        ##    ######### ######   ## ### ## ######
##  ##  ## ##              ##    ##     ## ##       ##     ## ##
##  ##  ## ##              ##    ##     ## ##       ##     ## ##
 ###  ###  ##              ##    ##     ## ######## ##     ## ########
*/

class image_tag_theme extends image_tag_url {

	protected $filename = '';

	protected $files = array(
        '__smallest' => array(
            'width'  => 9999999,
            'height' => 9999999,
            'path'   => '',
            'url'    => ''
        ),
        '__largest' => array(
            'width'  => 0,
            'height' => 0,
            'path'   => '',
            'url'    => '',
        ),
    );

	function __construct() {
		$this->args = func_get_arg( 0 );
		$this->filename = $this->args['filename'];

		$this->files['full']['path'] = THEME_IMAGES_PATH . $this->filename;

		if ( !file_exists( $this->files['full']['path'] ) )
			return false;

		$this->attributes['src'] = THEME_IMAGES_URL . $this->filename;
		$this->attributes['class']['generated-theme'] = 'generated-image-theme-tag';

		parent::__construct();
	}

	function gather_files() {

		$get = @getimagesize( $this->files['full']['path'] );

		$this->files['__largest']
			= $this->files['__smallest']
			= $this->files['full']
			= array(
				'width' => $get[0],
				'height' => $get[1],
				'url' => THEME_IMAGES_URL . $this->filename,
				'path' => THEME_IMAGES_PATH . $this->filename,
			);

		if ( !count( $this->attributes['srcset'] ) )
			return;

		foreach ( $this->attributes['srcset'] as $size => $filename ) {
			$path = THEME_IMAGES_PATH . $filename;

			if ( !file_exists( $path ) ) {
				unset( $this->attributes['srcset'][$size] );
				continue;
			}

			$url = THEME_IMAGES_URL . $filename;
			$get = @getimagesize( $path );

			$this->attributes['srcset'][$size] = $url;

			$file = $this->files[$size] = array(
				'width' => $get[0],
				'height' => $get[1],
				'url' => $url,
				'path' => $path,
			);

            if ( $this->files['__largest']['width'] < $get[0] )
                $this->files['__largest'] = $file;

            if ( $this->files['__smallest']['width'] > $img[1] )
                $this->files['__smallest'] = $file;
		}

	}

}


/*
##     ## ########  ##
##     ## ##     ## ##
##     ## ##     ## ##
##     ## ########  ##
##     ## ##   ##   ##
##     ## ##    ##  ##
 #######  ##     ## ########
*/

class image_tag_url extends image_tag {

    function __construct() {
		if ( !count( $this->args ) )
			$this->args = func_get_arg( 0 );

        parent::__construct();

		$this->files['full']['url'] = $this->attributes['src'] = $this->args['source'];
		$this->attributes['class']['generated-url'] = 'generated-image-url-tag';
    }

    function gather_files() {}

	function being_lazy() {
        if ( !$this->lazyload && !$this->lazypreload )
            return false;

        if ( $this->lazypreload )
            $this->attributes['class']['lazypreload'] = 'lazypreload';

        if ( !$this->lazyload )
            return false;

		if ( !empty( $this->attributes['sizes'] ) )
        	$this->attributes['data-sizes'] = $this->attributes['sizes'];

        $this->attributes['class']['lazyload'] = 'lazyload';
		$this->attributes['data-src'] = $this->attributes['src'];
		$this->attributes['src'] = self::DATAURI;

        return true;
    }

}

if ( !function_exists( 'image_tag' ) ) {

	function image_tag( $source, $args = false ) {
	    if ( false === $args )
	        $args = array();

	    if ( is_int( $source ) ) {

	        $args['id'] = $source;
	        return new image_tag_wp_attachment( $args );

	    } else if (
	        false !== stripos( $source, 'http://' )
	        || false !== stripos( $source, 'https://' )
	    ) {

	        $args['source'] = $source;
			return new image_tag_url( $args );

	    } else if ( apply_filters( 'image-tag/enable-theme-images', defined( 'THEME_IMAGES_URL' ) ) ) {

	        $args['filename'] = $source;
			return new image_tag_theme( $args );

	    }

	    return false;
	}

}
