# WDS Mega Menus
**Contributors:**      [dustyf](https://github.com/dustyf), [jazzsequence](https://github.com/jazzsequence), [CamdenSegal](https://github.com/CamdenSegal), [aubreypwd](https://github.com/aubreypwd)
**Donate link:**       http://webdevstudios.com
**Tags:**
**Requires at least:** 3.6.0
**Tested up to:**      4.4.2
**Stable tag:**        0.2.0
**License:**           GPLv2
**License URI:**       http://www.gnu.org/licenses/gpl-2.0.html

## Description ##

Make magnificently magical Mega Menus.

**License:**           GPLv2

**License URI:**       http://www.gnu.org/licenses/gpl-2.0.html

This project is currently under development. If you want to help out,
check out the [Issues](https://github.com/WebDevStudios/WDS-Mega-Menu/issues) tab.

### To Use

To use, you must tell WDS Mega Menu which menu levels to apply to. E.g.:

```
function my_allowed_depths( $array ) {
	// Allow at depths 0, 1, 2, and 3.
	return array( 0, 1, 2, 3 );
}

add_filter( 'wds_mega_menus_walker_nav_menu_edit_allowed_depths', 'my_allowed_depths' );
```

This feature, in a future version, will be changed to allow all depths with
the ability to set max depths and disallowed depths.

## Installation ##

### Manual Installation ###

1. Upload the entire `/wds-mega-menus` directory to the `/wp-content/plugins/` directory.
2. Activate WDS Mega Menus through the 'Plugins' menu in WordPress.

## Frequently Asked Questions ##
_none yet_

## Screenshots ##
_none yet_

## Changelog ##

### 0.1.0 ###
* First release

