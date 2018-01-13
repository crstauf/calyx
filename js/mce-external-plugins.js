(function ( tinymce ) {
	'use strict';

	tinymce.PluginManager.add( 'webfontloader', function ( editor, url ) {
		editor.on( 'init', function () {
			var scriptId = editor.dom.uniqueId();

			var scriptElm = editor.dom.create( 'script', {
				id: scriptId,
				type: 'text/javascript',
				src: url + '/webfontloader.js'
			} );

            var scriptBlk = editor.dom.create( 'script', {
                type: 'text/javascript'
            } );
            scriptBlk.innerHTML = 'WebFontConfig = { custom: { families: ["Geometria", "Sentinel"], urls: ["/wp-content/themes/estatesettlementco/fonts/fonts.min.css"]} }';

            editor.getDoc().getElementsByTagName( 'head' )[ 0 ].appendChild( scriptBlk );
			editor.getDoc().getElementsByTagName( 'head' )[ 0 ].appendChild( scriptElm );

			var scriptId = editor.dom.uniqueId();

			var scriptElm = editor.dom.create( 'script', {
				id: scriptId,
				type: 'text/javascript',
				src: url + '/lazysizes.min.js'
			} );

			editor.getDoc().getElementsByTagName( 'head' )[ 0 ].appendChild( scriptElm );


		} );
	} );

})( window.tinymce );
