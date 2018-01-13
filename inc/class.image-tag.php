<?php

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

class image_tag {

    // const VERSION = '0.0.3.3';
    // const GITHUB = 'https://gist.github.com/crstauf/030df6bd6c436620e96cb92a44c9772f';

    const DATAURI = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

    var $alt = '',
        $width = 0,
        $height = 0,
        $title = '',
        $source = '',       // URL of the smallest/original image
        $theme_image = '',  // image filename
        $orientation = '',  // landscape|portrait
        $classes = array(), // array of classes
        $attributes = array(),

        $lazypreload = false,
        $lazyload = true,
        $srcset = array(),
        $sizes = 'auto',

        $echo = true,
        $noscript = true,

        $noscript_sizes = false,
        $noscript_image_size = 'full',

        $attachment_id = 0,
        $image_sizes = array(
            'thumbnail',
            'medium',
            'medium_large',
            'large',
            'full'
        ),

        $lazyload_class = 'lazyload',
        $lazypreload_class = 'lazypreload';

    private $tag = array();
    private $post = array();
    private $noscript_tag = array();

    private $args = array();
    public $files = array(
        '__smallest' => array(
            'width' => 9999999999,
            'height' => 999999999,
            'url' => '',
        ),
        '__largest' => array(
            'width' => 0,
            'height' => 0,
            'url' => '',
        ),
    );

    private $debug = true;
    private $done_output = false;
    private $generating_noscript = false;

    function __construct() {
        $args = func_get_args();
        $this->args = $args[0];

        if ($this->is_WP())
            $this->args = apply_filters('image-tag/args',$this->args);

        if (!count($this->args))
            return false;

        foreach ($this->args as $key => $value)
            if (isset($this->$key)) {
                $this->$key = $value;
                unset($this->args[$key]);
            }

        if ($this->is_AMP() || $this->is_FBIA())
            $this->noscript = false;

        if (!$this->get_image()) {
            if (
                $this->debug
                || ($this->is_WP() && defined('WP_DEBUG') && WP_DEBUG)
            )
                throw new Exception('No image source provided.');
            return false;
        }

        $this->collect_files();
        $this->tag = $this->generate();
        if ($this->noscript && !$this->is_AMP() && !$this->is_FBIA()) {
            $this->generating_noscript = true;
            $this->noscript_tag = $this->generate();
            $this->generating_noscript = false;
        }
    }

    function __destruct() {
		if (true === $this->done_output) return;
		if (true === $this->echo) $this->display();
	}

    function display() {
        $this->done_output = true;
        echo $this->output();
    }

    function output( $classes = array() ) {
        $output = '';
        do {
            $tag = $this->generating_noscript ? $this->noscript_tag : $this->tag;

			if (count($classes))
				$tag['class'] = array_merge($tag['class'],$classes);

            if ($this->generating_noscript)
                $output .= '<noscript>';

            $esc = $this->is_WP() ? 'esc_attr' : 'htmlentities';

            if ($this->is_AMP())
                $output .= '<amp-img';
            else if ($this->is_FBIA())
                $output .= '<figure><img';
            else
                $output .= '<img';

            foreach ($tag as $attr => $value)
                if (false !== $value)
                    $output .= ' ' . $esc($attr) . '="' . (is_array($value) ? $esc($this->output_array($attr,$value)) : $esc($value)) . '"';

            $output .= ' />';
            if ($this->is_FBIA())
                $output .= '<figcaption>' . $this->tag['title'] . '</figcaption></figure>';

            if ($this->generating_noscript)
                $output .= '</noscript>';

            if ($this->noscript && !$this->generating_noscript)
                $this->generating_noscript = true;
            else
                break;
        } while (1);

        return $output;
    }

        function output_array($attr,$array) {
            $glue = in_array($attr,array('data-srcset','srcset')) ? ', ' : ' ';
            return implode($glue,$array);
        }

    function get_image() {
		if (!empty($this->theme_image))
			$this->source = THEME_IMG_DIR_URI . $this->theme_image;

        if ($this->is_attachment()) {

            if (
                !$this->is_WP()
                || 'attachment' !== get_post_type($this->attachment_id)
                || false === stripos(get_post_mime_type($this->attachment_id),'image')
            )
                return false;

            $this->post = get_post($this->attachment_id);
            list($this->source,$this->width,$this->height) = wp_get_attachment_image_src($this->attachment_id,'full');

        } else if (!empty($this->source)) {
            list($this->width,$this->height) = @getimagesize(!empty($this->theme_image) ? THEME_IMG_DIR_PATH . $this->theme_image : $this->source);
        }

		if ($this->width > $this->height)
			$this->orientation = 'landscape';
		else if ($this->height > $this->width)
			$this->orientation = 'portrait';
		else if ($this->height === $this->width)
			$this->orientation = 'square';
		else
			$this->orientation = 'unknown';

		if ( is_array( $this->image_sizes ) ) {
			if (
				array_key_exists( 'landscape', $this->image_sizes )
				&& in_array( $this->orientation, array( 'landscape', 'square', 'unknown' ) )
			)
				$this->image_sizes = $this->image_sizes['landscape'];
			else if (
				array_key_exists( 'portrait', $this->image_sizes )
				&& 'portrait' === $this->orientation
			)
				$this->image_sizes = $this->image_sizes['portrait'];
		}

		$this->classes[] = 'orientation-' . $this->orientation;

        return true;
    }

    function collect_files() {
        if (!$this->is_attachment()) {

            $this->files['original']['url'] = $this->source;
            $this->files['original']['width'] = $this->width;
            $this->files['original']['height'] = $this->height;

            $this->check_dimensions($this->files['original']);

            if (false !== $this->srcset) {
                $i = 0;
                foreach ($this->srcset as $url) {
                    $this->files[$i]['url'] = $url;
                    list(
                        $this->files[$i]['width'],
                        $this->files[$i]['height']) = @getimagesize($url);
                    $this->check_dimensions($this->files[$i]);
                    $i++;
                }
            }

        } else {

            foreach ($this->image_sizes as $size) {
                list(
                    $this->files[$size]['url'],
                    $this->files[$size]['width'],
                    $this->files[$size]['height']) = wp_get_attachment_image_src($this->attachment_id,$size);

                $this->check_dimensions($this->files[$size]);
            }

        }
    }

        function check_dimensions($file) {
            if (
                $file['width'] > $this->files['__largest']['width']
                && $file['height'] > $this->files['__largest']['height']
            )
                $this->files['__largest'] = $file;

            if (
                $file['width'] < $this->files['__smallest']['width']
                && $file['height'] < $this->files['__smallest']['height']
            )
                $this->files['__smallest'] = $file;
        }

        function remove_smallest_largest_files() {
            $array = $this->files;
            unset($array['__smallest'],$array['__largest']);
            return $array;
        }

    function generate() {
        $tag['src'] = $this->generate_src();
        $tag['width'] = $this->width;
        $tag['height'] = $this->height;
        $tag[$this->lazyload && !$this->generating_noscript && !$this->is_AMP() ? 'data-srcset' : 'srcset'] = $this->generate_srcset();
        $tag[$this->lazyload && !$this->generating_noscript ? 'data-sizes' : 'sizes'] = $this->generating_noscript ? $this->noscript_sizes : $this->sizes;
        $tag['alt'] = !empty($this->tag['alt']) ? $this->tag['alt'] : $this->generate_title();
        $tag['title'] = !empty($this->tag['title']) ? $this->tag['title'] : $this->generate_title();
        $tag['class'] = $this->generate_class();

        if ($this->is_WP())
            $this->attributes = apply_filters('image-tag/attributes',$this->attributes,get_class_vars(__CLASS__));

        if (count($this->attributes))
            foreach ($this->attributes as $attr => $value)
                $tag[$attr] = $value;

        if ($this->is_AMP())
            $tag['layout'] = 'responsive';

        if ($this->is_WP())
            foreach ($tag as $attr => $value)
                $tag[$attr] = apply_filters('image-tag/tag/' . str_replace('data-','',$attr),$value,get_class_vars(__CLASS__));

        return $tag;
    }

        function generate_src() {
            if ($this->generating_noscript) {
                if ($this->is_attachment()) {
					$noscript_image_size = apply_filters(
						'image-tag/tag/noscript_image_size',
						$this->noscript_image_size,
						get_class_vars(__CLASS__)
					);
					if ( array_key_exists( $noscript_image_size, $this->files ) )
	                    return apply_filters(
	                        'image-tag/tag/noscript_src',
	                        $this->files[$noscript_image_size]['url'],
	                        get_class_vars(__CLASS__)
	                    );
					else
						return apply_filters(
							'image-tag/tag/noscript_src',
							$this->files['__largest']['url'],
							get_class_vars(__CLASS__)
						);
                } else
                    return $this->files['__largest']['url'];
            } else if ($this->lazyload && !$this->generating_noscript && !$this->is_AMP())
                return self::DATAURI;
            else if (1 < count($this->remove_smallest_largest_files()))
                return $this->files['__smallest']['url'];
            else if (!empty($this->source))
                return $this->source;
        }

        function generate_srcset() {
            if ($this->generating_noscript)
                return $this->noscript_sizes;
            else if (count($this->srcset))
                return $this->srcset;
            else if (count($this->remove_smallest_largest_files())) {
                foreach ($this->files as $key => $file)
                    if (!in_array($key,array('__smallest','__largest')) && is_array($file) && !empty($file['width']))
                        $this->srcset[] = $file['url'] . ' ' . $file['width'] . 'w';
            } else
                return false;
            return $this->srcset;
        }

        function generate_title() {
            if ($this->is_attachment()) return $this->post->post_title;
			else if (!empty($this->title)) return $this->title;
			else if (!empty($this->alt)) return $this->alt;
            else return basename($this->source);
        }

        function generate_class() {
            $classes = array('generated-image-tag');
            if ($this->noscript)
                $classes[] = $this->generating_noscript ? 'hide-if-js' : 'hide-if-no-js';
            if ($this->lazyload && !$this->generating_noscript) {
                $classes[] = $this->is_WP() ? apply_filters('image-tag/classes/lazyload',$this->lazyload_class) : $this->lazyload_class;
                if ($this->lazypreload)
                    $classes[] = $this->is_WP() ? apply_filters('image-tag/classes/lazypreload',$this->lazypreload_class) : $this->lazypreload_class;
            }
            if ($this->is_attachment()) {
                $classes[] = 'post-' . $this->post->ID;
                if ($this->generating_noscript)
                    $classes[] = 'size-' . apply_filters('image-tag/tag/noscript_image_size',$this->noscript_image_size,get_class_vars(__CLASS__));
                else
                    $classes = array_merge($classes,array_map(function($size) { return 'size-' . $size; },array_keys($this->remove_smallest_largest_files())));
            }
            return array_unique(array_merge($classes,$this->classes));
        }

    function is_attachment() { return 0 !== $this->attachment_id && intval($this->attachment_id) == $this->attachment_id; }
    function is_WP() { return defined('ABSPATH') && function_exists('is_attachment') && function_exists('apply_filters'); }

    function is_AMP() { // Google Accelerated Mobile Pages
        $is_AMP = defined('IS_AMP') && IS_AMP;
        if ($this->is_WP())
            $is_AMP = apply_filters('image-tag/test/amp',$is_AMP);
        return $is_AMP;
    }

    function is_FBIA() { // Facebook Instant Articles
        $is_FBIA = defined('IS_FBIA') && IS_FBIA;
        if ($this->is_WP())
            $is_FBIA = apply_filters('image-tag/test/fbia',$is_FBIA);
        return $is_FBIA;
    }

    function debug($var) {
        if ((
                !$this->is_WP()
                && false === $this->debug
            ) || (
                !defined('WP_DEBUG')
                || !WP_DEBUG
            )
        )
            return false;
        else if (is_string($var)) echo $var . '<br />' . "\n";
        else if (is_array($var)) echo print_r($var,true) . '<br />' . "\n";
        else if (is_object($var)) echo print_r(get_object_var($var),true) . '<br />' . "\n";
    }

}

?>
