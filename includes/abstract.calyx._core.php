<?php

abstract class _Calyx_Core {
	use Calyx_Singleton;

	protected function __construct() {

		do_action( 'qm/lap', THEME_PREFIX . ':init', get_called_class() );

	}

}

?>