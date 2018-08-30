<?php 
/*
Plugin Name:       ClassicPress Migration Plugin
Plugin URI:        https://github.com/
Description:       ClassicPress migration plugin, and say Goodbye to Gutenberg.
Version:           1.0.1
Requires at least: 4.9
Tested up to:      4.9
Requires PHP:      5.2.4
Author:            ClassicPress Contributors
Author URI:        https://classicpress.net/
License:           GPLv2 or later (license.txt)
License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
Domain Path:       /languages
Text Domain:       classicpress-migration-plugin
Network:           true
GitHub Plugin URI: https://github.com/marctenax/hello-classic/
GitHub Branch:     master
Requires WP:       4.9
*/

	/**
	 * ClassicPress Migration Plugin.
	 *
	 * @package ClassicPress_Migration_Plugin
	 * @version 1.0.1 - 2018-08-30
	 * @since   1.0.0 - 2018-08-29
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
 * Currently Plugin Version.
 *
 * Start at version 1.0.0 and use SemVer - https://semver.org/
 */
define( 'CLASSICPRESS_MIGRATION_PLUGIN_VERSION', '1.0.1' );

add_filter( 'admin_menu', 'classicpress_remove_gutenberg_demo_menu', 999 );
add_filter( 'admin_init', 'classicpress_remove_gutenberg_dashboard_widget' );

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
