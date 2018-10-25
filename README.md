# Switch to ClassicPress

![](assets/banner-772x250.png)

This is a WordPress plugin that switches a WordPress installation to
[ClassicPress](https://www.classicpress.net).

ClassicPress is for businesses seeking a powerful and versatile solution for
their website needs. Built on the firm foundation of WordPress 4.9.x,
ClassicPress takes your website to the next level with the same features and
functionality that you enjoyed with WordPress, but with more attention to the
most common needs of a business website.

ClassicPress is compatible with all plugins that work in WordPress 4.9.x, so
migration is easy.

The ClassicPress `1.0.0-alpha1` release (codename "Sunrise") is ready to be
tested by you, the users!

**PLEASE NOTE:** As ClassicPress is currently in Alpha testing stage, we do not
recommend that you switch a live production site to ClassicPress.

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

If something is wrong, please
[join our Slack group](https://www.classicpress.net/join-slack/)
and ask in the
[**#support**](https://classicpress.slack.com/messages/support/)
channel.

## Frequently Asked Questions

### Will my current plugins and themes work in ClassicPress?

If your current plugins work in WordPress 4.9.x, they will work in ClassicPress
too.  If you’re seeing something otherwise, that’s probably a bug with
ClassicPress, and we’d appreciate you reporting it on
[Slack](https://www.classicpress.net/join-slack/)
or
[GitHub](https://github.com/ClassicPress).

### I’m a developer, will I need to learn any new language or framework to develop in ClassicPress?

Not unless you want to!  We have some exciting features planned for version 2
and beyond, but they will all be optional and fully backwards-compatible.

### I need help with something else, what should I do?

Like all of ClassicPress, our support is a volunteer effort by the community.
If you need help with something, please
[join our Slack group](https://www.classicpress.net/join-slack/)
and ask in the
[**#support**](https://classicpress.slack.com/messages/support/)
channel.

## Screenshots

![](assets/screenshot-1.png)
_The plugin's admin page with information and the controls to start the migration_
<br><br>
![](assets/screenshot-2.png)
_The plugin's admin page with an error that is blocking migration_
<br><br>
![](assets/screenshot-3.png)
_The plugin's migration progress page_
<br><br>
![](assets/screenshot-4.png)
_The About ClassicPress screen that appears at the end of the migration_

## Changelog

### 0.1.0

The initial public release of this plugin.  Switches to an early ClassicPress alpha release.
