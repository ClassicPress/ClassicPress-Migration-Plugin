<?php

/**
 * Output the plugin's styles for the relevant admin pages.
 *
 * @since 0.1.0
 */
function classicpress_print_admin_styles() {
?>
<style>
.cp-migration-action, .cp-emphasis {
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
</style>
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
	$wp_version_min = '4.9.0';
	$wp_version_max = '5.0.0';
	$wp_version_check_intro_message = sprintf( __(
		/* translators: 1: minimum supported WordPress version, 2: maximum supported WordPress version */
		'This plugin supports WordPress versions <strong>%1$s</strong> to <strong>%2$s</strong> (and some newer development versions).',
		'switch-to-classicpress'
	), $wp_version_min, $wp_version_max );
	$wp_version_check_intro_message .= "<br>\n";
	if (
		// Version is outside of our "stable release" range...
		(
			version_compare( $wp_version, $wp_version_min, 'lt' ) ||
			version_compare( $wp_version, $wp_version_max, 'gt' )
		) &&
		// ... and it's not a known development release.
		! preg_match( '#^5\.0\.1-(alpha|beta)\b#', $wp_version ) &&
		! preg_match( '#^5\.1-(alpha|beta)\b#', $wp_version )
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
				'We cannot guarantee that the migration process is going to work, and may even leave your current installation partially broken.',
				'switch-to-classicpress'
			);
			echo "<br>\n";
			_e(
				'<strong class="cp-emphasis">Proceed at your own risk!</strong>',
				'switch-to-classicpress'
			);
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
		action="update-core.php?action=do-core-upgrade&amp;migrate=classicpress"
		name="upgrade"
	>
		<?php wp_nonce_field( 'upgrade-core' ); ?>
		<button class="button button-primary button-hero" type="submit" name="upgrade">
<?php
	if ( is_multisite() ) {
		_e(
			'Switch this <strong>entire multisite installation</strong> to ClassicPress <strong>now</strong>!',
			'migrate-to-classicpress'
		);
	} else {
		_e(
			'Switch this site to ClassicPress <strong>now</strong>!',
			'migrate-to-classicpress'
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
		<?php _e(
			"If you're not sure how to fix the issues above, contact your hosting provider for help.",
			'switch-to-classicpress'
		); ?>
	</p>
<?php
}
