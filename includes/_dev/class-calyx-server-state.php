<?php
/**
 * Functionality to mark a dev install as dirtied (compared to production).
 *
 * @link https://gist.github.com/crstauf/70c983893cf88ed51d887cec4672e3af GitHub.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Class.
 */
class CSSLLC_ChangeIndicator {

	/** @var string */
	const VERSION = '1.0';

	/**
	 * Construct.
	 */
	protected function __construct() {

		add_action( 'wp_ajax_cssllc-changeindicator-set_dirty', array( &$this, 'ajax__set_dirty' ) );

		add_action( 'admin_bar_init', array( &$this, 'action__admin_bar_init' ) );
		add_action( 'admin_bar_menu', array( &$this, 'action__admin_bar_menu' ), 0 );

	}

	/**
	 * Initialize.
	 */
	public static function init() {
		static $_once = false;

		if ( $_once )
			return;

		new static;
		$_once = true;
	}

	/**
	 * Action: wp_ajax_cssllc-changeindicator-set_dirty
	 *
	 * @uses $this::set_dirty()
	 */
	function ajax__set_dirty() {
		wp_send_json( self::set_dirty() );
	}

	/**
	 * Set site as dirty.
	 *
	 * @uses $this::_set_option()
	 *
	 * @return bool
	 */
	public static function set_dirty() {
		return self::_set_option();
	}

	/**
	 * Check if site is set as dirty.
	 *
	 * @uses $this::_get_option()
	 *
	 * @return bool
	 */
	public static function is_dirty() {
		return !empty( self::_get_option() );
	}

	/**
	 * Get timestamp when site was set dirty.
	 *
	 * @uses $this::_get_option()
	 *
	 * @return int
	 */
	public static function get_dirty_timestamp() {
		$option = self::_get_option();
		return $option['time'];
	}

	/**
	 * Set site option.
	 *
	 * @return bool
	 */
	protected static function _set_option() {
		$array = array(
			'time' => time(),
			'user' => get_current_user_id()
		);

		return wp_using_ext_object_cache()
			? add_option(    __CLASS__ . '__dirty', $array )
			: set_transient( __CLASS__ . '__dirty', $array );
	}

	/**
	 * Get site option.
	 *
	 * @return false|array
	 */
	protected static function _get_option() {
		return wp_using_ext_object_cache()
			? get_option(    __CLASS__ . '__dirty' )
			: get_transient( __CLASS__ . '__dirty' );
	}

	/**
	 * Action: admin_bar_init
	 *
	 * @uses $this::get_css()
	 * @uses $this::get_js()
	 */
	function action__admin_bar_init() {
		$script_handle = 'jquery-core';

		if ( wp_script_is( $script_handle, 'done' ) )
			$script_handle = 'admin-bar';
		else if ( !wp_script_is( $script_handle ) )
			wp_enqueue_script( 'jquery' );

		wp_add_inline_style(  'admin-bar', $this->get_css() );
		wp_add_inline_script( $script_handle, $this->get_js()  );
	}

	/**
	 * Action: admin_bar_menu
	 *
	 * @param WP_Admin_Bar $bar
	 *
	 * @uses $this::_get_html()
	 */
	function action__admin_bar_menu( $bar ) {

		$bar->add_menu( array(
			'id' => 'cssllc-change-indicator',
			'title' => $this->_get_html(),
			'parent' => 'top-secondary',
		) );

	}

	/**
	 * Get HTML for admin bar menu.
	 *
	 * @uses $this::is_dirty()
	 *
	 * @return string
	 */
	protected function _get_html() {
		$output  = CSSLLC_ChangeIndicator::is_dirty() ? 'Dirty' : 'Clean';
		$output .= '<span id="dirty-server-state"' . ( CSSLLC_ChangeIndicator::is_dirty() ? ' class="active"' : '' ) . '>Dirty</span>';

		return $output;
	}

	/**
	 * Get styles for admin bar menu.
	 *
	 * @return string
	 */
	function get_css() {
		ob_start();
		?>

		#wpadminbar ul li#wp-admin-bar-cssllc-change-indicator {
			position: relative;
		}

			body:not( .wp-admin ) li#wp-admin-bar-cssllc-change-indicator,
			body.admin-color-fresh li#wp-admin-bar-cssllc-change-indicator     { --bgClean: #0073aa; --bgDirty: #00a0d2; }
			body.admin-color-light li#wp-admin-bar-cssllc-change-indicator     { --bgClean:    #999; --bgDirty: #d64e07; }
			body.admin-color-blue li#wp-admin-bar-cssllc-change-indicator      { --bgClean: #4796b3; --bgDirty: #096484; }
			body.admin-color-coffee li#wp-admin-bar-cssllc-change-indicator    { --bgClean: #46403c; --bgDirty: #9ea476; }
			body.admin-color-ectoplasm li#wp-admin-bar-cssllc-change-indicator { --bgClean: #413256; --bgDirty: #d46f15; }
			body.admin-color-midnight li#wp-admin-bar-cssllc-change-indicator  { --bgClean: #25282b; --bgDirty: #e14d43; }
			body.admin-color-ocean li#wp-admin-bar-cssllc-change-indicator     { --bgClean: #627c83; --bgDirty: #aa9d88; }
			body.admin-color-sunrise li#wp-admin-bar-cssllc-change-indicator   { --bgClean: #b43c38; --bgDirty: #dd823b; }

		#wpadminbar ul li#wp-admin-bar-cssllc-change-indicator > .ab-item,
		#wpadminbar ul li#wp-admin-bar-cssllc-change-indicator:hover > .ab-item,
		#wpadminbar ul li#wp-admin-bar-cssllc-change-indicator > .ab-item::after {
			position: relative;
			background-color: #00a0d2;
			background-color: var( --bgClean );
			text-transform: uppercase;
			letter-spacing: 1px;
			font-size: 10px;
			color: #FFF;
		}

		#wpadminbar ul li#wp-admin-bar-cssllc-change-indicator > .ab-item::after {
			content: "Clean";
			position: absolute;
			left: 0;
			top: 0;
			z-index: 2;
			width: 100%;
			height: 100%;
			text-align: center;
		}

		#dirty-server-state {
			position: absolute;
			right: 0;
			top: 0;
			z-index: 1;
			width: 100%;
			height: 100%;
			background-color: #f00;
			background-color: var( --bgDirty );
			text-transform: uppercase;
			letter-spacing: inherit;
			white-space: nowrap;
			text-align: center;
			font-size: inherit;
			cursor: pointer;
			color: #FFF;

			-webkit-transition: transform 0.2s, background-color 0.2s linear;
					transition: transform 0.2s, background-color 0.2s linear;

			-webkit-border-bottom-left-radius: 5px;
			    -moz-border-radius-bottomleft: 5px;
			        border-bottom-left-radius: 5px;
		}

			#wpadminbar ul li#wp-admin-bar-cssllc-change-indicator:hover #dirty-server-state:not( .active ) {
				transform: translateY( 100% );
			}

			#dirty-server-state:not( .active ):hover {
				background-color: #f00;
			}

			#dirty-server-state.active {
				z-index: 3;
				cursor: not-allowed;

				-webkit-border-bottom-left-radius: 0;
				    -moz-border-radius-bottomleft: 0;
				        border-bottom-left-radius: 0;
			}

		<?php
		return ob_get_clean();
	}

	/**
	 * Get JavaScript for admin bar menu.
	 *
	 * @return string
	 */
	function get_js() {
		ob_start();
		?>

		jQuery( document ).on( 'click', '#dirty-server-state', function( ev ) {
			jQuery.get( ajaxurl, { action: "cssllc-changeindicator-set_dirty" } );
			jQuery( "#dirty-server-state" ).addClass( 'active' );
		} );

		<?php
		return ob_get_clean();
	}

}

CSSLLC_ChangeIndicator::init();

?>