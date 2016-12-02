<?php
/**
 * Mega Menu Walker.
 *
 * @package WDS_Mega_Menus
 */

if ( ! class_exists( 'WDS_Mega_Menu_Walker' ) ) {

	/**
	 * Walker Customizations.
	 *
	 * @since   0.1.0
	 * @package WDS_Mega_Menus
	 * @uses    Walker_Nav_Menu
	 */
	class WDS_Mega_Menu_Walker extends Walker_Nav_Menu {
		/**
		 * What the class handles.
		 *
		 * @see   Walker::$tree_type
		 * @since 0.1.0
		 * @var   string
		 */
		public $tree_type = array( 'post_type', 'taxonomy', 'custom' );

		/**
		 * Constructor
		 *
		 * @since  0.3.0
		 * @author Pavel Korotenko
		 */
		public function __construct() {
			if ( file_exists( wds_mega_menus()->svg_defs ) ) {
				require_once( wds_mega_menus()->svg_defs );
			}

			/**
			 * Filter the db fields passed to the walker.
			 *
			 * @since  0.3.1
			 * @param  array $db_fields Array of fields for the DB. See Walker::$db_fields.
			 * @return array
			 */
			$this->db_fields = apply_filters( 'wdsmm_db_fields', array(
				'parent' => 'menu_item_parent',
				'id'     => 'db_id',
			) );
		}

		/**
		 * Starts the list before the elements are added.
		 *
		 * @see Walker::start_lvl()
		 *
		 * @since  0.1.0
		 * @author Dustin Filippini, Aubrey Portwood
		 *
		 * @param  string $output Passed by reference. Used to append additional content.
		 * @param  int    $depth  Depth of menu item. Used for padding.
		 * @param  array  $args   An array of arguments.
		 * @see    wp_nav_menu()
		 */
		public function start_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat( "\t", $depth );
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
		 * @since  0.1.0
		 * @author Dustin Filippini, Aubrey Portwood
		 *
		 * @param  string $output Passed by reference. Used to append additional content.
		 * @param  int    $depth  Depth of menu item. Used for padding.
		 * @param  array  $args   An array of arguments.
		 * @see    wp_nav_menu()
		 */
		public function end_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat( "\t", $depth );
			$output .= "$indent</ul>\n";
		}

		/**
		 * Start the element output.
		 *
		 * @see Walker::start_el()
		 *
		 * @since  0.1.0
		 * @author Dustin Filippini, Aubrey Portwood, Corey Collins
		 *
		 * @param  string $output Passed by reference. Used to append additional content.
		 * @param  object $item   Menu item data object.
		 * @param  int    $depth  Depth of menu item. Used for padding.
		 * @param  array  $args   An array of arguments.
		 * @param  int    $id     Current item ID.
		 * @see    wp_nav_menu()
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
			 * @since  0.1.0
			 * @author Dustin Filippini, Aubrey Portwood
			 *
			 * @param  array  $classes The CSS classes that are applied to the menu item's `<li>` element.
			 * @param  object $item    The current menu item.
			 * @param  array  $args    An array of {@see wp_nav_menu()} arguments.
			 * @param  int    $depth   Depth of menu item. Used for padding.
			 */
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			/**
			 * Filter the ID applied to a menu item's list item element.
			 *
			 * @since  0.1.0
			 * @author Dustin Filippini, Aubrey Portwood
			 *
			 * @param  string $menu_id The ID that is applied to the menu item's `<li>` element.
			 * @param  object $item    The current menu item.
			 * @param  array  $args    An array of {@see wp_nav_menu()} arguments.
			 * @param  int    $depth   Depth of menu item. Used for padding.
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
			 * @since  0.1.0
			 * @author Dustin Filippini, Aubrey Portwood
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
				// This filter is documented in wp-includes/post-template.php.
				$item_output .= isset( $args->link_before ) ? $args->link_before : '';
	            $item_output .= apply_filters( 'the_title', $item->title, $item->ID );
				$item_output .= isset( $args->link_after ) ? $args->link_after : '';
			$item_output .= '</a>';

			// The item title.
			if ( has_filter( 'wds-mega-menu-title' ) ) {
				_deprecated_hook( 'wds-mega-menu-title', '0.3.1', 'wdsmm_title' );
			}

			$item_title = apply_filters( 'wdsmm_title', '<a' . $attributes . ' class="menu-item-description-title"><h3>' . ( ! $icon ) ? '' : $this->get_svg( $icon ) . apply_filters( 'the_title', $item->title, $item->ID ) . '</h3></a>' );

			// The item content.
			if ( has_filter( 'wds-mega-menu-content' ) ) {
				_deprecated_hook( 'wds-mega-menu-content', '0.3.1', 'wdsmm_content' );
			}

			$item_content = apply_filters( 'wdsmm_content', wpautop( $item->post_content ) );

			// The item read more link.
			if ( has_filter( 'wds-mega-menu-read-more' ) ) {
				_deprecated_hook( 'wds-mega-menu-read-more', '0.3.1', 'wdsmm_read_more' );
			}

			$item_read_more = apply_filters( 'wdsmm_read_more', '<p><a' . $attributes . ' class="keep-reading-more">' . __( 'Keep Reading', 'wds-mega-menus' ) . '</a></p>' );

			// Use an inline image, or CSS on a Div?
			if ( has_filter( 'wds-mega-menu-inline-image' ) ) {
				_deprecated_hook( 'wds-mega-menu-inline-image', '0.3.1', 'wdsmm_inline_image' );
			}

			$item_use_real_image = apply_filters( 'wdsmm_inline_image', true );

			// Start the menu item wrap so it can contain a potential sidebar widget
			$item_output .= '<div class="menu-item-container">';

			// Add the content of the walker.
			if ( has_post_thumbnail( $item->ID ) ) {
				if ( has_filter( 'wds-mega-menus-image-size' ) ) {
					_deprecated_hook( 'wds-mega-menus-image-size', '0.3.1', 'wdsmm_image_size' );
				}

				$image_size = apply_filters( 'wdsmm_image_size', 'full' );

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
						$item_output .= $item_title;
						$item_output .= $item_content;
						$item_output .= $item_read_more;
					$item_output .= '</div>';
			}

			// Look for a widget area for this menu item
			$widget_area = get_post_meta( $item->ID, '_menu_item_widget_area', true );

			// Place the widget area into the menu item
			if ( $widget_area ) {
				ob_start();
				dynamic_sidebar( $widget_area );
				$item_output .= ob_get_contents();
				ob_end_clean();
			}

			$item_output .= isset( $args->after ) ? $args->after : '';

			// Close the menu item content container
			$item_output .= '</div>'; // .menu-item-container

			/**
			 * Filter a menu item's starting output.
			 *
			 * The menu item's starting output only includes `$args->before`, the opening `<a>`,
			 * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
			 * no filter for modifying the opening and closing `<li>` for a menu item.
			 *
			 * @since  0.1.0
			 * @author Dustin Filippini, Aubrey Portwood
			 *
			 * @param  string $item_output The menu item's starting HTML output.
			 * @param  object $item        Menu item data object.
			 * @param  int    $depth       Depth of menu item. Used for padding.
			 * @param  array  $args        An array of {@see wp_nav_menu()} arguments.
			 */
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}

		/**
		 * Ends the element output, if needed.
		 *
		 * @see Walker::end_el()
		 *
		 * @since  0.1.0
		 * @author Dustin Filippini, Aubrey Portwood
		 *
		 * @param  string $output Passed by reference. Used to append additional content.
		 * @param  object $item   Page data object. Not used.
		 * @param  int    $depth  Depth of page. Not Used.
		 * @param  array  $args   An array of arguments.
		 * @see    wp_nav_menu()
		 */
		public function end_el( &$output, $item, $depth = 0, $args = array() ) {
			$output .= "</li>\n";
		}

		/**
		 * Returns the SVG markup.
		 *
		 * @since  0.1.0
		 * @author Dustin Filippini, Aubrey Portwood
		 *
		 * @param  string $icon_name The SVG icon slug/name.
		 * @return string            The full SVG markup.
		 */
		private function get_svg( $icon_name ) {
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
} // if ( ! class_exists( 'WDS_Mega_Menus_Walker_Nav_Menu_Edit' )
