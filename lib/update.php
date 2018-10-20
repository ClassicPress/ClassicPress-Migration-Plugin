<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Show a message on the screen and in the error log.
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
 * @since 0.0.1
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
 * @since 0.0.1
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
		'#^https://api\.wordpress\.org/core/version-check/1\.\d/\?#',
		$url
	) ) {
		// Not a request we're interested in; do not override.
		return $preempt;
	}

	// TODO:
	// - pull locale out of $url
	// - forward to real ClassicPress API

	$json = <<<__JSON__
{
	"offers": [
		{
			"response": "upgrade",
			"download": "https://github.com/ClassyBot/ClassicPress-builds/archive/1.0.0-alpha0+build.20181018.zip",
			"locale": "en_US",
			"packages": {
				"full": "https://github.com/ClassyBot/ClassicPress-builds/archive/1.0.0-alpha0+build.20181018.zip",
				"no_content": false,
				"new_bundled": false,
				"partial": false,
				"rollback": false
			},
			"current": "1.0.0-alpha0+build.20181018",
			"version": "1.0.0-alpha0+build.20181018",
			"php_version": "5.6.0",
			"mysql_version": "5.0",
			"new_bundled": "4.7",
			"partial_version": false
		}
	],
	"translations": []
}
__JSON__;

	return array(
		'headers'       => array(),
		'body'          => $json,
		'response'      => array(
			'code'    => 200,
			'message' => 'OK',
		),
		'cookies'       => array(),
		'http_response' => null,
	);
}

/**
 * Hook into the core upgrade page to do our magic.
 *
 * @since 0.0.1
 */
function classicpress_override_upgrade_page() {
	if (
		! isset( $_GET['action'] ) ||
		$_GET['action'] !== 'do-core-upgrade' ||
		! isset( $_GET['migrate'] ) ||
		$_GET['migrate'] !== 'classicpress'
		// TODO verify pre-flight checks again here too?
	) {
		// Not a page load we're interested in.
		return;
	}
	add_filter( 'gettext', 'classicpress_override_strings', 10, 3 );
	add_filter( 'pre_http_request', 'classicpress_override_wp_update_api', 10, 3 );
	// Force loading a fresh response from the update API, which we will
	// override with our own data.
	wp_version_check( array(), true );
	// Override `$_POST['version']` and `$_POST['locale']` with the same
	// results from our update data, so that `find_core_update` will return a
	// result.
	$_POST['version'] = '1.0.0-alpha0+build.20181018';
	$_POST['locale'] = 'en_US';
	// Finished overriding the upgrade, now let it proceed in
	// wp-admin/update-core.php (see `do_core_upgrade`).
}
add_action( 'admin_head-update-core.php', 'classicpress_override_upgrade_page' );
