<?php
/**
 * Load the theme.
 *
 * @package calyx
 */

defined( 'ABSPATH' ) || die();


/*
 ######  ######## ######## ##     ## ########
##    ## ##          ##    ##     ## ##     ##
##       ##          ##    ##     ## ##     ##
 ######  ######      ##    ##     ## ########
      ## ##          ##    ##     ## ##
##    ## ##          ##    ##     ## ##
 ######  ########    ##     #######  ##
*/

defined( 'THEME_PREFIX'   ) || define( 'THEME_PREFIX', 'calyx' );
defined( 'THEME_ABSPATH'  ) || define( 'THEME_ABSPATH',  trailingslashit( __DIR__ ) );
defined( 'THEME_INCLUDES' ) || define( 'THEME_INCLUDES', THEME_ABSPATH . 'includes/' );

add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script' ) );
add_theme_support( 'title-tag' );
add_theme_support( 'post-thumbnails' );

register_nav_menus( array(
	'primary' => 'Primary',
) );


/*
######## ##     ## ##    ##  ######  ######## ####  #######  ##    ##  ######
##       ##     ## ###   ## ##    ##    ##     ##  ##     ## ###   ## ##    ##
##       ##     ## ####  ## ##          ##     ##  ##     ## ####  ## ##
######   ##     ## ## ## ## ##          ##     ##  ##     ## ## ## ##  ######
##       ##     ## ##  #### ##          ##     ##  ##     ## ##  ####       ##
##       ##     ## ##   ### ##    ##    ##     ##  ##     ## ##   ### ##    ##
##        #######  ##    ##  ######     ##    ####  #######  ##    ##  ######
*/

/**
 * Define empty helpers to gracefully handle missing plugins.
 *
 * @link https://github.com/crstauf/enhance-assets Enhance Assets
 * @link https://gist.github.com/crstauf/9a2f412e48c6630e6de945bd1d0e9e53 CSSLLC Subresource Integrity
 * @link https://gist.github.com/crstauf/46a29f046cfffcaf2829401ae0773c90 CSSLLC WordPress Helpers
 */
if ( !function_exists( 'enhance_asset'  ) ) { function enhance_asset(  string $handle, string $enhancement, $args = array(), bool $is_script = true ) {} }
if ( !function_exists( 'enhance_script' ) ) { function enhance_script( string $handle, string $enhancement, array $args = array() ) {} }
if ( !function_exists( 'enhance_style'  ) ) { function enhance_style(  string $handle, string $enhancement, array $args = array() ) {} }
if ( !function_exists( 'wp_set_script_sri' ) ) { function wp_set_script_sri( string $handle, string $hash ) {} }
if ( !function_exists( 'wp_set_style_sri'  ) ) { function wp_set_style_sri(  string $handle, string $hash ) {} }
if ( !function_exists( 'prerender' ) ) { function prerender( $prerender_urls ) {} }

/**
 * Create image tag with fallbacks.
 *
 * @param int $image_id
 * @param array $attributes
 * @param array $settings
 * @return Image_Tag
 */
function create_image_tag( int $image_id, array $attributes = array(), array $settings = array() ) : Image_Tag {
	if ( !empty( $image_id ) )
		$image = Image_Tag::create( $image_id, $attributes, $settings );
	
	if (
		!empty( $image )
		&& $image->is_valid()
	)
		return $image;
		
	if ( current_user_can( 'edit_post', get_queried_object_id() ) )
		return Image_Tag::create( 'placeholder', $attributes, $settings );
	
	if ( 'production' !== wp_get_environment_type() )
		return Image_Tag::create( 'picsum', $attributes, $settings );

	return new Image_Tag;
}


/*
#### ##    ##  ######  ##       ##     ## ########  ########  ######
 ##  ###   ## ##    ## ##       ##     ## ##     ## ##       ##    ##
 ##  ####  ## ##       ##       ##     ## ##     ## ##       ##
 ##  ## ## ## ##       ##       ##     ## ##     ## ######    ######
 ##  ##  #### ##       ##       ##     ## ##     ## ##             ##
 ##  ##   ### ##    ## ##       ##     ## ##     ## ##       ##    ##
#### ##    ##  ######  ########  #######  ########  ########  ######
*/

require_once THEME_INCLUDES . 'class-calyx.php';


/*
#### ##    ## #### ########
 ##  ###   ##  ##     ##
 ##  ####  ##  ##     ##
 ##  ## ## ##  ##     ##
 ##  ##  ####  ##     ##
 ##  ##   ###  ##     ##
#### ##    ## ####    ##
*/

Calyx::instance();
do_action( THEME_PREFIX . '/after_init' );

?>
