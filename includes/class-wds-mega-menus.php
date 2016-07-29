<?php
/**
 * As long as we don't have our base class already and we haven't
 * already initiated our base class.
 *
 * @package WDS_Mega_Menus
 */

if ( ! class_exists( 'WDS_Mega_Menus' ) && ! isset( $wds_mega_menus ) ) {
	require 'class-wds-mega-menu-walker.php';
	require 'class-wds-mega-menus-walker-nav-menu-edit.php';
	require 'class-wds-mega-menus-admin.php';

	/**
	 * WDS Mega Menus.
	 *
	 * This base class handles mostly the instance itself and the plugin
	 * as a whole.
	 *
	 * @since  0.1.0
	 * @package  WDS_Mega_Menus
	 */
	class WDS_Mega_Menus {

		/**
		 * Current version
		 *
		 * @var  string
		 * @since  NEXT
		 */
		const VERSION = '0.2.0';

		/**
		 * URL of plugin directory
		 *
		 * @var string
		 * @since  NEXT
		 */
		protected $url = '';

		/**
		 * Path of plugin directory
		 *
		 * @var string
		 * @since  NEXT
		 */
		protected $path = '';

		/**
		 * Plugin basename
		 *
		 * @var string
		 * @since  NEXT
		 */
		protected $basename = '';

		/**
		 * Singleton instance of plugin.
		 *
		 * @var WDS_Mega_Menus
		 * @since  0.1.0
		 */
		protected static $single_instance = null;

		/**
		 * Creates or returns an instance of this class.
		 *
		 * @since  0.1.0
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
		 * @since  0.1.0
		 */
		protected function __construct() {
			$this->basename = plugin_basename( __FILE__ );
			$this->url      = plugin_dir_url( __FILE__ );
			$this->path     = plugin_dir_path( __FILE__ );

			$this->plugin_classes();

			// Plugin text domain.
			load_plugin_textdomain( 'wds-mega-menus', false, dirname( __FILE__ ) . '/../languages/' );
		}

		/**
		 * Check if the theme has an svgs folder.
		 *
		 * @since  0.2.0
		 * @author Chris Reynolds
		 * @return bool Whether the theme has its own svgs directory.
		 */
		public function theme_has_svgs() {
			/**
			 * SVGs Directory
			 *
			 * Filter the directory path to the SVGs folder. Defaults to the current child theme in the /images/svg folder.
			 *
			 * @var   string wdsmm_svgs_directory
			 * @since 0.2.0
			 */
			$svgs_directory = apply_filters( 'wdsmm_svgs_directory', get_stylesheet_directory() . '/images/svg' );
			return file_exists( $svgs_directory );
		}

		/**
		 * Attach other plugin classes to the base plugin class.
		 *
		 * @author Chris Reynolds
		 * @since  0.2.0
		 */
		public function plugin_classes() {
			$this->admin = new WDS_Mega_Menus_Admin(); // Most of the stuff is here!
		}

		/**
		 * Magic getter for our object.
		 *
		 * @since  0.1.0
		 * @param  string $field The field we're trying to fetch.
		 * @throws Exception     Throws an exception if the field is invalid.
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
	 * @since  0.1.0
	 * @return WDS_Mega_Menus  Singleton instance of plugin class.
	 */
	function wds_mega_menus() {
		return WDS_Mega_Menus::get_instance();
	}

	// Launch our class.
	$wds_mega_menus = wds_mega_menus();
} // WDS_Mega_Menus class exists
