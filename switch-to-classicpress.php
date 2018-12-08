<?php
/*
 * Plugin Name:       Switch to ClassicPress
 * Plugin URI:        https://github.com/ClassicPress/ClassicPress-Migration-Plugin
 * Description:       Switch your WordPress installation to ClassicPress.
 * Version:           0.4.0
 * Tested up to:      5.0
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
		'switch-to-classicpress',
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
 * On multisite, the plugin must be network activated.
 *
 * @since 0.2.0
 */
function classicpress_ensure_network_activated() {
	if ( ! is_multisite() ) {
		return;
	}

	if ( ! is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
		add_action( 'admin_notices', 'classicpress_deactivated_notice' );

		deactivate_plugins( array( plugin_basename( __FILE__ ) ) );

		// HACK: Prevent the "Plugin activated" notice from showing.
		unset( $_GET['activate'] );
	}
}
add_action( 'admin_head', 'classicpress_ensure_network_activated' );

/**
 * Shows a notice that the plugin was deactivated.
 *
 * @since 0.2.0
 */
function classicpress_deactivated_notice() {
	echo '<div class="error"><p>';
	_e(
		'The "Switch to ClassicPress" plugin must be <strong>network activated</strong> on multisite installations.',
		'switch-to-classicpress'
	);
	echo '</p><p>';
	_e(
		'If you need help with this, please contact your site administrator.',
		'switch-to-classicpress'
	);
	echo '</p></div>';
}

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
	if ( function_exists( 'classicpress_version' ) ) {
		// Already running ClassicPress - showing this link is more confusing
		// than helpful.
		return $links;
	}

	if ( is_multisite() ) {
		// Multisite: Only add the "Switch" link if the plugin is network
		// activated and the current user can upgrade core.
		if (
			! is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ||
			! current_user_can( 'update_core' )
		) {
			return $links;
		}
		$upgrade_page_url = network_admin_url( 'index.php?page=switch-to-classicpress' );
	} else {
		// Single site: Always add the "Switch" link.
		$upgrade_page_url = admin_url( 'tools.php?page=switch-to-classicpress' );
	}

	$links = array_merge( array(
		'<a class="cp-migration-action" href="' . esc_url( $upgrade_page_url ) . '">'
		. __( 'Switch', 'switch-to-classicpress' )
		. '</a>'
	), $links );

	return $links;
}
add_filter(
	'plugin_action_links_' . plugin_basename( __FILE__ ),
	'classicpress_plugin_action_links'
);
add_filter(
	'network_admin_plugin_action_links_' . plugin_basename( __FILE__ ),
	'classicpress_plugin_action_links'
);
