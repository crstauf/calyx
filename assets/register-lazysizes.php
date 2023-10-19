<?php declare( strict_types=1 );
/**
 * Register scripts for Lazysizes.
 *
 * Lazy loads images, and other elements.
 *
 * @link https://github.com/aFarkas/lazysizes GitHub repository.
 */

namespace Calyx;

defined( 'WPINC' ) || die();
defined( 'CONCATENATE_SCRIPTS' ) || define( 'CONCATENATE_SCRIPTS', ! constant( 'WP_DEBUG' ) || ! constant( 'SCRIPT_DEBUG' ) );

/**
 * Lazysizes core.
 */
wp_register_script( 'lazysizes/core', 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js', array(), '5.3.2' );
wp_set_script_sri( 'lazysizes/core', 'sha512-q583ppKrCRc7N5O0n2nzUiJ+suUv7Et1JGels4bXOaMFQcamPk9HjdUknZuuFjBNs7tsMuadge5k9RzdmO+1GQ==' );

/**
 * Lazysizes plugin: polyfill for fitting content to parent.
 */
wp_register_script( 'lazysizes/parent-fit', 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/plugins/parent-fit/ls.parent-fit.min.js', array(), '5.3.2' );
wp_set_script_sri( 'lazysizes/parent-fit', 'sha512-1oXBldvRhlG5dHYmpmBFccqjN+ncdNSs6uwLtxiOufvBQy4Or63PsXibQSuokBUcY8SN7eQ3uJ4SqPM+E4xcFQ==' );

/**
 * Lazysizes plugin: polyfill for CSS object-fit property.
 */
wp_register_script( 'lazysizes/object-fit', 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/plugins/object-fit/ls.object-fit.min.js', array(), '5.3.2' );
wp_set_script_sri( 'lazysizes/object-fit', 'sha512-uq8vhRSzhuN8xiniPi20LTGnDZs2UumLLjBHgwfAZnDtS4C/tNCqvr/ZZ4mzkt7BIKe1HB/O1o4zfiu5GX1S9g==' );

/**
 * Lazysizes plugin: unveil hooks.
 */
wp_register_script( 'lazysizes/unveilhooks', 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/plugins/unveilhooks/ls.unveilhooks.min.js', array(), '5.3.2' );
wp_set_script_sri( 'lazysizes/unveilhooks', 'sha512-hQ7LIAYhD17CZh6bDzdQI7NThUHmZGcAbGDfCWHO/sOEPRAdlkQFg4gTsKhWWbI1PMUvjD7JjA+5x3pH23Bnyg==' );

/**
 * Lazysizes core and plugins concatenated.
 */
wp_register_script( 'lazysizes', false, array(
	'lazysizes/parent-fit',
	// 'lazysizes/object-fit',
	// 'lazysizes/unveilhooks',
	'lazysizes/core',
), '5.3.2' );

/**
 * Defer the scripts.
 */
wp_enhance_script( 'lazysizes/parent-fit', 'defer' );
wp_enhance_script( 'lazysizes/object-fit', 'defer' );
wp_enhance_script( 'lazysizes/unveilhooks', 'defer' );
wp_enhance_script( 'lazysizes/core', 'defer' );

/**
 * Setup the config.
 */
wp_add_inline_script( 'lazysizes/parent-fit', 'window.lazySizesConfig = window.lazySizesConfig || {};', 'before' );
wp_add_inline_script( 'lazysizes', 'window.lazySizesConfig = window.lazySizesConfig || {};', 'before' );
