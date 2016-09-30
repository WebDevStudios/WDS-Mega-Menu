<?php

class BaseTest extends WP_UnitTestCase {

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'WDS_Mega_Menus') );
	}
	
	function test_get_instance() {
		$this->assertTrue( wds_mega_menus() instanceof WDS_Mega_Menus );
	}
}
