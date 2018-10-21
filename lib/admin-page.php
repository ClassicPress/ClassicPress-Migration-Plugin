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
.cp-migration-info {
	max-width: 600px;
}
</style>
<?php
}
add_action( 'admin_head-plugins.php', 'classicpress_print_admin_styles' );
add_action( 'admin_head-tools_page_switch-to-classicpress', 'classicpress_print_admin_styles' );

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

	<p class="cp-migration-info">
		<?php _e( 'First things first, just in case something does not go as planned, <strong class="cp-emphasis">please make a backup of your site files and database</strong>.', 'switch-to-classicpress' ); ?>
	</p>
	<p class="cp-migration-info">
		<?php _e( 'After clicking the button below, the migration process will start.', 'switch-to-classicpress' ); ?>
	</p>

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
		<?php _e( 'We thank you for switching WordPress to ClassicPress! The business-focused CMS. Powerful. Versatile. Predictable.', 'switch-to-classicpress' ); ?>
	</p>

</div><!-- .wrap -->
<?php
}
