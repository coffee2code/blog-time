=== Blog Time ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: server, blog, time, clock, datetime, admin, widget, widgets, template tag, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.6
Tested up to: 5.7
Stable tag: 4.0

Display the time according to your blog via an admin toolbar widget, a sidebar widget, and/or a template tag.

== Description ==

This plugin adds a dynamic, functional clock to the admin bar (at top of all admin pages) to show the server time for the blog. The clock automatically updates as time passes, as you would expect of a digital clock.

This plugin also supports a static mode which puts a timestamp string at the top of all admin pages instead of the dynamic clock. This static admin time widget can be clicked to update the time in-place (without a page reload) to show the new current server time.

Also provided is a "Blog Time" widget providing the same functionality as the admin widget, but for your sidebars. You may also utilize the plugin's functionality directly within a theme template via use of the template tag `c2c_blog_time()`.

NOTE: For the front-end widget, if the "Use dynamic clock?" configuration option is unchecked, this plugin generates a timestamp and NOT a clock. The time being displayed is the time of the page load, or if clicked, the time when the widget last retrieved the time. It won't actively increment time on the display. By default the widget displays a dynamic clock that does increment time.

This is most useful to see the server/blog time to judge when a time sensitive post, comment, or action would be dated by the blog (i.e. such as monitoring for when to close comments on a contest post, or just accounting for the server being hosted in a different timezone). Or, when used statically as a timestamp and not a clock, it can indicate/preserve when the page was loaded.

Thanks to <a href="https://momentjs.com/">Moment.js</a> for the JavaScript date handling library.

Links: [Plugin Homepage](https://coffee2code.com/wp-plugins/blog-time/) | [Plugin Directory Page](https://wordpress.org/plugins/blog-time/) | [GitHub](https://github.com/coffee2code/blog-time/) | [Author Homepage](https://coffee2code.com)


== Installation ==

1. Install via the built-in WordPress plugin installer or download and unzip `blog-time.zip` inside the plugins directory for your site (typically `wp-content/plugins/`)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Optional: Customize the time format used for displaying the time via the "Blog Time Format" setting found in Settings -> General.
1. Optional: Use the 'Blog Time' widget or the template tag `c2c_blog_time()` in a theme template file to display the blog's time at the time of the page's rendering.


== Frequently Asked Questions ==

= How do I customize the format of the time string? =

Under the site's general admin settings -- at Settings -> General -- you'll find a "Blog Time Format" setting that accepts any valid PHP time format token. See https://www.php.net/manual/en/datetime.format.php for more information regarding valid time format tokens.

The widget and template tag also allow you to specify a time format directly.

The default value for the time format, and the one used by the display of the blog time in the static admin widget, can be overridden by hooking the ``c2c_blog_time_format`` filter and returning the desired time format. This takes precedence over the setting's value.

= Why is the time not changing in the sidebar widget? =

The widget's "Use dynamic clock?" configuration setting may not be checked (which it is by default). Or JavaScript could be disabled in the browser.

= How do I know if this thing is working if the time matches my computer's time? =

Your machine may well be synced with the server's clock. One test you can perform is to change the blog's time zone (under Settings -> General). The blog's time will then be set to a different hour, which should then be reflected by the widget. Remember to change the time zone back to its proper value!

= Can the clock be enabled/disabled on a per-user basis? =

Yes, but only programmatically at the moment. Check out the docs for the `'c2c_blog_time_toolbar_widget_for_user'` filter for more information and a code example.

= How do I go back to having the legacy static timestamp as opposed to the dynamic clock? =

See the Filters section for the `'c2c_blog_time_active_clock'` filter, which includes an example line of code you'll need to add to your theme.

= How can I show the blog's date instead of the time? =

Via Settings -> General, you can set the "Blog Time Format" value to something like `M d, Y`, which results in a time format like "Jun 21, 2021". See https://www.php.net/manual/en/datetime.format.php for other month, day, and year time format tokens.

= Does this plugin include unit tests? =

Yes.


== Screenshots ==

1. The blog time being displayed in the admin toolbar.
2. The "Blog Time" widget.
3. The "Blog Time Format" setting found on Settings -> General.


== Template Tags ==

The plugin provides one template tag for use in your theme templates, functions.php, or plugins.

= Functions =

* `<?php function c2c_blog_time( $time_format = '', $echo = true ) ?>`
Returns and/or displays the formatted time for the site.

= Arguments =

* `$time_format` (string)
Optional. PHP-style time format string. See https://www.php.net/manual/en/datetime.format.php for more info. Default is '' (which, unless otherwise modified, uses the default time forat: 'g:i A').

* `$echo` (bool)
Optional. Echo the template info? Default is true.

= Examples =

* `<?php // Output the site's current time.
c2c_blog_time();
?>`

* `<?php // Retrieve the value for use in code, so don't display/echo it.
$site_date = c2c_blog_time( 'M d, Y', false );
?>`


== Hooks ==

The plugin exposes four filters for hooking. Code using these filters should ideally be put into a mu-plugin or site-specific plugin (which is beyond the scope of this readme to explain). Less ideally, you could put them in your active theme's functions.php file.

**c2c_blog_time (filter)**

The `'c2c_blog_time'` hook allows you to use an alternative approach to safely invoke `c2c_blog_time()` in such a way that if the plugin were deactivated or deleted, then your calls to the function won't cause errors in your site.

Arguments:

* same as for `c2c_blog_time()`

Example:

Instead of:

`<?php c2c_blog_time(); ?>`

Do:

`<?php echo apply_filters( 'c2c_blog_time', '' ); ?>`

**c2c_blog_time_format (filter)**

The `'c2c_blog_time_format'` hook allows you to customize the default format for the blog time. By default this is 'g:i A' (though this may be different if modified by localization).

Arguments:

* $format (string): The default format for the blog time.

Example:

`
/**
 * Change the default blog time string
 *
 * @param string $format The default time format.
 * @return string
 */
function change_blog_time_format( $format ) {
	return 'b, g:i A';
}
add_filter( 'c2c_blog_time_format', 'change_blog_time_format' );
`

**c2c_blog_time_toolbar_widget_for_user (filter)**

The `c2c_blog_time_toolbar_widget_for_user` hook allows you to control if the admin toolbar clock widget should be shown, on a per-user basis. By default the admin toolbar clock is shown to everyone who can see the admin toolbar.

Arguments:

* $shown (boolean): Whether the admin toolbar clock widget should be shown. Default of true.

Example:

`
/**
 * Only show the admin toolbar clock for the 'boss' user.
 *
 * @param $show bool Status of whether the admin toolbar clock should be shown.
 * @return bool
 */
function restrict_blog_time_widget_appearance( $show ) {
	return 'boss' === get_current_user()->user_login;
}
add_filter( 'c2c_blog_time_toolbar_widget_for_user', 'restrict_blog_time_widget_appearance' );
`

**c2c_blog_time_active_clock (filter)**

The `'c2c_blog_time_active_clock'` hook returns the boolean value indicating if the Javascript-powered dynamic clock introduced in v2.0 should be enabled or if instead the v1.x era behavior of a static timestamp that can be clicked to update the timestamp via AJAX should be enabled. By default the dynamic clock is enabled.

Arguments:

* $allow (boolean): Boolean indicating if the admin widget should be a dynamic clock. Default is true.

Example:

`
// Disable the dynamic clock and use the static timestamp (whcih can be clicked to update the time via AJAX) instead.
add_filter( 'c2c_blog_time_active_clock', '__return_false' );
`


== Changelog ==

= 4.0 (2021-06-19) =
Highlights:

This recommended release introduces a setting for configuring the blog time format, adds support for the 'T' timezone format token, updates the bundled Moment.js library, improves documentation, restructures unit test files, notes compatibility through 5.7+, and incorporates numerous behind-the-scenes tweaks.

Details:

* New: Add setting for configuring time format
    * New: Add "Blog Time Format" setting to the "General Settings" page.
    * New: Add link to setting from plugin's action links
    * New: Show default time format when setting is blank
    * New: Show inline notice below setting when time format is filtered, and indicate that it takes precedence over setting
    * New: Add `initialize_setting()`, `allowed_options()`, `display_option()`, `plugin_action_links()`, and `is_wp_55_or_later()`
    * New: Add new screenshot
* Change: Use default time format if parameter or filter attempts to configure an empty string or non-string value
* Fix: Add support for the 'T' timezone format character to the dynamic clock (support for which was removed from Moment.js awhile ago)
* Change: Update bundled Moment.js to v2.29.1
    * 2.29.1: https://gist.github.com/marwahaha/cc478ba01a1292ab4bd4e861d164d99b
    * 2.29.0: https://gist.github.com/marwahaha/b0111718641a6461800066549957ec14
    * 2.28.0: https://gist.github.com/marwahaha/028fd6c2b2470b2804857cfd63c0e94f
    * 2.27.0: https://gist.github.com/marwahaha/5100c9c2f42019067b1f6cefc333daa7
* Removed: Dropped support for long-deprecated `'blog_time_format'` filter. Use `'c2c_blog_time_format'` instead.
* Change: Switch to use of `wp_add_inline_script()` instead of `wp_localize_script()`
* Change: Add optional `$exit` arg to `report_time()` to allow not exiting after outputting the time
* Change: Improve some inline documentation
* Change: Improve documentation and formatting in readme.txt
* Change: Note compatibility through WP 5.7+
* Change: Update URLs to PHP documentation for datetime formatting
* Change: Update copyright date (2021)
* Unit tests:
    * Change: Restructure unit test directories and files into `tests/` top-level directory
        * Change: Move `bin/` into `tests/`
        * Change: Move `tests/bootstrap.php` into `tests/phpunit/`
        * Change: In bootstrap, store path to plugin file constant so its value can be used within that file and in test file
        * Change: Move `tests/*.php` into `tests/phpunit/tests/`
        * Change: Remove 'test-' prefix from unit test files
        * Change: Rename `phpunit.xml` to `phpunit.xml.dist` per best practices
    * New: Add tests for `enqueue_js()`, `report_time()`, `admin_bar_menu()`
* New: Add a few more possible TODO items

= 3.6.2 (2020-06-11) =
* Change: Update Moment.js to v2.26.0
    * 2.26.0: https://gist.github.com/marwahaha/0725c40740560854a849b096ea7b7590
    * 2.25.3: https://github.com/moment/moment/blob/develop/CHANGELOG.md#2253
    * 2.25.2: https://github.com/moment/moment/blob/develop/CHANGELOG.md#2252
    * 2.25.1: https://github.com/moment/moment/blob/develop/CHANGELOG.md#2251
    * 2.25.0: https://gist.github.com/ichernev/6148e64df2427e455b10ce6a18de1a65
* Change: Remove `is_wp_login()` since it is no longer necessary
* Change: Remove redundant check in `enqueue_js()` that is already performed in `show_in_toolbar_for_user()`
* New: Add TODO.md and move existing TODO list from top of main plugin file into it
* Change: Note compatibility through WP 5.4+
* Change: Update links to coffee2code.com to be HTTPS
* Change: Add an FAQ and tweak docs in readme.txt
* Unit tests:
    * New: Add tests for `display_time()`
    * Change: Add more tests for `get_time_format()`
    * Change: Use HTTPS for link to WP SVN repository in bin script for configuring unit tests (and delete commented-out code)
    * Change: Remove unnecessary unregistering of hooks in `tearDown()`

= 3.6.1 (2019-12-01) =
* Change: Update unit test install script and bootstrap to use latest WP unit test repo
* Change: Note compatibility through WP 5.3+
* Change: Update copyright date (2020)

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/blog-time/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

= 4.0 =
Recommended update: ntroduced setting for configuring blog time format, added support for 'T' timezone format token, updated bundled Moment.js library, improved documentation, restructured unit test files, noted compatibility through 5.7+, and incorporated numerous behind-the-scenes tweaks.

= 3.6.2 =
Minor update: Updated the Moment.js library, added TODO.md file, updated a few URLs to be HTTPS, expanded unit testing, and noted compatibility through WP 5.4+.

= 3.6.1 =
Trivial update: modernized unit tests, noted compatibility through WP 5.3+, and updated copyright date (2020)

= 3.6 =
Recommended update: updated packaged Moment.js to v2.24.0, defer to using Moment.js now enqueued by WP, tweaked plugin initialization, added more unit tests, noted compatibility through WP 5.1+, updated copyright date (2019), more.

= 3.5.1 =
Minor update: updated Moment.js to v2.22.2, noted compatibility through WP 4.9+, added README.md for GitHub, updated copyright date (2018), and other minor changes

= 3.5 =
Recommended update: major refactoring of dynamic clock (which now honors custom time format), added clock dashicon, compatibility is now WP 4.6 through 4.7+ (though it should continue to work for earlier versions of WP), and other improvements.

= 3.4 =
Minor update: bugfix to add proper markup around widget; improved support for localization; verified compatibility through WP 4.4; updated widget framework; updated copyright date (2016)

= 3.3.2 =
Minor bugfix update: Prevented PHP notice under PHP7+ for widget; added more unit tests; updated widget framework to 010; noted compatibility through WP 4.3+

= 3.3.1 =
Minor bugfix release for users running PHP 5.2.x: revert use of a constant only defined in PHP 5.3+. You really should upgrade your PHP or your host if this affects you.

= 3.3 =
Recommended update: added unit tests; minor backend improvements; noted compatibility through WP 4.1+; updated copyright date (2015)

= 3.2 =
Recommended update: incorporated unreleased 3.1; noted compatibility through WP 4.0+; added plugin icon.

= 3.1 =
Recommended update: slight dynamic clock reimplementation that should fix DST off-by-one-hour bug; deprecate 'blog_time_format' filter in favor of 'c2c_blog_time_format'; noted compatibility through WP 3.4+; explicitly stated license

= 3.0 =
Major update: Admin widget now appears in admin toolbar; new dynamic JS clock; fixed JS clock time bug; added support for WP 3.3+; dropped support for versions of WP older than 3.3; internationalization; and a lot more.

= 2.0.1 =
Recommended update: fixed bug relating to showing time in incorrect timezone.

= 2.0 =
Feature update: added dynamic clock capability (new default behavior; admin side only); added more hooks; display admin widget even if JS disabled; noted compatibility with WP 3.2; dropped compatibility with versions of WP older than 3.1; and more.

= 1.3 =
Minor update: noted compatibility through WP 3.2+

= 1.2 =
Recommended update: fixed incompatibility introduced by WP 3.1; updated copyright date; other minor code changes.

= 1.1 =
Recommended minor update. Highlights: added hook for customization; minor fixes and tweaks; renamed blog_time() to c2c_blog_time(); renamed class; verified WP 3.0 compatibility; dropped compatbility with versions of WP older than 2.8.
