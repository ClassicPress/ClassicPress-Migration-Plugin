<?php
/*
 * Plugin Name:       Switch to ClassicPress
 * Plugin URI:        https://github.com/ClassicPress/ClassicPress-Migration-Plugin
 * Description:       Switch your WordPress installation to ClassicPress.
 * Version:           1.5.0
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
	$cp_api_parameters = get_transient( 'classicpress_migration_parameters' );

	if ( ! $cp_api_parameters ) {
		$response   = wp_remote_get( 'https://api-v1.classicpress.net/migration/' );
		$cp_api_parameters = null;

		if ( is_wp_error( $response ) ) {
			$status = $response->get_error_message();
		} else {
			$status = wp_remote_retrieve_response_code( $response );
		}

		if ( $status === 200 ) {
			$cp_api_parameters = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( is_array( $cp_api_parameters ) ) {
				set_transient(
					'classicpress_migration_parameters',
					$cp_api_parameters,
					1 * HOUR_IN_SECONDS
				);
			}
		}

		if ( ! is_array( $cp_api_parameters ) ) {
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

	return $cp_api_parameters;
}


/**
 * Get a list of ClassicPress released versions from api-v1.classicpress.net.
 *
 * @return array|false Array of CP versions or false on API failure.
 */
function get_cp_versions() {
	$cp_versions = get_transient( 'classicpress_release_versions' );

if ( ! $cp_versions ) {
	$response = wp_remote_get('https://api-v1.classicpress.net/v1/upgrade/index.php', ['timeout'=>3]);
	if ( is_wp_error( $response ) ) {
		$status = $response->get_error_message();
	} else {
		$status = wp_remote_retrieve_response_code( $response );
	}

	if ( $status === 200 ) {
		$cp_versions = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( is_array( $cp_versions ) ) {
			set_transient(
				'classicpress_release_versions',
				$cp_versions,
				1 * HOUR_IN_SECONDS
			);
		}
	}

	if ( ! is_array( $cp_versions ) ) {
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
//	$versions = json_decode(wp_remote_retrieve_body($response));

	// Get only stable releases
	foreach ($cp_versions as $key => $version) {
		if(!strpos($version, 'nightly') && !strpos($version, 'rc') && !strpos($version, 'alpha') && !strpos($version, 'beta')) {
			continue;
		}
		unset($cp_versions[$key]);
	}

	// Strip .json from version
	$cp_versions = array_map(
		function($v) {
			return substr($v, 0, -5);
		},
		$cp_versions
	);

	// Sort using SemVer
	usort($cp_versions, 'version_compare');

	return array_values($cp_versions);

}

function get_migration_from_cp_version($version) {
	$response = wp_remote_get('https://api.github.com/repos/ClassicPress/ClassicPress-release/releases/tags/'.$version, ['timeout'=>3]);
	if (is_wp_error($response) || empty($response)) {
		return false;
	}

	$data = json_decode(wp_remote_retrieve_body($response), true);
	if(isset($data['message']) && $data['message'] === 'Not Found') {
		return '';
	}

	$created_at = new \DateTime($data['created_at']);
	$day        = $created_at->format('Ymd');
	$exploded   = explode('.', $version);
	$major      = $exploded[0];
	$url        = 'https://github.com/ClassyBot/ClassicPress-v'.$major.'-nightly/releases/download/'.$version.'%2Bmigration.'.$day.'/ClassicPress-nightly-'.$version.'-migration.'.$day.'.zip';
	return $url;
}

/**
 * Get release URL.
 *
 * @param string $version  Version to retrive migration URL.
 *
 * @return string          URL for release.
 */
function getReleaseFromCPVersion($version) {
	return 'https://github.com/ClassicPress/ClassicPress-release/archive/refs/tags/'.$version.'.zip';
}

/**
 * Get previous release version.
 *
 * @param string $version   Version to get previous release.
 * @param array  $versions  Array of ClassicPress versions as
 *                          returned by getCPVersions().
 *                          Used for caching.
 *
 * @return string|bool      Previous version. False if not found.
 */
function get_previous_version($version, $versions = []) {
	if (empty($versions)) {
//		$versions = self::getCPVersions();
		$versions = get_cp_versions();
	} else {
		usort($versions, 'version_compare');
	}
	if(!in_array($version, $versions)) {
		return false;
	}
	$pos = array_search($version, $versions, true);
	if(!isset($versions[$pos - 1])) {
		return false;
	}
	return $versions[$pos - 1];;
}