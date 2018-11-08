( function() {
	"use strict";

	window.calyx = {

		init: function() {
			if ( _calyx_data )
				Object.assign( this, _calyx_data );
		},

		/* https://remysharp.com/2010/07/21/throttling-function-calls */
		throttle: function ( fn, threshhold, scope ) {
			threshhold || ( threshhold = 250 );
			var last,
			deferTimer;
			return function () {
				var context = scope || this;
				var now = +new Date,
				args = arguments;

				if ( last && ( now < ( last + threshhold ) ) ) {
					// hold on to it
					clearTimeout( deferTimer );
					deferTimer = setTimeout( function () {
						last = +new Date;
						fn.apply( context, args );
					}, threshhold + ( last - now ) );
				} else {
					last = now;
					fn.apply( context, args );
				}
			};
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

		/* convert new lines (\n) to break tags */
		nl2br: function( str ) {
			return ( str + "" ).replace( /([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, "$1<br />$2" );
		}

	};

	window.calyx.init();

} () );