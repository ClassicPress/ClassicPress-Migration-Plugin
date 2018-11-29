=== Switch to ClassicPress ===

Contributors: classicpress
Donate link: https://www.classicpress.net
Tags: classicpress, migrate, migration, switch
Requires PHP: 5.2.4
Requires at least: 4.9
Tested up to: 4.9
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Switch your WordPress installation to ClassicPress.

== Description ==

ClassicPress: The Business-Focused CMS.

The **Switch to ClassicPress plugin** will switch a WordPress installation to ClassicPress.

ClassicPress is for businesses seeking a powerful and versatile solution for their website needs. Built on the firm foundation of WordPress 4.9.x, ClassicPress takes your website to the next level with the same features and functionality that you enjoyed with WordPress, but with more attention to the most common needs of a business website.

ClassicPress is compatible with all plugins and themes that work in WordPress `4.9.x`, so migration is easy.

**PLEASE NOTE:** As ClassicPress is currently in the beta release stage, we do not recommend that you switch a live production site to ClassicPress.

For more information, see:

* [The official ClassicPress website](https://www.classicpress.net "The official ClassicPress website")
* [This plugin's GitHub page](https://github.com/ClassicPress/ClassicPress-Migration-Plugin "This plugin's GitHub page")

== Installation ==

To **install a fresh version of ClassicPress on a new site**, you do not need this plugin.  See our [installation instructions](https://docs.classicpress.net/installing-classicpress/ "installation instructions") instead.

To **install ClassicPress on a current WordPress site** (switch a current WordPress site to ClassicPress):

1. Back up the current site files and database. You can do a manual backup in your hosting panel and export the database, or you can use a backup plugin.
2. Install this plugin from within the WordPress dashboard.
3. Activate this plugin ("Switch to ClassicPress") after it is installed.
4. Go to the ClassicPress Migration plugin by clicking the **Switch** link in this plugin's table row, or at **Tools -> Switch to ClassicPress**.
5. If all the checks pass, press the **Switch this site to ClassicPress now!** button. If not, you'll probably need to upgrade WordPress or PHP.
6. The migration process may take a few minutes depending on your hosting provider, so go grab some water or a beverage of your choice 🙂
7. When the process is finished, you should see the ClassicPress About screen.
8. You may be prompted to upgrade ClassicPress immediately after switching from WordPress.  This is normal, and it is safe to upgrade.

If something is wrong, please visit us in [our support forum](https://forums.classicpress.net/c/support/ "visit our support forum").

== Frequently Asked Questions ==

= Will my current plugins and themes work in ClassicPress? =

If your current plugins work in WordPress 4.9.x, they will work in ClassicPress too.  If you’re seeing something otherwise, that’s probably a bug with ClassicPress, and we’d appreciate you reporting it in [our support forum](https://forums.classicpress.net/c/support/ "support forum") or [GitHub](https://github.com/ClassicPress "GitHub").

= I’m a developer, will I need to learn any new language or framework to develop in ClassicPress? =

Not unless you want to!  We have some exciting features planned for version 2 and beyond, but they will all be optional and fully backwards-compatible.


= How may I use this plugin with an unsupported version of WordPress? =

If you would like to migrate from a different version of WordPress _AT YOUR OWN RISK_, you can use the following code in your current theme or a mu-plugin to enable the migration:

`add_filter( 'classicpress_ignore_wp_version', '__return_true' );`


= I need help with something else, what should I do? =

Like all of ClassicPress, our support is a volunteer effort by the community.  If you need help with something, please let us know in [our support forum](https://forums.classicpress.net/c/support/ "visit our support forum").

== Screenshots ==

1. The plugin's admin page with information and the controls to start the migration
2. The plugin's admin page with an error that is blocking migration
3. The plugin's migration progress page
4. The About ClassicPress screen that appears at the end of the migration

== Changelog ==

= 0.1.0 =

The initial public release of this plugin.  Switches to an early ClassicPress alpha release.

= 0.2.0 =

Switches to ClassicPress `1.0.0-beta1` with working auto-updates.  Also supports migrating multisite WordPress installations to ClassicPress.

= 0.3.0 =

- Switches to ClassicPress `1.0.0-beta1` without an intermediate upgrade step.
- Changes "Upgrade" back to "Migrate" to avoid confusion with plugin updates.
- Removes the "Switch" link from the plugins page when running ClassicPress.

== Upgrade Notice ==

= 0.1.0 =

The initial public release of this plugin.  Switches to an early ClassicPress alpha release.

= 0.2.0 =

Switches to ClassicPress `1.0.0-beta1` with working auto-updates.  Also supports migrating multisite WordPress installations to ClassicPress.

= 0.3.0 =

- Switches to ClassicPress `1.0.0-beta1` without an intermediate upgrade step.
- Changes "Upgrade" back to "Migrate" to avoid confusion with plugin updates.
- Removes the "Switch" link from the plugins page when running ClassicPress.
