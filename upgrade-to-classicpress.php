<?php
/*
 * Plugin Name:       Upgrade to ClassicPress
 * Plugin URI:        https://github.com/ClassicPress/ClassicPress-Migration-Plugin
 * Description:       Upgrade your WordPress installation to ClassicPress.
 * Version:           0.1.0
 * Tested up to:      4.9
 * Author:            ClassicPress
 * Author URI:        https://www.classicpress.net
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * Text Domain:       upgrade-to-classicpress
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
 * @since 0.1.0
 */
add_filter( 'plugins_loaded', 'classicpress_load_plugin_textdomain' );

/**
 * Load the plugin's translated strings.
 *
 * @since 0.1.0
 */
function classicpress_load_plugin_textdomain() {
	load_plugin_textdomain(
		'upgrade-to-classicpress',
		false,
		basename( dirname( __FILE__ ) ) . '/languages'
	);
}

/**
 * Load the plugin's admin page.
 *
 * @since 0.1.0
 */
require_once dirname( __FILE__ ) . '/lib/admin-page.php';

/**
 * Load helper functions.
 *
 * @since 0.2.0
 */
require_once dirname( __FILE__ ) . '/lib/check-core-files.php';

/**
 * Load the update hijacking mechanism.
 *
 * @since 0.1.0
 */
require_once dirname( __FILE__ ) . '/lib/update.php';

/**
 * Add plugin action links.
 *
 * Add a link to the Switch page on the plugins.php page.
 *
 * @since 0.1.0
 *
 * @param  array  $links List of existing plugin action links.
 *
 * @return array         List of modified plugin action links.
 */
function classicpress_plugin_action_links( $links ) {
	if ( ! is_multisite() ) {
		$links = array_merge( array(
			'<a class="cp-upgrade-action" href="' . esc_url( admin_url( 'tools.php?page=upgrade-to-classicpress' ) ) . '">' . __( 'Upgrade', 'upgrade-to-classicpress' ) . '</a>'
		), $links );
	}
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'classicpress_plugin_action_links' );
