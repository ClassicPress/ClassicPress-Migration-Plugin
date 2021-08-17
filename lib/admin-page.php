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
</style>
<script>
jQuery( function( $ ) {
	$( '#cp-show-advanced-migration-form' ).on( 'click', function() {
		$( '#cp-advanced-migration-form' ).removeClass( 'hidden' );
		$( this ).remove();
	} );
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
	add_management_page(
		__( 'Switch to ClassicPress', 'switch-to-classicpress' ),
		__( 'Switch to ClassicPress', 'switch-to-classicpress' ),
		'read',
		'switch-to-classicpress',
		'classicpress_show_admin_page'
	);
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
	<h2><?php _e( 'Feedback and Support', 'switch-to-classicpress' ); ?></h2>

	<p class="cp-migration-info">
		<?php _e(
			"Do you have feedback about this plugin, or about ClassicPress itself? Need help with something? We'd love to know what you think!",
			'switch-to-classicpress'
		); ?>
	</p>
	<ul class="cp-migration-info">
		<li><?php printf(
			__(
				/* translators: 1: link with instructions to join our Forum, 2: link to join ClassicPress Slack */
				'For support, suggestions for improvement, or general discussion about how the plugin works, visit us in our <a href="%1$s">support forum</a> or <a href="%2$s">Slack group</a>.',
				'switch-to-classicpress'
			),
			'https://forums.classicpress.net/c/support/migration-plugin',
			'https://www.classicpress.net/join-slack/'
		); ?></li>
		<li><?php printf(
			__(
				/* translators: link to create a new GitHub issue for this plugin */
				'For <strong>specific</strong> bug reports or suggestions, <a href="%s">add a new issue on GitHub</a>.',
				'switch-to-classicpress'
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
		if ( is_multisite() ) {
			$delete_plugin_url = network_admin_url( 'plugins.php' );
		} else {
			$delete_plugin_url = admin_url( 'plugins.php' );
		}
?>
		<div class="notice notice-success">
			<p>
				<?php esc_html_e(
					"Hey, good job, you're already running ClassicPress!",
					'switch-to-classicpress'
				); ?>
			</p>
			<p>
				<?php echo sprintf(
					/* translators: %s: URL to plugins page */
					__(
						'You can <a href="%s">delete this plugin</a> now.',
						'switch-to-classicpress'
					),
					$delete_plugin_url
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
					"Sorry, this plugin doesn't support sites hosted on WordPress.com.",
					'switch-to-classicpress'
				); ?>
			</p>
			<p>
				<?php _e(
					'In order to switch to ClassicPress, you\'ll need to <a href="https://move.wordpress.com/">move to a self-hosted WordPress site</a> first.',
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
					"Sorry, you're not allowed to perform this action.",
					'switch-to-classicpress'
				); ?>
			</p>
			<p>
				<?php esc_html_e(
					"Please contact a site administrator for more information.",
					'switch-to-classicpress'
				); ?>
			</p>
		</div>
<?php
		return false;
	}

	// Get migration plugin parameters.
	$parameters = classicpress_migration_parameters();
	if ( is_wp_error( $parameters ) ) {
?>
		<div class="notice notice-error">
			<p>
				<?php echo $parameters->get_error_message(); ?>
				<?php echo json_encode( $parameters->get_error_data() ); ?>
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
	$wp_version_min = $parameters['wordpress']['min'];
	$wp_version_max = $parameters['wordpress']['max'];
	$wp_version_check_intro_message = sprintf( __(
		/* translators: 1: minimum supported WordPress version, 2: maximum supported WordPress version */
		'This plugin supports WordPress versions <strong>%1$s</strong> to <strong>%2$s</strong> (and some newer development versions).',
		'switch-to-classicpress'
	), $wp_version_min, $wp_version_max );
	$wp_version_check_intro_message .= "<br>\n";

	if (
		// Version is outside of our target range of WP stable releases...
		(
			version_compare( $wp_version, $wp_version_min, 'lt' ) ||
			version_compare( $wp_version, $wp_version_max, 'gt' )
		) &&
		// ... and it doesn't match any other acceptable version patterns
		empty( array_filter( $parameters['wordpress']['other'], function( $pattern ) {
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
		if ( apply_filters( 'classicpress_ignore_wp_version', false ) ) {
			$preflight_checks['wp_version'] = true;
			echo "<tr>\n<td>$icon_preflight_warn</td>\n<td>\n";
			echo "<p>\n";
			echo $wp_version_check_intro_message;
			_e(
				'The preflight check for supported WordPress versions has been <strong class="cp-emphasis">disabled</strong>.',
				'switch-to-classicpress'
			);
			echo "<br>\n";
			_e(
				'We cannot guarantee that the migration process is going to work, and it may even leave your current installation partially broken.',
				'switch-to-classicpress'
			);
			echo "<br>\n";
			_e(
				'<strong class="cp-emphasis">Proceed at your own risk!</strong>',
				'switch-to-classicpress'
			);
			echo "<br>\n";
		} else {
			$preflight_checks['wp_version'] = false;
			echo "<tr>\n<td>$icon_preflight_fail</td>\n<td>\n";
			echo "<p>\n";
			echo $wp_version_check_intro_message;
		}
	} else {
		$preflight_checks['wp_version'] = true;
		if ( substr( $wp_version, 0, 1 ) === '5' ) {
			echo "<tr>\n<td>$icon_preflight_warn</td>\n<td>\n";
		} else {
			echo "<tr>\n<td>$icon_preflight_pass</td>\n<td>\n";
		}
		echo "<p>\n";
		echo $wp_version_check_intro_message;
	}
	printf( __(
		/* translators: current WordPress version */
		'You are running WordPress version <strong>%s</strong>.',
		'switch-to-classicpress'
	), $wp_version );
	if ( substr( $wp_version, 0, 1 ) === '5' && $preflight_checks['wp_version'] ) {
		echo "<br>\n";
		_e(
			'Migration is supported, but content edited in the new WordPress block editor may not be fully compatible with ClassicPress.',
			'switch-to-classicpress'
		);
		echo "<br>\n";
		_e(
			'After the migration, we recommend reviewing each recently edited post or page and restoring to an earlier revision if needed.',
			'switch-to-classicpress'
		);
	}
	echo "\n</p>\n";
	// TODO: Add instructions if WP too old.
	echo "</td></tr>\n";

	// Check: Conflicting Theme
	$theme = wp_get_theme();
	if ( isset( $parameters['themes'] ) &&
		in_array( $theme->stylesheet, (array) $parameters['themes'] ) ||
		( is_child_theme() && in_array( $theme->parent()->stylesheet, (array) $parameters['themes'] ) )
	) {
		$preflight_checks['theme'] = false;
		echo "<tr>\n<td>$icon_preflight_fail</td>\n<td>\n";
		printf( __(
			/* translators: active theme name */
			'It looks like you are using the <strong>%1$s</strong> theme. Unfortunately it is incompatible with ClassicPress.',
			'switch-to-classicpress'
		), $theme->name );
		echo "<br>\n";
		_e(
			'Consider switching to a different theme, perhaps an older core theme, and try again.',
			'switch-to-classicpress'
		);
	} elseif ( empty ( $theme->get( 'RequiresWP' ) ) ) {
		$preflight_checks['theme'] = true;
		echo "<tr>\n<td>$icon_preflight_warn</td>\n<td>\n";
		printf( __(
			/* translators: active theme name */
			'It looks like you are using the <strong>%1$s</strong> theme. We cannot be sure it is compatible with ClassicPress because it is not declaring a minimum required version of WordPress.',
			'switch-to-classicpress'
		), $theme->name );
	} elseif ( version_compare( $theme->get( 'RequiresWP' ), '5.0' ) >= 0 ) {
		$preflight_checks['theme'] = false;
		echo "<tr>\n<td>$icon_preflight_fail</td>\n<td>\n";
		printf( __(
			/* translators: active theme name */
			'It looks like you are using the <strong>%1$s</strong> theme. Unfortunately it seems it requires WordPress %2$s or above and it may therefore be incompatible with ClassicPress.',
			'switch-to-classicpress'
		), $theme->name, $theme->get( 'RequiresWP' ) );
		echo "<br>\n";
		_e(
			'Consider switching to a different theme, perhaps an older core theme, and try again.',
			'switch-to-classicpress'
		);
	} else {
		$preflight_checks['theme'] = true;
		echo "<tr>\n<td>$icon_preflight_warn</td>\n<td>\n";
		printf( __(
			/* translators: active theme name */
			'It looks like you are using the <strong>%1$s</strong> theme. We are not aware of any incompatibilities between this theme and ClassicPress.',
			'switch-to-classicpress'
		), $theme->name );
	}
	echo "</td></tr>\n";

	// Check: Conflicting Plugins
	$plugins = get_option( 'active_plugins' );
	$plugin_headers = array( 'Name' => 'Plugin Name', 'RequiresWP'  => 'Requires at least' );
	$declared_incompatible_plugins = array();
	$undeclared_compatibility_plugins = array();

	// Start by checking if plugins have declared they require WordPress 5.0 or higher
	foreach ( $plugins as $plugin ) {
		if ( isset( $parameters['plugins'] ) && in_array( $plugin, $parameters['plugins'] ) ) {
			continue;
		}

		$plugin_data = get_file_data( WP_PLUGIN_DIR . '/' . $plugin, $plugin_headers );
		$plugin_name = $plugin_data['Name'];
		if ( version_compare( $plugin_data['RequiresWP'], '5.0' ) >= 0 ) {
			$declared_incompatible_plugins[ $plugin ] = $plugin_name;
		} else {
			$plugin_files = get_plugin_files( $plugin );
			$readmes = array_filter( $plugin_files, function( $files ) {
				return ( stripos( $files, 'readme') !== false );
			} );
			foreach( $readmes as $readme ) {
				if ( empty( $readme ) ) {
					continue;
				}
				$readme_data = get_file_data( WP_PLUGIN_DIR . '/' . $readme, $plugin_headers );
				if ( version_compare( $readme_data['RequiresWP'], '5.0' ) >= 0 ) {
					$declared_incompatible_plugins[ $plugin ] = $plugin_name;
					continue;
				}
			}
		}
		if (
			empty( $plugin_data['RequiresWP'] ) &&
			( empty( $readmes ) || empty( $readme_data['RequiresWP'] ) ) &&
			false === array_key_exists( $plugin, $declared_incompatible_plugins )
		) {
			$undeclared_compatibility_plugins[ $plugin ] = $plugin_name;
		}
	}

	// Compare active plugins with API response of known conflicting plugins
	if (
		isset( $parameters['plugins'] ) && $plugins !== array_diff( $plugins, $parameters['plugins'] ) ||
		! empty( $declared_incompatible_plugins )
	) {
		$preflight_checks['plugins'] = false;

		$conflicting_plugins = array_intersect( $parameters['plugins'], $plugins );
		$conflicting_plugin_names = array();
		foreach( $conflicting_plugins as $conflicting_plugin ) {
			$conflicting_plugin_data = get_plugin_data( WP_CONTENT_DIR . '/plugins/' . $conflicting_plugin );
			$conflicting_plugin_names[] = $conflicting_plugin_data['Name'];
		}

		if ( ! empty( $declared_incompatible_plugins ) ) {
			foreach( $declared_incompatible_plugins as $slug => $name ) {
				$conflicting_plugin_names[] = $name;
			}
		}

		echo "<tr>\n<td>$icon_preflight_fail</td>\n<td>\n";
		_e(
			'We have detected one or more incompatible plugins that prevent migrating your site to ClassicPress.',
			'switch-to-classicpress'
		);
		echo "<br>\n";
		 _e(
			'Please deactivate the following plugins if you wish to continue migrating your site to ClassicPress:',
			'switch-to-classicpress'
		);
		echo "<br>\n";
		/* translators: List of conflicting plugin names */
		printf( __(
			'<strong>%s<strong>',
			'switch-to-classicpress'
		), implode( ', ', $conflicting_plugin_names ) );
	} else {
		$preflight_checks['plugins'] = true;
		echo "<tr>\n<td>$icon_preflight_warn</td>\n<td>\n";
		_e(
			'We are not aware that any of your active plugins are incompatible with ClassicPress.',
			'switch-to-classicpress'
		);
	}
	echo "</td></tr>\n";

	if ( ! empty( $undeclared_compatibility_plugins ) ) {
		echo "<tr>\n<td>$icon_preflight_warn</td>\n<td>\n";
		_e(
			'We have detected one or more plugins that fail to declare a minimum compatible WordPress version. They may prevent or impact on migrating your site to ClassicPress.',
			'switch-to-classicpress'
		);
		echo "<br>\n";
		 _e(
			'We would recommned deactivating the following plugins if you wish to continue migrating your site to ClassicPress:',
			'switch-to-classicpress'
		);
		echo "<br>\n";
		/* translators: List of conflicting plugin names */
		printf( __(
			'<strong>%s<strong>',
			'switch-to-classicpress'
		), implode( ', ', $undeclared_compatibility_plugins ) );
		echo "</td></tr>\n";
	}

	// Check: Supported PHP version
	$php_version_min = '5.6';
	if ( version_compare( PHP_VERSION, $php_version_min, 'lt' ) ) {
		$preflight_checks['php_version'] = false;
		echo "<tr>\n<td>$icon_preflight_fail</td>\n<td>\n";
	} else {
		$preflight_checks['php_version'] = true;
		echo "<tr>\n<td>$icon_preflight_pass</td>\n<td>\n";
	}
	echo "<p>\n";
	printf( __(
		/* translators: minimum supported PHP version */
		'ClassicPress supports PHP versions <strong>%1$s</strong> and <strong>newer</strong>.',
		'switch-to-classicpress'
	), $php_version_min );
	echo "<br>\n";
	printf( __(
		/* translators: current PHP version */
		'You are using PHP version <strong>%s</strong>.',
		'switch-to-classicpress'
	), PHP_VERSION );
	echo "\n</p>\n";
	// TODO: Add instructions if PHP too old.
	echo "</td></tr>\n";

	// Check: Support for outgoing HTTPS requests
	if ( ! wp_http_supports( array( 'ssl' ) ) ) {
		$preflight_checks['wp_http_supports_ssl'] = false;
		echo "<tr>\n<td>$icon_preflight_fail</td>\n<td>\n";
	} else {
		$preflight_checks['wp_http_supports_ssl'] = true;
		echo "<tr>\n<td>$icon_preflight_pass</td>\n<td>\n";
	}
	echo "<p>\n";
	_e(
		'ClassicPress only supports communicating with the ClassicPress.net API over SSL.',
		'switch-to-classicpress'
	);
	echo "\n<br>\n";
	if ( $preflight_checks['wp_http_supports_ssl'] ) {
		_e(
			'This site supports making outgoing connections securely using SSL.',
			'switch-to-classicpress'
		);
	} else {
		_e(
			'This site <strong class="cp-emphasis">does not</strong> support making outgoing connections securely using SSL.',
			'switch-to-classicpress'
		);
		// TODO: Add instructions if SSL not supported.
	}
	echo "\n</p>\n";
	echo "</td></tr>\n";

	// Check: Existing `composer.json` file
	$composer_json_exists = file_exists( ABSPATH . 'composer.json' );
	if ( $composer_json_exists ) {
		echo "<tr>\n<td>$icon_preflight_warn</td>\n<td>\n";
		echo "<p>\n";
		_e(
			'An existing <code>composer.json</code> file was detected on your site. This file will be <strong class="cp-emphasis">overwritten</strong> during migration.',
			'switch-to-classicpress'
		);
		echo "<br>\n";
		_e(
			'If you have previously installed ClassicPress on this site, then you can ignore this warning.',
			'switch-to-classicpress'
		);
		echo "<br>\n";
		_e(
			'If you are using <code>composer.json</code> to manage dependencies for this site, then you should <strong class="cp-emphasis">back up this file</strong> before continuing, and restore it after the migration.',
			'switch-to-classicpress'
		);
		echo "\n</p>\n";
		echo "</td></tr>\n";
	}

	// Check: Core files checksums
	$modified_files = classicpress_check_core_files();
	if ( $modified_files === false || ! empty( $modified_files ) ) {
		echo "<tr>\n<td>$icon_preflight_warn</td>\n<td>\n";
	} else {
		echo "<tr>\n<td>$icon_preflight_pass</td>\n<td>\n";
	}
	echo "<p>\n";
	_e(
		'Your WordPress core files will be overwritten.',
		'switch-to-classicpress'
	);
	echo "\n<br>\n";
	if ( $modified_files === false ) {
		_e(
			'<strong class="cp-emphasis">Unable to determine whether core files were modified</strong>.',
			'switch-to-classicpress'
		);
		echo "\n<br>\n";
		_e(
			'This is most likely because you are running a development version of WordPress.',
			'switch-to-classicpress'
		);
	} else if ( empty( $modified_files ) ) {
		_e(
			'You have not modified any core files.',
			'switch-to-classicpress'
		);
	} else {
		echo '<strong class="cp-emphasis">';
		_e(
			'Modified core files detected. These customisations will be lost.',
			'switch-to-classicpress'
		);
		echo "</strong>\n<br>\n";
		_e(
			'If you have JavaScript enabled, you can see a list of modified files <strong>in your browser console</strong>.',
			'switch-to-classicpress'
		);
		echo "\n<script>console.log( 'modified core files:', ";
		echo wp_json_encode( $modified_files );
		echo ' );</script>';
	}
	echo "\n</p>\n";
	echo "</td></tr>\n";

	// TODO: Any other checks needed?

	if ( is_multisite() ) {
		// Show a reminder to backup the multisite install first
		echo "<tr>\n<td>$icon_preflight_warn</td>\n<td>\n";
		echo "<p>\n";
		_e(
			'Multisite installation detected.',
			'switch-to-classicpress'
		);
		echo "\n<br>\n";
		_e(
			'Migrating to ClassicPress is supported, but it is <strong class="cp-emphasis">very important</strong> that you perform a backup first.',
			'switch-to-classicpress'
		);
		echo "\n<br>\n";
		_e(
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
 * Otherwise you *will* end up with a broken site!
 *
 * @since 0.1.0
 */
function classicpress_show_migration_controls() {
?>
	<h2 class="cp-migration-info cp-migration-ready">
		<?php _e( "It looks like you're ready to switch to ClassicPress!", 'switch-to-classicpress' ); ?>
	</h2>
	<p class="cp-migration-info">
		<?php _e( 'First things first, just in case something does not go as planned, <strong class="cp-emphasis">please make a backup of your site files and database</strong>.', 'switch-to-classicpress' ); ?>
	</p>
	<p class="cp-migration-info">
		<?php _e( 'After clicking the button below, the migration process will start.', 'switch-to-classicpress' ); ?>
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
		_e(
			'Switch this <strong>entire multisite installation</strong> to ClassicPress <strong>now</strong>!',
			'switch-to-classicpress'
		);
	} else {
		_e(
			'Switch this site to ClassicPress <strong>now</strong>!',
			'switch-to-classicpress'
		);
	}
?>
		</button>
	</form>

	<h2><?php _e( 'More Details', 'switch-to-classicpress' ); ?></h2>

	<p class="cp-migration-info">
		<?php _e( 'All core WordPress files will be replaced with their ClassicPress versions. Depending on the server this website is hosted on, this process can take a while.', 'switch-to-classicpress' ); ?>
	</p>
	<p class="cp-migration-info">
		<?php _e( 'We want to emphasise that <strong>all your own content (posts, pages, themes, plugins, uploads, wp-config.php file, .htaccess file, etc.) is 100% safe</strong> as the migration process is not touching any of that.', 'switch-to-classicpress' ); ?>
	</p>
	<p class="cp-migration-info">
		<?php _e( 'Once the process has completed, you will see the about page of ClassicPress where you can read more information about the project.', 'switch-to-classicpress' ); ?>
	</p>
	<p class="cp-migration-info">
		<?php _e( 'We thank you for switching from WordPress to ClassicPress!<br>The business-focused CMS. Powerful. Versatile. Predictable.', 'switch-to-classicpress' ); ?>
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
		<?php _e(
			"Sorry, we can't switch this site to ClassicPress at this time.",
			'switch-to-classicpress'
		); ?>
	</h2>

	<p class="cp-migration-info">
		<?php printf(
			__(
				/* translators: link to ClassicPress migration builds */
				'If you\'re not sure how to fix the issues above, you can ask for help in our <a href="%s">Support Forum</a>.',
				'switch-to-classicpress'
			),
			'https://forums.classicpress.net/c/support/migration-plugin'
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
	$is_wp = ! function_exists( 'classicpress_version' );

	if ( $ok ) { ?>
		<button
			id="cp-show-advanced-migration-form"
			class="button button-large hide-if-no-js"
		>
			<?php esc_html_e(
				'Show advanced controls',
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
				'Switch to any version of ClassicPress or WordPress',
				'switch-to-classicpress'
			); ?>
		</h2>
		<table class="form-table">
			<?php if ( $is_wp ) { ?>
				<tr>
					<th scope="row" class="cp-emphasis">
						<?php esc_html_e(
							'Warning 1:',
							'switch-to-classicpress'
						); ?>
					</th>
					<td>
						<p>
							<?php _e(
								'Use this form to switch to other versions of WordPress, or ClassicPress <strong class="cp-emphasis">migration builds</strong>!',
								'switch-to-classicpress'
							); ?>
						</p>
						<p>
							<?php _e(
								'Official release builds of ClassicPress <strong class="cp-emphasis">will not work</strong>.',
								'switch-to-classicpress'
							); ?>
						</p>
						<p>
							<?php printf(
								__(
									/* translators: link to ClassicPress migration builds */
									'You can find ClassicPress migration builds <a href="%s">on GitHub</a>.',
									'switch-to-classicpress'
								),
								'https://github.com/ClassyBot/ClassicPress-nightly/releases'
							); ?>
						</p>
					</td>
				</tr>
			<?php } ?>
			<tr>
				<th scope="row" class="cp-emphasis">
					<?php if ( $is_wp ) {
						esc_html_e( 'Warning 2:', 'switch-to-classicpress' );
					} else {
						esc_html_e( 'Warning:', 'switch-to-classicpress' );
					} ?>
				</th>
				<td>
					<p>
						<?php esc_html_e(
							"As long as the regular requirements for your target version are met, like the preflight checks for ClassicPress, then we haven't been able to break this yet. However, that doesn't mean it works under all circumstances!",
							'switch-to-classicpress'
						); ?>
					</p>
					<p>
						<?php _e(
							'Please, definitely <strong class="cp-emphasis">take a backup of your site</strong> before using this feature.',
							'switch-to-classicpress'
						); ?>
					</p>
				</td>
			</tr>
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
					<input
						type="text"
						id="cp-build-url"
						name="_build_url"
						value=""
					/>
				</td>
			</tr>
		</table>
		<?php wp_nonce_field( 'upgrade-core' ); ?>
		<button class="button button-primary button-hero" type="submit" name="upgrade">
			<?php esc_html_e(
				'Do the custom migration now!',
				'switch-to-classicpress'
			); ?>
		</button>
	</form>
<?php
}
