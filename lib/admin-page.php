<?php

/**
 * Outputs the plugin's styles for the relevant admin pages.
 *
 * @since 0.0.1
 */
function classicpress_print_admin_styles() {
?>
<style>
.cp-migration-action, .cp-emphasis {
	font-weight: bold;
	color: #800;
}
.cp-migration-action:hover {
	color: #f00;
}
</style>
<?php
}
add_action( 'admin_head-plugins.php', 'classicpress_print_admin_styles' );
add_action( 'admin_head-tools.php', 'classicpress_print_admin_styles' );

/**
 * Adds an entry to the plugin row meta for this plugin.
 *
 * @since 0.0.1
 *
 * @param array  $plugin_meta An array of the plugin's metadata.
 * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
 * @param array  $plugin_data An array of plugin data. (Omitted here.)
 * @param string $status      Status of the plugin. (Omitted here.)
 * @return array Updated plugin metadata.
 */
function classicpress_add_plugins_page_link( $plugin_meta, $plugin_file ) {
	if ( $plugin_file === 'switch-to-classicpress/switch-to-classicpress.php' ) {
		$plugin_meta[] = sprintf(
			'<a class="cp-migration-action" href="%s">%s</a>',
			admin_url( 'tools.php?page=switch-to-classicpress' ),
			esc_html__( 'Switch to ClassicPress', 'switch-to-classicpress' )
		);
	}

	return $plugin_meta;
}
add_filter( 'plugin_row_meta', 'classicpress_add_plugins_page_link', 10, 2 );

/**
 * Registers the plugin's admin page under the Tools menu.
 *
 * @since 0.0.1
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
add_action( 'admin_menu', 'classicpress_register_admin_page' );

/**
 * Shows the plugin's admin page.
 *
 * @since 0.0.1
 */
function classicpress_show_admin_page() {
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Switch to ClassicPress', 'switch-to-classicpress' ); ?></h1>

	<?php if ( function_exists( 'classicpress_version' ) ) { ?>
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
					admin_url( 'plugins.php' )
				); ?>
			</p>
		</div>
</div><!-- .wrap -->
		<?php return;
	} ?>

	<?php if ( defined( 'WPCOMSH_VERSION' ) && WPCOMSH_VERSION ) { ?>
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
</div><!-- .wrap -->
		<?php return;
	} ?>

	<?php if ( ! current_user_can( 'update_core' ) ) { ?>
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
</div><!-- .wrap -->
		<?php return;
	} ?>

<?php
	// TODO: pre-flight checks:
	// - PHP >= 5.6
	// - wp_http_supports( array( 'ssl' ) )
	// - FS_METHOD === 'direct'
	// - Not a multisite install
	// - Any others?
?>

	<form
		method="post"
		action="update-core.php?action=do-core-upgrade&migrate=classicpress"
		name="upgrade"
	>
		<?php wp_nonce_field( 'upgrade-core' ); ?>
		<button class="button button-primary button-hero" type="submit" name="upgrade">
			<?php _e(
				'Switch this site to ClassicPress <strong>now</strong>!',
				'switch-to-classicpress'
			); ?>
		</button>
	</form>
	
	<p>After clicking the button above, the migration process will start.</p>
	<p>Without going to deep into technicalities, basically what happens is that all the files <strong>except those in the wp-content folder</strong> of the current WordPress installation, will be replaced with the files of ClassicPress.<br>
		Depending on the server this website is hosted on, this process can take a while.</p>
	<p>Once the process has completed, you will see a familiar screen: the about page ClassicPress where you can read more information.</p>
	<p>We thank you for switching WordPress to ClassicPress! The business-focused CMS. Powerful. Versatile. Predictable.</p>

</div><!-- .wrap -->
<?php
}
