<?php
/*
 * Plugin Name:       Switch to ClassicPress
 * Plugin URI:        https://github.com/ClassicPress/ClassicPress-Migration-Plugin
 * Description:       Switch your WordPress installation to ClassicPress.
 * Version:           0.0.1
 * Tested up to:      4.9
 * Author:            ClassicPress
 * Author URI:        https://www.classicpress.net
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * Text Domain:       switch-to-classicpress
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
 * Global actions and filters.
 *
 * @since 0.0.1
 */
add_filter( 'plugins_loaded', 'classicpress_load_plugin_textdomain' );

/**
 * Load the plugin's translated strings.
 *
 * @since 0.0.1
 */
function classicpress_load_plugin_textdomain() {
	load_plugin_textdomain(
		'switch-to-classicpress',
		false,
		basename( dirname( __FILE__ ) ) . '/languages'
	);
}

/**
 * Load the plugin's admin page.
 *
 * @since 0.0.1
 */
require_once dirname( __FILE__ ) . '/lib/admin-page.php';


/**
 * Load the update hijacking mechanism.
 *
 * @since 0.0.1
 */
require_once dirname( __FILE__ ) . '/lib/update.php';
