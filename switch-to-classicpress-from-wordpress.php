<?php 
/*
Plugin Name:       Switch to ClassicPress from WordPress
Plugin URI:        https://github.com/classicpress/classicpress-migration-plugin
Description:       Switch to ClassicPress from WordPress and say Goodbye to Gutenberg.
Version:           1.0.0
Requires at least: 4.9
Tested up to:      4.9
Requires PHP:      5.2.4
Author:            ClassicPress
Author URI:        https://www.classicpress.net
License:           GPLv2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
Domain Path:       /languages
Text Domain:       switch-to-classicpress-from-wordpress
Network:           true
GitHub Plugin URI: https://github.com/classicpress/classicpress-migration-plugin
GitHub Branch:     master
Requires WP:       4.9
*/

	/**
	 * Switch to ClassicPress from WordPress.
	 *
	 * ClassicPress Migration Plugin.
	 *
	 * @package Switch_to_ClassicPress_from_WordPress
	 * @version 1.0.0
	 * @todo    ClassicPress syle:
	 *           - All changes that reflect new direction.
	 */

/**
 * Prevent direct access to plugin files.
 *
 * For security reasons, exit without any notifications:
 * - without show the details of the system
 * - without warn the existence of this plugin
 * - show the generic header 403 forbidden error
 * - close the connection header
 */
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! defined(  'WPINC'  ) ) exit;

if ( ! function_exists( 'add_action' ) ) {
	header( 'HTTP/0.9 403 Forbidden' );
	header( 'HTTP/1.0 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	header( 'HTTP/2.0 403 Forbidden' );
	header( 'Status:  403 Forbidden' );
	header( 'Connection: Close'      );
		exit;
}

/**
 * Current Plugin Version.
 *
 * Start at version 1.0.0 and use SemVer - https://semver.org/
 */
define( 'SWITCH_TO_CLASSICPRESS_FROM_WORDPRESS_VERSION', '1.0.0' );

add_filter( 'admin_menu', 'classicpress_remove_gutenberg_demo_menu', 999 );
add_filter( 'admin_init', 'classicpress_remove_gutenberg_dashboard_widget' );
add_filter( 'plugins_loaded', 'classicpress_load_plugin_textdomain' );
add_filter( 'plugins_loaded', 'classicpress_load_muplugin_textdomain' );

/**
 * Load Plugin Textdomain.
 */
function classicpress_load_plugin_textdomain() {
	load_plugin_textdomain( 'switch-to-classicpress-from-wordpress', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

/**
 * Load MU-Plugin (dir) Textdomain.
 */
function classicpress_load_muplugin_textdomain() {
	load_muplugin_textdomain( 'switch-to-classicpress-from-wordpress', basename( dirname( __FILE__ ) ) . '/languages' );
}

/**
 * Remove Gutenberg Dashboard Widget.
 */
function classicpress_remove_gutenberg_dashboard_widget() {
	remove_filter( 'try_gutenberg_panel', 'wp_try_gutenberg_panel' );
}

/*
 * Remove Gutenberg Demo Menu.
 */
function classicpress_remove_gutenberg_demo_menu() {
	remove_menu_page( 'gutenberg' );
}
