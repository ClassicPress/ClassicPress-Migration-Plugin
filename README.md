# Switch to ClassicPress

![](assets/banner-772x250.png)

This is a plugin for WordPress that switches your installation to
[ClassicPress](https://www.classicpress.net).

Version 1.5 of this plugin also added a switcher tool for ClassicPress
that can be used to install other versions, test nightly builds and even revert your
site to WordPress.

## The CMS for Creators
ClassicPress takes your website to the next level with the same features and
functionality that you enjoyed with WordPress, but with more attention to the
most common needs of a business website.

**PLEASE NOTE:** ClassicPress is stable software, but it is important to take a
backup before switching a live production site to ClassicPress. Please also
ensure any known conflicting plugins are **deactivated** (see list
[here](https://docs.classicpress.net/installing-classicpress/#plugin-conflicts)).

## What version of WordPress does the plugin work with?

You can always see the latest supported version by going to [Get ClassicPress](https://www.classicpress.net/get-classicpress/#switch-to-classicpress).

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

Plugins that require Blocks might not work well in ClassicPress, you should test
any plugins you plan to use and verify they work correctly.
If you see something unusual it may be a bug with
ClassicPress, and you can get help on
[our support forum](https://forums.classicpress.net/c/support/)
or
[GitHub](https://github.com/ClassicPress).

### How can I use this plugin with an unsupported version of WordPress?

This plugin currently supports migrating from WordPress versions starting at
`4.9.0`.

We update the plugin as quickly as possible when a new version of WordPress
comes out, but we do need to test each new version for compatibility with the
plugin first.

If you would like to migrate from a different version of WordPress **at your
own risk**, you can use the following code in your current theme's
`functions.php` file or a mu-plugin to enable the migration:

`add_filter( 'classicpress_ignore_wp_version', '__return_true' );`

With each new WordPress release, it's very helpful for us to have multiple
reports of whether the migration plugin works with the new version.  You can
use the filter above to test the migration process, and you can tell us about
the results of your testing
[on GitHub](https://github.com/ClassicPress/ClassicPress-Migration-Plugin/issues)
or
[on the support subforum for the migration plugin](https://forums.classicpress.net/c/support/migration-plugin).

### I need help with something else, what should I do?

Like all of ClassicPress, our support is a volunteer effort by the community.
If you need help with something, please let us know in
[our support forum](https://forums.classicpress.net/c/support/)
or
[Zulip chat](https://classicpress.zulipchat.com/register/).

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

### 0.5.0

- Support migrating from WP up to 5.0.2.
- Switch to ClassicPress `1.0.0-beta2`.

### 0.5.1

- Undo a change that was preventing some users from using the plugin.

### 0.5.2

- Support migrating from WP up to 5.0.3.

### 1.0.0

- Support migrating from WP up to 5.1.0.
- Switch to ClassicPress `1.0.0` by default.
- Add an advanced feature that allows switching to any version of WordPress or
  ClassicPress.

### 1.0.1

- Load the supported WordPress version and the target ClassicPress version from
  a remote API endpoint, so that a new release of the migration plugin is not
  required with every new WordPress or ClassicPress release.

### 1.1.0

- Change pre-existing `composer.json` from an error to a warning
  ([details](https://github.com/ClassicPress/ClassicPress-Migration-Plugin/issues/61))
- Minor fix for disorganized/jumbled output in some cases
  ([details](https://github.com/ClassicPress/ClassicPress-Migration-Plugin/issues/59#issuecomment-495459514))

### 1.2.0

- Add translation files and translate the plugin into Russian
  ([details](https://github.com/ClassicPress/ClassicPress-Migration-Plugin/pull/60))
- Fix an issue with incorrect upgrade notices appearing immediately after a
  custom migration
  ([details](https://github.com/ClassicPress/ClassicPress-Migration-Plugin/issues/62))
- Fix an issue with stale WordPress news items appearing in the dashboard after
  migrating to ClassicPress
  ([details](https://github.com/ClassicPress/ClassicPress-Migration-Plugin/issues/67))
  
  
### 1.3.0

- Add Plugin and Theme check sections
  ([details](https://github.com/ClassicPress/ClassicPress-Migration-Plugin/pull/74))
  
### 1.3.1

- Remove stray files from release

  
### 1.4.0

- Direct blocking issues to ClassicPress Forum (#82)
- Add plugin header checking (#83)
- Add theme header comparison check to compatibility logic (#76)


### 1.4.1

- Version Bump


### 1.5.0

- Add smart drop down to Advanced Controls
- Implement PHP version checking based on migration API endpoint
- Adjust checks, notifications and information text as needed for CP v2
- Warn that Migration requires Re-Install (reminder if you go back into the plugin)
- Chat link changed from Slack to Zulip
- Offer CP v1 & v2 current and one version back (unless no previous as with 2 right now)
- Offer WP current, 6.3.x and 4.9.x (4.9 offered only if running PHP 7)
- Offer release version if running CP migration version
- Will not offer re-installation
- Suggest a ClassicPress Default theme

