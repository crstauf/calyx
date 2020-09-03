<?php
/**
 * Register scripts for Lazysizes.
 *
 * Lazy loads images, and other elements.
 *
 * @link https://github.com/aFarkas/lazysizes GitHub repository.
 */

defined( 'ABSPATH' ) || die();

/**
 * Lazysizes core.
 */
wp_register_script( 'lazysizes/core', 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.2.2/lazysizes.min.js', null, '5.2.2' );
wp_set_script_sri(  'lazysizes/core', 'sha512-TmDwFLhg3UA4ZG0Eb4MIyT1O1Mb+Oww5kFG0uHqXsdbyZz9DcvYQhKpGgNkamAI6h2lGGZq2X8ftOJvF/XjTUg==' );

/**
 * Lazysizes plugin: polyfill for fitting content to parent.
 */
wp_register_script( 'lazysizes/parent-fit', 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.2.2/plugins/parent-fit/ls.parent-fit.min.js', null, '5.2.2' );
wp_set_script_sri(  'lazysizes/parent-fit', 'sha512-AGFYzeoBdwBg55nfE9a0WFn1TW0RY169KZxocaa5ItravYcR/C4kPmdo2DNv+Lq9u+9TMzQQaY+YwDf43S2SDQ==' );

/**
 * Lazysizes plugin: polyfill for CSS object-fit property.
 */
wp_register_script( 'lazysizes/object-fit', 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.2.2/plugins/object-fit/ls.object-fit.min.js', null, '5.2.2' );
wp_set_script_sri(  'lazysizes/object-fit', 'sha512-7sz1GUGqkW8+40bj2SYo+5EGDq41XcZ3pAJSxgYsBalekwnTe5aSMa2S96adYXjpFH6+pSjj4jz1A+aWIP604Q==' );

/**
 * Lazysizes plugin: unveil hooks.
 */
wp_register_script( 'lazysizes/unveilhooks', 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.2.2/plugins/unveilhooks/ls.unveilhooks.min.js', null, '5.2.2' );
wp_set_script_sri(  'lazysizes/unveilhooks', 'sha512-FQ0MgvxcxFX4MSh8AWiQT+McTjZkTVrzEdi4Gv5j5/VhGRvO3HNoH/ZO4ruhZTKVXvZippdjoeXk+7bns6jfTQ==' );

/**
 * Group core and plugins together.
 */
wp_register_script( 'lazysizes', null, array(
	'lazysizes/parent-fit',
	'lazysizes/object-fit',
	'lazysizes/unveilhooks',
	'lazysizes/core',
), '5.2.0' );

wp_add_inline_script( 'lazysizes/parent-fit', 'window.lazySizesConfig = window.lazySizesConfig || {};', 'before' ); // setup the config

enhance_script( 'lazysizes/parent-fit',  'defer' );
enhance_script( 'lazysizes/object-fit',  'defer' );
enhance_script( 'lazysizes/unveilhooks', 'defer' );
enhance_script( 'lazysizes/core', 'defer' );

?>