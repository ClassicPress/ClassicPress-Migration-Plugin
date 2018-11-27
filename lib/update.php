<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Show a message on the screen and in the error log.
 *
 * @since 0.1.0
 *
 * @param string $message The message to show.
 */
function classicpress_show_message( $message ) {
	show_message( "[CP] $message" );
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		error_log( "[CP] $message" );
	}
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
				'Welcome to ClassicPress! You will be redirected to the About ClassicPress screen. If not, click <a href="%2$s">here</a>.',
				'switch-to-classicpress'
			);

		case 'Welcome to WordPress %1$s. <a href="%2$s">Learn more</a>.':
			return __(
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
	if ( ! preg_match(
		'#^https?://api\.wordpress\.org/core/version-check/1\.\d/\?#',
		$url
	) ) {
		// Not a request we're interested in; do not override.
		return $preempt;
	}

	// TODO:
	// - pull locale out of $url
	// - forward to real ClassicPress API
	// - POST variables are not the best place to store version & URL

	$data = array(
		'offers' => array(
			array(
				'response' => 'upgrade',
				'download' => $_POST['_build_url'],
				'locale'   => 'en_US',
				'packages' => array(
					'full'        => $_POST['_build_url'],
					'no_content'  => false,
					'new_bundled' => false,
					'partial'     => false,
					'rollback'    => false,
				),
				'current'         => $_POST['version'],
				'version'         => $_POST['version'],
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
 * Hook into the core upgrade page to do our magic.
 *
 * @since 0.1.0
 */
function classicpress_override_upgrade_page() {
	if (
		! isset( $_GET['action'] ) ||
		$_GET['action'] !== 'do-core-upgrade' ||
		! isset( $_GET['migrate'] ) ||
		$_GET['migrate'] !== 'classicpress'
	) {
		// Not a page load we're interested in.
		return;
	}

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

	// Save the WP version for possible later restoration by a future version
	// of this plugin.
	global $wp_version;
	update_option( 'classicpress_restore_wp_version', $wp_version, false );

	// Add our hooks into the upgrade process.
	add_filter( 'gettext', 'classicpress_override_strings', 10, 3 );
	add_filter( 'pre_http_request', 'classicpress_override_wp_update_api', 10, 3 );
	add_filter( 'pre_http_request', 'classicpress_override_wp_checksums_api', 10, 3 );

	// Set the migration build version and date.
	$build_version = '1.0.0-beta1';
	$build_date = '20181122';

	// Set `$_POST['version']` and `$_POST['locale']` with the same results
	// from our update data, so that `find_core_update` will return a result.
	$_POST['version'] = "$build_version+migration.$build_date";
	$_POST['locale'] = 'en_US';
	// Set `$_POST['_build_url']` for `classicpress_override_wp_update_api`.
	$_POST['_build_url'] = 'https://github.com/ClassyBot/ClassicPress-nightly'
		. "/releases/download/$build_version%2Bmigration.$build_date"
		. "/ClassicPress-nightly-$build_version-migration.$build_date.zip";

	// Force loading a fresh response from the update API, which we will
	// override with our own data.
	wp_version_check( array(), true );

	// Finished overriding the upgrade, now let it proceed in
	// wp-admin/update-core.php (see `do_core_upgrade`).
}
add_action( 'admin_head-update-core.php', 'classicpress_override_upgrade_page' );
