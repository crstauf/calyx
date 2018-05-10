( function() {
	"use script";

	window.calyx = {
		
		init: function() {
			if ( _calyx_data )
				Object.assign( this, _calyx_data );
		}
		
	};

	window.calyx.init();

} () );