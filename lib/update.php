<?php

/**
 * Prevent direct access to plugin files.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Override a few strings during the core update.
 *
 * @since 0.1.0
 *
 * @param string $translation  Translated text.
 * @param string $text         Text to translate.
 * @param string $domain       Text domain.
 *
 * @return string              Possibly-overridden translated text.
 */
function classicpress_override_strings( $translation, $text, $domain ) {
	switch ( $text ) {
		// Main page header.
		case 'Update WordPress':
			return __(
				'Migrating WordPress to ClassicPress',
				'switch-to-classicpress'
			);

		// The rest of these strings appear when the upgrade process is done.

		case 'WordPress updated successfully':
			return __(
				'WordPress successfully migrated to ClassicPress!',
				'switch-to-classicpress'
			);

		// Note: %1$s placeholder omitted, because it is the WP version, which
		// we cannot override cleanly.

		case 'Welcome to WordPress %1$s. You will be redirected to the About WordPress screen. If not, click <a href="%2$s">here</a>.':
			return __(
				/* translators: 1: omitted; 2: link to about.php (About ClassicPress screen) */
				'Welcome to ClassicPress! You will be redirected to the About ClassicPress screen. If not, click <a href="%2$s">here</a>.',
				'switch-to-classicpress'
			);

		case 'Welcome to WordPress %1$s. <a href="%2$s">Learn more</a>.':
			return __(
				/* translators: 1: omitted; 2: link to about.php (About ClassicPress screen) */
				'Welcome to ClassicPress! <a href="%2$s">Learn more</a>.',
				'switch-to-classicpress'
			);

		default:
			return $translation;
	}
}

/**
 * Hijack the version check call to the WP API.
 *
 * @since 0.1.0
 *
 * @see WP_Http::request
 *
 * @param bool   $preempt Whether to override the HTTP request.
 * @param array  $r       Request details.
 * @param string $url     Request URL.
 *
 * @return Overridden request, or false to proceed normally.
 */
function classicpress_override_wp_update_api( $preempt, $r, $url ) {
	if (
		! preg_match(
			'#^https?://api\.wordpress\.org/core/version-check/1\.\d/\?#',
			$url
		) &&
		! preg_match(
			'#^https://api-v1\.classicpress\.net/upgrade/#',
			$url
		)
	) {
		// Not a request we're interested in; do not override.
		return $preempt;
	}

	if ( ! classicpress_is_migration_request() ) {
		// Not a request we're interested in; do not override.
		return $preempt;
	}

	switch ( $_GET['_migrate'] ) {
		case 'classicpress':
			$parameters = classicpress_migration_parameters();
			if ( ! is_array( $parameters ) ) {
				// Not sure what happened, but it's not good.
				return $preempt;
			}
			$build_url = $parameters['classicpress']['build'];
			$version   = $parameters['classicpress']['version'];
			break;

		case '_custom':
			if (
				! isset( $_POST['_build_url'] ) ||
				! isset( $_POST['version'] )
			) {
				// Not sure what happened, but it's not good.
				return $preempt;
			}
			$build_url = $_POST['_build_url'];
			$version   = $_POST['version'];
			break;
	}

	// TODO: do locales other than en_US need different handling?

	$data = array(
		'offers' => array(
			array(
				'response' => 'upgrade',
				'download' => $build_url,
				'locale'   => 'en_US',
				'packages' => array(
					'full'        => $build_url,
					'no_content'  => false,
					'new_bundled' => false,
					'partial'     => false,
					'rollback'    => false,
				),
				'current'         => $version,
				'version'         => $version,
				'php_version'     => '5.6.0',
				'mysql_version'   => '5.0',
				'new_bundled'     => '4.7',
				'partial_version' => false,
			),
		),
		'translations' => array(),
	);

	return array(
		'headers'       => array(),
		'body'          => json_encode( $data ),
		'response'      => array(
			'code'    => 200,
			'message' => 'OK',
		),
		'cookies'       => array(),
		'http_response' => null,
	);
}

/**
 * Override the WP core checksums API to return an invalid result.
 *
 * This API endpoint is used to determine whether to skip updating WordPress
 * files whose hashes didn't change in between WordPress versions.  We don't
 * want this check - we want to update everything.
 *
 * @since 0.1.0
 *
 * @see get_core_checksums()
 * @see https://nylen.io/wp/4.9.8/src/wp-admin/includes/update-core.php#L956-L980
 * @see WP_Http::request
 *
 * @param bool   $preempt Whether to override the HTTP request.
 * @param array  $r       Request details.
 * @param string $url     Request URL.
 *
 * @return Overridden request, or false to proceed normally.
 */
function classicpress_override_wp_checksums_api( $preempt, $r, $url ) {
	if ( ! preg_match(
		'#^https?://api\.wordpress\.org/core/checksums/1\.\d/\?#',
		$url
	) ) {
		// Not a request we're interested in; do not override.
		return $preempt;
	}

	return array(
		'headers'       => array(),
		'body'          => '',
		'response'      => array(
			'code'    => 400,
			'message' => 'Bad Request',
		),
		'cookies'       => array(),
		'http_response' => null,
	);
}

/**
 * Determine whether the current request looks like a migration.
 *
 * @since 1.2.0
 */
function classicpress_is_migration_request() {
	return (
		isset( $_GET['action'] ) &&
		$_GET['action'] === 'do-core-upgrade' &&
		isset( $_GET['_migrate'] ) &&
		in_array( $_GET['_migrate'], array( 'classicpress', '_custom' ), true )
	);
}

/**
 * Hook into the core upgrade page to do our magic.
 *
 * @since 0.1.0
 */
function classicpress_override_upgrade_page() {
	if ( ! classicpress_is_migration_request() ) {
		// Definitely not a page load we're interested in.
		return;
	}

	if ( $_GET['_migrate'] === '_custom' ) {
		if ( empty( $_POST['_build_url'] ) ) {
			// The request is no good.
			return;
		}

		// Set the (fake) version string used in the custom migration.  This is
		// used in the `find_core_update()` function - it only needs to match
		// the version returned when we override the update API.
		$_POST['version'] = '_custom_migration';

		// Add our hooks into the upgrade process.
		add_filter( 'pre_http_request', 'classicpress_override_wp_update_api', 10, 3 );
		add_filter( 'pre_http_request', 'classicpress_override_wp_checksums_api', 10, 3 );

		// Force loading a fresh response from the update API, which we will
		// override with our own data.
		wp_version_check( array(), true );

		// Save a flag indicating that we've just done a migration.
		set_site_transient( 'classicpress_migrated', true, 5 * 60 );

		// Finished overriding the upgrade, now let it proceed in
		// wp-admin/update-core.php (see `do_core_upgrade`).
		return;
	}

	// Migrate to ClassicPress.

	// Verify that the plugin's preflight checks passed, just in case.
	$preflight_checks = get_option( 'classicpress_preflight_checks', null );
	if (
		! is_array( $preflight_checks ) ||
		empty( $preflight_checks['wp_version'] ) ||
		empty( $preflight_checks['php_version'] ) ||
		empty( $preflight_checks['wp_http_supports_ssl'] )
	) {
		return;
	}

	$parameters = classicpress_migration_parameters();
	if ( ! is_array( $parameters ) ) {
		// Not sure what happened, but it's not good.
		return;
	}

	// Save the WP version for possible later restoration by a future version
	// of this plugin.
	global $wp_version;
	update_option( 'classicpress_restore_wp_version', $wp_version, false );

	// Save a flag indicating that we've just done a migration.
	set_site_transient( 'classicpress_migrated', true, 5 * 60 );

	// Add our hooks into the upgrade process.
	add_filter( 'gettext', 'classicpress_override_strings', 10, 3 );
	add_filter( 'pre_http_request', 'classicpress_override_wp_update_api', 10, 3 );
	add_filter( 'pre_http_request', 'classicpress_override_wp_checksums_api', 10, 3 );

	// Set the migration build version and date.
	$build_version = '1.0.0';
	$build_date = '20190305';

	// Set `$_POST['version']` and `$_POST['locale']` with the same results
	// from our update data, so that `find_core_update()` will return a result.
	$_POST['version'] = $parameters['classicpress']['version'];
	$_POST['locale']  = 'en_US';

	// Force loading a fresh response from the update API, which we will
	// override with our own data.
	wp_version_check( array(), true );

	// Finished overriding the upgrade, now let it proceed in
	// wp-admin/update-core.php (see `do_core_upgrade()`).
}
add_action( 'admin_head-update-core.php', 'classicpress_override_upgrade_page' );

/**
 * Clear stale data after migration:
 *
 *  - Invalid upgrade notices stating "WordPress _custom_migration is
 *    available! Please update now" after a custom migration
 *  - WordPress news showing up in the ClassicPress news dashboard widget
 *
 * @since 1.2.0
 */
function classicpress_clear_stale_data() {
	global $wpdb;

	// Bail if currently doing a migration.
	if ( classicpress_is_migration_request() ) {
		return;
	}

	// Bail if we have not recently done a migration.
	// Save a flag indicating that we've just done a migration.
	if ( ! get_site_transient( 'classicpress_migrated' ) ) {
		return;
	}

	// See if update data is still stored from a custom migration.
	$core = get_site_transient( 'update_core' );
	if (
		is_object( $core ) &&
		! empty( $core->updates ) &&
		is_array( $core->updates ) &&
		is_object( $core->updates[0] ) &&
		! empty( $core->updates[0]->version ) &&
		$core->updates[0]->version === '_custom_migration'
	) {
		// Delete the expired update data, in case `wp_version_check()` fails.
		delete_site_transient( 'update_core' );
		// Force refreshing the expired update data.
		wp_version_check( array(), true );
	}

	// Delete any cached data from the dashboard RSS widgets.
	// NOTE - for multisite installs this will apply to the current site only!
	$wpdb->query(
		"DELETE FROM {$wpdb->options}
		WHERE option_name LIKE '!_transient!_dash!_v2!_%' ESCAPE '!'"
	);
	$wpdb->query(
		"DELETE FROM {$wpdb->options}
		WHERE option_name LIKE '!_transient!_timeout!_dash!_v2!_%' ESCAPE '!'"
	);

	// Remove the flag indicating that a migration was recently performed.
	delete_site_transient( 'classicpress_migrated' );
}
add_action( 'admin_head-about.php', 'classicpress_clear_stale_data' );
add_action( 'admin_head-update-core.php', 'classicpress_clear_stale_data' );
