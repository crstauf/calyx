<?php
/**
 * Credentials for the theme.
 * Ex: APIs, third-party services.
 */

if ( !defined( 'ABSPATH' ) || !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Class.
 */
class Calyx_Credentials {

	/**
	 * Public getter.
	 */
	public static function get( $group, $key ) {
		$function = $group . '__' . $key;
		
		error_log( 'Credentials requested: ' . $function );
		
		if ( !is_callable( array( __CLASS__, $function ) ) )
			return null;
			
		return self::$function();
	}
	
	/*protected static function group__key() {
		return 'foobar';
	}*/

}

?>