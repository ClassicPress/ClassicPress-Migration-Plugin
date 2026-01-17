<?php

/**
 * Prevent direct access to plugin files.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Output the plugin's styles for the relevant admin pages.
 *
 * @since 0.1.0
 */
function classicpress_print_admin_styles() {
?>
<style>
.cp-migration-action, .cp-emphasis,
.form-table th.cp-emphasis {
	font-weight: bold;
	color: #800;
}
.cp-migration-ready {
	font-weight: bold;
	color: #080;
}
.cp-migration-action:hover {
	color: #f00;
}
.cp-migration-info {
	max-width: 600px;
}
ul.cp-migration-info {
	list-style: disc outside none;
}
ul.cp-migration-info li {
	margin-left: 2em;
	padding-left: 0.3em;
}
table#cp-preflight-checks {
	margin: 1.5em 0 2em;
	border-spacing: 0;
}
#cp-preflight-checks p {
	margin: 0;
}
#cp-preflight-checks td {
	padding: 0.5em 0 0.5em 1em;
	margin: 0;
}
#cp-preflight-checks td + td {
	padding-right: 0;
}
#cp-preflight-checks tr + tr td {
	border-top: 1px solid #ccc;
}
.cp-preflight-icon {
	font-size: 250%;
	font-weight: bold;
	border-radius: 0.5em;
	color: #f1f1f1; /* default wp-admin background */
	display: block;
	width: 1em;
	height: 1em;
}
.cp-preflight-icon .dashicons {
	font-size: 1em;
	display: block;
	width: 1em;
	height: 1em;
	position: relative;
}
.cp-preflight-icon.cp-pass {
	background: #080;
}
.cp-preflight-icon.cp-pass .dashicons-yes {
	left: -0.025em;
	top: 0.030em;
}
.cp-preflight-icon.cp-fail {
	background: #800;
}
.cp-preflight-icon.cp-fail .dashicons-no {
	left: 0.005em;
	top: 0.010em;
}
.cp-preflight-icon.cp-warn {
	background: #ffb900;
}
.cp-preflight-icon.cp-warn .dashicons-flag {
	font-size: 0.8em;
	left: 0.140em;
	top: 0.100em;
}
#cp-migration-form {
	margin: 2em 0 3em;
}
#cp-show-advanced-migration-form {
	margin-top: 2em;
	font-size: 120%;
}
#cp-advanced-migration-form {
	margin: 4em 0;
}
#cp-advanced-migration-form table.form-table {
	width: auto;
}
#cp-advanced-migration-form th {
	width: auto;
	padding-right: 1em;
	white-space: nowrap;
}
#cp-advanced-migration-form #cp-build-url,
#cp-advanced-migration-form td p {
	width: 100%;
	max-width: 50em;
}
#picker-label {
    display: block;
    margin-bottom: 0.75em;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
	var showForm = document.getElementById( 'cp-show-advanced-migration-form' );
	if ( showForm != null ) {
		showForm.addEventListener( 'click', function() {
			document.getElementById( 'cp-advanced-migration-form' ).classList.remove( 'hidden' );
			this.remove();
		} );
	}
} );
</script>
<?php
}
add_action( 'admin_head-plugins.php', 'classicpress_print_admin_styles' );
add_action( 'admin_head-tools_page_switch-to-classicpress', 'classicpress_print_admin_styles' );
add_action( 'admin_head-index_page_switch-to-classicpress', 'classicpress_print_admin_styles' );

/**
 * Remove the WP update nag from the Switch to ClassicPress page.
 *
 * @since 0.1.0
 */
function classicpress_remove_update_nag() {
	remove_action( 'admin_notices', 'update_nag', 3 );
	remove_action( 'network_admin_notices', 'update_nag', 3 );
}
add_action( 'admin_head-tools_page_switch-to-classicpress', 'classicpress_remove_update_nag' );
add_action( 'admin_head-index_page_switch-to-classicpress', 'classicpress_remove_update_nag' );

/**
 * Register the plugin's admin page under the Dashboard menu for multisite
 * installations.
 *
 * @since 0.2.0
 */
function classicpress_register_network_admin_menu() {
	add_submenu_page(
		'index.php',
		__( 'Switch to ClassicPress', 'switch-to-classicpress' ),
		__( 'Switch to ClassicPress', 'switch-to-classicpress' ),
		'manage_network',
		'switch-to-classicpress',
		'classicpress_show_admin_page'
	);
}

/**
 * Register the plugin's admin page under the Tools menu for single-site
 * installations.
 *
 * @since 0.1.0
 */
function classicpress_register_admin_page() {
	if ( current_user_can( 'update_core' ) ) {
		add_management_page(
			__( 'Switch to ClassicPress', 'switch-to-classicpress' ),
			__( 'Switch to ClassicPress', 'switch-to-classicpress' ),
			'read',
			'switch-to-classicpress',
			'classicpress_show_admin_page'
		);
	}
}

if ( is_multisite() ) {
	add_action( 'network_admin_menu', 'classicpress_register_network_admin_menu' );
} else {
	add_action( 'admin_menu', 'classicpress_register_admin_page' );
}

/**
 * Show the plugin's admin page.
 *
 * @since 0.1.0
 */
function classicpress_show_admin_page() {
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Switch to ClassicPress', 'switch-to-classicpress' ); ?></h1>
<?php
	$preflight_checks_ok = classicpress_check_can_migrate();

	if ( $preflight_checks_ok ) {
		classicpress_show_migration_controls();
	} else {
		classicpress_show_migration_blocked_info();
	}

?>
	<h2><?php esc_html_e( 'Feedback and Support', 'switch-to-classicpress' ); ?></h2>
	<p class="cp-migration-info">
		<?php esc_html_e(
			'Do you have feedback about this plugin, or about ClassicPress itself? Need help with something? We\'d love to know what you think!',
			'switch-to-classicpress'
		); ?>
	</p>
	<ul class="cp-migration-info">
		<li><?php printf(
		/* translators: 1: link with instructions to join our Forum, 2: link to join ClassicPress Zulip */
			wp_kses_post(
				__( 'For support, suggestions for improvement, or general discussion about how the plugin works, visit us in our <a href="%1$s">support forum</a> or <a href="%2$s">Zulip chat</a>.',
				'switch-to-classicpress' )
			),
			'https://forums.classicpress.net/tags/c/plugins/9/migration-plugin',
			'https://classicpress.zulipchat.com/register/'
		); ?></li>
		<li><?php printf(
		/* translators: link to create a new GitHub issue for this plugin */
			wp_kses_post(
				__( 'For <strong>specific</strong> bug reports or suggestions, <a href="%s">add a new issue on GitHub</a>.',
				'switch-to-classicpress' )
			),
			'https://github.com/ClassicPress/ClassicPress-Migration-Plugin/issues/new'
		); ?></li>
	</ul>

	<?php classicpress_show_advanced_migration_controls(
		// On WP, preflight checks passed...
		$preflight_checks_ok ||
		// On ClassicPress, assume the user knows what they're doing.
		function_exists( 'classicpress_version' )
	); ?>
</div><!-- .wrap -->
<?php
}

/**
 * Add an option under General Settings to ignore wp_version on migration.
 *
 * @since 1.6.0
 */
function classicpress_ignore_wp_version_settings_init() {
	if ( function_exists( 'classicpress_version' ) ) {
		return;
	}
	add_settings_field(
		'classicpress_ignore_wp_version',
		__( 'Ignore WordPress version', 'switch-to-classicpress' ),
		'classicpress_ignore_wp_version_checkbox_callback',
		'general',
		'default'
	);
	register_setting( 'general', 'classicpress_ignore_wp_version' );
}

/**
 * Render an option under General Settings to ignore wp_version on migration.
 *
 * @since 1.6.0
 */
function classicpress_ignore_wp_version_checkbox_callback() {
	$option = get_option( 'classicpress_ignore_wp_version' );
	echo '<input type="checkbox" id="classicpress_ignore_wp_version" name="classicpress_ignore_wp_version" value="1" ' . checked( 1, $option, false ) . '>';
	printf(
		esc_html__(
			/* translators: 1: markup for the link at the migration page, 2: closing markup */
			'Ignore WordPress version when migrating to ClassicPress using the %1$sClassicPress Migration Plugin%2$s.',
			'switch-to-classicpress'
		),
		'<a href="' . sanitize_url( admin_url( 'tools.php?page=switch-to-classicpress' ) ) . '">', //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.WP.DeprecatedFunctions.sanitize_urlFound
		'</a>'
	);
}

add_action( 'admin_init', 'classicpress_ignore_wp_version_settings_init' );

/**
 * Determine whether this WP install can be migrated to ClassicPress.
 *
 * Also output messages relevant to the checks performed.
 *
 * @since 0.1.0
 *
 * @return bool Whether to show the controls to proceed with the migration.
 */
function classicpress_check_can_migrate() {
	// First: Run a series of checks for conditions that are inherent to this
	// WordPress install and this user session.

	// Check: Are we already on ClassicPress?
	if ( function_exists( 'classicpress_version' ) ) {
		global $cp_version;
		if ( is_multisite() ) {
			$delete_plugin_url = network_admin_url( 'plugins.php' );
			$reinstall_url = network_admin_url( 'update-core.php' );
		} else {
			$delete_plugin_url = admin_url( 'plugins.php' );
			$reinstall_url = admin_url( 'update-core.php' );
		}
?>
		<div class="notice notice-success">
<?php
			if (strpos($cp_version, 'migration')) {
				printf(
					wp_kses_post(
						'<h2>' . __( 'You\'re almost done switching to ClassicPress v%s!', 'switch-to-classicpress' ) . '</h2>'
					),
					esc_html( preg_replace( '#[+-].*$#', '', $cp_version ) )
				);
				printf(
					wp_kses_post(
						'<strong class="cp-emphasis">' . __( 'You must visit the <a href="%s">Updates Page</a> and Press the Re-Install Now button to complete the migration process!', 'switch-to-classicpress' ) . '</strong>'
					),
					esc_url( $reinstall_url )
				);
			} else {
				printf(
					wp_kses_post(
						'<h2>' . __( 'Good job, you\'re running ClassicPress v%s!', 'switch-to-classicpress' ) . '</h2>'
					),
					esc_html( preg_replace( '#[+-].*$#', '', $cp_version ) )
				);
			}
?>
			<p>
				<strong><?php esc_html_e(
					'This Plugin is also an Advanced Version Control Tool for Development and Troubleshooting.',
					'switch-to-classicpress'
				); ?></strong>
			</p>
			<p>
				<?php printf(
					/* translators: %s: URL to plugins page */
					wp_kses_post(
						__( 'If no longer needed you can <a href="%s">delete the plugin</a>.',
						'switch-to-classicpress' )
					),
					esc_url( $delete_plugin_url )
				); ?>
			</p>
		</div>
<?php
		return false;
	}

	// Check: Are we running on WordPress.com?
	// @see https://github.com/Automattic/jetpack/blob/6.6.1/functions.global.php#L32-L43
	$at_options = get_option( 'at_options', array() );
	if ( ! empty( $at_options ) || defined( 'WPCOMSH__PLUGIN_FILE' ) ) {
?>
		<div class="notice notice-error">
			<p>
				<?php esc_html_e(
					'Sorry, this plugin doesn\'t support sites hosted on WordPress.com.',
					'switch-to-classicpress'
				); ?>
			</p>
			<p>
				<?php esc_html_e(
					'In order to switch to ClassicPress, you\'ll need to move to a self-hosted WordPress site first.',
					'switch-to-classicpress'
				); ?>
			</p>
		</div>
<?php
		return false;
	}

	// Check: Does the current user have permission to update core?
	if ( ! current_user_can( 'update_core' ) ) {
?>
		<div class="notice notice-error">
			<p>
				<?php esc_html_e(
					'Sorry, you\'re not allowed to perform this action.',
					'switch-to-classicpress'
				); ?>
			</p>
			<p>
				<?php esc_html_e(
					'Please contact a site administrator for more information.',
					'switch-to-classicpress'
				); ?>
			</p>
		</div>
<?php
		return false;
	}

	// Get migration plugin parameters.
	$cp_api_parameters = classicpress_migration_parameters();
	if ( is_wp_error( $cp_api_parameters ) ) {
		$cp_api_object = (object) $cp_api_parameters;
?>
		<div class="notice notice-error">
			<p>
				<?php echo esc_html( $cp_api_object->get_error_message() ); ?>
				<?php echo wp_json_encode( $cp_api_object->get_error_data() ); ?>
			</p>
		</div>
<?php
		return false;
	}

	// The first round of checks has passed.  Now, run a second round related
	// to conditions that the user (or at least the hosting provider) has
	// control over, and display the results in a table.

	$preflight_checks = array();
	$icon_preflight_pass = (
		'<div class="cp-preflight-icon cp-pass">'
			. '<div class="dashicons dashicons-yes"></div>'
		. '</div>'
	);
	$icon_preflight_fail = (
		'<div class="cp-preflight-icon cp-fail">'
			. '<div class="dashicons dashicons-no"></div>'
		. '</div>'
	);
	$icon_preflight_warn = (
		'<div class="cp-preflight-icon cp-warn">'
			. '<div class="dashicons dashicons-flag"></div>'
		. '</div>'
	);
	echo '<table id="cp-preflight-checks">' . "\n";

	// Check: Supported WP version
	// More versions can be added after they are confirmed to work.
	global $wp_version;
	$wp_version_min = $cp_api_parameters['wordpress']['min'];
	$wp_version_max = $cp_api_parameters['wordpress']['max'];
	/* translators: 1: minimum supported WordPress version, 2: maximum supported WordPress version, 3: current WordPress version */
	$wp_version_check_intro_message = sprintf( __(
		'This plugin supports WordPress versions <strong>%1$s</strong> to <strong>%2$s</strong> (and some newer development versions).<br>You are running WordPress version <strong>%3$s</strong>.',
		'switch-to-classicpress'
	), $wp_version_min, $wp_version_max, $wp_version );
	$wp_version_check_intro_message .= "<br>\n";

	if (
		// Version is outside of our target range of WP stable releases...
		(
			version_compare( $wp_version, $wp_version_min, 'lt' ) ||
			version_compare( $wp_version, $wp_version_max, 'gt' )
		) &&
		// ... and it doesn't match any other acceptable version patterns
		empty( array_filter( $cp_api_parameters['wordpress']['other'], function ( $pattern ) {
			global $wp_version;
			return preg_match( $pattern, $wp_version );
		} ) )
	) {
		/**
		 * Filters whether to ignore the result of the WP version check.
		 *
		 * @since 0.4.0
		 *
		 * @param bool $ignore Ignore the WP version check. Defaults to false.
		 */
		if ( apply_filters( 'classicpress_ignore_wp_version', false ) || get_option( 'classicpress_ignore_wp_version' ) === '1' ) {
			$preflight_checks['wp_version'] = true;
			echo "<tr>\n<td>" . wp_kses_post($icon_preflight_warn) . "</td>\n<td>\n";
			echo "<p>\n";
			echo wp_kses_post( $wp_version_check_intro_message );
			echo wp_kses_post(
				__( 'The check for supported WordPress versions has been <strong class="cp-emphasis">Manually Disabled</strong>.',
				'switch-to-classicpress' )
			);
			echo "<br>\n";
			esc_html_e(
				'We cannot guarantee that the migration process is going to work, and it may leave your current installation broken.',
				'switch-to-classicpress'
			);
			echo "<br>\n";
			echo wp_kses_post(
				'<strong class="cp-emphasis">' . __( 'Proceed At Your Own Risk!', 'switch-to-classicpress' ) . '</strong>'
			);
			echo "<br>\n";
		} else {
			$preflight_checks['wp_version'] = false;
			echo wp_kses_post( "<tr>\n<td>$icon_preflight_fail</td>\n<td>\n" );
			echo "<p>\n";
			echo wp_kses_post( $wp_version_check_intro_message );
			printf(
				esc_html__(
					/* translators: 1: markup for the link at the general settings page, 2: closing markup */
					'You can enable migration from this version of WordPress At Your Own Risk activating "Ignore WordPress version" in the %1$sGeneral options%2$s.',
					'switch-to-classicpress'
				),
				'<a href="' . sanitize_url( admin_url( 'options-general.php' ) ) . '">' , //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.WP.DeprecatedFunctions.sanitize_urlFound
			'</a>'
			);
		}
	} else {
		$preflight_checks['wp_version'] = true;
		if ( substr( $wp_version, 0, 1 ) === '5' ) {
			echo wp_kses_post( "<tr>\n<td>$icon_preflight_warn</td>\n<td>\n" );
		} else {
			echo wp_kses_post( "<tr>\n<td>$icon_preflight_pass</td>\n<td>\n" );
		}
		echo "<p>\n";
		echo wp_kses_post( $wp_version_check_intro_message );
	}

	if ( substr( $wp_version, 0, 1 ) >= '5' && $preflight_checks['wp_version'] ) {
		//echo "<br>\n";
		esc_html_e(
			'Content edited with the WordPress Block Editor may not be fully compatible with ClassicPress.',
			'switch-to-classicpress'
		);

	}
	echo "\n</p>\n";
	// TODO: Add instructions if WP too old.
	echo "</td></tr>\n";

	// Theme Check
	$theme = wp_get_theme();
	$theme_name = $cp_api_parameters['defaults']['theme_name'];
	$theme_url = $cp_api_parameters['defaults']['theme_url'];
	$default_theme = '<a href="'.$theme_url.'">'.$theme_name.'</a>';
	// THEME CHECKS DISABLED / WARN ONLY (Since v1.6)
	if ( $theme->name === $theme_name ) {
		$preflight_checks['theme'] = true;
		echo "<tr>\n<td>" . wp_kses_post($icon_preflight_pass) . "</td>\n<td>\n<p>\n";
		printf( wp_kses_post(
			// translators: default theme name
			__( 'It looks like you are using the <strong>%1$s</strong> theme.<br>%1$s is the suggested theme to use when migrating from ClassicPress to WordPress.',
			'switch-to-classicpress' )
		), esc_html( $theme->name ) );
	} else {
		$preflight_checks['theme'] = true;
		echo "<tr>\n<td>" . wp_kses_post($icon_preflight_warn) . "</td>\n<td>\n<p>\n";
		printf( wp_kses_post(
			// translators: active theme name
			__( 'It looks like you are using the theme <strong>%1$s</strong>, you should test the theme(s) you plan to use after migration and verify that they work correctly.',
			'switch-to-classicpress' )
		), esc_html( $theme->name ) );
		echo "<br>\n";
		printf( wp_kses_post(
			// translators: default theme
			'<strong>' . __( 'The safest way of switching to ClassicPress is to install and activate the fully compatible theme <em>%1$s</em>.', 'switch-to-classicpress' ) . '</strong><br>' .
			__( 'You can <strong class="cp-emphasis">Continue at Your Own Risk</strong> with your current theme, but you may experience issues if the theme is not compatible with ClassicPress.', 'switch-to-classicpress' )
			), wp_kses_post( $default_theme )
		);
	}
	echo "</p></td></tr>\n";

	// Plugins Check
	$plugins = get_option( 'active_plugins' );
	$plugin_headers = array( 'Name' => 'Plugin Name', 'RequiresWP'  => 'Requires at least' );
	$declared_incompatible_plugins = array();
	$undeclared_compatibility_plugins = array();
	$plugin_info = __( 'It looks like you have active plugins, you should test the plugins you plan to use after migration and verify they work correctly.', 'switch-to-classicpress' );

		if ( count($plugins) > 1 ) {
		$preflight_checks['plugins'] = true;
		echo "<tr>\n<td>" . wp_kses_post($icon_preflight_warn) . "</td>\n<td>\n<p>\n";
		echo wp_kses_post(
			$plugin_info
		);
		echo "<br>\n";
		echo wp_kses_post(
			'<strong>' . __( 'The safest way of switching to ClassicPress is to (temporarily) deactivate your plugins, except <em>Switch to ClassicPress</em>.', 'switch-to-classicpress' ) . '</strong><br>' .
			__( 'You can <strong class="cp-emphasis">Continue at Your Own Risk</strong> with active plugins, but you may experience issues if any plugins are not compatible with ClassicPress.', 'switch-to-classicpress' )
		);
		echo "</p></td></tr>\n";
		} else {
		$preflight_checks['plugins'] = true;
		echo "<tr>\n<td>" . wp_kses_post($icon_preflight_pass) . "</td>\n<td>\n<p>\n";
		esc_html_e(
			'It looks like you have no plugins or have deactived plugins other than Switch to ClassicPress, this is the safest way to migrate your site to ClassicPress.',
			'switch-to-classicpress'
		);
		}
	echo "</p></td></tr>\n";

	// Check: Supported PHP version
	if (
		version_compare( PHP_VERSION, $cp_api_parameters['php']['min'], 'lt' ) ||
		version_compare( PHP_VERSION, $cp_api_parameters['php']['max'], 'gt' )
	) {
		$preflight_checks['php_version'] = false;
		echo "<tr>\n<td>" . wp_kses_post($icon_preflight_fail) . "</td>\n<td>\n";
		$php_message = __( ', which prevents migrating your site to ClassicPress.', 'switch-to-classicpress' );
	} else {
		$preflight_checks['php_version'] = true;
		echo "<tr>\n<td>" . wp_kses_post($icon_preflight_pass) . "</td>\n<td>\n";
		$php_message = ".";
	}
	echo "<p>\n";
		printf( wp_kses_post(
			/* translators: 1: minimum supported PHP version, 2: maximum supported PHP version */
			__( 'ClassicPress supports PHP versions <strong>%1$s</strong> through <strong>%2$s</strong>.',
			'switch-to-classicpress' )
		), esc_html( $cp_api_parameters['php']['min'] ), esc_html( $cp_api_parameters['php']['max_display'] ) );
	echo "<br>\n";
	printf( wp_kses_post(
		/* translators: current PHP version */
		__( 'You are using PHP version <strong>%s</strong>',
		'switch-to-classicpress' ) . $php_message
	), PHP_VERSION );
	echo "\n</p>\n";
	// TODO: Add instructions if PHP too old.
	echo "</td></tr>\n";

	// Check: Support for outgoing HTTPS requests
	if ( ! wp_http_supports( array( 'ssl' ) ) ) {
		$preflight_checks['wp_http_supports_ssl'] = false;
		echo "<tr>\n<td>" . wp_kses_post($icon_preflight_fail) . "</td>\n<td>\n";
	} else {
		$preflight_checks['wp_http_supports_ssl'] = true;
		echo "<tr>\n<td>" . wp_kses_post($icon_preflight_pass) . "</td>\n<td>\n";
	}
	echo "<p>\n";
	esc_html_e(
		'ClassicPress only supports communicating with the ClassicPress.net API over SSL.',
		'switch-to-classicpress'
	);
	echo "\n<br>\n";
	if ( $preflight_checks['wp_http_supports_ssl'] ) {
		esc_html_e(
			'This site supports making outgoing connections securely using SSL.',
			'switch-to-classicpress'
		);
	} else {
		echo wp_kses_post(
			__( 'This site <strong class="cp-emphasis">does not</strong> support making outgoing connections securely using SSL.',
			'switch-to-classicpress' )
		);
		// TODO: Add instructions if SSL not supported.
	}
	echo "\n</p>\n";
	echo "</td></tr>\n";

	// Check: Existing `composer.json` file
	$composer_json_exists = file_exists( ABSPATH . 'composer.json' );
	if ( $composer_json_exists ) {
		echo "<tr>\n<td>" . wp_kses_post($icon_preflight_warn) . "</td>\n<td>\n";
		echo "<p>\n";
		echo wp_kses_post(
			__( 'An existing <code>composer.json</code> file was detected on your site. This file will be <strong class="cp-emphasis">overwritten</strong> during migration.',
			'switch-to-classicpress' )
		);
		echo "<br>\n";
		esc_html_e(
			'If you have previously installed ClassicPress on this site, then you can ignore this warning.',
			'switch-to-classicpress'
		);
		echo "<br>\n";
		echo wp_kses_post(
			__( 'If you are using <code>composer.json</code> to manage dependencies for this site, then you should <strong class="cp-emphasis">back up this file</strong> now, and restore it after the migration.',
			'switch-to-classicpress' )
		);
		echo "\n</p>\n";
		echo "</td></tr>\n";
	}

	// Check: Core files checksums
	$modified_files = classicpress_check_core_files( get_locale() );
	if ( $modified_files === false || ! empty( $modified_files ) ) {
		echo "<tr>\n<td>" . wp_kses_post($icon_preflight_warn) . "</td>\n<td>\n";
	} else {
		echo "<tr>\n<td>" . wp_kses_post($icon_preflight_pass) . "</td>\n<td>\n";
	}
	echo "<p>\n";
	esc_html_e(
		'WordPress core files will be overwritten during the migration.',
		'switch-to-classicpress'
	);
	echo "\n<br>\n";
	if ( $modified_files === false ) {
		echo wp_kses_post(
			'<strong class="cp-emphasis">' . __( 'Unable to determine whether core files were modified.', 'switch-to-classicpress' ) . '</strong>'
		);
		echo "\n<br>\n";
		esc_html_e(
			'This is most likely because you are running a development version of WordPress.',
			'switch-to-classicpress'
		);
	} else if ( empty( $modified_files ) ) {
		esc_html_e(
			'You have not modified any core files.',
			'switch-to-classicpress'
		);
	} else {
		echo '<strong class="cp-emphasis">';
		esc_html_e(
			'Modified core files detected. These customizations will be lost:',
			'switch-to-classicpress'
		);
		echo "</strong>\n<br>\n";
		foreach ( $modified_files as $file ) {
			// translators: modified core file name
			echo esc_html( sprintf( ' - %s', $file ) ) . "<br>\n";
		}

	}
	echo "\n</p>\n";
	echo "</td></tr>\n";

	// TODO: Any other checks needed?

	if ( is_multisite() ) {
		// Show a reminder to backup the multisite install first
		echo "<tr>\n<td>" . wp_kses_post($icon_preflight_warn) . "</td>\n<td>\n";
		echo "<p>\n";
		esc_html_e(
			'Multisite installation detected.',
			'switch-to-classicpress'
		);
		echo "\n<br>\n";
		echo wp_kses_post(
			__( 'Migrating to ClassicPress is supported, but it is <strong class="cp-emphasis">very important</strong> that you perform a backup first.',
			'switch-to-classicpress' )
		);
		echo "\n<br>\n";
		esc_html_e(
			'It would also be a good idea to try the migration on a development or staging site first.',
			'switch-to-classicpress'
		);
		echo "\n</p>\n";
		echo "</td></tr>\n";
	}

	echo "</table>\n";

	if (
		$preflight_checks['wp_version'] &&
		$preflight_checks['theme'] &&
		$preflight_checks['plugins'] &&
		$preflight_checks['php_version'] &&
		$preflight_checks['wp_http_supports_ssl']
	) {
		update_option( 'classicpress_preflight_checks', $preflight_checks, false );
		return true;
	} else {
		delete_option( 'classicpress_preflight_checks' );
		return false;
	}
}

/**
 * Show the controls and information needed to migrate to ClassicPress.
 *
 * NOTE: ONLY CALL THIS FUNCTION IF ALL PRE-FLIGHT CHECKS HAVE PASSED!
 * Otherwise you could end up with a broken site!
 *
 * @since 0.1.0
 */
function classicpress_show_migration_controls() {
	$cp_api_parameters = classicpress_migration_parameters();
	$cp_cv = substr($cp_api_parameters['classicpress']['version'], 0, strpos($cp_api_parameters['classicpress']['version'], '+'));
?>
	<h2 class="cp-migration-info cp-migration-ready">
		<?php echo wp_kses_post( sprintf(
			__( 'It looks like you\'re ready to switch to ClassicPress v%s!', 'switch-to-classicpress' ),
			$cp_cv
		) ); ?>
	</h2>
	<p class="cp-migration-info">
		<?php echo wp_kses_post(
			'<strong class="cp-emphasis">' . __( 'Please make a Complete Backup of your Site Files and Database before you continue!', 'switch-to-classicpress' ) . '</strong>'
		); ?>
	</p>
	<p class="cp-migration-info">
		<?php echo wp_kses_post(
			'<strong class="cp-emphasis">' . __( 'After the Initial Migration, you must visit the Updates Page and Press the Re-Install Now button!', 'switch-to-classicpress' ) . '</strong><br>' .
			__( 'This is needed to complete the switch to ClassicPress and to insure you get the latest updates.', 'switch-to-classicpress' )
		); ?>
	</p>
	<p class="cp-migration-info">
		<?php esc_html_e(
			'Once you click the button below, the migration process will start.',
			'switch-to-classicpress'
		); ?>
	</p>

	<form
		id="cp-migration-form"
		method="post"
		action="update-core.php?action=do-core-upgrade&amp;_migrate=classicpress"
		name="upgrade"
	>
		<?php wp_nonce_field( 'upgrade-core' ); ?>
		<button class="button button-primary button-hero" type="submit" name="upgrade">
<?php
	if ( is_multisite() ) {
		echo wp_kses_post(
			__( 'Switch this <strong>entire multisite installation</strong> to ClassicPress <strong>now</strong>!',
			'switch-to-classicpress' )
		);
	} else {
		echo wp_kses_post(
			__( 'Switch this site to ClassicPress <strong>now</strong>!',
			'switch-to-classicpress' )
		);
	}
?>
		</button>
	</form>

	<h2><?php esc_html_e( 'More Details', 'switch-to-classicpress' ); ?></h2>

	<p class="cp-migration-info">
		<?php esc_html_e(
			'All core WordPress files will be replaced with their ClassicPress versions. Depending on the server this website is hosted on, this process can take a while.',
			'switch-to-classicpress'
		); ?>
	</p>
	<p class="cp-migration-info">
		<?php echo wp_kses_post(
			__( 'We want to emphasise that <strong>all your own content (posts, pages, themes, plugins, uploads, wp-config.php file, .htaccess file, etc.) is 100% safe</strong> as the migration process is not touching any of that.',
			'switch-to-classicpress' )
		); ?>
	</p>
	<p class="cp-migration-info">
		<?php esc_html_e(
			'Once the process has completed, you will see the about page of ClassicPress where you can read more information about the project.',
			'switch-to-classicpress'
		); ?>
	</p>
	<p class="cp-migration-info">
		<?php echo wp_kses_post(
			__( 'We thank you for switching from WordPress to ClassicPress!<br>The CMS for Creators. Stable. Lightweight. Instantly Familiar.',
			'switch-to-classicpress' )
		); ?>
	</p>
<?php
}

/**
 * Show information about what to do when we can't migrate to ClassicPress.
 *
 * @since 0.1.0
 */
function classicpress_show_migration_blocked_info() {
	if ( function_exists( 'classicpress_version' ) ) {
		// No need to show an error message if we're already on ClassicPress.
		return;
	}
?>
	<h2 class="cp-migration-info cp-emphasis">
		<?php esc_html_e(
			'Sorry, we can\'t switch this site to ClassicPress at this time.',
			'switch-to-classicpress'
		); ?>
	</h2>

	<p class="cp-migration-info">
		<?php printf(
			wp_kses_post(
				/* translators: link to ClassicPress support forum */
				__( 'If you\'re not sure how to fix the issues above, you can ask for help in our <a href="%s">Support Forum</a>.',
				'switch-to-classicpress' )
			),
			'https://forums.classicpress.net/tags/c/plugins/9/migration-plugin'
		);
		?>
	</p>
<?php
}

/**
 * Show the controls and information needed to migrate to any version of
 * WordPress or ClassicPress.
 *
 * @since 1.0.0
 *
 * @bool $ok Whether we can continue with a custom migration. This is used to
 *           hide the "advanced controls" button if preflight checks failed.
 */
function classicpress_show_advanced_migration_controls( $ok = true ) {
	$cp_api_parameters = classicpress_migration_parameters();
	$cp_api_error = false;
	if (is_wp_error( $cp_api_parameters ) ) {
		$cp_api_parameters = array();
		$cp_api_error = true;
	}
	$cp_versions = get_cp_versions();
	// Get version information here
	if ( $cp_api_error === false ) {
		$cp_cv = substr($cp_api_parameters['classicpress']['version'], 0, strpos($cp_api_parameters['classicpress']['version'], '+'));
		$v2_previous = get_previous_version($cp_cv, $cp_versions);
		$v2_prev_url = get_migration_from_cp_version($v2_previous);
		$cp_v1 = substr($cp_api_parameters['links']['ClassicPress v1'], 119, -23);
		$v1_previous = get_previous_version($cp_v1, $cp_versions);
		$v1_prev_url = get_migration_from_cp_version($v1_previous);
		$wp_v6 = substr($cp_api_parameters['links']['WordPress 6.2.x'], 32, -4);
		$wp_v4 = substr($cp_api_parameters['links']['WordPress 4.9.x'], 32, -4);
	}
	$is_wp = ! function_exists( 'classicpress_version' );
	global $wp_version;
	if (!$is_wp) {
		global $cp_version;
		if (strpos($cp_version, 'migration')) {
			$my_cp = "0.0.0";
		} else {
			$my_cp = preg_replace('#[+-].*$#', '', $cp_version);
		}
		// Build Proper URL here (ClassicPress can use Release)
		if ( $cp_api_error === false ) {
			$cp_cv_build = getReleaseFromCPVersion($cp_cv);
			$cp_p2_build = getReleaseFromCPVersion($v2_previous);
			$cp_v1_build = getReleaseFromCPVersion($cp_v1);
			$cp_p1_build = getReleaseFromCPVersion($v1_previous);
		}
	} else {
		$my_cp = "0.0.0";
		// Build Proper URL here (WordPress must use Migration)
		$cp_cv_build = $cp_api_parameters['links']['ClassicPress v2'];
		$cp_p2_build = $v2_prev_url;
		$cp_v1_build = $cp_api_parameters['links']['ClassicPress v1'];
		$cp_p1_build = $v1_prev_url;
	}

	if ( $ok ) { ?>
		<button
			id="cp-show-advanced-migration-form"
			class="button button-large hide-if-no-js"
		>
			<?php esc_html_e(
				'Show Advanced Version Control',
				'switch-to-classicpress'
			); ?>
		</button>
	<?php } ?>

	<form
		id="cp-advanced-migration-form"
		class="hidden"
		method="post"
		action="update-core.php?action=do-core-upgrade&amp;_migrate=_custom"
		name="upgrade"
	>
		<h2>
			<?php esc_html_e(
				'Advanced Version Control Tool',
				'switch-to-classicpress'
			); ?>
		</h2>

		<table class="form-table">
				<tr>
					<th scope="row" class="cp-emphasis">
						<?php esc_html_e(
							'Notice:',
							'switch-to-classicpress'
						); ?>
					</th>
					<td>
						<p>
							<?php esc_html_e(
								'You can use this tool to install another version of ClassicPress or WordPress.',
								'switch-to-classicpress'
							); ?>
						</p>
						<p>
							<?php echo wp_kses_post(
								__( 'Release Builds of ClassicPress <strong class="cp-emphasis"><u>do not work</u></strong> for migration from WordPress.',
								'switch-to-classicpress' )
							); ?>
						</p>
						<p>
							<?php printf(
								wp_kses_post(
									/* translators: link to ClassicPress migration builds */
									__( 'You can find ClassicPress Nightly Migration & Nightly Update Builds on <a href="%s">GitHub</a>.',
									'switch-to-classicpress' )
								),
								'https://github.com/ClassyBot'
							); ?>
						</p>
					</td>
				</tr>
			<tr>
				<th scope="row" class="cp-emphasis">
					<?php esc_html_e( 'WARNING:', 'switch-to-classicpress' ); ?>
				</th>
				<td>
					<p>
						<?php esc_html_e(
							'If all requirements for your custom version have been met, then migration should complete.',
							'switch-to-classicpress'
						); ?>
					</p>
					<p>
						<?php echo wp_kses_post(
							__( 'That does not mean it will work in every case and <strong class="cp-emphasis">Older Versions may have Password or Security Issues!</strong>',
							'switch-to-classicpress' )
						); ?>
					</p>
					<p>
						<?php echo wp_kses_post(
							__( 'Please, make a <strong class="cp-emphasis">Complete Backup of your Site Files and Database</strong> before using this tool <strong class="cp-emphasis">At Your Own Risk!</strong>',
							'switch-to-classicpress' )
						); ?>
					</p>
					<?php
					$php_version_49 = '7.5';
					if ( version_compare( PHP_VERSION, $php_version_49, 'lt' ) ) {
						echo wp_kses_post(
							'<p><strong class="cp-emphasis">' . __( '* MIGRATING TO WP v4.9 SHOULD BE A LAST RESORT AND IS DONE AT YOUR OWN RISK!', 'switch-to-classicpress' ) . '</strong></p>'
						);
					}
					?>
				</td>
			</tr>
			<?php if ( $cp_api_error === false ) : ?>
			<tr>
				<th colspan="2">
				<label for="picker" id="picker-label"><?php esc_html_e( 'Enter or Select a Custom Build URL', 'switch-to-classicpress' ); ?></label>
					<select id="picker" onchange="SelectVersion()">
						<option value="">-- <?php esc_html_e( 'Please Select an Option', 'switch-to-classicpress' ); ?> --</option>
					<optgroup label="<?php esc_html_e( 'ClassicPress Builds', 'switch-to-classicpress' ); ?>">
<?php if ($my_cp !== $cp_cv) { ?>
						<option value="<?php echo esc_url($cp_cv_build); ?>">ClassicPress v<?php echo esc_html($cp_cv); ?></option>
<?php
	}
	if ($my_cp !== $v2_previous && substr($v2_previous, 0, 1) === substr($cp_cv, 0, 1)) {
?>
						<option value="<?php echo esc_url($cp_p2_build); ?>">ClassicPress v<?php echo esc_html($v2_previous); ?></option>
<?php
	}
	if ($my_cp !== $cp_v1) {
?>
						<option value="<?php echo esc_url($cp_v1_build); ?>">ClassicPress v<?php echo esc_html($cp_v1); ?></option>
<?php
	}
	if ($my_cp !== $v1_previous) {
?>
						<option value="<?php echo esc_url($cp_p1_build); ?>">ClassicPress v<?php echo esc_html($v1_previous); ?></option>
<?php } ?>
					</optgroup>
					<optgroup label="<?php esc_html_e( 'WordPress Builds', 'switch-to-classicpress' ); ?>">
<?php if ($wp_version !== $cp_api_parameters['wordpress']['max']) { ?>
						<option value="<?php echo esc_url($cp_api_parameters['links']['WordPress Latest']); ?>">WordPress v<?php echo esc_html($cp_api_parameters['wordpress']['max']); ?></option>
<?php
	}
	if (!$is_wp && $wp_v6 === $wp_version) { $wp_check = "0.0.0"; } else { $wp_check = $wp_version; }
	if ($wp_check !== $wp_v6) {
?>
						<option value="<?php echo esc_url($cp_api_parameters['links']['WordPress 6.2.x']); ?>">WordPress v<?php echo esc_html($wp_v6); ?></option>
<?php
	}
	// WPv4.9 DOES NOT WORK with php8 - Block the option here
	if (!$is_wp && $wp_v4 === $wp_version) { $wp_check = "0.0.0"; } else { $wp_check = $wp_version; }
	if ( version_compare( PHP_VERSION, $php_version_49, 'lt' ) && $wp_check !== $wp_v4 ) {
?>
						<option value="<?php echo esc_url($cp_api_parameters['links']['WordPress 4.9.x']); ?>">WordPress v<?php echo esc_html($wp_v4); ?></option>
<?php } ?>
					</optgroup>
					</select>
				</th>
			</tr>
			<?php endif; ?>
			<tr>
				<th scope="row">
					<label for="cp-build-url">
						<?php esc_html_e(
							'Build URL:',
							'switch-to-classicpress'
						); ?>
					</label>
				</th>
				<td>
					<input type="text" id="cp-build-url" name="_build_url">
				</td>
			</tr>
		</table>
		<?php wp_nonce_field( 'upgrade-core' ); ?>
		<button class="button button-primary button-hero" type="submit" name="upgrade">
			<?php esc_html_e(
				'Perform the Custom Migration now!',
				'switch-to-classicpress'
			); ?>
		</button>
	</form>
<script>
function SelectVersion() {
    var txt = document.getElementById("picker").value;
    document.getElementById("cp-build-url").value = txt;
}
</script>
<?php
}
