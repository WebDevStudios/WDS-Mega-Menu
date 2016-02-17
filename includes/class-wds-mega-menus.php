<?php

/*
 * As long as we don't have our base class already and we haven't
 * already initiated our base class.
 */
if ( ! class_exists( 'WDS_Mega_Menus' ) && ! isset( $wds_mega_menus ) ) {
	require 'class-wds-mega-menu-walker.php';
	require 'class-wds-mega-menus-walker-nav-menu-edit.php';

	/**
	 * WDS Mega Menus.
	 *
	 * @since  1.0.0
	 * @package  WDS_Mega_Menus
	 */
	class WDS_Mega_Menus {

		/**
		 * Current version
		 *
		 * @var  string
		 * @since  1.0.0
		 */
		const VERSION = '1.0.0';

		/**
		 * URL of plugin directory
		 *
		 * @var string
		 * @since  1.0.0
		 */
		protected $url      = '';

		/**
		 * Path of plugin directory
		 *
		 * @var string
		 * @since  1.0.0
		 */
		protected $path     = '';

		/**
		 * Plugin basename
		 *
		 * @var string
		 * @since  1.0.0
		 */
		protected $basename = '';

		/**
		 * Singleton instance of plugin
		 *
		 * @var WDS_Mega_Menus
		 * @since  1.0.0
		 */
		protected static $single_instance = null;

		/**
		 * Creates or returns an instance of this class.
		 *
		 * @since  1.0.0
		 * @return WDS_Mega_Menus A single instance of this class.
		 */
		public static function get_instance() {
			if ( null === self::$single_instance ) {
				self::$single_instance = new self();
			}

			return self::$single_instance;
		}

		/**
		 * Sets up our plugin
		 *
		 * @since  1.0.0
		 * @return  null
		 */
		protected function __construct() {
			$this->basename = plugin_basename( __FILE__ );
			$this->url      = plugin_dir_url( __FILE__ );
			$this->path     = plugin_dir_path( __FILE__ );

			require $this->path . 'class-wds-mega-menus-admin.php';
			$this->plugin_classes();
			$this->hooks();
		}

		/**
		 * Attach other plugin classes to the base plugin class.
		 *
		 * @since 1.0.0
		 * @return  null
		 */
		function plugin_classes() {
			$this->admin = new WDS_Mega_Menus_Admin( $this );
		}

		/**
		 * Add hooks and filters
		 *
		 * @since 1.0.0
		 * @return null
		 */
		public function hooks() {
			register_activation_hook( __FILE__, array( $this, '_activate' ) );
			register_deactivation_hook( __FILE__, array( $this, '_deactivate' ) );

			add_action( 'init', array( $this, 'init' ) );
		}

		/**
		 * Activate the plugin
		 *
		 * @since  1.0.0
		 * @return null
		 */
		function _activate() {
			// Make sure any rewrite functionality has been loaded
			flush_rewrite_rules();
		}

		/**
		 * Deactivate the plugin
		 * Uninstall routines should be in uninstall.php
		 *
		 * @since  1.0.0
		 * @return null
		 */
		function _deactivate() {}

		/**
		 * Init hooks
		 *
		 * @since  1.0.0
		 * @return null
		 */
		public function init() {
			load_plugin_textdomain( 'wds-mega-menus', false, dirname( $this->basename ) . '/languages/' );
		}

		/**
		 * Magic getter for our object.
		 *
		 * @since  1.0.0
		 * @param string $field
		 * @throws Exception Throws an exception if the field is invalid.
		 * @return mixed
		 */
		public function __get( $field ) {
			switch ( $field ) {
				case 'version':
					return self::VERSION;
				case 'basename':
				case 'url':
				case 'path':
					return $this->$field;
				default:
					throw new Exception( 'Invalid '. __CLASS__ .' property: ' . $field );
			}
		}
	} // class WDS_Mega_Menus

	/**
	 * Grab the WDS_Mega_Menus object and return it.
	 *
	 * Wrapper for WDS_Mega_Menus::get_instance()
	 *
	 * @since  1.0.0
	 *
	 * @return WDS_Mega_Menus  Singleton instance of plugin class.
	 */
	function wds_mega_menus() {
		return WDS_Mega_Menus::get_instance();
	}

	// Launch our class.
	$wds_mega_menus = wds_mega_menus();
} // WDS_Mega_Menus class exists
