<?php
/**
 * Mega Menus Admin Options.
 *
 * @package WDS_Mega_Menus
 * @author  Zach Owen
 * @version 0.2.1
 */

if ( ! class_exists( 'WDS_Mega_Menus_Options' ) ) {
	/**
	 * Mega Menus Options.
	 *
	 * @package WDS_Mega_Menus
	 * @since   0.2.1
	 */
	class WDS_Mega_Menus_Options {
		/**
		 * Registered fields to display and save.
		 *
		 * @since 0.2.1
		 * @var   array
		 */
		private $fields = array();

		/**
		 * Slug to use for the menu.
		 *
		 * @since 0.2.1
		 * @var   string
		 */
		private $menu_slug = 'wds-mega-menu';

		/**
		 * Nonce action to use.
		 *
		 * @since 0.2.1
		 * @var   string
		 */
		private $nonce_action = 'wds-mega-menus-update';

		/**
		 * Nonce field to use.
		 *
		 * @since 0.2.1
		 * @var   string
		 */
		private $nonce_field = 'wmm-nonce';

		/**
		 * Option key to use for getting admin options.
		 *
		 * @since 0.2.1
		 * @var   string
		 */
		private $option_key = 'wds_mega_menus_options';

		/**
		 * Static holder for options.
		 *
		 * @since 0.2.1
		 * @var   array
		 */
		private static $options;
		/**
		 * The page to hook to for the menu.
		 *
		 * @since 0.2.1
		 * @var   string
		 */
		private $page = 'themes.php';

		/**
		 * Page title to use.
		 *
		 * @since 0.2.1
		 * @var   string
		 */
		private $page_title = '';
		/**
		 * Parent class plugin instance.
		 *
		 * @since 0.2.1
		 * @var   WDS_Mega_Menus
		 */
		public $plugin;

		/**
		 * Constructor
		 *
		 * @since  0.2.1
		 * @author Zach Owen
		 * @param  WDS_Mega_Menus $plugin The parent plugin of this class.
		 */
		public function __construct( $plugin ) {
			$this->plugin     = $plugin;
			$this->page_title = __( 'WDS Mega Menus Options', 'wds-mega-menus' );
			$this->hooks();
		}

		/**
		 * Deepest menu depth.
		 *
		 * @since  0.2.1
		 * @author Zach Owen
		 * @var    int
		 */
		private $deepest_menu = 1;

		/**
		 * Show the options page. This also handles saving.
		 *
		 * @since  0.2.1
		 * @author Zach Owen, Chris Reynolds
		 */
		public function handle_options_page() {
			$this->deepest_menu = $this->get_deepest_menu() ?: 1;

			/**
			 * Some quick todos for later.
			 *
			 * @TODO this looks ugly with that long description :/
			 * @TODO Also, I think I've just created a poor man's CMB. Butts.
			 */
			$this->add_field( array(
				'key'   => 'wds_mega_menus_depth',
				'title' => __( 'Applied Menu Depth(s)', 'wds-mega-menus' ),
				'desc'  => __( '<em>Select menu levels to apply the Mega Menu to.</em>', 'wds-mega-menus' ), // Replaces wdsmm_walker_nav_allowed_depths filter).
			) );

			// Check to see if anything is saved.
			$this->check_for_save();

			// Output the form and fields.
			echo $this->open_options_page();
			echo $this->get_options_fields();

			wp_nonce_field( $this->nonce_action, $this->nonce_field );

			echo $this->close_options_page();
		}

		/**
		 * Hooks to activate the options page.
		 *
		 * @since  0.2.1
		 * @author Zach Owen
		 */
		public function hooks() {
			add_action( 'admin_menu', array( $this, 'register_menu' ) );
			add_filter( 'wds_mega_menus_options_save', array( $this, 'process_depth_value' ), 10, 2 );
			add_filter( 'wds_mega_menus_input', array( $this, 'render_depth_input' ), 10, 3 );
		}

		/**
		 * Register the submenu.
		 *
		 * @since  0.2.1
		 * @author Zach Owen, Chris Reynolds
		 */
		public function register_menu() {
			if ( has_filter( 'wdsmm_walker_nav_allowed_depths' ) ) {
				return;
			}

			// Check for the old filter and display a notice if it's being used. Add the submenu page for those folks.
			if ( has_filter( 'wds_mega_menus_walker_nav_menu_edit_allowed_depths' ) ) {
				_deprecated_hook( 'wds_mega_menus_walker_nav_menu_edit_allowed_depths', '0.3.0', 'wdsmm_walker_nav_allowed_depths' );
			}

			add_submenu_page(
				$this->page,
				$this->page_title,
				__( 'WDS Mega Menus', 'wds-mega-menus' ),
				'edit_theme_options',
				$this->menu_slug,
				array( $this, 'handle_options_page' )
			);
		}

		/**
		 * Returns the opening of the form page.
		 *
		 * @since  0.2.1
		 * @author Zach Owen
		 */
		private function open_options_page() {
			return <<<HTML
<form action="{$this->page}?page={$this->menu_slug}" method="post">
	<h2>{$this->page_title}</h2>
	<p>
	</p>
	<table class="form-table">
		<tbody>
HTML;
		}

		/**
		 * Close the options form table.
		 *
		 * @since  0.2.1
		 * @author Zach Owen, Pavel Korotenko
		 *
		 * @return string
		 * @todo   Properly enqueue the javascript here.
		 */
		private function close_options_page() {
			return <<<HTML
		</tbody>
	</table>
	<p class="submit"><input type="submit" value="Save Changes" class="button-primary" name="Submit"></p>
</form>
<script>
jQuery( document ).ready( function( $ ) {
	// Check all items if All depths checked and lock them
	$( '#all_depths' ).change( function() {
		if(this.checked) {
			$( ".depth_options" ).find( "li:not(:last-child) input" ).attr({
				//disabled: "true", @TODO CMB2 doesn't save checkboxes with disabled attr, need another way for locking these
			    checked: "checked"
			});
	    }
	    else {
	    	//$( ".depth_options" ).find( "li:not(:last-child) input" ).removeAttr( 'disabled' );
	    }

	});
	// Uncheck All depth checkbox if any other item was unchecked
	$( ".depth_options li:not(:last-child) input" ).change( function() {
		$( '#all_depths' ).removeAttr( 'checked' );
	});
});
</script>
HTML;
		}

		/**
		 * Get an option from the serialized options array.
		 *
		 * @since  0.2.1
		 * @author Zach Owen
		 *
		 * @param  string $key     The option key to get.
		 * @param  mixed  $default Optional default value to reutrn.
		 * @return mixed
		 */
		public function get_option( $key, $default = null ) {
			if ( null === self::$options ) {
				self::$options = get_option( $this->option_key );

				if ( ! self::$options ) {
					self::$options = array();
					update_option( $this->option_key, self::$options );
					return $default;
				}
			}

			if ( ! isset( self::$options[ $key ] ) ) {
				return $default;
			}

			// Return the test value if truthy, otherwise $default.
			return self::$options[ $key ] ?: $default;
		}

		/**
		 * Set an option to the serialized array and sync to the options table.
		 *
		 * @since  0.2.1
		 * @author Zach Owen
		 *
		 * @param  string $key   The option key to use.
		 * @param  mixed  $value The option value to set.
		 */
		private function set_option( $key, $value ) {
			self::$options[ $key ] = $value;
			update_option( $this->option_key, self::$options );
		}

		/**
		 * Add a field to the set to be rendered and saved.
		 * Fields should contain a key, title, and description.
		 *
		 * @since  0.2.1
		 * @author Zach Owen
		 *
		 * @param  array $field The field definition.
		 */
		private function add_field( $field ) {
			if ( ! isset( $this->fields[ $field['key'] ] ) ) {
				$this->fields[ $field['key'] ] = $field;
			}
		}

		/**
		 * Render option fields to go in the table. Bear in mind that this only accounts for text fields currently.
		 *
		 * @since  0.2.1
		 * @author Zach Owen
		 *
		 * @return string
		 */
		private function get_options_fields() {
			$html = '';

			foreach ( $this->fields as $field ) {
				$value = $this->get_option( $field['key'] );

				$input = vsprintf( '<input type="text" value="%1$s" id="%2$s" name="%2$s">', array(
					esc_attr( $value ),
					esc_attr( $field['key'] ),
				) );

				/**
				 * Filter an input's HTML before rendering the field.
				 *
				 * @since  0.2.1
				 * @author Zach Owen
				 *
				 * @param  string $field The field name.
				 * @param  mixed  $value The field value.
				 * @return string
				 */
				$input = apply_filters( 'wds_mega_menus_input', $input, $field, $value );

				$html .= <<<HTML
				<tr>
					<th scope="row">
						<label for="{$field['key']}">{$field['title']}</label>
					</th>
					<td>
						{$input}
						<br>
						<span class="description">
							{$field['desc']}
						</span>
					</td>
				</tr>
HTML;
			}

			return $html;
		}

		/**
		 * Check for and save form data.
		 *
		 * @since  0.2.1
		 * @author Zach Owen
		 */
		private function check_for_save() {
			if ( ! empty( $_POST ) && check_admin_referer( $this->nonce_action, $this->nonce_field ) ) {
				foreach ( $this->fields as $field ) {
					$key = $field['key'];
					if ( ! isset( $_POST[ $key ] ) ) {
						continue;
					}

					if ( empty( $key ) ) {
						$this->set_option( $key );
						continue;
					}

					/**
					 * Filter the value before saving.
					 *
					 * @since  0.2.1
					 * @author Zach Owen
					 *
					 * @param  string $key The key of the field being processed.
					 * @return mixed
					 */
					$value = apply_filters( 'wds_mega_menus_options_save', wp_unslash( $_POST[ $key ] ), $key );
					$this->set_option( $key, $value );
				}
			}
		}

		/**
		 * Process a saved depth value.
		 *
		 * @since  0.2.1
		 * @author Zach Owen, Pavel Korotenko
		 *
		 * @param  mixed  $value The menu depths value. Comma-separated list of depths.
		 * @param  string $key   The key of the field being processed.
		 * @return mixed
		 */
		public function process_depth_value( $value, $key ) {
			if ( 'wds_mega_menus_depth' !== $key ) {
				return array();
			}

			if ( empty( $value ) ) {
				return array();
			}

			return implode( ',', $value );
		}

		/**
		 * Render the depth input field.
		 *
		 * @since  0.2.1
		 * @author Zach Owen, Pavel Korotenko
		 *
		 * @param  string $input The input field HMTL.
		 * @param  string $field The field name.
		 * @param  mixed  $value The field value.
		 * @return string
		 */
		public function render_depth_input( $input, $field, $value ) {
			if ( 'wds_mega_menus_depth' !== $field['key'] ) {
				return;
			}

			// Set to 1 when the deepest menu is 0.
			$checked_items = explode( ',', $value );
			$html = '';
			$html .= '<ul class="depth_options">';

			for ( $i = 0; $i <= $this->deepest_menu; $i++ ) {
				$checked = '';

				if ( in_array( $i, $checked_items ) ) {
					$checked = 'checked="checked"';
				}

				$key   = esc_attr( $field['key'] );
				$html .= sprintf( '<li><input type="checkbox" name="%1$s[]" value="%3$s" %2$s />', $key, $checked, $i );
				$html .= sprintf( __( '<label for="%1$s">Menu Depth: %2$s</label></li>', 'wds-mega-menus' ), $key, $i );


			}
			$html .= sprintf( '<li><input id="all_depths" type="checkbox" name="%1$s[]" value="all" %2$s />', $key, in_array( "all", $checked_items ) ? 'checked' : '');
			$html .= sprintf( __( '<label for="%1$s">All Depths</label></li>', 'wds-mega-menus' ), $key );


			$i--;

			do {
				$html .= '</ul>';
			} while ( $i-- );

			return $html;
		}

		/**
		 * Find the deepest menu depth.
		 *
		 * @since  0.2.1
		 * @author Zach Owen
		 *
		 * @return int
		 */
		private function get_deepest_menu() {
			$deepest = 0;
			$menu_items = get_terms( 'nav_menu', array( 'hide_empty' => true ) );

			foreach ( $menu_items as $item ) {
				$nav_menu_items = isset( $menus[ $item->slug ] ) ? $menus[ $item->slug ] : wp_get_nav_menu_items( $item->slug );

				foreach ( $nav_menu_items as $nav_item ) {
					if ( ! $nav_item->menu_item_parent ) {
						continue;
					}

					$parent_meta = get_post_meta( $nav_item->menu_item_parent, '_menu_item_menu_item_parent', true );
					$depth       = 0;

					do {
						$depth++;
						$nav_item = get_post( $parent_meta );

						if ( ! $parent_meta || ! $nav_item ) {
							break;
						}

						$parent_meta = get_post_meta( $nav_item->ID, '_menu_item_menu_item_parent', true );
					} while ( ! empty( $parent_meta ) );

					if ( $depth > $deepest ) {
						$deepest = $depth;
					}
				}
			}

			// Account for WP menu levels.
			return $deepest + 1;
		}
	}
}
