<?php
/**
 * Mega Menu Nav Walker.
 *
 * @package WDS_Mega_Menus
 */

if ( ! class_exists( 'WDS_Mega_Menus_Walker_Nav_Menu_Edit' ) ) {
	require_once ABSPATH . 'wp-admin/includes/nav-menu.php'; // We'll need the nav menu stuff here.

	/**
	 * Nav walker customizations.
	 *
	 * @package  WDS_Mega_Menus
	 * @since  0.1.0
	 * @uses Walker_Nav_Menu_Edit
	 */
	class WDS_Mega_Menus_Walker_Nav_Menu_Edit extends Walker_Nav_Menu_Edit {
		/**
		 * Override the start of elements in the walker.
		 *
		 * @param string $output (Required) Passed by reference. Used to append additional content.
		 * @param object $item   (Required) Menu item data object.
		 * @param int    $depth  (Required) Depth of menu item. Used for padding.
		 * @param array  $args   Not used.
		 * @param int    $id     Not used.
		 * @since 0.1.0
		 */
		function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			$item_output = '';

			parent::start_el( $item_output, $item, $depth, $args );

			$new_fields  = $this->field_display( $item->ID, array(
				'args'        => $args,
				'depth'       => $depth,
				'item'        => $item,
				'item_output' => $item_output,
			) );

			$item_output = preg_replace( '/(?=<p[^>]+class="[^"]*field-move)/', $new_fields, $item_output );
			$output .= $item_output;

		}

		/**
		 * Create the markup for our custom field
		 *
		 * @param  int   $id   Menu item ID.
		 * @param  array $args Array of arguments passed from start_el.
		 * @return string      The markup for the custom field.
		 * @since  0.1.0
		 */
		public function field_display( $id, $args = array() ) {
			ob_start();

			$args = wp_parse_args( $args, array(
				'args'        => array(),
				'depth'       => false,
				'item'        => false,
				'item_output' => false,
			) );

			// Disable on mobile.
			if ( isset( $args['depth'] ) && false == $args['depth'] ) :
				$hide_on_mobile = get_post_meta( $id, 'hide_menu_on_mobile', true );
				?>
					<p class="field-menu-item-icon description description-wide">
						<div class="hide-menu-on-mobile">
							<label for="hide-menu-on-mobile">
								<input type="checkbox" <?php echo ( $hide_on_mobile ) ? 'checked="checked"' : ''; ?> name="hide-menu-on-mobile[<?php echo absint( $id ); ?>]" /> <?php esc_html_e( 'Hide this on mobile', 'wds-mega-menus' ); ?>
							</label>
						</div>
					</p>
				<?php
			endif;
			/**
			 * Filter what depths these custom fields are allowed on.
			 *
			 * E.g.
			 *
			 *     function my_filter( $depth ) {
			 *     	return array( 1 ); // Only allow at depth 1
			 *     }
			 *     add_filter( 'wdsmm_walker_nav_allowed_depths', 'my_filter' );
			 */
			$allowed_depths = apply_filters( 'wdsmm_walker_nav_allowed_depths', array() );

			// Check for version 0.2.1+ option.
			if ( empty( $allowed_depths ) ) {
				$option_value = WDS_Mega_Menus::get_instance()->options->get_option( 'wds_mega_menus_depth', '' );

				if ( strlen( $option_value ) ) {
					$allowed_depths = explode( ',', $option_value );
				}
			}

			if ( ! empty( $allowed_depths ) && in_array( $args['depth'], $allowed_depths ) ) :

				$img_id  = get_post_thumbnail_id( $id );
				$img_url = wp_get_attachment_image_src( $img_id, 'large' );
				?>

					<div class="field-menu-item-icon description description-wide">
						<div class="btn-group">
							<p class="description"><?php esc_html_e( 'Menu Item Icon', 'wds-mega-menus' ); ?></p>
							<div>
								<button data-toggle="dropdown" id="<?php echo esc_attr( absint( $id ) ); ?>_icon" class="btn btn-default dropdown-toggle">
									<?php
									$current_value = get_post_meta( $id, '_menu_item_icon', true );
									echo ( ! $current_value ) ? esc_html__( '- Choose an icon -', 'wds-mega-menus' ) : $this->get_svg( $current_value ) . ' ' .  ucfirst( str_replace( '-', ' ', $current_value ) ); // WPCS: XSS ok. Actual string being echoed is validated.
									?>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu">
									<li><input type="radio" name="menu-item-icon[<?php echo esc_attr( absint( $id ) ); ?>]" id="<?php echo esc_attr( absint( $id ) ); ?>_icon_none" value="" <?php checked( $current_value, '' ); ?> /><label for="<?php echo esc_attr( absint( $id ) ); ?>_icon_none"> <?php esc_html_e( 'No Icon', 'wds-mega-menus' ); ?></label></li>
									<?php
									$options = $this->get_svg_list();
									foreach ( $options as $slug => $name ) {
										echo '<li><input type="radio" name="menu-item-icon[' . esc_attr( absint( $id ) ) . ']" id="' . esc_attr( absint( $id ) ) . '_icon_' . esc_attr( $slug ) . '" value="' . esc_attr( $slug ) . '"' . checked( $slug, $current_value, false ) . '><label for="' . esc_attr( absint( $id ) ) . '_icon_' . esc_attr( $slug ) . '">' . wp_kses_data( $name ) . '</label></li>';
									}
									?>
								</ul>
							</div>
						</div>
					</div>
					<div class="field-menu-item-image description description-wide">
						<p class="description">
							<?php esc_html_e( 'Menu Item Image', 'wds-mega-menus' ); ?><br />
							<small><?php esc_html_e( 'Images should be 130px wide by 250px high to prevent cropping.', 'wds-mega-menus' ); ?></small>
						</p>
						<p class="hide-if-no-js">
							<button title="<?php esc_html_e( 'Set Menu Item Image', 'wds-mega-menus' ); ?>" href="javascript:void(0);" id="set-menu-item-image-<?php echo esc_attr( absint( $id ) ); ?>"><?php esc_html_e( 'Set menu item image', 'wds-mega-menus' ); ?></button>
						</p>
						<p id="menu-item-image-container-<?php echo esc_attr( absint( $id ) ); ?>" class="hidden menu-item-image-container">
							<img src="<?php echo esc_url( $img_url[0] ); ?>" alt="" title="" style="width: 130px;" />
							<input id="menu-item-image-<?php echo esc_attr( absint( $id ) ); ?>" name="menu-item-image[<?php echo esc_attr( absint( $id ) ); ?>]" type="hidden" value="<?php echo esc_attr( $img_id ); ?>" />
						</p>
						<p class="hide-if-no-js hidden">
								<button title="<?php esc_html_e( 'Remove Menu Item Image', 'wds-mega-menus' ); ?>" href="javascript:;" id="remove-menu-item-image-<?php echo esc_attr( absint( $id ) ); ?>"><?php esc_html_e( 'Remove menu item image', 'wds-mega-menus' ); ?></button>
						</p>
					</div>
					<script>
						(function( $ ) {
							// @TODO localize and enqueue!
							WDS.MegaMenu.renderFeaturedImage( <?php echo esc_attr( absint( $id ) ); ?> );

							'use strict';

							$(function() {
								$( '#set-menu-item-image-<?php echo esc_attr( absint( $id ) ); ?>' ).on( 'click', function( e ) {
									e.preventDefault();
									WDS.MegaMenu.renderMediaUploader(<?php echo esc_attr( absint( $id ) ); ?>);
								});

								$( '#remove-menu-item-image-<?php echo esc_attr( absint( $id ) ); ?>' ).on( 'click', function( evt ) {

									// Stop the anchor's default behavior
									evt.preventDefault();

									// Remove the image, toggle the anchors
									WDS.MegaMenu.resetUploadForm( <?php echo esc_attr( absint( $id ) ); ?> );

								});

							});

						})( jQuery );
					</script>
					<div class="field-menu-item-widget-area description description-wide">
						<p class="description"><?php esc_html_e( 'Select Widget Area to Display', 'wds-mega-menus' ); ?></p>
						<p>
							<select id="widget-area-<?php echo esc_attr( absint( $id ) ); ?>" name="menu-item-widget-area[<?php echo esc_attr( absint( $id ) ); ?>]">
								<?php $current_area = get_post_meta( $id, '_menu_item_widget_area', true ); ?>
								<option value=""<?php selected( $current_area, '' ); ?>><?php esc_html_e( '- Select Widget Area -', 'wds-mega-menus' ); ?></option>
								<?php
								global $wp_registered_sidebars;
								foreach ( $wp_registered_sidebars as $sidebar ) {
									echo '<option value="' . esc_attr( $sidebar['id'] ) . '"' . selected( $sidebar['id'], $current_area, false ) . '>' . esc_html( $sidebar['name'] ) . '</option>';
								} ?>
							</select>
						</p>
					</div>
				<?php
			endif;

			return ob_get_clean();
		}

		/**
		 * Get the SVGs.
		 * @todo   Need to provide a fallback to use SVGs in the plugin.
		 * @return array An array of all the SVG names/slugs.
		 */
		public function get_svg_list() {
			$svgs = array();

			// Loop through all the svgs to build the SVG list.
			foreach ( glob( wds_mega_menus()->svg . '*.svg' ) as $svg ) {
				$slug = str_replace( array( wds_mega_menus()->svg, '.svg' ), '', $svg );
				$svgs[ $slug ] = $this->get_svg( $slug ) . ' ' . ucfirst( str_replace( '-', ' ', $slug ) );
			}

			return $svgs;
		}

		/**
		 * Return the SVG icon markup.
		 * @param  string $icon_name The SVG icon name/slug (based on the original filename).
		 * @return string            The SVG markup.
		 */
		function get_svg( $icon_name ) {

			$svg = '<svg class="icon icon-' . esc_html( $icon_name ) . '">';
			$svg .= '	<use xlink:href="#icon-' . esc_html( $icon_name ) . '"></use>';
			$svg .= '</svg>';

			return $svg;
		}
	} // class WDS_Mega_Menus_Walker_Nav_Menu_Edit.

	// We don't have the requirements to do this.
} else {
	$wds_mega_menus = false; // Destroy our instance!
} // class WDS_Mega_Menus_Walker_Nav_Menu_Edit exists.
