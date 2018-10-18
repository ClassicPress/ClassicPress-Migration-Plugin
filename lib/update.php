<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// TODO: If we're now actually ClassicPress we should disable/delete ourself.

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
 * Override the header text during the core update.
 *
 * @since 0.0.1
 *
 * @param string $translation  Translated text.
 * @param string $text         Text to translate.
 * @param string $domain       Text domain.
 *
 * @return string              Possibly-overridden translated text.
 */
function classicpress_override_upgrade_header( $translation, $text, $domain ) {
	if ( $text === 'Update WordPress' ) {
		return __(
			'Migrating WordPress to ClassicPress',
			'switch-to-classicpress'
		);
	}
	return $translation;
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
			"download": "https://github.com/ClassicPress/ClassicPress/archive/1.0.0-alpha0.zip",
			"locale": "en_US",
			"packages": {
				"full": "https://github.com/ClassicPress/ClassicPress/archive/1.0.0-alpha0.zip",
				"no_content": false,
				"new_bundled": false,
				"partial": false,
				"rollback": false
			},
			"current": "1.0.0-alpha0",
			"version": "1.0.0-alpha0",
			"php_version": "5.6.0",
			"mysql_version": "5.0",
			"new_bundled": "4.7",
			"partial_version": false
		}
	],
	"translations": []
}
__JSON__;

	return [
		'headers'       => [],
		'body'          => $json,
		'response'      => [
			'code'    => 200,
			'message' => 'OK'
		],
		'cookies'       => [],
		'http_response' => null
	];
}

/**
 * Hook into and override the 'filesystem_method' filter.
 *
 * In order to do this correctly, we need to determine where this filter was
 * called from.  `request_filesystem_credentials` needs to receive a value of
 * 'direct', or it will ask for filesystem credentials.
 *
 * Then, later on, we can tell `WP_Filesystem` about our desired filesystem
 * method, and it will instantiate `$wp_filesystem` for us with our custom
 * behavior.
 *
 * @since 0.0.1
 *
 * @see WP_Filesystem_Shenanigans
 */
function classicpress_override_filesystem_method( $method ) {
	if ( $method !== 'direct' ) {
		classicpress_show_message(
			"Failed to override filesystem method! Expected 'direct'"
			. " but found '$method'."
		);
		return $method;
	}

	$caller = null;

	$trace = debug_backtrace();
	for ( $i = 0; $i < count( $trace ); $i++ ) {
		$frame = $trace[ $i ];
		if ( $frame['function'] === 'get_filesystem_method' ) {
			$caller = $trace[ $i + 1 ]['function'];
			break;
		}
	}

	if ( $caller === 'request_filesystem_credentials' ) {
		// Do not modify the fs method here - it is expecting 'direct'.
		return $method;
	} else if ( $caller === 'WP_Filesystem' ) {
		// Override WP_Filesystem_Direct with our own class here.  In
		// `WP_Filesystem` the class that will be instantiated is
		// "WP_Filesystem_$method".
		require_once dirname( __FILE__ ) . '/class.wp-filesystem-shenanigans.php';
		return 'shenanigans';
	}

	classicpress_show_message(
		'Failed to override filesystem method! '
		. json_encode( compact( 'caller' ) )
	);

	return $method;
}

/**
 * Hook into the core upgrade page to do our magic.
 *
 * @since 0.0.1
 */
function classicpress_override_upgrade_page() {
	if ( ! isset( $_GET['action'] ) || $_GET['action'] !== 'do-core-upgrade' ) {
		// Not a page load we're interested in.
		return;
	}
	add_filter( 'gettext', 'classicpress_override_upgrade_header', 10, 3 );
	add_filter( 'pre_http_request', 'classicpress_override_wp_update_api', 10, 3 );
	add_filter( 'filesystem_method', 'classicpress_override_filesystem_method' );
	// Force loading a fresh response from the update API, which we will
	// override with our own data.
	wp_version_check( array(), true );
	// Override `$_POST['version']` and `$_POST['locale']` with the same
	// results from our update data, so that `find_core_update` will return a
	// result.
	$_POST['version'] = '1.0.0-alpha0';
	$_POST['locale'] = 'en_US';
	// Finished overriding the upgrade, now let it proceed in
	// wp-admin/update-core.php (see `do_core_upgrade`).
}
add_action( 'admin_head-update-core.php', 'classicpress_override_upgrade_page' );
