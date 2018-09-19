jQuery( document ).ready( function ( e ) {

	jQuery( '#easy_post_embed_code' ).click(function() {
		jQuery( this ).select();
	});

	jQuery( '.embed_code_size_option' ).change(function() {

		var width = jQuery( '#embed_code_width' ).val();
		var height = jQuery( '#embed_code_height' ).val();
		var post_id = jQuery( '#post_ID' ).val();

		jQuery.post(
		    ajaxurl,
		    {
		        'action': 'update_embed_code',
		        'width': width,
		        'height': height,
		        'post_id': post_id,
		    },
		    function( response ){
		        if( response ) {
		        	jQuery( '#easy_post_embed_code' ).val( response );
		        	jQuery( '#easy_post_embed_code' ).select();
		        }
		    }
		);
	});

});