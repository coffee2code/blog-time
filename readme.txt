=== Blog Time ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: server, blog, time, clock, datetime, admin, widget, widgets, template tag, coffee2code
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 3.3
Tested up to: 4.4
Stable tag: 3.4

Display the time according to your blog via an admin toolbar widget, a sidebar widget, and/or a template tag.

== Description ==

This plugin adds a dynamic, functional clock to the the admin bar (at top of all admin pages) to show the server time for the blog. The clock automatically updates as time passes, as you would expect of a digital clock.

This plugin also supports a static mode which puts a timestamp string at the top of all admin pages instead of the dynamic clock. This static admin time widget is AJAX-ified so that if you click the timestamp, it updates in place (without a page reload) to show the new current server time.

Also provided is a "Blog Time" widget providing the same functionality as the admin widget, but for your sidebars. You may also utilize the plugin's functionality directly within a theme template via use of the template tag `c2c_blog_time()`.

NOTE: For the front-end widget, if the "Use dynamic clock?" configuration option is unchecked, this plugin generates a timestamp and NOT a clock. The time being displayed is the time of the page load, or if clicked, the time when the widget last retrieved the time. It won't actively increment time on the display. By default the widget displays a dynamic clock that does increment time.

This is most useful to see the server/blog time to judge when a time sensitive post, comment, or action would be dated by the blog (i.e. such as monitoring for when to close comments on a contest post, or just accounting for the server being hosted in a different timezone). Or, when used statically as a timestamp and not a clock, it can indicate/preserve when the page was loaded.

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/blog-time/) | [Plugin Directory Page](https://wordpress.org/plugins/blog-time/) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Unzip `blog-time.zip` inside the `/wp-content/plugins/` directory for your site (or install via the built-in WordPress plugin installer)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Optionally use the 'Blog Time' widget or the template tag `c2c_blog_time()` in a theme template file to display the blog's time at the time of the page's rendering.


== Frequently Asked Questions ==

= How do I customize the format of the time string? =

The widget and template tag allow you specify a time format directly. The default value for the time format, and the one used by the display of the blog time in the static admin widget, can be overridden by adding a filter to 'c2c_blog_time_format' and returning the desired time format. See http://php.net/date for more information regarding the time format.

NOTE: The time string is currently only configurable for the static clock and the widget, not the dynamic admin toolbar clock enabled by default.

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
 * See http://php.net/date for more information regarding the time format.
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


== Filters ==

The plugin exposes four filters for hooking. Typically, customizations utilizing these hooks would be put into your active theme's functions.php file, or used by another plugin.

= c2c_blog_time (filter) =

The 'c2c_blog_time' hook allows you to use an alternative approach to safely invoke `c2c_blog_time()` in such a way that if the plugin were deactivated or deleted, then your calls to the function won't cause errors in your site.

Arguments:

* same as for `c2c_blog_time()`

Example:

Instead of:

`<?php c2c_blog_time(); ?>`

Do:

`<?php echo apply_filters( 'c2c_blog_time', '' ); ?>`

= c2c_blog_time_format (filter) =

The 'c2c_blog_time_format' hook allows you to customize the default format for the blog time. By default this is 'g:i A' (though this may be different if modified by localization). *NOTE: This currently only applies to the static clock and not the dynamic clock.*

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

= c2c_blog_time_toolbar_widget_for_user (filter) =

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

= c2c_blog_time_active_clock (filter) =

The 'c2c_blog_time_active_clock' hook returns the boolean value indicating if the Javascript-powered dynamic clock introduced in v2.0 should be enabled or if instead the v1.x era behavior of a static timestamp that can be clicked to update the timestamp via AJAX should be enabled. By default the dynamic clock is enabled.

Arguments:

* $allow (boolean): Boolean indicating if the admin widget should be a dynamic clock. Default is true.

Example:

`
// Disable the dynamic clock and use the static timestamp (whcih can be clicked to update the time via AJAX) instead.
add_filter( 'c2c_blog_time_active_clock', '__return_false' );
`


== Changelog ==

= 3.4 (2016-01-15) =
* Bugfix: Properly output markup around widget.
* Change: Update widget framework to 011:
    * Change class name to c2c_BlogTime_Widget_011 to be plugin-specific.
    * Set textdomain using a string instead of a variable.
    * Remove `load_textdomain()` and textdomain class variable.
    * Formatting improvements to inline docs.
* Change: Add support for language packs:
    * Set textdomain using a string instead of a variable.
    * Don't load textdomain from file.
    * Remove .pot file and /lang subdirectory.
* Change: Explicitly declare methods in unit tests as public or protected.
* Change: Minor improvements to inline docs and test docs.
* Add: Create empty index.php to prevent files from being listed if web server has enabled directory listings.
* Change: Note compatibility through WP 4.4+.
* Change: Update copyright date (2016).

= 3.3.2 (2015-08-22) =
* Change: Discontinue use of PHP4-style constructor invocation of WP_Widget to prevent PHP notices in PHP7.
* Use `DIRECTORY_SEPARATOR` in path for include files instead of hard-coded `/`.
* Change: Update widget framework to version 010.
* Change: Minor widget file header reformatting.
* Change: Update widget to version 005.
* Change: Note compatibility through WP 4.3+.
* New: Add unit tests for widget class versions.
* New: Add `c2c_BlogTimeWidget::version()` to get version of the widget class.

= 3.3.1 (2015-03-12) =
* Revert back to using `dirname(__FILE__)`; __DIR__ is only PHP 5.3+

= 3.3 (2015-02-16) =
* Add unit tests
* Explicitly declare all class methods static
* Output dynamic clock time components more efficiently
* Use __DIR__ instead of `dirname(__FILE__)`
* Various inline code documentation improvements (spacing, punctuation)
* Use phpDoc formatting for example code in readme
* Note compatibility through WP 4.1+
* Update copyright date (2015)
* Regenerate .pot

= 3.2 (2014-10-15) =
* Update widget to use C2C_Widget base class (v008)
* Remove appending of random number to plugin JS version when enqueuing
* Add check to prevent execution of code if file is directly accessed
* Minor plugin header reformatting
* Minor code reformatting (spacing, bracing)
* Change documentation links to wp.org to be https
* Note compatibility through WP 4.0+
* Update copyright date (2014)
* Add assets directory to plugin repository checkout
* Add banner image
* Add plugin icon
* Move screenshots into repo's assets directory
* Update screenshots
* Update donate link
* Regenerate .pot
* Update some out-of-date documentation

= 3.1 (not publicly released) =
* Slight dynamic clock reimplementation that should fix DST off-by-one-hour bug
* Add filter 'c2c_blog_time_format'
* Deprecate support for 'blog_time_format' filter (use 'c2c_blog_time_format' instead)
* Use string instead of variable to specify translation textdomain
* Re-license as GPLv2 or later (from X11)
* Add 'License' and 'License URI' header tags to readme.txt and plugin file
* Remove ending PHP close tag
* Note compatibility through WP 3.4+

= 3.0 =
* Move admin widget into admin toolbar
* Discontinue inclusion of jqClock jQuery plugin and instead use custom developed code
* Fix bug with incorrect hour being shown for dynamic clock
* Change JavaScript to detect if 'c2c-blog-time-dynamic' class is present on span to determine if clock should be dynamic
* Add support for dynamic clock in sidebar widget
* Support multiple occurrences of widget (admin and/or sidebar) on single page
* Update widget to use C2C_Widget base class (v005)
* Clear link title attribute when using dynamic clock since instructions for clicking to update don't apply
* Enqueue JavaScript (which has been moved into new js/blog-time.js)
* Enqueue JavaScript on front-end also if admin toolbar or widget are being shown
* Add filter 'c2c_blog_time_toolbar_widget_for_user' for per-user control of admin toolbar widget (default is true)
* Remove support for 'c2c_blog_time_js_insert_action' filter
* Remove support for 'c2c_blog_time_target' filter
* Add version() to return plugin version
* Add admin_bar_menu(), is_wp_login(), show_in_toolbar_for_user()
* Remove load_textdomain(), add_js()
* Remove support for deprecated blog_time()
* Code reformatting (function reorganization)
* Note compatibility through WP 3.3+
* Drop support for versions of WP older than 3.3
* Create 'lang' subdirectory and move .pot file into it
* Regenerate .pot
* Change description
* Add 'Domain Path' directive to top of main plugin file
* Add link to plugin directory page to readme.txt
* Update screenshots (now based on WP 3.3)
* Update copyright date (2012)

= 2.0.1 =
* Fix bug relating to showing time in incorrect timezone

= 2.0 =
* Integrate jqClock to provide dynamic clock in admin section
* Add filter 'c2c_blog_time_js_insert_action' to allow overriding default JS insertion method used to insert admin widget onto page
* Add filter 'c2c_blog_time_target' to allow overriding target relative to which the JS insertion of the admin widget is performed
* Add filter 'c2c_blog_time_active_clock' to allow disabling dynamic Javascript clock and use v1.x era static timestamp (that can be click to update time)
* Display admin blog time widget even if JS is disabled
* Create add_js() and use it to output JS (code moved from add_widget())
* Hook add_widget() to 'in_admin_header' action
* Add additional CSS rules to maintain appearance in latest WP
* Remove unused JS variable wpcontenturl
* Note compatibility through WP 3.2+
* Drop support for versions of WP older than 3.1
* Tiny code formatting change (spacing)
* Documentation updates to reflect recent changes
* Add Credits section to readme.txt
* Fix plugin homepage and author links in description in readme.txt

= 1.2 =
* Fix UI compatibility issue introduced by WP 3.1
* Use class variable to store ID for the admin widget (and change it from previous value)
* Switch from object instantiation to direct class invocation
* Explicitly declare all functions public and class variables private
* Move template tag functions and class initialization call within primary if(class_exists())
* Output CSS in a single line
* Rename widget class from 'BlogTimeWidget' to 'c2c_BlogTimeWidget'
* Note compatibility through WP 3.1+
* Update copyright date (2011)

= 1.1 =
* Rename blog_time() template tag to c2c_blog_time()
* Deprecate blog_time() template tag, but retain it for backwards compatibility
* Add hook 'c2c_blog_time' (filter) to respond to the function of the same name so that users can use the apply_filters() notation for invoking template tag
* Move most of the code in constructor into init()
* Invoke add_widget() against 'admin_print_footer_scripts' hook rather than 'admin_footer'
* Fix PHP warnings/notices in widget code
* Full support for localization
* Rename class from 'BlogTime' to 'c2c_BlogTime'
* Note compatibility with WP 2.9+, 3.0+
* Drop support for versions of WP older than 2.8
* Assign object instance to global variable, $c2c_blog_time as global, to allow for external manipulation
* Remove docs from top of plugin file (all that and more are in readme.txt)
* Minor code reformatting (spacing)
* Add PHPDoc documentation
* Add package info
* Remove trailing whitespace in header docs
* Update copyright date
* Add Changelog, Filters, and Upgrade Notice sections to readme.txt
* Add .pot file

= 1.0 =
* Initial release


== Upgrade Notice ==

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
