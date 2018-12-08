# Switch to ClassicPress

![](assets/banner-772x250.png)

This is a WordPress plugin that switches a WordPress installation to
[ClassicPress](https://www.classicpress.net).

ClassicPress is for businesses seeking a powerful and versatile solution for
their website needs. Built on the firm foundation of WordPress 4.9.x,
ClassicPress takes your website to the next level with the same features and
functionality that you enjoyed with WordPress, but with more attention to the
most common needs of a business website.

ClassicPress is compatible with all plugins and themes that work in WordPress
`4.9.x`, so migration is easy.

**PLEASE NOTE:** ClassicPress is currently in the beta release stage. It is stable,
but it is important to take a backup before switching a live production site to
ClassicPress. Please also ensure any known conflicting plugins are **deactivated**
(see list
[here](https://docs.classicpress.net/installing-classicpress/#plugin-conflicts)).

## Installation

To **install a fresh version of ClassicPress on a new site**, you do not need
this plugin.  See our
[installation instructions](https://docs.classicpress.net/installing-classicpress/)
instead.

To **install ClassicPress on a current WordPress site** (switch a current
WordPress site to ClassicPress):

1. Back up the current site files and database. You can do a manual backup in
   your hosting panel and export the database, or you can use a backup plugin.
2. Download this plugin from the
   [Releases page](https://github.com/ClassicPress/ClassicPress-Migration-Plugin/releases)
   here on GitHub.
3. Install the downloaded zip file from within the WordPress dashboard
   ("Plugins" -> "Add New" -> "Upload Plugin").
4. Activate this plugin ("Switch to ClassicPress") after it is installed.
5. Go to the ClassicPress Migration plugin by clicking the **Switch** link in
   this plugin's table row, or at **Tools -> Switch to ClassicPress**.
6. If all the checks pass, press the **Switch this site to ClassicPress now!**
   button. If not, you'll probably need to upgrade WordPress or PHP.
7. The migration process may take a few minutes depending on your hosting
   provider, so go grab some water or a beverage of your choice 🙂
8. When the process is finished, you should see the ClassicPress About screen.
   At this point it is safe to delete the “Switch to ClassicPress” plugin.
9. You may be prompted to upgrade ClassicPress immediately after switching from
   WordPress.  This is normal, and it is safe to upgrade.

If something is wrong, please let us know in
[our support forum](https://forums.classicpress.net/c/support/migration-plugin).

## Frequently Asked Questions

### Will my current plugins and themes work in ClassicPress?

If your current plugins work in WordPress 4.9.x, they will work in ClassicPress
too.  If you’re seeing something otherwise, that’s probably a bug with
ClassicPress, and we’d appreciate you reporting it on
[our support forum](https://forums.classicpress.net/c/support/)
or
[GitHub](https://github.com/ClassicPress).

### I’m a developer, will I need to learn any new language or framework to develop in ClassicPress?

Not unless you want to!  We have some exciting features planned for version 2
and beyond, but they will all be optional and fully backwards-compatible.

### How can I use this plugin with an unsupported version of WordPress?

This plugin currently supports migrating from WordPress 4.9.8 through 5.0.0, as
well as a few newer development versions.

If you would like to migrate from a different version of WordPress **at your
own risk**, you can use the following code in your current theme's
`functions.php` file or a mu-plugin to enable the migration:

`add_filter( 'classicpress_ignore_wp_version', '__return_true' );`

### I need help with something else, what should I do?

Like all of ClassicPress, our support is a volunteer effort by the community.
If you need help with something, please let us know in
[our support forum](https://forums.classicpress.net/c/support/)
or
[Slack group](https://www.classicpress.net/join-slack/).

## Screenshots

The plugin's admin page with information and the controls to start the migration: <br>
<img width="600" src="assets/screenshot-1.png">

<br>

The plugin's admin page with an error that is blocking the migration: <br>
<img width="600" src="assets/screenshot-2.png">

<br>

The plugin's migration progress page: <br>
<img width="600" src="assets/screenshot-3.png">

<br>

The About ClassicPress screen that appears at the end of the migration: <br>
<img width="600" src="assets/screenshot-4.png">

## Changelog

### 0.1.0

The initial public release of this plugin.  Switches to an early ClassicPress
alpha release.

### 0.2.0

Switches to ClassicPress `1.0.0-beta1` with working auto-updates.  Also
supports migrating multisite WordPress installations to ClassicPress.

### 0.3.0

- Switches to ClassicPress `1.0.0-beta1` without an intermediate upgrade step.
- Changes "Upgrade" back to "Migrate" to avoid confusion with plugin updates.
- Removes the "Switch" link from the plugins page when running ClassicPress.

### 0.4.0

- Support migrating from WP 5.0.
- Add a filter to allow overriding the WP version check.
- Remove unnecessary files and distribute a smaller plugin.
