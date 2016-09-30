(function(global){
	'use strict';
	var $ = $ || jQuery;

	/**
	 * WDS Mega Menu JS Class
	 */
	function WDS_Mega_Menu() {
		if ( WDS_Mega_Menu.prototype._singleton ) {
			return WDS_Mega_Menu.prototype._singleton;
		}

		WDS_Mega_Menu.prototype._singleton = this;

		/**
		* Callback function for the 'click' event of the 'Set Footer Image'
		* anchor in its meta box.
		*
		* Displays the media uploader for selecting an image.
		*
		* @since  0.1.0
		* @author Dustin Filippini, Zach Owen
		*
		* @param  string element_id The ID of the mega menu target.
		*/
		this.renderMediaUploader = function( element_id ) {
			var file_frame;
			var image_data;

			/**
			 * If an instance of file_frame already exists, then we can open it
			 * rather than creating a new instance.
			 */
			if ( undefined !== file_frame ) {
				file_frame.open();
				return;
			}

			/**
			 * If we're this far, then an instance does not exist, so we need to
			 * create our own.
			 *
			 * Here, use the wp.media library to define the settings of the Media
			 * Uploader. We're opting to use the 'post' frame which is a template
			 * defined in WordPress core and are initializing the file frame
			 * with the 'insert' state.
			 *
			 * We're also not allowing the user to select more than one image.
			 */
			file_frame = wp.media.frames.file_frame = wp.media({
				frame:    'post',
				state:    'insert',
				multiple: false
			});

			/**
			 * Setup an event handler for what to do when an image has been
			 * selected.
			 *
			 * Since we're using the 'view' state when initializing
			 * the file_frame, we need to make sure that the handler is attached
			 * to the insert event.
			 *
			 * @TODO this function should be lifted out of the anonymous call if there could ever be
			 * @TODO a case where someone might want to unbind it from the 'insert' event. -ZO
			 */
			file_frame.on( 'insert', function() {
				// Read the JSON data returned from the Media Uploader
				var json = file_frame.state().get( 'selection' ).first().toJSON();

				// First, make sure that we have the URL of an image to display
				if ( 0 > jQuery.trim( json.url.length ) ) {
					return;
				}

				// After that, set the properties of the image and display it
				jQuery( '#menu-item-image-container-' + element_id )
					.children( 'img' )
						.attr({
							'src':   json.url,
							'alt':   json.caption,
							'title': json.title
						})
						.show()
					.parent()
					.removeClass( 'hidden' );

				jQuery( '#menu-item-image-' + element_id ).val( json.id );

				// Next, hide the anchor responsible for allowing the user to select an image
				jQuery( '#menu-item-image-container-' + element_id )
					.prev()
					.hide();

				jQuery( '#menu-item-image-container-' + element_id )
					.next()
					.show();
			});

			// Now display the actual file_frame
			file_frame.open();
		};

		/**
		 * Callback function for the 'click' event of the 'Remove Footer Image'
		 * anchor in its meta box.
		 *
		 * Resets the meta box by hiding the image and by hiding the 'Remove
		 * Footer Image' container.
		 *
		 * @since 0.2.0
		 * @param string element_id The ID of the mega menu target.
		 */
		this.resetUploadForm = function( element_id ) {
			alert('hay!');
			// First, we'll hide the image
			$( '#menu-item-image-container-' + element_id )
				.children( 'img' )
				.hide();

			// Then display the previous container
			$( '#menu-item-image-container-' + element_id )
				.prev()
				.show();

			// Finally, we add the 'hidden' class back to this anchor's parent
			$( '#menu-item-image-container-' + element_id )
				.next()
				.hide()
				.addClass( 'hidden' );

			jQuery( '#menu-item-image-' + element_id ).val( '' );
		};

		/**
		 * Checks to see if the input field for the thumbnail source has a value.
		 * If so, then the image and the 'Remove featured image' anchor are displayed.
		 *
		 * Otherwise, the standard anchor is rendered.
		 *
		 * @since  0.1.0
		 * @author Dustin Filippini, Zach Owen
		 *
		 * @param  string element_id The ID of the mega menu target.
		 */
		this.renderFeaturedImage = function( element_id ) {
			/* If a thumbnail URL has been associated with this image
			 * Then we need to display the image and the reset link.
			 */
			if ( '' !== $.trim ( $( '#menu-item-image-' + element_id ).val() ) ) {
				$( '#menu-item-image-container-' + element_id ).removeClass( 'hidden' );

				$( '#set-menu-item-image-' + element_id )
					.parent()
					.hide();

				$( '#remove-menu-item-image-' + element_id )
					.parent()
					.removeClass( 'hidden' );
			}
		};
	}

	global.WDS = global.WDS || {};
	global.WDS.MegaMenu = new WDS_Mega_Menu();
})(window);
