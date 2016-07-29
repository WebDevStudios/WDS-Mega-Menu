<?php
/**
 * Mega Menus Admin Options.
 *
 * @package WDS_Mega_Menus
 * @version 0.1.0
 */

if ( ! class_exists( 'WDS_Mega_Menus_Options' ) ) {
	/**
	 * Mega Menus Options.
	 *
	 * @package  WDS_Mega_Menus
	 * @since  0.2.1
	 */
	class WDS_Mega_Menus_Options {
		/**
		 * Registered fields to display and save.
		 *
		 * @since 0.1.0
		 * @var   array
		 */
		private $fields = array();

		/**
		 * Slug to use for the menu.
		 *
		 * @since 0.1.0
		 * @var   string
		 */
		private $menu_slug = 'wds-mega-menu';

		/**
		 * Nonce action to use.
		 *
		 * @since 0.1.0
		 * @var   string
		 */
		private $nonce_action = 'wds-mega-menus-update';

		/**
		 * Nonce field to use.
		 *
		 * @since 0.1.0
		 * @var   string
		 */
		private $nonce_field = 'wmm-nonce';

		/**
		 * Option key to use for getting admin options.
		 *
		 * @since 0.1.0
		 * @var   string
		 */
		private $option_key = 'wds_mega_menus_options';

		/**
		 * Static holder for options.
		 *
		 * @since 0.1.0
		 * @var   array
		 */
		private static $options;
		/**
		 * The page to hook to for the menu.
		 *
		 * @since 0.1.0
		 * @var   string
		 */
		private $page = 'themes.php';

		/**
		 * Page title to use.
		 *
		 * @since 0.1.0
		 * @var   string
		 */
		private $page_title = '';
		/**
		 * Parent class plugin instance.
		 *
		 * @since 0.1.0
		 * @var   WDS_Mega_Menus
		 */
		public $plugin;

		/**
		 * Constructor
		 *
		 * @since 0.1.0
		 * @param WDS_Mega_Menus $plugin The parent plugin of this class.
		 */
		public function __construct( $plugin ) {
			$this->plugin     = $plugin;
			$this->page_title = __( 'WDS Mega Menus Options', 'wds-mega-menus' );
			$this->hooks();
		}

		/**
		 * Show the options page. This also handles saving.
		 *
		 * @since 0.1.0
		 */
		public function handle_options_page() {
			/**
			 * Some quick todos for later.
			 *
			 * @TODO this looks ugly with that long description :/
			 * @TODO Also, I think I've just created a poor man's CMB. Butts.
			 */
			$this->add_field( array(
				'key'   => 'wds_mega_menus_depth',
				'title' => __( 'Applied Menu Depth(s)', 'wds-mega-menus' ),
				'desc'  => __( '<em>Should be a comma-separated list of depths, e.g. "1,2,4"</em> (replaces <code>wds_mega_menus_walker_nav_menu_edit_allowed_depths</code> filter).', 'wds-mega-menus' ),
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
		 * @since 0.1.0
		 */
		public function hooks() {
			add_action( 'admin_menu', array( $this, 'register_menu' ) );
			add_filter( 'wds_mega_menus_options_save', array( $this, 'clean_depths' ), 10, 2 );
		}

		/**
		 * Register the submenu.
		 *
		 * @since 0.1.0
		 */
		public function register_menu() {
			if ( has_filter( 'wds_mega_menus_walker_nav_menu_edit_allowed_depths' ) ) {
				return;
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
		 * @since 0.1.0
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
		 * @since 0.1.0
		 *
		 * @return string
		 */
		private function close_options_page() {
			return <<<HTML
		</tbody>
	</table>
	<p class="submit"><input type="submit" value="Save Changes" class="button-primary" name="Submit"></p>
</form>
HTML;
		}

		/**
		 * Get an option from the serialized options array.
		 *
		 * @since  0.1.0
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
		 * @since 0.1.0
		 * @param string $key   The option key to use.
		 * @param mixed  $value The option value to set.
		 */
		private function set_option( $key, $value ) {
			self::$options[ $key ] = $value;
			update_option( $this->option_key, self::$options );
		}

		/**
		 * Add a field to the set to be rendered and saved.
		 * Fields should contain a key, title, and description.
		 *
		 * @since 0.1.0
		 * @param array $field The field definition.
		 */
		private function add_field( $field ) {
			if ( ! isset( $this->fields[ $field['key'] ] ) ) {
				$this->fields[ $field['key'] ] = $field;
			}
		}

		/**
		 * Render option fields to go in the table. Bear in mind that this only accounts for text fields currently.
		 *
		 * @since  0.1.0
		 * @return string
		 */
		private function get_options_fields() {
			$html = '';

			foreach ( $this->fields as $field ) {
				$value = $this->get_option( $field['key'] );
				$html .= <<<HTML
				<tr>
					<th scope="row">
						<label for="{$field['key']}">{$field['title']}</label>
					</th>
					<td>
						<input type="text" value="{$value}" id="{$field['key']}" name="{$field['key']}">
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
		 * @since 0.1.0
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

					$value = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );

					/**
					 * Filter the value before saving.
					 *
					 * @since  0.1.0
					 * @param  string $key The key of the field being processed.
					 * @return mixed
					 */
					$value = apply_filters( 'wds_mega_menus_options_save', $value, $key );
					$this->set_option( $key, $value );
				}
			}
		}

		/**
		 * Clean the mega menu depths option.
		 *
		 * @since 0.1.0
		 *
		 * @param  mixed  $value The menu depths value. Comma-separated list of depths.
		 * @param  string $key   The key of the field being processed.
		 * @return mixed
		 */
		public function clean_depths( $value, $key ) {
			if ( 'wds_mega_menus_depth' !== $key ) {
				continue;
			}

			if ( ! is_numeric( strtr( $value, array( ',' => '' ) ) ) ) {
				return '';
			}

			$value = strtr( trim( $value ), array( ' ' => '' ) );
			$value = rtrim( $value, ',' );
			return $value;
		}
	}
}
