<?php
/**
 * Mega Menu Admin.
 *
 * @package WDS_Mega_Menus
 */

if ( ! class_exists( 'WDS_Mega_Menus_Admin' ) ) {

	/**
	 * Mega Menu Administration.
	 *
	 * @package  WDS_Mega_Menus
	 * @since  0.1.0
	 */
	class WDS_Mega_Menus_Admin {
		/**
		 * Constructor
		 *
		 * @since 0.1.0
		 * @return  null
		 */
		public function __construct() {
			add_filter( 'wp_setup_nav_menu_item', array( $this, 'register_nav_field' ) );
			add_action( 'wp_update_nav_menu_item', array( $this, 'update_nav_fields' ), 10, 3 );
			add_filter( 'wp_edit_nav_menu_walker', array( $this, 'nav_menu_edit_walker' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}

		/**
		 * Enqueue scripts.
		 */
		public function admin_enqueue_scripts() {
			if ( 'nav-menus' !== get_current_screen()->id ) {
				return; // Only show on nav-menu's screen.
			}

			wp_enqueue_media();
			wp_enqueue_style( 'wdsmm-admin', plugins_url( '../assets/css/admin.css', __FILE__ ), array(), time() );
			wp_enqueue_script( 'wds-mega-menus', plugins_url( '../assets/js/wds-mega-menus.js', __FILE__ ), array( 'jquery' ), time() );
			wp_enqueue_script( 'bootstrap-dropdown', plugins_url( '../assets/js/dropdowns-enhancement.js', __FILE__ ), array( 'jquery' ), time(), true );
			wp_enqueue_script( 'wdsmm-svg-defs', wds_mega_menus()->url . '/assets/js/svgs.js', array( 'jquery' ), wds_mega_menus()->version, true );

			wp_localize_script( 'wdsmm-svg-defs', 'svg_defs', array(
				'svgs' => $this->include_svg_definitions(),
			) );
		}

		/**
		 * Filter the walker being used for the menu edit screen
		 *
		 * @return string
		 */
		public function nav_menu_edit_walker() {
			return 'WDS_Mega_Menus_Walker_Nav_Menu_Edit';
		}

		/**
		 * Register a field for the nav menu
		 *
		 * @param object $menu_item The menu item object.
		 * @return mixed
		 */
		public function register_nav_field( $menu_item ) {
			$menu_item->image = get_post_thumbnail_id( $menu_item->ID );
			$menu_item->icon = get_post_meta( $menu_item->ID, '_menu_item_icon', true );
			$menu_item->icon = get_post_meta( $menu_item->ID, '_menu_item_widget_area', true );
			return $menu_item;
		}

		/**
		 * Save the new field data for the nav menu.
		 *
		 * @param int   $menu_id         Not used here.
		 * @param int   $menu_item_db_id The menu item post ID.
		 * @param array $args            Not used here.
		 * @since 0.1.0
		 * @todo Maybe add nonces when getting data from $_POST?
		 */
		public function update_nav_fields( $menu_id, $menu_item_db_id, $args ) {

			// Hide on mobile.
			if ( isset( $_POST['hide-menu-on-mobile'][ $menu_item_db_id ] ) ) {
				update_post_meta( $menu_item_db_id, 'hide_menu_on_mobile', empty( $_POST['hide-menu-on-mobile'][ $menu_item_db_id ] ) ? false : 'on' );
			} else {
				delete_post_meta( $menu_item_db_id, 'hide_menu_on_mobile' );
			}

			// Image.
			if ( isset( $_POST['menu-item-image'] ) && is_array( $_POST['menu-item-image'] ) ) {
				if ( ! isset( $_POST['menu-item-image'][$menu_item_db_id] ) || ! $_POST['menu-item-image'][$menu_item_db_id] ) {
					delete_post_thumbnail( $menu_item_db_id );
				}

				if ( isset( $_POST['menu-item-image'][$menu_item_db_id] ) ) {
					set_post_thumbnail( $menu_item_db_id, absint( $_POST['menu-item-image'][$menu_item_db_id] ) );
				}
			}

			if ( isset( $_POST['menu-item-icon'] ) && is_array( $_POST['menu-item-icon'] ) ) {
				if ( isset( $_POST['menu-item-icon'][$menu_item_db_id] ) ) {
					update_post_meta( $menu_item_db_id, '_menu_item_icon', sanitize_text_field( $_POST['menu-item-icon'][$menu_item_db_id] ) );
				}
			}

			if ( isset( $_POST['menu-item-widget-area'] ) && isset( $_POST['menu-item-widget-area'][$menu_item_db_id] ) && is_array( $_POST['menu-item-widget-area'] ) ) {
				update_post_meta( $menu_item_db_id, '_menu_item_widget_area', sanitize_text_field( $_POST['menu-item-widget-area'][$menu_item_db_id] ) );
			}

		}

		/**
		 * Add SVG definitions to <head>.
		 *
		 * @author Chris Reynolds
		 * @since  0.2.0
		 */
		public function include_svg_definitions() {
			// Require the svg-defs.svg file.
			if ( file_exists( wds_mega_menus()->svg_defs ) ) {
				require_once( wds_mega_menus()->svg_defs );
			}
		}
	} // class WDS_Mega_Menus_Admin
} // if class WDS_Mega_Menus_Admin.
