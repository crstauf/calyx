<?php

add_theme_support( 'woocommerce' );

define( 'THEME_IMAGES_PATH', get_stylesheet_directory() . '/' );
define( 'THEME_IMAGES_URL', get_stylesheet_directory_uri() . '/' );

function my_notice() {
  $one = $two;
}

if ( function_exists( 'QMX_Dump' ) )
    QMX_Dump($_SERVER,'$_SERVER');

@my_notice();

//define('QM_HIDE_CORE_HOOKS',true);
//define('QMX_HIDE_CORE_FILES',true);

add_image_size('test',250,250,true);
add_image_size('small',151,151,true);
add_image_size('prime',311,281,true);
add_image_size('large_prime',1039,1033);
add_image_size('height_prime',1035,1033);
add_image_size('width_prime',1039,1035);
add_image_size('large_ratio',600,20);

// $http = new WP_HTTP;
// $http->request('127.0.0.6');
//
// $http = new WP_HTTP;
// $http->request('127.0.0.8',array(
//     'timeout' => 2,
// ));
//
// $http = new WP_HTTP;
// $http->request('127.0.0.3',array(
//     'timeout' => 9,
// ));

// $wpdb->query("SELECT SLEEP(6)");
// $wpdb->query("SELECT SLEEP(1)");
// $wpdb->query("SELECT SLEEP(0.06)");
// $wpdb->query("SELECT SLEEP(3)");
// $wpdb->query("SELECT SLEEP(2.5)");

// $wpdb->query("SELECT * FROM wp_comments WHERE AND comment_parent IN (1) ORDER BY comment_date_gmt ASC, comment_ID ASC");

// include 'non-existent.php';

//new image_tag(array('attachment_id' => 8));

$sizes = array(
    'jeep_type'				=> array(270,170,true,	array('post_type' => 'product_cat')),

    /* slider */
    'slide'					=> array(1140,346,true,	array('post_type' => 'slider')),
    'slide_940'				=> array(940,280,true),
    'slide_720'				=> array(720,220,true),

    /* testimonials */
    'testy'					=> array(370,245,true,	array('post_type' => 'testimonial')),
    'testy_medium'			=> array(560,370,true),
    'testy_large'			=> array(678,449,true),

    /* products */
    'shop_thumbnail'		=> array(270,153,true),
    'shop_catalog'			=> array(360,225,true),
    'shop_single'			=> array(1140,346,true),
    'shop_catalog_large'	=> array(720,451,true),
    'shop_single_laptop'	=> array(940,280,true),
    'shop_single_tablet'	=> array(720,220,true),
    'shop_single_mobile'	=> array(750,500,true),

    'swatches_image_size'	=> array(32,32,true,	array('post_type' => 'product_swatch'))
);

if (function_exists('add_featured_image_size'))
    foreach ($sizes as $size => $details) {
        if (4 === count($details)) $criteria = $details[3];
        add_featured_image_size($details[0],$details[1],$details[2],$size,$criteria);
    }

foreach (array(
    array( 'name'=>'jeep_type',		'width'=>270, 'height'=>170,'crop'=>true ), // Thumbnails used on Choose Your Jeep drop-down
    array( 'name'=>'admin_thumb',	'width'=>100, 'height'=>75,	'crop'=>true ), // Thumbnails used on CPT admin pages
    array( 'name'=>'slide',			'width'=>1140,'height'=>346,'crop'=>true ), // Slider background images
    array( 'name'=>'slide_thumb',	'width'=>300, 'height'=>89,	'crop'=>true ), // Slider background thumb
    array( 'name'=>'testy', 		'width'=>370, 'height'=>245,'crop'=>true ), // Testimonial images
) as $image_size )
    add_image_size( $image_size['name'], $image_size['width'], $image_size['height'], $image_size['crop'] );

// add_action('init','dev_init');
function dev_init() {
    if (defined('DOING_AJAX') && DOING_AJAX) return;
    set_transient('diff',time(),45);
    set_transient('first',time());
    set_transient('fast',time(),5);
    set_transient('fast-same','value never changes',5);
    set_transient('same','value never changes',3000);
    global $wp_scripts,$wp_styles;
    wp_enqueue_script('test','http://www.google.com/jquery.js');
    echo '<style>#qm { display: block !important; } .screen-reader-text { display: none; }</style>';
    // include 'non-existent.php';
}

add_filter('heartbeat_settings','calyx_heartbeat_settings');
function calyx_heartbeat_settings($settings) {
    $settings['interval'] = 15;
    return $settings;
}

if ( 0 && function_exists( 'image_tag' ) ) {
// echo image_tag( 177, array() );
// echo image_tag( 'screenshot.png', array( 'attributes' => array( 'width' => '150px', 'height' => 'auto' ) ) );
echo image_tag( 'http://wpdev.dev/wp-content/uploads/2017/01/ra6vbivis2y-thomas-tixtaaz-150x150.jpg' );
}

add_action( 'init', 'monq_implement_degraded_password_strength' );
function monq_implement_degraded_password_strength() {
	wp_add_inline_script( 'password-strength-meter', monq_degraded_password_strength_meter() );
}

function monq_degraded_password_strength_meter() {
    ob_start();
    ?>

    wp.passwordStrength.meter = function( password1, blacklist, password2 ) {

        if ( ! jQuery.isArray( blacklist ) )
            blacklist = [ blacklist.toString() ];

        if (password1 != password2 && password2 && password2.length > 0)
            return 5;

		if ( 'undefined' === typeof window.zxcvbn ) {
			// Password strength unknown.
			return -1;
		}

		var result = zxcvbn( password1, blacklist );
        var score = result.score;

		if ( 1 > score )
	        if (
    	        password1.length < 8
    	        && password1.match( /[A-Z]/ )
    	        && password1.match( /[0-9]/ )
    	        && password1.match( /[?\-\.*^#@!\(\)+\[\]%{}|"'\/\\:;$&<>~` ]/ )
    	    )
    	        score = 1;

        return score;

    };

    <?php
    return ob_get_clean();
}

?>
