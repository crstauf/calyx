<?php

class img_tag {

	const DATAURI = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

	var $url = false,
		$image = false,
		$image_id = false,

		$alt = false,
		$title = false,
		$width = false,
		$height = false,
		$preload = false,

		$sizes = 'auto',
		$output = '',

		$echo = true,
		$lazy = true,
		$noscript = true,

		$first = array(),
		$classes = array(),
		$attributes = array(),
		$image_sizes = array(),
		$image_hires_sizes = array();

	private $tag = array();
	private $files = array();
	private $done_output = false;
	private $doing_noscript = false;   // generating noscript image tag

	function __construct($args) {
		if (array_key_exists('url',$args))
			$this->url = $args['url'];
		else if (
			array_key_exists('image_id',$args) &&
			is_numeric($args['image_id'])
		)
			$this->image_id = $args['image_id'];
		else
			return false;

		foreach ($args as $k => $v)
			$this->$k = $v;
	}

	function __destruct() {
		if (true === $this->done_output) return;
		if (true === $this->echo) $this->display();
	}

	function output() {
		if (false !== $this->image_id)
			$generated = $noscript = $this->generate_from_id();
		else
			$generated = $noscript = $this->generate_from_url();

		$generated['class'][] = $noscript['class'][] = 'generated-img-tag';

		if (
			array_key_exists('srcset',$generated) // multiple sizes of image available
			&& (
				$this->lazy || // loading image lazily
				'auto' === $this->sizes // 'sizes' attribute is being set dynamically
			)
		) {
			$generated['data-sizes'] = $generated['sizes'];
			$generated['data-srcset'] = $generated['data-srcset-hires'] = $generated['srcset'];
			unset($generated['sizes'],$generated['srcset']);
		}

		if (array_key_exists('srcset-hires',$generated)) {
			$generated['data-srcset-hires'] = $generated['srcset-hires'];
			$generated['class'][] = 'srcset-hires';
			unset($generated['srcset-hires']);
		}

		if (true === $this->lazy) {
			if (!array_key_exists('data-srcset',$generated))
				$generated['data-src'] = $generated['src'];
			$generated['src'] = self::DATAURI;
		}

		if (true === $this->noscript) {
			$generated['class'][] = 'hide-if-no-js';

			if ('auto' === $this->sizes)
				unset($noscript['sizes'],$noscript['srcset']);

			if (array_key_exists('id',$noscript))
				$noscript['id'] .= '-noscript';
		}

		if (
			array_key_exists('data-src',$generated) ||
			array_key_exists('data-sizes',$generated)
		)
			$generated['class'][] = 'lazyload';

		if (true === $this->preload)
			$generated['class'][] = 'lazypreload';

		$output = $this->generate_output($generated);
		if (true === $this->noscript) $output .= '<noscript>' . $this->generate_output($noscript,true) . '</noscript>' . "\n";

		return $output;
	}

		function generate_output($attributes,$noscript = false) {
			$output[] = '<img';
			$output[] = 'src="' . $attributes['src'] . '"';

			if (array_key_exists('data-src',$attributes))
				$output[] = 'data-src="' . $attributes['data-src'] . '"';

			$output[] = (array_key_exists('id',$attributes) ? 'id="' . $attributes['id'] . '" ' : '') .
				'title="' . $attributes['title'] . '" ' .
				'alt="' . $attributes['alt'] . '"';

			$output[] = 'class="' . implode(' ',$attributes['class']) . '"';

			$output[] = 'width="' . $attributes['width'] . '" height="' . $attributes['height'] . '"' .
				(array_key_exists('data-sizes',$attributes) && 'auto' === $attributes['data-sizes'] ? ' data-sizes="' . $attributes['data-sizes'] . '"' : '');

			if (array_key_exists('data-sizes',$attributes) && 'auto' === $attributes['data-sizes'])
				unset($attributes['data-sizes']);

			$sep = ", \n\t\t" . (true === $noscript ? "\t" : '');
			foreach (array(
				'data-srcset-hires',
				'data-srcset',
				'data-sizes',
				'srcset',
				'sizes'
			) as $var)
				if (array_key_exists($var,$attributes))
					$output[] = $var . '="' . implode($sep,$attributes[$var]) . '"';

			$sep = " \n\t" . (true === $noscript ? "\t" : '');
			return "\n" . (true === $noscript ? "\t" : '') . implode($sep,$output) . " \n" . (true === $noscript ? "\t" : '') . '/>' . "\n";
		}

	function display() { echo $this->output(); }

	function generate_from_id() {
		if (false === $this->image)
			$this->image = get_post($this->image_id);

		if (false === $this->image_sizes)
			$this->image_sizes = array('full');
		else if (!is_array($this->image_sizes))
			$this->image_sizes = (array) $this->image_sizes;

		if (is_array($this->image_sizes) && 0 === count($this->image_sizes))
			$this->image_sizes = array('full','large','medium','thumbnail');

		if (0 === count($this->first)) {
			$this->first = wp_get_attachment_image_src($this->image_id,$this->image_sizes[0]);
			$this->width = $this->first[1];
			$this->height = $this->first[2];
		}

		if (false === $this->url)
			$this->url = $this->first[0];

		$attributes = array(
			'src'        => $this->first[0],
			'title'      => false === $this->title ? get_the_title($this->image) : $this->title,
			'alt'        => false === $this->alt ? $this->image->post_content : $this->alt,
			'width'      => $this->first[1],
			'height'     => $this->first[2],
			'class'      => array(
							'attachment-' . $this->image_id,
							'attachment-' . $this->image_sizes[0],
						)
		);
		if (false === $this->alt)
			$attributes['alt'] = ('' === $this->image->post_content ? $attributes['title'] : apply_filters('get_the_content',strip_tags($this->image->post_content)));

		if (
			1 < count($this->image_sizes)            // multiple image sizes for 'srcset'
			&& (
				false === $this->doing_noscript ||   // not creating noscript image tag
				'auto' !== $this->sizes              // creating noscript image tag, but 'sizes' tag is not dynamic
			)
		) {

			$this->files['__largest'] = array(
				'width' => 0,
				'url' => self::DATAURI,
			);

			$this->files['__smallest'] = array(
				'width' => 9999999,
				'url' => self::DATAURI,
			);

			if (true === $this->image_hires_sizes)
				$this->image_hires_sizes = array('medium','medium_large','large','full');

			foreach (array(
				'srcset' => 'image_sizes',
				'srcset-hires' => 'image_hires_sizes',
			) as $attribute => $var)
				if (is_array($this->{$var}) && count($this->{$var}))
					foreach ($this->{$var} as $k => $size) {
						if ('full' !== $size && false === image_get_intermediate_size($this->image_id,$size)) {
							unset($this->{$var}[$k]);
							continue;
						}

						$attributes['class'][] = 'size-' . $size;

						if (array_key_exists($size,$this->files)) {
							$this_size = $this->files[$size];
							$mq = $k;
							if (!is_string($k)) {
								$mq = $this_size[1];
								if ('srcset-hires' === $attribute)
									$mq = round($mq / 2);
								$mq .= 'w';
							}
							$attributes[$attribute][$this_size[1] . 'x' . $this_size[2]] = $this_size[0] . ' ' . $mq;
							unset($this_size);
							continue;
						}

						$src = $this->files[$size] = wp_get_attachment_image_src($this->image_id,$size);
						$mq = $k;
						if (!is_string($k)) {
							$mq = $src[1];
							if ('srcset-hires' === $attribute)
								$mq = round($mq / 2);
							$mq .= 'w';
						}
						$attributes[$attribute][$src[1] . 'x' . $src[2]] = $src[0] . ' ' . $mq;

						if (intval($src[1]) > $this->files['__largest']['width'])
							$this->files['__largest'] = array(
								'width' => intval($src[1]),
								'name' => $size,
								'src' => $src[0],
							);

						if (intval($src[1]) < $this->files['__smallest']['width'])
							$this->files['__smallest'] = array(
								'width' => intval($src[1]),
								'name' => $size,
								'src' => $src[0],
							);
					}

			if (array_key_exists('srcset',$attributes))
				$attributes['sizes'] = $this->sizes;

		}

		$attributes['class'] = array_unique(array_merge($attributes['class'],$this->classes));

		if (is_array($this->attributes) && count($this->attributes)) // merge passed attributes into tag
			$attributes = array_merge($attributes,$this->attributes);

		return $this->tag = $attributes;

	}

	function generate_from_url() {
		if (false === $this->image)
			$this->image = @getimagesize($this->url);

		$attributes = array(
			'src'        => $this->url,
			'title'      => false !== $this->title ? $this->title : '',
			'alt'        => false !== $this->alt ? $this->alt : '',
			'width'      => false !== $this->width ? $this->width : (false !== $this->image ? $this->image[0] : false),
			'height'     => false !== $this->height ? $this->height : (false !== $this->image ? $this->image[1] : false),
			'class'		 => $this->classes,
		);

		if (is_array($this->attributes) && count($this->attributes)) // merge passed attributes into tag
			$attributes = array_merge($attributes,$this->attributes);

		return $attributes;
	}

}

?>
