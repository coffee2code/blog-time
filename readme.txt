=== Blog Time ===
Contributors: coffee2code
Donate link: http://coffee2code.com/donate
Tags: server, blog, time, datetime, widget, widgets, template tag, coffee2code
Requires at least: 2.6
Tested up to: 2.8.1
Stable tag: 1.0
Version: 1.0

Display the time according to your blog via a widget, admin widget, and/or template tag.

== Description ==

Display the time according to your blog via a widget, admin widget, and/or template tag.

This plugin adds a timestamp string to the top of all admin pages to show the server time for the blog.  This admin time widget is AJAX-ified so that if you click the timestamp, it updates in place (without a page reload) to show the new current server time.

Also provided is a "Blog Time" widget (for WP2.8+) providing the same functionality as the admin widget, but for your sidebars.  You may also utilize the plugin's capabilities directly within a theme template via use of the template tag 'blog_time()'.

NOTE: This plugin generates a timestamp and NOT a clock.  The time being displayed is the time of the page load, or if clicked, the time when the widget last retrieved the time.  It does not actively increment time on the display.

This is most useful to see the server/blog time to judge when a time sensitive post, comment, or action would be dated by the blog (i.e. such as monitoring for when to close comments on a contest post, or just accounting for the server being hosted in a different timezone).


== Installation ==

1. Unzip `blog-time.zip` inside the `/wp-content/plugins/` directory for your site
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Optionally use the 'Blog Time' widget (only in WP2.8+), or the template tag `blog_time()` in a theme template file, to display the blog's time at the time of the page's rendering.

== Frequently Asked Questions ==

= How do I customize the format of the time string? =

The widget and template tag allow you specify a time format directly. The default value for the time format, and the one used by the display of the blog time in the admin, can be overridden by adding a filter to 'blog_time_format' and returning the desired time format.  See http://php.net/date for more information regarding the time format.

= Why is the time not changing on the page? =

This plugin does not provide an active clock that continues to update to reflect the current time as time passes.  It merely displays the current time, according to your server, at the time the page was created and sent to your browser.  You can click on the time itself to see if dynamically refresh (without a page reload) to the current time.  Or if the page gets manually reloaded you'll see a new current time.


== Screenshots ==

1. A screenshot of the 'Blog Time' widget.
2. A screenshot of the blog time being displayed in the admin header.


