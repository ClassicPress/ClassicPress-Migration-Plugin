<?php 
/*
 * Plugin Name:       Switch to ClassicPress
 * Plugin URI:        https://github.com/classicpress/classicpress-migration-plugin
 * Description:       Switch to ClassicPress from WordPress. ClassicPress Migration Plugin.
 * Version:           1.0.0
 * Requires at least: 4.9
 * Tested up to:      4.9
 * Requires PHP:      5.2.4
 * Author:            ClassicPress
 * Author URI:        https://www.classicpress.net
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * Text Domain:       switch-to-classicpress
 * Network:           true
 * GitHub Plugin URI: https://github.com/classicpress/classicpress-migration-plugin
 * GitHub Branch:     master
 * Requires WP:       4.9
 *
 * @package    ClassicPress
 * @subpackage Switch_to_ClassicPress
 * @version    1.0.0
 * @todo       ClassicPress syle:
 *              - Include plugin code in a class.
 *              - Add PHP Constructor.
 *              - Remove Gutenberg Demo Menu.
 *              - All changes that reflect new direction.
 */

add_filter( 'admin_init', 'classicpress_remove_gutenberg_dashboard_widget' );
add_filter( 'plugins_loaded', 'classicpress_load_plugin_textdomain' );
add_filter( 'plugins_loaded', 'classicpress_load_muplugin_textdomain' );

/**
 * Load Plugin Textdomain.
 */
function classicpress_load_plugin_textdomain() {
	load_plugin_textdomain( 'switch-to-classicpress', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

/**
 * Load MU-Plugin (dir) Textdomain.
 */
function classicpress_load_muplugin_textdomain() {
	load_muplugin_textdomain( 'switch-to-classicpress', basename( dirname( __FILE__ ) ) . '/languages' );
}

/**
 * Remove Gutenberg Dashboard Widget.
 */
function classicpress_remove_gutenberg_dashboard_widget() {
	remove_filter( 'try_gutenberg_panel', 'wp_try_gutenberg_panel' );
}

