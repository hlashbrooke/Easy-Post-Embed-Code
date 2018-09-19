jQuery( document ).ready( function ( e ) {

	jQuery( '.easy_post_embed_code' ).click(function() {
		jQuery( this ).select();
	});

	jQuery( '.copy-embed-code' ).click(function() {
		jQuery( this ).parent().siblings( 'textarea' ).select();
		document.execCommand('copy');
	});

	jQuery( '.embed_code_size_option' ).change(function() {

		var post_id = jQuery( this ).siblings( '.embed_code_post_id' ).val();
		var width = jQuery( '#embed_code_width-' + post_id ).val();
		var height = jQuery( '#embed_code_height-' + post_id ).val();

		var target = jQuery( this ).parent().siblings( 'textarea' );

		jQuery.post(
		    easy_post_embed_code_obj.ajaxurl,
		    {
		        'action': 'update_embed_code',
		        'width': width,
		        'height': height,
		        'post_id': post_id,
		    },
		    function( response ){
		        if( response ) {
		        	target.val( response );
		        }
		    }
		);
	});

});