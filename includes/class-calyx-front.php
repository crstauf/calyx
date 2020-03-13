<?php
/**
 * Actions, filters, and functions for frontend.
 */

defined( 'ABSPATH' ) || die();

/**
 * Class: Calyx_Front
 */
abstract class Calyx_Front {

	/**
	 * @var null|self
	 */
	protected static $_instance = null;

	/**
	 * Get instance.
	 *
	 * @return self
	 */
	static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self;

		return self::$_instance;
	}

	/**
	 * Initialize.
	 *
	 * @uses static::instance()
	 */
	static function init() {
		static::instance();
	}

	/**
	 * Construct.
	 *
	 * - register hooks
	 */
	protected function __construct() {

		add_action( 'wp_head',                 array( $this, 'action__wp_head' ), 5 );
		add_action( 'login_enqueue_scripts',   array( $this, 'action__login_enqueue_scripts' ) );
		add_action( 'wp_print_footer_scripts', array( $this, 'action__wp_print_footer_scripts' ) );

		add_filter( 'body_class',  array( $this, 'filter__body_class'  ) );
		add_filter( 'post_class',  array( $this, 'filter__post_class'  ), 10, 3 );
		add_filter( 'the_content', array( $this, 'filter__the_content' ) );

	}


	/*
	   ###     ######  ######## ####  #######  ##    ##  ######
	  ## ##   ##    ##    ##     ##  ##     ## ###   ## ##    ##
	 ##   ##  ##          ##     ##  ##     ## ####  ## ##
	##     ## ##          ##     ##  ##     ## ## ## ##  ######
	######### ##          ##     ##  ##     ## ##  ####       ##
	##     ## ##    ##    ##     ##  ##     ## ##   ### ##    ##
	##     ##  ######     ##    ####  #######  ##    ##  ######
	*/

	/**
	 * Action: wp_head
	 *
	 * - add JS class adjustment
	 * - add head meta and link tags
	 */
	function action__wp_head() {
		?>

		<script type="text/javascript">document.documentElement.className = document.documentElement.className.replace( 'no-js', 'js' );</script>

		<meta charset="<?php bloginfo( 'charset' ) ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
		<link rel="profile" href="http://gmpg.org/xfn/11" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ) ?>" />

		<?php
	}

	/**
	 * Action: login_enqueue_scripts
	 *
	 * - enqueue login page stylesheet
	 */
	function action__login_enqueue_scripts() {

		wp_enqueue_style( THEME_PREFIX . '/login' );

	}

	/**
	 * Action: wp_print_footer_scripts
	 *
	 * - label broken links
	 * - label links that should be buttons
	 */
	function action__wp_print_footer_scripts() {
		if (
			   !current_user_can( 'edit_post', get_queried_object_id() )
			&& !WP_DEVELOP
		)
			return;

		$selectors = apply_filters( THEME_PREFIX . '/empty-link-selectors', array(
			'a[href=""]',
			'a[href="#"]',
			'a:not( [href] )',
			'a[href$="google.com"]',
			'a[href^="javascript"]',
		) );
		?>

		<style type="text/css" id="empty-link-identifier">
			<?php echo implode( ', ', $selectors ) ?> {
				position: relative;
				counter-increment: calyx-broken-link;
			}

			<?php echo implode( '::before, ', $selectors ) . '::before' ?> {
				content: 'Empty link';
				position: absolute;
				left: 0;
				top: 0;
				z-index: 2;
				padding: 8px 15px;
				background-color: #f00;
				transform: rotate( -20deg ) translate( -10%, -10% );
				text-transform: uppercase;
				font-family: sans-serif;
				pointer-events: none;
				white-space: nowrap;
				letter-spacing: 2px;
				font-weight: 600;
				font-size: 9px !important;
				color: #FFF;
				opacity: 1;

				-webkit-transition: opacity 0.2s;
				        transition: opacity 0.2s;

				-webkit-box-shadow: 2px 2px 5px 0px rgba( 0, 0, 0, 0.5 );
				   -moz-box-shadow: 2px 2px 5px 0px rgba( 0, 0, 0, 0.5 );
				        box-shadow: 2px 2px 5px 0px rgba( 0, 0, 0, 0.5 );
			}

				a[href^="javascript"]::before {
					content: '[ [ MAKE A <BUTTON> ] ]';
					background-color: #f90;
				}

				<?php echo implode( ':hover::before, ', $selectors ) . ':hover::before' ?> {
					opacity: 0;
				}
		</style>

		<?php
	}


	/*
	######## #### ##       ######## ######## ########   ######
	##        ##  ##          ##    ##       ##     ## ##    ##
	##        ##  ##          ##    ##       ##     ## ##
	######    ##  ##          ##    ######   ########   ######
	##        ##  ##          ##    ##       ##   ##         ##
	##        ##  ##          ##    ##       ##    ##  ##    ##
	##       #### ########    ##    ######## ##     ##  ######
	*/

	/**
	 * Filter: body_class
	 *
	 * - add post thumbnail classes (set and ID)
	 * - add user role class
	 *
	 * @param array $classes
	 * @return array
	 */
	function filter__body_class( $classes ) {
		if (
			is_singular()
			&& has_post_thumbnail( get_queried_object_id() )
		) {
			$classes[] = 'has-post-thumbnail';
			$classes[] = 'post-thumbnail-' . get_post_thumbnail_id( get_queried_object_id() );
		}

		if ( is_user_logged_in() )
			foreach ( wp_get_current_user()->roles as $user_role )
				$classes[] = 'user-' . $user_role;

		return $classes;
	}

	/**
	 * Filter: post_class
	 *
	 * - add post thumbnail ID class
	 *
	 * @param array $classes
	 * @param array $class
	 * @param int $post_id
	 * @return array
	 */
	function filter__post_class( $classes, $class, $post_id ) {
		if ( has_post_thumbnail( $post_id ) ) {
			$classes[] = 'has-post-thumbnail';
			$classes[] = 'post-thumbnail-' . get_post_thumbnail_id( $post_id );
		}

		return $classes;
	}

	/**
	 * Filter: the_content
	 *
	 * - add headline anchors
	 *
	 * @param string $content
	 * @return string
	 */
	function filter__the_content( $content ) {
		return preg_replace_callback(
			"/<\/h[0-9].*>/",
			function( $matches ) {
				static $_count = 1;

				$output = '<i id="section-' . $_count . '" aria-hidden="true"></i>';

				if ( current_user_can( 'edit_post', get_queried_object_id() ) )
					$output .= '<a class="headline-anchor dashicons dashicons-admin-links" href="#section-' . $_count++ . '" style="font-size: 12px; vertical-align: baseline;"></a>';

				return $output . $matches[0];
			},
			$content
		);
	}

}

add_action( THEME_PREFIX . '/init', function() {
	Calyx_Front::init();
} );

?>
