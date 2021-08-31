<?php
/*
 * Plugin Name:       Switch to ClassicPress
 * Plugin URI:        https://github.com/ClassicPress/ClassicPress-Migration-Plugin
 * Description:       Switch your WordPress installation to ClassicPress.
 * Version:           1.4.1
 * Author:            ClassicPress
 * Author URI:        https://www.classicpress.net
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * Text Domain:       switch-to-classicpress
 * Requires at least: 4.9
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

/**
 * Call the ClassicPress API to determine which versions of WordPress and
 * ClassicPress are supported by the migration plugin.
 *
 * This is handled on the ClassicPress servers, because most of the time a new
 * version of WordPress or ClassicPress does not require a new version of the
 * migration plugin.
 *
 * @since 1.0.1
 *
 * @return array {
 *     "wordpress": {
 *         "min": "4.9.0",
 *         "max": "5.x.x",
 *         "other": [
 *             "^4\\.9$",
 *             // patterns for development versions ...
 *          ]
 *      },
 *      "classicpress": {
 *          "build": "https://github.com/[...].zip",
 *          "version": "1.x.x"
 *      }
 * }
 */
function classicpress_migration_parameters() {
	$parameters = get_transient( 'classicpress_migration_parameters' );

	if ( ! $parameters ) {
		$response   = wp_remote_get( 'https://api-v1.classicpress.net/migration/' );
		$parameters = null;

		if ( is_wp_error( $response ) ) {
			$status = $response->get_error_message();
		} else {
			$status = wp_remote_retrieve_response_code( $response );
		}

		if ( $status === 200 ) {
			$parameters = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( is_array( $parameters ) ) {
				set_transient(
					'classicpress_migration_parameters',
					$parameters,
					1 * HOUR_IN_SECONDS
				);
			}
		}

		if ( ! is_array( $parameters ) ) {
			return new WP_Error(
				'classicpress_server_error',
				__(
					'Could not communicate with the ClassicPress API server',
					'switch-to-classicpress'
				),
				array( 'status' => $status )
			);
		}
	}

	return $parameters;
}
