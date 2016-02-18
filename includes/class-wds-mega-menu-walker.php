<?php

if ( ! class_exists( 'WDS_Mega_Menu_Walker' ) ) {
	/**
	 * Walker Customizations.
	 *
	 * @since  1.0.0
	 * @package  WDS_Mega_Menus
	 * @uses  Walker_Nav_Menu
	 */
	class WDS_Mega_Menu_Walker extends Walker_Nav_Menu {
		/**
		 * What the class handles.
		 *
		 * @see Walker::$tree_type
		 * @since 1.0.0
		 * @var string
		 */
		public $tree_type = array( 'post_type', 'taxonomy', 'custom' );

		/**
		 * Database fields to use.
		 *
		 * @see Walker::$db_fields
		 * @since 1.0.0
		 * @todo Decouple this.
		 * @var array
		 */
		public $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );

		/**
		 * Starts the list before the elements are added.
		 *
		 * @see Walker::start_lvl()
		 *
		 * @since 1.0.0
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   An array of arguments. @see wp_nav_menu()
		 */
		public function start_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat("\t", $depth);
			if ( 1 == $depth ) {
				$output .= "\n$indent<ul class=\"sub-menu depth-$depth\">\n";
			} else {
				$output .= "\n$indent<ul class=\"sub-menu depth-$depth\">\n";
			}
		}

		/**
		 * Ends the list of after the elements are added.
		 *
		 * @see Walker::end_lvl()
		 *
		 * @since 1.0.0
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   An array of arguments. @see wp_nav_menu()
		 */
		public function end_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat("\t", $depth);
			$output .= "$indent</ul>\n";
		}

		/**
		 * Start the element output.
		 *
		 * @see Walker::start_el()
		 *
		 * @since 1.0.0
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item   Menu item data object.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   An array of arguments. @see wp_nav_menu()
		 * @param int    $id     Current item ID.
		 */
		public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {


			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'menu-item-' . $item->ID;

			// Hide on mobile.
			$hide_on_mobile = get_post_meta( $item->ID, 'hide_menu_on_mobile', true );
			if ( $hide_on_mobile ) {
				$classes[] = 'menu-item-hide-on-mobile';
			}

			/**
			 * Filter the CSS class(es) applied to a menu item's list item element.
			 *
			 * @since 1.0.0
			 *
			 * @param array  $classes The CSS classes that are applied to the menu item's `<li>` element.
			 * @param object $item    The current menu item.
			 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
			 * @param int    $depth   Depth of menu item. Used for padding.
			 */
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			/**
			 * Filter the ID applied to a menu item's list item element.
			 *
			 *
			 * @param string $menu_id The ID that is applied to the menu item's `<li>` element.
			 * @param object $item    The current menu item.
			 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
			 * @param int    $depth   Depth of menu item. Used for padding.
			 */
			$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

			$output .= $indent . '<li' . $id . $class_names .'>';

			$atts = array();
			$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
			$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
			$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
			$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

			/**
			 * Filter the HTML attributes applied to a menu item's anchor element.
			 *
			 * @param array $atts {
			 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
			 *
			 *     @type string $title  Title attribute.
			 *     @type string $target Target attribute.
			 *     @type string $rel    The rel attribute.
			 *     @type string $href   The href attribute.
			 * }
			 * @param object $item  The current menu item.
			 * @param array  $args  An array of {@see wp_nav_menu()} arguments.
			 * @param int    $depth Depth of menu item. Used for padding.
			 */
			$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$icon = get_post_meta( $item->ID, '_menu_item_icon', true );
			$item_output = isset( $args->before ) ? $args->before : '';

			// Add the menu link.
			$item_output .= '<a' . $attributes . '>';
				$item_output .= ( ! $icon ) ? $this->get_svg( apply_filters( 'wds_mega_menu_default_icon', false ) ) : $this->get_svg( $icon );
				// This filter is documented in wp-includes/post-template.php
				$item_output .= isset( $args->link_before ) ? $args->link_before : '';
	            $item_output .= apply_filters( 'the_title', $item->title, $item->ID );
				$item_output .= isset( $args->link_after ) ? $args->link_after : '';
			$item_output .= '</a>';

			// The item content.
			$item_content = apply_filters( 'wds-mega-menu-content', wpautop( $item->post_content ) );

			// Use an inline image, or CSS on a Div?
			$item_use_real_image = apply_filters( 'wds-mega-menu-inline-image', true );

			// Add the content of the walker.
			if ( has_post_thumbnail( $item->ID ) ) {
				$image_size = apply_filters( 'wds-mega-menus-image-size', 'full' );

				$item_output .= '<div class="menu-item-container">';
					$item_output .= '<div class="menu-item-image">';
						$item_output .= '<a' . $attributes . '>';

						if ( $item_use_real_image ) {
							$item_output .= get_the_post_thumbnail( $item->ID, $image_size );
						} else {
							$item_output .= '<div class="post-thumbnail" style="background-image: url(' . current( wp_get_attachment_image_src( get_post_thumbnail_id( $item->ID ), $image_size ) ) . ');"><!-- CSS Image. --></div>';
						}

						$item_output .= '</a>';
					$item_output .= '</div>';

					$item_output .= '<div class="menu-item-description">';
						$item_output .= '<a' . $attributes . ' class="menu-item-description-title"><h3>';
						$item_output .= ( ! $icon ) ? '' : $this->get_svg( $icon );
						$item_output .=  apply_filters( 'the_title', $item->title, $item->ID );
						$item_output .= '</h3></a>';

						$item_output .=  $item_content;

						$item_output .= '<p><a' . $attributes . ' class="keep-reading-more">';
						$item_output .= __( 'Keep Reading', 'wds-mega-menus' );
						$item_output .= '</a></p>';
					$item_output .= '</div>';
				$item_output .= '</div>';
			}

			$widget_area = get_post_meta( $item->ID, '_menu_item_widget_area', true );
			if ( $widget_area ) {
				dynamic_sidebar( $widget_area );
			}

			$item_output .= isset( $args->after ) ? $args->after : '';

			/**
			 * Filter a menu item's starting output.
			 *
			 * The menu item's starting output only includes `$args->before`, the opening `<a>`,
			 * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
			 * no filter for modifying the opening and closing `<li>` for a menu item.
			 *
			 * @since 1.0.0
			 *
			 * @param string $item_output The menu item's starting HTML output.
			 * @param object $item        Menu item data object.
			 * @param int    $depth       Depth of menu item. Used for padding.
			 * @param array  $args        An array of {@see wp_nav_menu()} arguments.
			 */
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}

		/**
		 * Ends the element output, if needed.
		 *
		 * @see Walker::end_el()
		 *
		 * @since 1.0.0
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item   Page data object. Not used.
		 * @param int    $depth  Depth of page. Not Used.
		 * @param array  $args   An array of arguments. @see wp_nav_menu()
		 */
		public function end_el( &$output, $item, $depth = 0, $args = array() ) {
			$output .= "</li>\n";
		}

		function get_svg( $icon_name ) {
			if ( $icon_name && ! empty( $icon_name ) ) {
				$svg = '<svg class="icon icon-' . esc_html( $icon_name ) . '">';
				$svg .= '	<use xlink:href="#icon-' . esc_html( $icon_name ) . '"></use>';
				$svg .= '</svg>';

				return $svg;
			}

			return false;
		}

	} // class WDS_Mega_Menu_Walker.

// We don't have the requirements for this.
} else {
	$wds_mega_menus = false; // Destroy our instance!
} // if ( ! class_exists( 'WDS_Mega_Menus_Walker_Nav_Menu_Edit' )
