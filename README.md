# WDS Mega Menus
**Contributors:**      [dustyf](https://github.com/dustyf), [jazzsequence](https://github.com/jazzsequence), [CamdenSegal](https://github.com/CamdenSegal), [aubreypwd](https://github.com/aubreypwd), [coreymcollins](https://github.com/coreymcollins), [phatsk](https://github.io/phatsk), [PavelK27](https://github.com/PavelK27), [jomurgel](https://github.com/jomurgel)  
**Donate link:**       http://webdevstudios.com  
**Tags:**  
**Requires at least:** 3.6.0  
**Tested up to:**      4.6.1  
**Stable tag:**        0.3.1  
**License:**           GPLv2  
**License URI:**       http://www.gnu.org/licenses/gpl-2.0.html  

## Description ##

Make magnificently magical Mega Menus.

**License:**           GPLv2

**License URI:**       http://www.gnu.org/licenses/gpl-2.0.html

This project is currently under development. If you want to help out,
check out the [Issues](https://github.com/WebDevStudios/WDS-Mega-Menu/issues) tab.

### To Use

#### The Easy Way

As of version 0.2.1, if you have nothing hooked on the `wdsmm_walker_nav_allowed_depths` filter, a
new admin page called "WDS Mega Menus" will appear under the WordPress Appearance menu.

#### The Manual Way

To use, you must tell WDS Mega Menu which menu levels to apply to. E.g.:

```
function my_allowed_depths( $array ) {
	// Allow at depths 0, 1, 2, and 3.
	return array( 0, 1, 2, 3 );
}

add_filter( 'wdsmm_walker_nav_allowed_depths', 'my_allowed_depths' );
```

#### ❕0.3.0 Compatibility Notice

Previously, the filter was named `wds_mega_menus_walker_nav_menu_edit_allowed_depths`. This filter has been renamed to `wdsmm_walker_nav_allowed_depths`. Please update your functions if you are upgrading from a version earlier than 0.3.0.

## Installation ##

### Manual Installation ###

1. Upload the entire `/wds-mega-menus` directory to the `/wp-content/plugins/` directory.
2. Activate WDS Mega Menus through the 'Plugins' menu in WordPress.

## Frequently Asked Questions ##
_none yet_

## Screenshots ##
_none yet_

## Changelog ##

### 0.3.1 ###
* Various bug fixes.
* Better SVG support.
* Javascript updates for better practices.

### 0.3.0 ###
* Default SVG icons now appear in admin dropdown if no SVG icons exist in theme.
* Depths updated to checkboxes for allowed levels.
* Code cleanup.

### 0.2.1 ###
* Add options page (hidden if filter is set).
* Allow depths to be configured from the admin area.

### 0.1.0 ###
* First release

