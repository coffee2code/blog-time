=== Blog Time ===
Contributors: coffee2code
Donate link: http://coffee2code.com/donate
Tags: server, blog, time, datetime, admin, widget, widgets, template tag, coffee2code
Requires at least: 2.8
Tested up to: 3.1
Stable tag: 1.2
Version: 1.2

Display the time according to your blog via a widget, admin widget, and/or template tag.

== Description ==

Display the time according to your blog via a widget, admin widget, and/or template tag.

This plugin adds a timestamp string to the top of all admin pages to show the server time for the blog.  This admin time widget is AJAX-ified so that if you click the timestamp, it updates in place (without a page reload) to show the new current server time.

Also provided is a "Blog Time" widget providing the same functionality as the admin widget, but for your sidebars.  You may also utilize the plugin's capabilities directly within a theme template via use of the template tag 'c2c_blog_time()'.

NOTE: This plugin generates a timestamp and NOT a clock.  The time being displayed is the time of the page load, or if clicked, the time when the widget last retrieved the time.  It does not actively increment time on the display.

This is most useful to see the server/blog time to judge when a time sensitive post, comment, or action would be dated by the blog (i.e. such as monitoring for when to close comments on a contest post, or just accounting for the server being hosted in a different timezone).

Links: [Plugin Homepage]:(http://coffee2code.com/wp-plugins/blog-time/) | [Author Homepage]:(http://coffee2code.com)


== Installation ==

1. Unzip `blog-time.zip` inside the `/wp-content/plugins/` directory for your site (or install via the built-in WordPress plugin installer)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Optionally use the 'Blog Time' widget or the template tag `c2c_blog_time()` in a theme template file, to display the blog's time at the time of the page's rendering.


== Frequently Asked Questions ==

= How do I customize the format of the time string? =

The widget and template tag allow you specify a time format directly. The default value for the time format, and the one used by the display of the blog time in the admin, can be overridden by adding a filter to 'blog_time_format' and returning the desired time format.  See http://php.net/date for more information regarding the time format.

= Why is the time not changing on the page? =

This plugin does not provide an active clock that continues to update to reflect the current time as time passes.  It merely displays the current time, according to your server, at the time the page was created and sent to your browser.  You can click on the time itself to see if dynamically refresh (without a page reload) to the current time.  Or if the page gets manually reloaded you'll see a new current time.


== Screenshots ==

1. A screenshot of the 'Blog Time' widget.
2. A screenshot of the blog time being displayed in the admin header.


== Filters ==

The plugin exposes two filters for hooking.  Typically, customizations utilizing these hooks would be put into your active theme's functions.php file, or used by another plugin.

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

`// Change the default blog time string
add_filter( 'blog_time_format', 'change_blog_time_format' );
function change_blog_time_format( $format ) {
	return 'b, g:i A';
}`
`


== Changelog ==

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

= 1.2 =
Recommended update: fixed incompatibility introduced by WP 3.1; updated copyright date; other minor code changes.

= 1.1 =
Recommended minor update. Highlights: added hook for customization; minor fixes and tweaks; renamed blog_time() to c2c_blog_time(); renamed class; verified WP 3.0 compatibility; dropped compatbility with versions of WP older than 2.8.