<?php

if ( ! class_exists( 'WDS_Mega_Menus_Admin' ) ) {
	/**
	 * Mega Menu Administration.
	 *
	 * @package  WDS_Mega_Menus
	 * @since  1.0
	 */
	class WDS_Mega_Menus_Admin {
		/**
		 * Parent plugin class
		 *
		 * @var class
		 * @since  1.0.0
		 */
		protected $plugin = null;

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 * @return  null
		 */
		public function __construct( $plugin ) {
			$this->plugin = $plugin;
			$this->hooks();
		}

		/**
		 * Initiate our hooks
		 *
		 * @since 1.0.0
		 * @return  null
		 */
		public function hooks() {

			add_filter( 'wp_setup_nav_menu_item', array( $this, 'register_nav_field' ) );
			add_action( 'wp_update_nav_menu_item', array( $this, 'update_nav_fields'), 10, 3 );
			add_filter( 'wp_edit_nav_menu_walker', array( $this, 'nav_menu_edit_walker' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
			add_action( 'admin_print_styles', array( $this, 'print_styles' ) );
			add_action( 'admin_print_scripts', array( $this, 'include_svg_definitions' ) );

		}

		/**
		 * Enqueue JavaScript
		 */
		public function enqueue() {
			$screen = get_current_screen();

			if ( 'nav-menus'  !== $screen->id ) {
				return;
			}

			wp_enqueue_media();
			wp_enqueue_script( 'wds-mega-menus', $this->plugin->url . 'assets/js/wds-mega-menus.js', array( 'jquery' ), '1.0.0' );

			wp_enqueue_style( 'wdsmm-admin', $this->plugin->url . 'assets/css/admin.css', '', '20150727' );
			wp_enqueue_script( 'bootstrap-dropdown', $this->plugin->url . 'assets/js/dropdowns-enhancement.js', array( 'jquery' ), '20150724', true );
		}

		/**
		 * Add a small bit of styling
		 */
		public function print_styles() {
			$screen = get_current_screen();
			if ( 'nav-menus'  !== $screen->id ) {
				return;
			}
			?>
			<style>
				.menu-item-image-container img {
					width:  100%;
					height: auto;
				}
			</style>
			<?php
		}

		/**
		 * Add SVG definitions to <head>.
		 */
		public function include_svg_definitions() {
			$screen = get_current_screen();
			if ( 'nav-menus'  !== $screen->id ) {
				return;
			}

			// Define svg sprite file
			$svg_defs = get_template_directory() . '/images/svg-defs.svg';

			// If it exsists, include it
			if ( file_exists( $svg_defs ) ) {
				require_once( $svg_defs );
			}
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
		 * @param $menu_item
		 *
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
		 * @param $menu_id
		 * @param $menu_item_db_id
		 * @param $args
		 */
		public function update_nav_fields( $menu_id, $menu_item_db_id, $args ) {

			// Hide on mobile.
			if ( isset( $_POST['hide-menu-on-mobile'][$menu_item_db_id] ) ) {
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

	} // class WDS_Mega_Menus_Admin
} // if class WDS_Mega_Menus_Admin.
