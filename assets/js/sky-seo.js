jQuery(document).ready(function($) {		
	 // Only show the "remove image" button when needed
	 if ( ! $('#images_home').val() )
		 $('.remove_image_button').hide();

	// Uploading files
	var file_frame;

	$(document).on( 'click', '.upload_image_button', function( event ){

		event.preventDefault();
		var id = $(this).data('id');

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			file_frame.open();
			return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.downloadable_file = wp.media({
			title: skySeo.title,
			button: {
				text: skySeo.text,
			},
			multiple: false
		});

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			attachment = file_frame.state().get('selection').first().toJSON();

			$('#' + id + 's').val( attachment.id );
			$('#' + id + ' img').attr('src', attachment.url );
			$('.remove_image_button').show();
		});

		// Finally, open the modal.
		file_frame.open();
	});

	$(document).on( 'click', '.remove_image_button', function( event ){
		var id = $(this).data('id');
		$('#' + id + ' img').attr('src', skySeo.thumbnail);
		$('#' + id).val('');
		$(this).hide();
		return false;
	});
});