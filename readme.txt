=== Blog Time ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: server, blog, time, clock, datetime, admin, widget, widgets, template tag, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.6
Tested up to: 5.3
Stable tag: 3.6.1

Display the time according to your blog via an admin toolbar widget, a sidebar widget, and/or a template tag.

== Description ==

This plugin adds a dynamic, functional clock to the the admin bar (at top of all admin pages) to show the server time for the blog. The clock automatically updates as time passes, as you would expect of a digital clock.

This plugin also supports a static mode which puts a timestamp string at the top of all admin pages instead of the dynamic clock. This static admin time widget is AJAX-ified so that if you click the timestamp, it updates in place (without a page reload) to show the new current server time.

Also provided is a "Blog Time" widget providing the same functionality as the admin widget, but for your sidebars. You may also utilize the plugin's functionality directly within a theme template via use of the template tag `c2c_blog_time()`.

NOTE: For the front-end widget, if the "Use dynamic clock?" configuration option is unchecked, this plugin generates a timestamp and NOT a clock. The time being displayed is the time of the page load, or if clicked, the time when the widget last retrieved the time. It won't actively increment time on the display. By default the widget displays a dynamic clock that does increment time.

This is most useful to see the server/blog time to judge when a time sensitive post, comment, or action would be dated by the blog (i.e. such as monitoring for when to close comments on a contest post, or just accounting for the server being hosted in a different timezone). Or, when used statically as a timestamp and not a clock, it can indicate/preserve when the page was loaded.

Thanks to <a href="https://momentjs.com/">Moment.js</a> for the JavaScript date handling library.

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/blog-time/) | [Plugin Directory Page](https://wordpress.org/plugins/blog-time/) | [GitHub](https://github.com/coffee2code/blog-time/) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Install via the built-in WordPress plugin installer or download and unzip `blog-time.zip` inside the plugins directory for your site (typically `wp-content/plugins/`)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Optionally use the 'Blog Time' widget or the template tag `c2c_blog_time()` in a theme template file to display the blog's time at the time of the page's rendering.


== Frequently Asked Questions ==

= How do I customize the format of the time string? =

The widget and template tag allow you specify a time format directly. The default value for the time format, and the one used by the display of the blog time in the static admin widget, can be overridden by adding a filter to 'c2c_blog_time_format' and returning the desired time format. See https://php.net/date for more information regarding the time format.

= Why is the time not changing in the sidebar widget? =

The widget's "Use dynamic clock?" configuration setting may not be checked (which it is by default).

= The time matches my computer's time; how do I know this thing is working? =

Your machine may well be synced with the server's clock. One test you can perform is to change the blog's time zone (under Settings -> General). The blog's time will then be set to a different hour, which should then be reflected by the widget.

= How do I go back to having the legacy static timestamp as opposed to the dynamic clock? =

See the Filters section for the `c2c_blog_time_active_clock` filter, which includes an example line of code you'll need to add to your theme.

= How can I show the blog's date instead of the time? =

You could do something like this:

`
/* Insert the following code in the active theme's functions.php or, even better,
in a site-specific plugin. */

// Disable dynamic clock since a clock is not being displayed.
add_filter( 'c2c_blog_time_active_clock', '__return_false' );
// Change the datetime format string used by the plugin.
add_filter( 'c2c_blog_time_format', 'my_blog_time_format' );

/**
 * Returns a custom datetime format string for default use
 * by the Blog Time plugin.
 *
 * See https://php.net/date for more information regarding the time format.
 *
 * @param string $format Original format string (ignored)
 * @return string New format string
 */
function my_blog_time_format( $format ) {
	return 'M d, Y';
}
`

== Screenshots ==

1. A screenshot of the blog time being displayed in the admin toolbar.
2. A screenshot of the 'Blog Time' widget.


== Template Tags ==

The plugin provides one template tag for use in your theme templates, functions.php, or plugins.

= Functions =

* `<?php function c2c_blog_time( $time_format = '', $echo = true ) ?>`
Gets the formatted time for the site.

= Arguments =

* `$time_format` (string)
Optional. PHP-style time format string. See https://php.net/date for more info. Default is '' (which, unless otherwise modified, uses the default time forat: 'g:i A').

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

The 'c2c_blog_time' hook allows you to use an alternative approach to safely invoke `c2c_blog_time()` in such a way that if the plugin were deactivated or deleted, then your calls to the function won't cause errors in your site.

Arguments:

* same as for `c2c_blog_time()`

Example:

Instead of:

`<?php c2c_blog_time(); ?>`

Do:

`<?php echo apply_filters( 'c2c_blog_time', '' ); ?>`

**c2c_blog_time_format (filter)**

The 'c2c_blog_time_format' hook allows you to customize the default format for the blog time. By default this is 'g:i A' (though this may be different if modified by localization).

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

The 'c2c_blog_time_toolbar_widget_for_user' hook allows you to control if the admin toolbar clock widget should be shown, on a per-user basis. By default the admin toolbar clock is shown to everyone who can see the admin toolbar.

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
	return 'boss' == get_current_user()->user_login;
}
add_filter( 'c2c_blog_time_toolbar_widget_for_user', 'restrict_blog_time_widget_appearance' );
`

**c2c_blog_time_active_clock (filter)**

The 'c2c_blog_time_active_clock' hook returns the boolean value indicating if the Javascript-powered dynamic clock introduced in v2.0 should be enabled or if instead the v1.x era behavior of a static timestamp that can be clicked to update the timestamp via AJAX should be enabled. By default the dynamic clock is enabled.

Arguments:

* $allow (boolean): Boolean indicating if the admin widget should be a dynamic clock. Default is true.

Example:

`
// Disable the dynamic clock and use the static timestamp (whcih can be clicked to update the time via AJAX) instead.
add_filter( 'c2c_blog_time_active_clock', '__return_false' );
`


== Changelog ==

= 3.6.1 (2019-12-01) =
* Change: Update unit test install script and bootstrap to use latest WP unit test repo
* Change: Note compatibility through WP 5.3+
* Change: Update copyright date (2020)

= 3.6 (2019-04-02) =
* Change: Use minimized version of Moment.js library to reduce resource usage
* Change: Update Moment.js to v2.24.0
* Change: Change handle for enqueuing Moment.js to allow only one copy of the library being enqueued now that WP packages the library
* Change: Remove `set_js_ajaxurl()` and localize `ajaxurl` alongside other variables instead of outputting it directly
* Change: Initialize plugin on `plugins_loaded` action instead of on load
* Change: Merge `do_init()` into `init()`
* Change: Update widget framework to 013
    * Add `get_config()` as a getter for config array
* Change: Update widget to 008
    * Update to use v013 of the widget framework
* Change: Cast return value of `c2c_blog_time_toolbar_widget_for_user` and `c2c_blog_time_active_clock` filters as boolean
* Change: Ensure widget markup uses double-quotes rather than single-quotes for class attribute values
* Unit tests:
    * Add unit test for `add_widget()`
    * Add unit tests for `show_in_toolbar_for_user()`
    * Add unit test for `c2c_blog_time_active_clock` filter
    * Add unit test for `c2c_blog_time_toolbar_widget_for_user` filter
    * Add unit tests for hooking of various actions
* New: Add CHANGELOG.md file and move all but most recent changelog entries into it
* New: Add inline documentation for hooks
* Change: Use `apply_filters_deprecated()` when using the deprecated filter
* Change: Note compatibility through WP 5.1+
* Change: Update copyright date (2019)
* Change: Update License URI to be HTTPS
* Change: Split paragraph in README.md's "Support" section into two

= 3.5.1 (2018-07-09) =
* Change: Update Moment.js to v2.22.2
* New: Add README.md
* New: Add GitHub link to readme
* New: Add LICENSE file
* Change: Minor whitespace tweaks to unit test bootstrap
* Change: Rename readme.txt section from 'Filters' to 'Hooks'
* Change: Modify formatting of hook name in readme to prevent being uppercased when shown in the Plugin Directory
* Change: Update installation instruction to prefer built-in installer over .zip file
* Change: Note compatibility through WP 4.9+
* Change: Update copyright date (2018)

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/blog-time/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

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
