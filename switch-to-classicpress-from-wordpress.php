<?php
/**
 * Plugin Name:       Switch to ClassicPress from WordPress
 * Plugin URI:        https://github.com/classicpress/classicpress-migration-plugin
 * Description:       Switch to ClassicPress from WordPress.
 * Version:           1.0.0
 * Tested up to:      4.9
 * Author:            ClassicPress
 * Author URI:        https://www.classicpress.net
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * Text Domain:       switch-to-classicpress-from-wordpress
 *
 * @package ClassicPress
 */

/**
 * Prevent direct access to plugin files.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Actions and Filters.
 */
add_filter( 'plugins_loaded', 'classicpress_load_plugin_textdomain' );
add_filter( 'muplugins_loaded', 'classicpress_load_muplugin_textdomain' );
add_filter( 'admin_init', 'classicpress_remove_gutenberg_dashboard_widget' );

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
