<?php

class enhance_wp_enqueues {

	public static $tags = array();
	public static $grouping = '';

	public static function tag($tag,$handle) {
		$return = '';

		if (false !== stripos($tag,'script')) {
			global $wp_scripts;

			$obj = $wp_scripts->registered[$handle];

			if (isset($obj->extra['async']) && true === $obj->extra['async'])
				$tag = str_replace('></script>',' async></script>',$tag);

			if (isset($obj->extra['defer']) && true === $obj->extra['defer'])
				$tag = str_replace('></script>',' defer></script>',$tag);

			if (count(self::$tags) && (!isset($obj->extra['conditional']) || $obj->extra['conditional'] !== self::$grouping)) {
				$tags = self::$tags;
				$grouping = self::$grouping;
				self::$tags = array();
				self::$grouping = '';
				$return = '<!--[if ' . $grouping . ']>' . "\n\t" . implode("\n\t",$tags) . "\n" . '<![endif]-->' . "\n";
			}

			if (!isset($obj->extra['conditional'])) return $return . $tag;

			self::$tags[] = trim(str_replace('<!--[if ' . $obj->extra['conditional'] . ']>','',str_replace('<![endif]-->','',$tag)));
			self::$grouping = $obj->extra['conditional'];

			return $return;

		} else if (false !== stripos($tag,'link')) {
			global $wp_styles;

			$obj = $wp_styles->registered[$handle];

			if (
				count(self::$tags) &&
				(!isset($obj->extra['conditional']) || $obj->extra['conditional'] !== self::$grouping) &&
				('noscript' !== self::$grouping || !isset($obj->extra['noscript']) || false === $obj->extra['noscript'])
			) {
				$tags = self::$tags;
				$grouping = self::$grouping;
				self::$tags = array();
				self::$grouping = '';
				if ('noscript' == $grouping)
					$return = '<noscript>' . "\n\t" . implode("\n\t",$tags) . '</noscript>' . "\n";
				else
					$return = '<!--[if ' . $grouping . ']>' . "\n\t" . implode("\n\t",$tags) . "\n" . '<![endif]-->' . "\n";
			}

			if (
				(isset($obj->extra['conditional']) && isset($obj->extra['noscript'])) ||
				(
					(!isset($obj->extra['conditional']) || !$obj->extra['conditional']) &&
					!isset($obj->extra['noscript'])
				)
			)
				return $return . $tag;

			if (isset($obj->extra['conditional'])) {
				self::$tags[] = trim(str_replace('<!--[if ' . $obj->extra['conditional'] . ']>','',str_replace('<![endif]-->','',$tag)));
				self::$grouping = $obj->extra['conditional'];
			} else if (isset($obj->extra['noscript'])) {
				self::$tags[] = trim($tag);
				self::$grouping = 'noscript';
			}

			return $return;
		}

		return $tag;
	}

	public static function tags() {

		if (count(self::$tags)) {
			$tags = self::$tags;
			$grouping = self::$grouping;
			self::$tags = array();
			self::$grouping = '';

			if ('noscript' == $grouping)
				echo '<noscript>' . "\n\t" . implode("\n\t",$tags) . "\n" . '</noscript>' . "\n";
			else
				echo '<!--[if ' . $grouping . ']>' . "\n\t" . implode("\n\t",$tags) . "\n" . '<![endif]-->' . "\n";
		}

	}
}

?>
