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
	use Calyx_Singleton;

	/**
	 * Public getter.
	 *
	 * @param string $key Key to identify the credential.
	 * @param string $group Group credential is in (optional).
	 *
	 * @return mixed
	 */
	function get( $key, $group = null ) {
		$function = ( !empty( $group ) ? $group . '__' : null ) . $key;
		
		error_log( 'Credentials requested: ' . $function );
		
		if ( !is_callable( array( &$this, $function ) ) )
			return null;
			
		return $this->$function();
	}
	
	/*protected function group__key() {
		return 'foobar';
	}*/

}

?>