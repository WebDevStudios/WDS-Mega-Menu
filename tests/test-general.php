<?php
/**
 * Class WDS_Mega_Menu_Tests
 *
 * @package WDS_Mega_Menu
 */

/**
 * General Tests for the plugin.
 */
class WDS_Mega_Menu_Tests extends WP_UnitTestCase {

	// Check if main class could be called.
	function test_main_class_init() {
		$main_class = wds_mega_menus();
		$this->assertNotNull( $main_class );
	}

	// Check if admin class init went fine.
	function test_admin_class_init() {
		$admin = wds_mega_menus()->admin;
		$this->assertNotNull( $admin );
	}

	// Check if options class init went fine.
	function test_options_class_init() {
		$options = wds_mega_menus()->options;
		$this->assertNotNull( $options );
	}

	// Check if the requirements are met.
	function test_requirements_met() {
		$meets_requirements = wds_mega_menus()->check_requirements();
		$this->assertTrue( $meets_requirements );
	}

	// Check if plugin's directory is set.
	function test_directory_exits() {
		$dir = wds_mega_menus()->dir();
		$this->assertNotNull( $dir );
	}

	// Check if plugin's url is set.
	function test_url_exits() {
		$url = wds_mega_menus()->url();
		$this->assertNotNull( $url );
	}

}
