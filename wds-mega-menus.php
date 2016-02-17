<?php

/*
Plugin Name: WDS Mega Menus
Plugin URI:  http://webdevstudios.com
Description: Make magnificently magical Mega Menus and more.
Version:     1.1-dev
Author:      WebDevStudios
Author URI:  http://webdevstudios.com
Donate link: http://webdevstudios.com
License:     GPLv2
Text Domain: wds-mega-menus
Domain Path: /languages
*/

/*
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

// Our base class.
require_once( 'includes/class-wds-mega-menus.php' );
