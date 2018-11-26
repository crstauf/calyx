<?php
/**
 * Theme setup.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Theme helper class.
 */
final class Calyx {
	use Calyx_ManageFeatures, Calyx_ManageAPIs;

	/**
	 * @var Calyx_Data Helper for data.
	 */
	private $_data = null;

	/**
	 * @var Calyx_Admin Helper for admin.
	 */
	private $_admin = null;

	/**
	 * @var Calyx_Front Helper for frontend.
	 */
	private $_front = null;

	/**
	 * @var Calyx_Actions Helper for actions.
	 */
	private $_actions = null;

	/**
	 * @var Calyx_Filters Helper for filters.
	 */
	private $_filters = null;

	/**
	 * @var Calyx_Server Helper for server.
	 */
	private $_server = null;

	/**
	 * @var Calyx_Customizer Helper for WordPress Customizer.
	 */
	private $_customizer = null;

	/**
	 * @var Calyx_WooCommerce Helper for WooCommerce.
	 */
	private $_woocommerce = null;

	/**
	 * @var array Array of CPT helpers.
	 */
	private $_cpts = array();


	/*
	 ######   #######  ##    ##  ######  ######## ########  ##     ##  ######  ########
	##    ## ##     ## ###   ## ##    ##    ##    ##     ## ##     ## ##    ##    ##
	##       ##     ## ####  ## ##          ##    ##     ## ##     ## ##          ##
	##       ##     ## ## ## ##  ######     ##    ########  ##     ## ##          ##
	##       ##     ## ##  ####       ##    ##    ##   ##   ##     ## ##          ##
	##    ## ##     ## ##   ### ##    ##    ##    ##    ##  ##     ## ##    ##    ##
	 ######   #######  ##    ##  ######     ##    ##     ##  #######   ######     ##
	*/

	/**
	 * Construct.
	 */
	protected function __construct() {
		$this->register_hooks();
		$this->include_files();
		$this->initialize();
	}

	/**
	 * Get singleton instance.
	 */
	public static function get_instance() {
		static $_instance = null;

		if ( is_null( $_instance ) )
			$_instance = new self();

		return $_instance;
	}

	/** Prevent cloning. */
	function __clone() {}

	/**
	 * Register core action and filter hooks.
	 *
	 * Step 1 in construct.
	 * Next: include_files().
	 */
	protected function register_hooks() {

		add_action( THEME_PREFIX . '/apis/add',     array( &$this, 'add_api'     ), 10, 2 );
		add_action( THEME_PREFIX . '/cpts/add',     array( &$this, 'add_cpt'     ) );
		add_action( THEME_PREFIX . '/features/add', array( &$this, 'add_feature' ), 10, 2 );

	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * Step 2 in construct.
	 * Next: initialize().
	 */
	protected function include_files() {
		do_action( 'qm/start', THEME_PREFIX . '/' . __FUNCTION__ . '()' );

		/**
		 * Core files.
		 */
		require_once CALYX_ABSPATH . 'includes/constants.php';
		require_once CALYX_ABSPATH . 'temporary/global.php';
		require_once CALYX_ABSPATH . 'includes/utilities.php';

		( is_admin() && require_once CALYX_ABSPATH . 'includes/class-calyx-admin.php' )
		             || require_once CALYX_ABSPATH . 'includes/class-calyx-front.php';

		  WP_DEVELOP && include_once CALYX_ABSPATH . 'includes/_dev/dev-functions.php';

		/**
		 * Core abstract files.
		 */
		require_once CALYX_ABSPATH . 'includes/abstract-calyx-cpt.php';

		/**
		 * Core class files.
		 */
		require_once CALYX_ABSPATH . 'includes/class-calyx-data.php';
		require_once CALYX_ABSPATH . 'includes/class-calyx-server.php';
		require_once CALYX_ABSPATH . 'includes/class-calyx-actions.php';
		require_once CALYX_ABSPATH . 'includes/class-calyx-filters.php';
		include_once CALYX_ABSPATH . 'includes/class-calyx-customizer.php';
		require_once CALYX_ABSPATH . 'includes/class-calyx-credentials.php';
		current_theme_supports( 'woocommerce' ) && class_exists( 'WooCommerce' ) && include_once CALYX_ABSPATH . 'includes/class-calyx-woocommerce.php';

		/**
		 * Enhancements class files.
		 */
		require_once CALYX_ABSPATH . 'includes/class-image-tag.php';
		require_once CALYX_ABSPATH . 'includes/class-calyx-compress.php';

		do_action( 'qm/lap', THEME_PREFIX . '/' . __FUNCTION__ . '()', 'core' );
		do_action( THEME_PREFIX . '/include_files/after_core' );

		/**
		 * Custom post type files.
		 */

		do_action( 'qm/lap', THEME_PREFIX . '/' . __FUNCTION__ . '()', 'cpts' );
		do_action( THEME_PREFIX . '/include_files/after_cpts' );

		/**
		 * Frontend and backend feature files.
		 *
		 * Features limited to one side should be included in the appropriate class's method, i.e.: Calyx_Front::include_files().
		 */


		do_action( 'qm/lap', THEME_PREFIX . '/' . __FUNCTION__ . '()', 'features' );

		do_action( THEME_PREFIX . '/include_files' );

		do_action( 'qm/lap',  THEME_PREFIX . '/' . __FUNCTION__ . '()', 'others' );
		do_action( 'qm/stop', THEME_PREFIX . '/' . __FUNCTION__ . '()' );

		do_action( THEME_PREFIX . '/files_loaded' );
	}

	/**
	 * Initialize Calyx.
	 *
	 * Final step in construct.
	 */
	protected function initialize() {
		do_action( THEME_PREFIX . '/before_init' );

		$this->_credentials = Calyx_Credentials::create_instance();
		$this->_data        = Calyx_Data::create_instance();
		$this->_server      = Calyx_Server::create_instance();
		$this->_actions     = Calyx_Actions::create_instance();
		$this->_filters     = Calyx_Filters::create_instance();

		class_exists( 'Calyx_Admin' ) && $this->_admin = Calyx_Admin::create_instance();
		class_exists( 'Calyx_Front' ) && $this->_front = Calyx_Front::create_instance();

		class_exists( 'Calyx_Customizer'  ) && $this->_customizer  = Calyx_Customizer::create_instance();
		class_exists( 'Calyx_WooCommerce' ) && $this->_woocommerce = Calyx_WooCommerce::create_instance();

		do_action( THEME_PREFIX . '/init' );
		do_action( THEME_PREFIX . '/after_init' );
	}


	/*
	##      ## ########     ###    ########  ########  ######## ########   ######
	##  ##  ## ##     ##   ## ##   ##     ## ##     ## ##       ##     ## ##    ##
	##  ##  ## ##     ##  ##   ##  ##     ## ##     ## ##       ##     ## ##
	##  ##  ## ########  ##     ## ########  ########  ######   ########   ######
	##  ##  ## ##   ##   ######### ##        ##        ##       ##   ##         ##
	##  ##  ## ##    ##  ##     ## ##        ##        ##       ##    ##  ##    ##
	 ###  ###  ##     ## ##     ## ##        ##        ######## ##     ##  ######
	*/

	/**
	 * Public access to $_admin property.
	 *
	 * @param null|string $hook Quick access to actions or filters.
	 *
	 * @return Calyx_Admin|Calyx_Admin_Actions|Calyx_Admin_Filters
	 */
	function admin( $hook = null ) {
		return is_null( $hook )
			? $this->_admin
			: $this->_admin->$hook();
	}

	/**
	 * Public access to $_front property.
	 *
	 * @param null|string $hook Quick access to actions or filters.
	 *
	 * @return Calyx_Front|Calyx_Front_Actions|Calyx_Front_Filters
	 */
	function front( $hook = null ) {
		return is_null( $hook )
			? $this->_front
			: $this->_front->$hook();
	}

	/**
	 * Public access to $_actions property.
	 * @return Calyx_Actions
	 */
	function actions() { return $this->_actions; }

	/**
	 * Public access to $_filters property.
	 * @return Calyx_Filters
	 */
	function filters() { return $this->_filters; }

	/**
	 * Public access to $_server property.
	 * @return Calyx_Server
	 */
	function server() { return $this->_server; }

	/**
	 * Public access to $_woocommerce property.
	 * @return Calyx_WooCommerce
	 */
	function wc() { return $this->_woocommerce; }

	/**
	 * Alias.
	 * @uses $this::wc()
	 */
	function woocommerce() { return $this->wc(); }

	/**
	 * Get theme credential.
	 *
	 * @param string $key
	 * @param string $group
	 *
	 * @uses Calyx_Credentials::get()
	 *
	 * @return mixed
	 */
	function get_credential( $key, $group = null ) {
		return $this->_credentials->get( $key, $group );
	}


	/*
	 ######  ########  ########  ######
	##    ## ##     ##    ##    ##    ##
	##       ##     ##    ##    ##
	##       ########     ##     ######
	##       ##           ##          ##
	##    ## ##           ##    ##    ##
	 ######  ##           ##     ######
	*/

	/**
	 * Check if theme CPT exists.
	 *
	 * @param string $type Post type.
	 *
	 * @return bool
	 */
	function has_cpt( $type ) {
		return array_key_exists( $type, $this->_cpts );
	}

	/**
	 * Add theme CPT.
	 *
	 * @param _Calyx_CPT $object Post type object.
	 *
	 * @return bool Theme CPT exists or was registered.
	 */
	function add_cpt( _Calyx_CPT $object ) {
		      !$this->has_cpt( $object->get_type() ) && $this->_cpts[$object->get_type()] = $object;
		return $this->has_cpt( $object->get_type() );
	}

	/**
	 * Check if theme CPT exists.
	 *
	 * @param string $type Post type.
	 *
	 * @uses $this::has_cpt()
	 *
	 * @return null|_Calyx_CPT
	 */
	function get_cpt( $type ) {
		return $this->has_cpt( $type )
			? $this->_cpts[$type]
			: null;
	}


	/*
	 ######  ######## ########  ##     ## ######## ########
	##    ## ##       ##     ## ##     ## ##       ##     ##
	##       ##       ##     ## ##     ## ##       ##     ##
	 ######  ######   ########  ##     ## ######   ########
	      ## ##       ##   ##    ##   ##  ##       ##   ##
	##    ## ##       ##    ##    ## ##   ##       ##    ##
	 ######  ######## ##     ##    ###    ######## ##     ##
	*/

	/**
	 * Get template part.
	 *
	 * @param array $template_paths Array of template paths to check.
	 * @param array $args           Array of variables for template part to use.
	 *
	 * @see locate_template()
	 */
	function get_template_part( $template_paths, $args = array() ) {
		$template_part = locate_template( $template_paths );

		if ( !empty( $template_part ) ) {
			extract( $args );

			$template_part_slug = trim( str_replace( '.php', '', str_replace( array(
				get_stylesheet_directory(),
				get_template_directory(),
			), '', $template_part ) ) );

			$action_handle = 'get_template_part_' . ltrim( $template_part_slug, '/' );

			do_action( $action_handle, $template_part_slug, '' );

			require $template_part;
		}
	}

	/**
	 * Log wrong procedure.
	 *
	 * @param string $function Function used.
	 * @param string $message Message to log.
	 * @param string $version Version the message was added in.
	 */
	function doing_it_wrong( $function, $message, $version ) {
		if ( $this->doing_ajax() ) {
			do_action( 'doing_it_wrong_run', $function, $message, $version );
			error_log( "{$function} was called incorrectly. {$message}. This message was added in version {$version}." );
		} else {
			_doing_it_wrong( $function, $message, $version );
		}
	}

	/**
	 * Log use of deprecated function.
	 *
	 * @param string $function_name
	 * @param string $commit_hash
	 * @param string $replacement_function_name
	 *
	 * @uses $this::doing_ajax()
	 * @uses trim_commit_hash()
	 * @uses _deprecated_function()
	 */
	function deprecated_function( $function_name, $commit_hash, $replacement_function_name = '' ) {
		if ( $this->doing_ajax() ) {
			do_action( 'deprecated_function_run', $function_name, $replacement_function_name, $commit_hash );
			!is_null( $replacement_function_name )
				? error_log( sprintf( '%1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.', $function_name, $commit_hash, $replacement_function_name ) )
				: error_log( sprintf( '%1$s is <strong>deprecated</strong> since version %2$s with no alternative available.', $function_name, trim_commit_hash( $commit_hash ) ) );
		} else
			_deprecated_function( $function_name, $commit_hash, $replacement_function_name );
	}


	/*
	######## ##     ## ######## ##    ## ########  ######
	##       ##     ## ##       ###   ##    ##    ##    ##
	##       ##     ## ##       ####  ##    ##    ##
	######   ##     ## ######   ## ## ##    ##     ######
	##        ##   ##  ##       ##  ####    ##          ##
	##         ## ##   ##       ##   ###    ##    ##    ##
	########    ###    ######## ##    ##    ##     ######
	*/

	/**
	 * Check if doing AJAX.
	 *
	 * @return bool
	 */
	function doing_ajax() {
		return !!(
			wp_doing_ajax()
			|| (
				defined( 'WC_DOING_AJAX' )
				       && WC_DOING_AJAX
			)
			|| (
				isset( $_GET )
				&& array_key_exists( 'wc-ajax', $_GET )
			)
		);
	}

	/**
	 * Check if doing cron.
	 *
	 * @return bool
	 */
	function doing_cron() {
		return !!( defined( 'DOING_CRON' ) && DOING_CRON );
	}

	/**
	 * Check if doing autosave.
	 *
	 * @return bool
	 */
	function doing_autosave() {
		return !!( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE );
	}

	/**
	 * Check if Query Monitor disabled.
	 *
	 * @return bool
	 */
	function QM_disabled() {
		return !!( defined( 'QM_DISABLED' ) && QM_DISABLED );
	}

	/**
	 * Check if is REST request.
	 *
	 * @return bool
	 */
	 function doing_rest() {
		return !!(
			(
				defined( 'REST_REQUEST' )
				       && REST_REQUEST
			)
			|| (
				defined( 'WC_API_REQUEST' )
				       && WC_API_REQUEST
			)
		);
	}


	/*
	 ######   #######  ##    ## ########  #### ######## ####  #######  ##    ##    ###    ##        ######
	##    ## ##     ## ###   ## ##     ##  ##     ##     ##  ##     ## ###   ##   ## ##   ##       ##    ##
	##       ##     ## ####  ## ##     ##  ##     ##     ##  ##     ## ####  ##  ##   ##  ##       ##
	##       ##     ## ## ## ## ##     ##  ##     ##     ##  ##     ## ## ## ## ##     ## ##        ######
	##       ##     ## ##  #### ##     ##  ##     ##     ##  ##     ## ##  #### ######### ##             ##
	##    ## ##     ## ##   ### ##     ##  ##     ##     ##  ##     ## ##   ### ##     ## ##       ##    ##
	 ######   #######  ##    ## ########  ####    ##    ####  #######  ##    ## ##     ## ########  ######
	*/




	/*
	##     ## ######## ##    ## ########   #######  ########   ######
	##     ## ##       ###   ## ##     ## ##     ## ##     ## ##    ##
	##     ## ##       ####  ## ##     ## ##     ## ##     ## ##
	##     ## ######   ## ## ## ##     ## ##     ## ########   ######
	 ##   ##  ##       ##  #### ##     ## ##     ## ##   ##         ##
	  ## ##   ##       ##   ### ##     ## ##     ## ##    ##  ##    ##
	   ###    ######## ##    ## ########   #######  ##     ##  ######
	*/

	/**
	 * Register vendor (third-party) assets.
	 *
	 * @uses $this::_register_vendor_assets__lazysizes()
	 */
	function _register_vendor_assets() {

		/**
		 * Register webfontloader.
		 *
		 * @link https://github.com/typekit/webfontloader GitHub repository for webfontloader.
		 * @version 1.6.28
		 */
		wp_register_script( 'webfontloader', get_theme_file_uri( 'assets/js/webfontloader.min.js' ), array(), '1.6.28' );

		/**
		 * Register fontawesome.
		 *
		 * @link https://fontawesome.com/
		 * @version 5.3.1
		 */
		wp_register_style( 'fontawesome', get_theme_file_uri( 'assets/fonts/fontawesome.min.js' ), array(), '5.3.1' );

		$this->_register_vendor_assets__lazysizes();
	}

	/**
	 * Get settings for 'webfontloader' script.
	 *
	 * @return array
	 */
	function get_webfontloader_settings() {
		return array(
			'all' => array(
				'custom' => array(
					'families' => array( 'Font Awesome' ),
					'testStrings' => array( 'FontAwesome' => '\uf240\uf00c\uf000' ),
					'urls' => array(
						wp_styles()->_css_href(
							wp_styles()->registered['fontawesome']->src,
							wp_styles()->registered['fontawesome']->ver,
							'fontawesome'
						),
					),
				),
				'google' => array(
					'families' => array( 'Open Sans:300,400,400i,700' ),
				),
			),
			'header' => array(
				'google' => array(
					'families' => array( 'Open Sans:400' ),
				),
			),
			'footer' => array(
				'custom' => array(
					'families' => array( 'Font Awesome' ),
					'testStrings' => array( 'FontAwesome' => '\uf240\uf00c\uf000' ),
					'urls' => array(
						wp_styles()->_css_href(
							wp_styles()->registered['fontawesome']->src,
							wp_styles()->registered['fontawesome']->ver,
							'fontawesome'
						)
					),
				),
				'google' => array(
					'families' => array( 'Open Sans:300,700' ),
				),
			),
		);
	}


	/**
	 * Register lazysizes scripts.
	 *
	 * @link https://github.com/aFarkas/lazysizes GitHub repository for lazysizes.
	 * @version 4.0.2
	 */
	protected function _register_vendor_assets__lazysizes() {
		$version = '4.0.2';
		$lazysizes_handles = array(
			'lazysizes/core',
			'lazysizes/object-fit',
			'lazysizes/progressive',
			'lazysizes/respimg'
		);

		wp_register_script( 'lazysizes/core',        get_theme_file_uri( 'assets/js/lazysizes--core.min.js'        ), array(),                   $version );
		wp_register_script( 'lazysizes/object-fit',  get_theme_file_uri( 'assets/js/lazysizes--object-fit.min.js'  ), array( 'lazysizes/core' ), $version );
		wp_register_script( 'lazysizes/progressive', get_theme_file_uri( 'assets/js/lazysizes--progressive.min.js' ), array( 'lazysizes/core' ), $version );
		wp_register_script( 'lazysizes/respimg',     get_theme_file_uri( 'assets/js/lazysizes--respimg.min.js'     ), array( 'lazysizes/core' ), $version );

		list( $src, $deps ) = (
			!CONCATENATE_SCRIPTS
			? array(
				null,
				$lazysizes_handles
			)
			: array(
				get_theme_file_uri( 'assets/js/lazysizes.min.js' ),
				array()
			)
		);

		wp_register_script( 'lazysizes', $src, $deps, $version );

	}

}

?>
