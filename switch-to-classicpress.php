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
 * Actions and Filters.
 *
 * @since 1.0.0
 */
add_filter( 'plugins_loaded', 'classicpress_load_plugin_textdomain' );
add_filter( 'muplugins_loaded', 'classicpress_load_muplugin_textdomain' );
add_filter( 'admin_init', 'classicpress_remove_gutenberg_dashboard_widget' );

/**
 * Load Plugin Textdomain.
 *
 * @since 1.0.0
 */
function classicpress_load_plugin_textdomain() {
	load_plugin_textdomain( 'switch-to-classicpress-from-wordpress', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

/**
 * Load MU-Plugin (dir) Textdomain.
 *
 * @since 1.0.0
 */
function classicpress_load_muplugin_textdomain() {
	load_muplugin_textdomain( 'switch-to-classicpress-from-wordpress', basename( dirname( __FILE__ ) ) . '/languages' );
}

/**
 * Remove Gutenberg Dashboard Widget.
 *
 * @since 1.0.0
 */
function classicpress_remove_gutenberg_dashboard_widget() {
	remove_filter( 'try_gutenberg_panel', 'wp_try_gutenberg_panel' );
}

/**
 * Hijack the version check call to the WP API.
 *
 * @since 2.0.0
 *
 * @see WP_Http::request
 *
 * @param bool   $always_false
 * @param array  $r
 * @param string $ul
 */
function classicpress_pre_http_request_filter( $always_false, $r, $url ) {
	if ( strpos( $url, 'api.wordpress.org/core' ) ) {
		// TODO: pull locale out of $url

		$json = <<<__JSON__
{
    "offers": [
        {
            "response": "upgrade",
            "download": "http://github.com/ClassicPress/ClassicPress/archive/1.0.0-alpha0.zip",
            "locale": "en_US",
            "packages": {
                "full": "http://github.com/ClassicPress/ClassicPress/archive/1.0.0-alpha0.zip",
                "no_content": false,
                "new_bundled": false,
                "partial": false,
                "rollback": false
            },
            "current": "1.0.0",
            "version": "1.0.0",
            "php_version": "5.6.0",
            "mysql_version": "5.0",
            "new_bundled": "4.7",
            "partial_version": false
        }],
    "translations": []
}
__JSON__;

		return [
			'headers'		=> [],
			'body'			=> $json,
			'response'		=> [
				'code'			=> 200,
				'mesage'		=> 'OK'
			],
			'cookies'		=> [],
			'http_response'	=> null
		];
	}
}
add_filter( 'pre_http_request', 'classicpress_pre_http_request_filter', 10, 3 );

/**
 * Filter the output. Complete over-kill.
 *
 * @since 2.0.0
 *
 * @param string $txt
 */
function classicpress_ob_start_callback( $txt ) {
    $txt = str_replace('wordpress.org', 'classicpress.net', $txt);
    $txt = str_replace('WordPress', 'ClassicPress', $txt);

    return $txt;
}

/**
 * TODO: If we're now actually ClassicPress we should disable/delete ourself.
 */

/**
 * If we're on the core update page filter the output
 *
 * @since 2.0.0
 */
if ( '/wp-admin/update-core.php' == $_SERVER['SCRIPT_NAME'] ) {
    ob_start( 'classicpress_ob_start_callback' );
}
