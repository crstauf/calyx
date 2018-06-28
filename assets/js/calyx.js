( function() {
	"use strict";

	window.calyx = {

		init: function() {
			if ( _calyx_data )
				Object.assign( this, _calyx_data );
		},

		/* https://davidwalsh.name/javascript-debounce-function */
		debounce: function( func, wait, immediate ) {
		 	var timeout;

			return function executedFunction() {
				var context = this;
				var args = arguments;

				var later = function() {
					timeout = null;
					if ( !immediate )
						func.apply( context, args );
				};

				var callNow = immediate && !timeout;

				clearTimeout( timeout );

				timeout = setTimeout( later, wait );

				if ( callNow )
					func.apply( context, args );
			};
		},

		nl2br: function( str ) {
			return ( str + "" ).replace( /([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, "$1<br />$2" );
		}

	};

	window.calyx.init();

} () );