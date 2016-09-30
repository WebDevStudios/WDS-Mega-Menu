<?php
/**
 * Plugin Name: WDS Mega Menus
 * Plugin URI:  http://webdevstudios.com
 * Description: Make Magnificently Magical Mega Menus and More
 * Version:     0.3.0
 * Author:      WebDevStudios
 * Author URI:  http://webdevstudios.com
 * Donate link: http://webdevstudios.com
 * License:     GPLv2
 * Text Domain: wds-mega-menus
 * Domain Path: /languages
 *
 * @link http://webdevstudios.com
 *
 * @package WDS Mega Menus
 * @version 0.3.0
 */

/*
 *  _ _ _  ___  ___   __ __                  __ __
 * | | | || . \/ __> |  \  \ ___  ___  ___  |  \  \ ___ ._ _  _ _  ___
 * | | | || | |\__ \ |     |/ ._>/ . |<_> | |     |/ ._>| ' || | |<_-<
 * |__/_/ |___/<___/ |_|_|_|\___.\_. |<___| |_|_|_|\___.|_|_|`___|/__/
 *                               <___'
 *
 * WDS Mega Menus is a plugin that helps you customize things in the WP Nav.
 *
 * Copyright (c) 2015 WebDevStudios (email : contact@webdevstudios.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( defined( 'DISABLE_WDS_MEGA_MENU' ) && DISABLE_WDS_MEGA_MENU ) {
	return; // Bail if they configure this not to load.
}

/**
 * Autoloads files with classes when needed
 *
 * @since  0.3.0
 * @author Chris Reynolds
 * @param  string $class_name Name of the class being requested.
 */
function wds_menus_autoload_classes( $class_name ) {
	if ( 0 !== strpos( $class_name, 'WDS_Mega_Menu_' ) ) {
		return;
	}

	$filename = strtolower( str_replace(
		'_', '-',
		substr( $class_name, strlen( 'WDS_Mega_Menu_' ) )
	) );

	WDS_Mega_Menus::include_file( $filename );
}
spl_autoload_register( 'wds_menus_autoload_classes' );

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
	 * @since  0.1.0
	 */
	const VERSION = '0.3.0';

	/**
	 * URL of plugin directory
	 *
	 * @var string
	 * @since  0.1.0
	 */
	protected $url = '';

	/**
	 * Path of plugin directory
	 *
	 * @var string
	 * @since  0.1.0
	 */
	protected $path = '';

	/**
	 * Plugin basename
	 *
	 * @var string
	 * @since  0.1.0
	 */
	protected $basename = '';

	/**
	 * Admin Nav Menus class instance
	 *
	 * @var   string
	 * @since 0.3.0
	 */
	protected $admin = '';

	/**
	 * Options class instance
	 *
	 * @var   string
	 * @since 0.3.0
	 */
	protected $options = '';

	/**
	 * Default svg-defs.svg path
	 *
	 * @var   string
	 * @since 0.3.0
	 */
	protected $svg_defs = '';

	/**
	 * Default /svg assets path
	 *
	 * @var   string
	 * @since 0.3.0
	 */
	protected $svg = '';

	/**
	 * Singleton instance of plugin.
	 *
	 * @var   WDS_Mega_Menus
	 * @since 0.1.0
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
		$this->svg_defs = $this->path . '/assets/svg-defs.svg';
		$this->svg      = $this->path . '/assets/svg/';

		require $this->path . 'includes/class-menu-walker.php';
		require $this->path . 'includes/class-walker-nav-menu-edit.php';
		require $this->path . 'includes/class-menu-admin.php';
		require $this->path . 'includes/class-options.php';
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @author Chris Reynolds
	 * @since  0.2.0
	 */
	public function plugin_classes() {
		// Attach other plugin classes to the base plugin class.
		$this->admin   = new WDS_Mega_Menus_Admin();
		$this->options = new WDS_Mega_Menus_Options( $this );
	} // END OF PLUGIN CLASSES FUNCTION

	/**
	 * Add hooks and filters
	 *
	 * @since  0.3.0
	 * @author Chris Reynolds
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Activate the plugin
	 *
	 * @since  0.3.0
	 * @author Chris Reynolds
	 */
	public function _activate() {
		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin
	 * Uninstall routines should be in uninstall.php
	 *
	 * @since  0.3.0
	 * @author Chris Reynolds
	 */
	public function _deactivate() {}


	/**
	 * Init hooks
	 *
	 * @since  0.3.0
	 * @return void
	 */
	public function init() {
		if ( $this->check_requirements() ) {
			load_plugin_textdomain( 'wds-mega-menus', false, dirname( $this->basename ) . '/languages/' );
			$this->plugin_classes();
			$this->update_svg_paths();
		}
	}

	/**
	 * Check if the plugin meets requirements and
	 * disable it if they are not present.
	 *
	 * @since  0.3.0
	 * @return boolean result of meets_requirements
	 */
	public function check_requirements() {
		if ( ! $this->meets_requirements() ) {

			// Add a dashboard notice.
			add_action( 'all_admin_notices', array( $this, 'requirements_not_met_notice' ) );

			// Deactivate our plugin.
			add_action( 'admin_init', array( $this, 'deactivate_me' ) );

			return false;
		}

		return true;
	}

	/**
	 * Deactivates this plugin, hook this function on admin_init.
	 *
	 * @since  0.3.0
	 * @author Chris Reynolds
	 */
	public function deactivate_me() {
		deactivate_plugins( $this->basename );
	}

	/**
	 * Check that all plugin requirements are met
	 *
	 * @since  0.3.0
	 * @author Chris Reynolds
	 * @return boolean True if requirements are met.
	 */
	public static function meets_requirements() {
		// Do checks for required classes / functions
		// function_exists('') & class_exists('').
		// We have met all requirements.
		return true;
	}

	/**
	 * Adds a notice to the dashboard if the plugin requirements are not met
	 *
	 * @since  0.3.0
	 * @author Chris Reynolds
	 */
	public function requirements_not_met_notice() {
		// Output our error.
		echo '<div id="message" class="error">';
		echo '<p>' . sprintf( __( 'WDS Mega Menus is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'wds-mega-menus' ), admin_url( 'plugins.php' ) ) . '</p>';
		echo '</div>';
	}

	/**
	 * Update SVG paths.
	 *
	 * Updates the default $this->svg_defs and $this->svg paths if the theme has svgs.
	 *
	 * @since  0.2.0
	 * @author Chris Reynolds
	 */
	public function update_svg_paths() {
		if ( $this->have_svgs() ) {
			/**
			 * SVG Defs Path
			 *
			 * @since  0.2.0
			 * @author Chris Reynolds
			 * @var string wdsmm_svg_defs_path
			 */
			$this->svg_defs = apply_filters( 'wdsmm_svg_defs_path', get_stylesheet_directory() . '/assets/svg-defs.svg' );

			/**
			 * SVGs Directory
			 *
			 * Filter the directory path to the SVGs folder. Defaults to the current child theme in the /images/svg folder.
			 *
			 * @var   string wdsmm_svgs_directory
			 * @since 0.2.0
			 */
			$this->svg = apply_filters( 'wdsmm_svgs_directory', get_stylesheet_directory() . '/assets/svg/' );
		}
	}

	/**
	 * Check if we already have an svgs folder.
	 *
	 * @since  0.2.0
	 * @author Chris Reynolds
	 * @return bool Whether we already have our own svgs directory. Checks the theme by default.
	 */
	public function have_svgs() {
		/**
		 * SVGs Directory
		 *
		 * Filter the directory path to the SVGs folder. Defaults to /images/svg in the current child theme folder.
		 *
		 * @var   string wdsmm_svgs_directory
		 * @since 0.2.0
		 */
		$svgs_directory = apply_filters( 'wdsmm_svgs_directory', get_stylesheet_directory() . '/assets/svg' );
		return file_exists( $svgs_directory );
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
			case 'svg_defs':
			case 'svg':
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

// Kick it off.
add_action( 'plugins_loaded', array( wds_mega_menus(), 'hooks' ) );

register_activation_hook( __FILE__, array( wds_mega_menus(), '_activate' ) );
register_deactivation_hook( __FILE__, array( wds_mega_menus(), '_deactivate' ) );

