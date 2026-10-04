/* global jQuery, wp */
( function ( $ ) {
	'use strict';

	$( function () {
		// Selectores de color.
		$( '.anderc-color' ).wpColorPicker();

		// Pestañas.
		$( '.anderc-tabs .nav-tab' ).on( 'click', function ( e ) {
			e.preventDefault();
			var target = $( this ).attr( 'href' );
			$( '.anderc-tabs .nav-tab' ).removeClass( 'nav-tab-active' );
			$( this ).addClass( 'nav-tab-active' );
			$( '.anderc-tab-panel' ).removeClass( 'is-active' );
			$( target ).addClass( 'is-active' );
		} );

		// Selector de medios para el logo.
		$( '.anderc-media-upload' ).on( 'click', function ( e ) {
			e.preventDefault();
			var targetId = $( this ).data( 'target' );
			var frame = wp.media( {
				title: 'Seleccionar logo',
				multiple: false,
				library: { type: 'image' }
			} );
			frame.on( 'select', function () {
				var attachment = frame.state().get( 'selection' ).first().toJSON();
				$( '#' + targetId ).val( attachment.url );
			} );
			frame.open();
		} );
	} );
} )( jQuery );
