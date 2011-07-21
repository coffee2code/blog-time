=== Blog Time ===
Contributors: coffee2code
Donate link: http://coffee2code.com/donate
Tags: server, blog, time, datetime, admin, widget, widgets, template tag, coffee2code
Requires at least: 3.1
Tested up to: 3.2
Stable tag: 2.0
Version: 2.0

Display the time according to your blog via a widget, admin widget, and/or template tag.

== Description ==

Display the time according to your blog via a widget, admin widget, and/or template tag.

This plugin adds a dynamic functional clock to the top of all admin pages to show the server time for the blog.  The clock automatically updates as time passes, as you would expect of a digital clock.

This plugin also supports a static mode which puts a timestamp string at the top of all admin pages instead of the dynamic clock.  This static admin time widget is AJAX-ified so that if you click the timestamp, it updates in place (without a page reload) to show the new current server time.

Also provided is a "Blog Time" widget providing the same functionality as the admin widget, but for your sidebars.  You may also utilize the plugin's capabilities directly within a theme template via use of the template tag 'c2c_blog_time()'.

NOTE: For the front-end widget, this plugin generates a timestamp and NOT a clock.  The time being displayed is the time of the page load, or if clicked, the time when the widget last retrieved the time.  It does not actively increment time on the display.  A static version is also available for the admin widget, though by default the admin widget displays a dynamic clock.

This is most useful to see the server/blog time to judge when a time sensitive post, comment, or action would be dated by the blog (i.e. such as monitoring for when to close comments on a contest post, or just accounting for the server being hosted in a different timezone).

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/blog-time/) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Unzip `blog-time.zip` inside the `/wp-content/plugins/` directory for your site (or install via the built-in WordPress plugin installer)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Optionally use the 'Blog Time' widget or the template tag `c2c_blog_time()` in a theme template file to display the blog's time at the time of the page's rendering.


== Frequently Asked Questions ==

= How do I customize the format of the time string? =

The widget and template tag allow you specify a time format directly. The default value for the time format, and the one used by the display of the blog time in the static admin widget, can be overridden by adding a filter to 'blog_time_format' and returning the desired time format.  See http://php.net/date for more information regarding the time format.

NOTE: The time string is currently only configurable for the static clock and the widget, not the dynamic clock enabled by default in the admin.

= Why is the time not changing in the sidebar widget? =

This plugin does not (yet) provide an active clock that continues to update to reflect the current time as time passes for the sidebar widget.  It merely displays the current time, according to your server, at the time the page was created and sent to your browser.  You can click on the time itself to see it dynamically refresh (without a page reload) to the current time.  Or if the page gets manually reloaded you'll see a new current time.  The dynamic clock is currently only available to the admin widget.

= The time matches my computer's time; how do I know this thing is working? =

Your machine may well be synced with the server's clock. One test you can perform is to change the blog's time zone (under Settings -> General). The blog's time will then be set to a different hour, which should then be reflected by the widget.

= How do I go back to having the static timestamp as opposed to the dynamic clock? =

See the Filters section for the `c2c_blog_time_active_clock` filter, which includes an example line of code you'll need to add to your theme.


== Screenshots ==

1. A screenshot of the 'Blog Time' widget.
2. A screenshot of the blog time being displayed in the admin header.


== Filters ==

The plugin exposes five filters for hooking.  Typically, customizations utilizing these hooks would be put into your active theme's functions.php file, or used by another plugin.

= c2c_blog_time (filter) =

The 'c2c_blog_time' hook allows you to use an alternative approach to safely invoke `c2c_blog_time()` in such a way that if the plugin were deactivated or deleted, then your calls to the function won't cause errors in your site.

Arguments:

* same as for `c2c_blog_time()`

Example:

Instead of:

    `<?php c2c_blog_time(); ?>`

Do:

    `<?php echo apply_filters( 'c2c_blog_time', '' ); ?>`

= blog_time_format (filter) =

The 'blog_time_format' hook allows you to customize the default format for the blog time.  By default this is 'g:i A' (though this may be different if modified by localization).

Arguments:

* $format (string): The default format for the blog time.

Example:

`
// Change the default blog time string
add_filter( 'blog_time_format', 'change_blog_time_format' );
function change_blog_time_format( $format ) {
	return 'b, g:i A';
}
`

= c2c_blog_time_js_insert_action (filter) =

The 'c2c_blog_time_js_insert_action' hook allows you to customize the default jQuery manipulation function used to insert the plugin's admin widget into the admin interface.  The insert method is performed relative to the DOM target (as filtered via 'c2c_blog_time_target').  By default this is 'insertBefore'.

Arguments:

* $format (string): The default JS insert action for the blog time widget.

Example:

`
// Change the location of the blog time widget to *before* the "Howdy" in the upper-right of admin pages
add_filter( 'c2c_blog_time_js_insert_action', 'change_blog_time_insert_action' );
function change_blog_time_insert_action( $format ) {
	return 'insertAfter';
}
`

= c2c_blog_time_target (filter) =

The 'c2c_blog_time_target' hook allows you to customize the DOM target relative to which the insertion action (as filtered via 'c2c_blog_time_js_insert_action') is performed.  By default this is '#user_info'. NOTE: More than likely you'll also probably want to override the CSS for '#blog-time-admin-widget' to at least change the float (see example).

Arguments:

* $target (string): The DOM target relative to which insertion action is performed for the blog time widget.

Example:

`
// Insert the blog widget before the WP logo
add_filter( 'c2c_blog_time_target', 'change_blog_time_target' );
function change_blog_time_target( $target ) {
	return '#site-heading';
}
// Need to override plugin's default CSS for the widget since it has float:right by default
add_action( 'admin_head', 'change_blog_time_admin_css', 20 );
function change_blog_time_admin_css() {
	echo '<style type="text/css">#blog-time-admin-widget { float:left; }</style>';
}
`

= c2c_blog_time_active_clock (filter) =

The 'c2c_blog_time_active_clock' hook returns the boolean value indicating if the Javascript-powered dynamic clock introduced in v2.0 should be enabled or if instead the v1.x era behavior of a static timestamp that can be clicked to update the timestamp via AJAX should be enabled.  By default the dynamic clock is enabled.

Arguments:

* $allow (boolean): Boolean indicating if the admin widget should be a dynamic clock. Default is true.

Example:

`
// Disable the dynamic clock and use the static timestamp (whcih can be clicked to update the time via AJAX) instead.
add_filter( 'c2c_blog_time_active_clock', '__return_false' );
`


== Credits ==

Special thanks to John R. D'Orazio (Lwangaman) for [jqClock](https://github.com/Lwangaman/jQuery-Clock-Plugin) which was used to make the blog time timestamp an actual functioning clock.


== Changelog ==

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

= 2.0 =
Feature update: added dynamic clock capability (new default behavior; admin side only); added more hooks; display admin widget even if JS disabled; noted compatibility with WP 3.2; dropped compatibility with versions of WP older than 3.1; and more.

= 1.3 =
Minor update: noted compatibility through WP 3.2+

= 1.2 =
Recommended update: fixed incompatibility introduced by WP 3.1; updated copyright date; other minor code changes.

= 1.1 =
Recommended minor update. Highlights: added hook for customization; minor fixes and tweaks; renamed blog_time() to c2c_blog_time(); renamed class; verified WP 3.0 compatibility; dropped compatbility with versions of WP older than 2.8.