# WDS Mega Menus

Make magnificently magical Mega Menus.

**License:**           GPLv2

**License URI:**       http://www.gnu.org/licenses/gpl-2.0.html

This project is currently under development. If you want to help out,
check out the [Issues](https://github.com/WebDevStudios/WDS-Mega-Menu/issues) tab.

# To Use

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
