<?php

require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
class WDS_Mega_Menus_Walker_Nav_Menu_Edit extends Walker_Nav_Menu_Edit {

	/**
	 * Override the start of elements in the walker.
	 *
	 * @param string $output
	 * @param object $item
	 * @param int    $depth
	 * @param array  $args
	 * @param int    $id
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$item_output = '';
		parent::start_el( $item_output, $item, $depth, $args );

		$new_fields  = $this->field_display( $item->ID );
		$item_output = preg_replace( '/(?=<p[^>]+class="[^"]*field-move)/', $new_fields, $item_output );
		$output .= $item_output;

	}

	/**
	 * Create the markup for our custom field
	 *
	 * @param $id
	 *
	 * @return string
	 */
	public function field_display( $id ) {
		$img_id  = get_post_thumbnail_id( $id );
		$img_url = wp_get_attachment_image_src( $img_id, 'full' );
		ob_start();
		?>
		<p class="field-menu-item-icon description description-wide">
			<div class="btn-group">
				<?php _e( 'Menu Item Icon', 'wds-mega-menus' ); ?><br />
				<button data-toggle="dropdown" id="<?php echo esc_attr( $id ); ?>_icon" class="btn btn-default dropdown-toggle">
					<?php
					$current_value = get_post_meta( $id, '_menu_item_icon', true );
					echo ( ! $current_value ) ? __( '- Choose an icon -', 'clp' ) : $this->get_svg( $current_value ) . ' ' .  ucfirst( str_replace( '-', ' ', $current_value ) );
					?>
					 <span class="caret"></span>
				 </button>
				<ul class="dropdown-menu">
					<li><input type="radio" name="menu-item-icon[<?php echo esc_attr( $id ); ?>]" id="<?php echo esc_attr( $id ); ?>_icon_none" value="" <?php checked( $current_value, '' ); ?> /><label for="<?php echo esc_attr( $id ); ?>_icon_none"> <?php _e( 'No Icon', 'wds-mega-menus' ); ?></label></li>
					<?php
					$options = $this->get_svg_list();
					foreach ( $options as $slug => $name ) {
						echo '<li><input type="radio" name="menu-item-icon[' . esc_attr( $id ) . ']" id="' . esc_attr( $id ) . '_icon_' . esc_attr( $slug ) . '" value="' . esc_attr( $slug ) . '"' . checked( $slug, $current_value, false ) . '><label for="' . esc_attr( $id ) . '_icon_' . esc_attr( $slug ) . '">' . $name . '</label></li>';
					}
					?>

				</ul>
			</div>
		</p>
		<p class="field-menu-item-image description description-wide">
			<?php _e( 'Menu Item Image', 'wds-mega-menus' ); ?><br />
			<span class="hide-if-no-js">
				<button title="Set Menu Item Image" href="javascript:;" id="set-menu-item-image-<?php echo esc_attr( $id ); ?>"><?php _e( 'Set menu item image', 'wds-mega-menus' ); ?></button>
			</span>
			<span id="menu-item-image-container-<?php echo esc_attr( $id ); ?>" class="hidden menu-item-image-container">
                <img src="<?php echo esc_url( $img_url[0] ); ?>" alt="" title="" />
                <input id="menu-item-image-<?php echo esc_attr( $id ); ?>" name="menu-item-image[<?php echo esc_attr( $id ); ?>]" type="hidden" value="<?php echo esc_attr( $img_id ); ?>" />
			</span>
			<span class="hide-if-no-js hidden">
                <button title="Remove Menu Item Image" href="javascript:;" id="remove-menu-item-image-<?php echo esc_attr( $id ); ?>"><?php _e( 'Remove menu item image', 'wds-mega-menus' ); ?></button>
			</span>
		</p>
		<script>
			(function( $ ) {

				renderFeaturedImage( $, <?php echo esc_attr( $id ); ?> );

				'use strict';

				$(function() {
					$( '#set-menu-item-image-<?php echo esc_attr( $id ); ?>' ).on( 'click', function( e ) {
						e.preventDefault();
						renderMediaUploader(<?php echo esc_attr( $id ); ?>);
					});

					$( '#remove-menu-item-image-<?php echo esc_attr( $id ); ?>' ).on( 'click', function( evt ) {

						// Stop the anchor's default behavior
						evt.preventDefault();

						// Remove the image, toggle the anchors
						resetUploadForm( $, <?php echo esc_attr( $id ); ?> );

					});

				});

			})( jQuery );
		</script>
		<p class="field-menu-item-widget-area description description-wide">
			<?php _e( 'Select Widget Area to Display', 'wds-mega-menus' ); ?><br />
			<select id="widget-area-<?php echo esc_attr( $id ); ?>" name="menu-item-widget-area[<?php echo esc_attr( $id ); ?>]">
				<?php $current_area = get_post_meta( $id, '_menu_item_widget_area', true ); ?>
				<option value=""<?php selected( $current_area, '' ); ?>><?php _e( '- Select Widget Area -', 'wds-mega-menus' ); ?></option>
				<?php
					global $wp_registered_sidebars;
					foreach( $wp_registered_sidebars as $sidebar ) {
						echo '<option value="' . esc_attr( $sidebar['id'] ) . '"' . selected( $sidebar['id'], $current_area, false ) . '>' . esc_html( $sidebar['name'] ) . '</option>';
					}
				?>
			</select>
		</p>
		<?php
		return ob_get_clean();
	}

	public function get_svg_list() {
		$svgs = array();
		foreach ( glob( get_stylesheet_directory() . '/images/svg/*.svg' ) as $svg ) {
			$slug          = str_replace( array( get_stylesheet_directory() . '/images/svg/', '.svg' ), '', $svg );
			$svgs[ $slug ] = $this->get_svg( $slug ) . ' ' . ucfirst( str_replace( '-', ' ', $slug ) );
		}

		return $svgs;
	}

	function get_svg( $icon_name ) {

		$svg = '<svg class="icon icon-' . esc_html( $icon_name ) . '">';
		$svg .= '	<use xlink:href="#icon-' . esc_html( $icon_name ) . '"></use>';
		$svg .= '</svg>';

		return $svg;
	}


}