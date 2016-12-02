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
		var me = this;

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
			me.target_element = element_id;

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

			file_frame.on( 'insert', me.insert_image );

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
			if ( 0 < $.trim( $( '#menu-item-image-' + element_id ).val() ).length ) {
				$( '#menu-item-image-container-' + element_id ).removeClass( 'hidden' );

				$( '#set-menu-item-image-' + element_id )
					.parent()
					.hide();

				$( '#remove-menu-item-image-' + element_id )
					.parent()
					.removeClass( 'hidden' );
			}
		};

		/**
		 * Setup an event handler for what to do when an image has been
		 * selected.
		 */
		this.insert_image = function() {
			var file_frame = wp.media.frames.file_frame;

			// Read the JSON data returned from the Media Uploader
			var json = file_frame.state().get( 'selection' ).first().toJSON();

			// First, make sure that we have the URL of an image to display
			if ( 0 > jQuery.trim( json.url.length ) ) {
				return;
			}

			// After that, set the properties of the image and display it
			jQuery( '#menu-item-image-container-' + me.target_element )
				.children( 'img' )
				.attr({
					'src':   json.url,
					'alt':   json.caption,
					'title': json.title
				})
			.show()
				.parent()
				.removeClass( 'hidden' );

			jQuery( '#menu-item-image-' + me.target_element ).val( json.id );

			// Next, hide the anchor responsible for allowing the user to select an image
			jQuery( '#menu-item-image-container-' + me.target_element )
				.prev()
				.hide();

			jQuery( '#menu-item-image-container-' + me.target_element )
				.next()
				.show();
		};

		/**
		 * Inits the edit menu buttons based on IDs from the localization.
		 *
		 * @since 0.3.1
		 *
		 * @author Zach Owen
		 */
		this.initEditMenu = function() {
			if ( ! global.hasOwnProperty( 'WDS_MegaMenu_Loc' ) ) {
				return;
			}

			for ( var key in global.WDS_MegaMenu_Loc.featured_ids ) {
				if ( ! global.WDS_MegaMenu_Loc.featured_ids.hasOwnProperty( key ) ) {
					continue;
				}

				registerMenuButton( parseInt( global.WDS_MegaMenu_Loc.featured_ids[ key ], 10 ) );
			}
		};

		/**
		 * Initialize the options page controls.
		 */
		this.initOptionsPage = function() {
			registerOptionsControls();
		};
	}

	global.WDS = global.WDS || {};
	global.WDS.MegaMenu = new WDS_Mega_Menu();
	$( document ).ready( global.WDS.MegaMenu.initEditMenu );
	$( document ).ready( global.WDS.MegaMenu.initOptionsPage );

	/**
	 * Register the menu button listeners for the menu editor.
	 *
	 * @since 0.3.1
	 *
	 * @param int id The ID of the post object we're setting buttons for.
	 *
	 * @author Zach Owen
	 */
	function registerMenuButton( id ) {
		WDS.MegaMenu.renderFeaturedImage( id );
		$( '#set-menu-item-image-' + id ).on( 'click', function( e ) {
			e.preventDefault();
			WDS.MegaMenu.renderMediaUploader( id );
		});

		$( '#remove-menu-item-image-' + id ).on( 'click', function( evt ) {

			// Stop the anchor's default behavior
			evt.preventDefault();

			// Remove the image, toggle the anchors
			WDS.MegaMenu.resetUploadForm( id );
		});
	}

	/**
	 * Registers our handlers for the Options Page checkboxes.
	 *
	 * @since  0.3.1
	 * @author Zach Owen
	 */
	function registerOptionsControls() {
		var $all_depths       = $( '#all_depths' );
		var $depth_input_last = $( ".depth_options li:not(:last-child) input" );
		// Check all items if All depths checked and lock them
		if ( $all_depths.length ) {
			$all_depths.change( function() {
				if(this.checked) {
					/**
					 * I don't think we need to disable these if the last is checked.
					 * The mechanism of unchecking it once another option is changed works well.
					 * -ZO
					 */
					$( ".depth_options" ).find( "li:not(:last-child) input" ).attr({
						checked: "checked"
					});
				}
			});
		}

		// Uncheck All depth checkbox if any other item was unchecked
		if ( $depth_input_last.length ) {
			$( $depth_input_last ).change( function() {
				$all_depths.removeAttr( 'checked' );
			});
		}
	}
})(window);
