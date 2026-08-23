( function ( $ ) {
	'use strict';

	$( document ).on( 'change', '.oriente-product-selector-checkbox', function () {
		var $selector = $( this ).closest( '.oriente-product-selector' );
		var selected = [];

		$selector.find( '.oriente-product-selector-checkbox:checked' ).each( function () {
			selected.push( $( this ).val() );
		} );

		$selector.prev( '.oriente-product-selector-value' ).val( selected.join( ',' ) ).trigger( 'change' );
	} );
}( jQuery ) );
