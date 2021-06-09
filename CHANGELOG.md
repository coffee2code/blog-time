# Changelog

## _(in-progress)_
* New: Add setting for configuring time format
    * New: Add "Blog Time Format" setting to the "General Settings" page.
    * New: Add link to setting from plugin's action links
    * New: Add `initialize_setting()`, `allowed_options()`, `display_option()`, and `plugin_action_links()`
* Change: Use default time format if parameter or filter attempts to configure an empty string or non-string value
* Fix: Add support for the 'T' timezone format character to the dynamic clock (support for which was removed from Moment.js awhile ago)
* Change: Note compatibility through WP 5.7+
* Change: Update URLs to PHP documentation for datetime formatting
* Change: Update copyright date (2021)

## 3.6.2 _(2020-06-11)_

### Highlights:

This minor release updates the Moment.js library, adds a TODO.md file, updates a few URLs to be HTTPS, expands unit testing, and notes compatibility through WP 5.4+.

### Details:

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

## 3.6.1 _(2019-12-01)_
* Change: Update unit test install script and bootstrap to use latest WP unit test repo
* Change: Note compatibility through WP 5.3+
* Change: Update copyright date (2020)

## 3.6 _(2019-04-02)_
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

## 3.5.1 _(2018-07-09)_
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

## 3.5 _(2017-01-21)_
* New: Add ability for dynamic clock to honor custom time format.
    * Package Moment.js (v2.17.1), a datatime library for JS.
    * Add `map_php_time_format_to_momentjs()` to remap PHP time format tokens to Moment.js time format tokens.
    * Simplify blog-time.js to defer time handling to Moment.js.
    * Make time format and UTC offset available for JS usage.
    * Output time format when customized (such as via widget or template tag) in markup for use by JS.
    * Remove documentation indicating dynamic clock does not honor custom time format.
* New: Add clock dashicon before adminbar time widget.
* Change: Determine the dynamic clock update time interval based on time format string to reduce update frequency when possible.
* Change: Move CSS into enqueued file.
    * CSS is now in file `css/blog-time.css`.
    * Remove `add_css()`.
* Change: Extract time format related code out of `display_time()` and into new `get_time_format()`.
* Chnage: Add context to handlers for time format and display.
    * Add `$context` arg to `get_format_time()` and `display_time()`.
    * Add `$context` arg to `c2c_blog_time_format` filter.
    * Add 'context' as configuration parameter for `add_widget()`.
    * Set context everywhere context can be set.
* Change: Update widget framework to 012, bumping c2c_BlogTimeWidget to 007.
* Bugfix: Add '.ab-item' class to time link so it gets proper styles in adminbar when JS is disabled.
* Change: Prevent dynamic adminbar clock from getting hover styling as if it were a link.
* Change: Ensure linked timestamp in static widget does not abide by a:visited styling.
* Change: Widget: Move `register_c2c_BlogTimeWidget()` to 'c2c_BlogTimeWidget::register_widget()`.
* Change: Sanitize the translated string used in an attribute.
* Change: Enable more error output for unit tests.
* Change: Default `WP_TESTS_DIR` to `/tmp/wordpress-tests-lib` rather than erroring out if not defined via environment variable.
* Change: Note compatibility through WP 4.7+.
* Change: Remove support for WordPress older than 4.6 (should still work for earlier versions though).
* Change: Add 'Template Tags' section to FAQ.
* Change: Minor inline documentation improvements and reformatting.
* Change: Update copyright date (2017).
* Change: Update both screenshots.

## 3.4 _(2016-01-15)_
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

## 3.3.2 _(2015-08-22)_
* Change: Discontinue use of PHP4-style constructor invocation of WP_Widget to prevent PHP notices in PHP7.
* Use `DIRECTORY_SEPARATOR` in path for include files instead of hard-coded `/`.
* Change: Update widget framework to version 010.
* Change: Minor widget file header reformatting.
* Change: Update widget to version 005.
* Change: Note compatibility through WP 4.3+.
* New: Add unit tests for widget class versions.
* New: Add `c2c_BlogTimeWidget::version()` to get version of the widget class.

## 3.3.1 _(2015-03-12)_
* Revert back to using `dirname(__FILE__)`; `__DIR__` is only PHP 5.3+

## 3.3 _(2015-02-16)_
* Add unit tests
* Explicitly declare all class methods static
* Output dynamic clock time components more efficiently
* Use `__DIR__` instead of `dirname(__FILE__)`
* Various inline code documentation improvements (spacing, punctuation)
* Use phpDoc formatting for example code in readme
* Note compatibility through WP 4.1+
* Update copyright date (2015)
* Regenerate .pot

## 3.2 _(2014-10-15)_
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

## 3.1 _(not publicly released)_
* Slight dynamic clock reimplementation that should fix DST off-by-one-hour bug
* Add filter `c2c_blog_time_format`
* Deprecate support for `blog_time_format` filter (use `c2c_blog_time_format` instead)
* Use string instead of variable to specify translation textdomain
* Re-license as GPLv2 or later (from X11)
* Add 'License' and 'License URI' header tags to readme.txt and plugin file
* Remove ending PHP close tag
* Note compatibility through WP 3.4+

## 3.0
* Move admin widget into admin toolbar
* Discontinue inclusion of jqClock jQuery plugin and instead use custom developed code
* Fix bug with incorrect hour being shown for dynamic clock
* Change JavaScript to detect if `c2c-blog-time-dynamic` class is present on span to determine if clock should be dynamic
* Add support for dynamic clock in sidebar widget
* Support multiple occurrences of widget (admin and/or sidebar) on single page
* Update widget to use C2C_Widget base class (v005)
* Clear link title attribute when using dynamic clock since instructions for clicking to update don't apply
* Enqueue JavaScript (which has been moved into new `js/blog-time.js`)
* Enqueue JavaScript on front-end also if admin toolbar or widget are being shown
* Add filter `c2c_blog_time_toolbar_widget_for_user` for per-user control of admin toolbar widget (default is true)
* Remove support for `c2c_blog_time_js_insert_action` filter
* Remove support for `c2c_blog_time_target` filter
* Add `version()` to return plugin version
* Add `admin_bar_menu()`, `is_wp_login()`, `show_in_toolbar_for_user()`
* Remove `load_textdomain()`, `add_js()`
* Remove support for deprecated `blog_time()`
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

## 2.0.1
* Fix bug relating to showing time in incorrect timezone

## 2.0
* Integrate jqClock to provide dynamic clock in admin section
* Add filter `c2c_blog_time_js_insert_action` to allow overriding default JS insertion method used to insert admin widget onto page
* Add filter `c2c_blog_time_target` to allow overriding target relative to which the JS insertion of the admin widget is performed
* Add filter `c2c_blog_time_active_clock` to allow disabling dynamic Javascript clock and use v1.x era static timestamp (that can be click to update time)
* Display admin blog time widget even if JS is disabled
* Create `add_js()` and use it to output JS (code moved from `add_widget()`)
* Hook `add_widget()` to `in_admin_header` action
* Add additional CSS rules to maintain appearance in latest WP
* Remove unused JS variable wpcontenturl
* Note compatibility through WP 3.2+
* Drop support for versions of WP older than 3.1
* Tiny code formatting change (spacing)
* Documentation updates to reflect recent changes
* Add Credits section to readme.txt
* Fix plugin homepage and author links in description in readme.txt

## 1.2
* Fix UI compatibility issue introduced by WP 3.1
* Use class variable to store ID for the admin widget (and change it from previous value)
* Switch from object instantiation to direct class invocation
* Explicitly declare all functions public and class variables private
* Move template tag functions and class initialization call within primary if(class_exists())
* Output CSS in a single line
* Rename widget class from `BlogTimeWidget` to `c2c_BlogTimeWidget`
* Note compatibility through WP 3.1+
* Update copyright date (2011)

## 1.1
* Rename `blog_time()` template tag to `c2c_blog_time()`
* Deprecate `blog_time()` template tag, but retain it for backwards compatibility
* Add hook `c2c_blog_time` (filter) to respond to the function of the same name so that users can use the `apply_filters()` notation for invoking template tag
* Move most of the code in constructor into `init()`
* Invoke `add_widget()` against `admin_print_footer_scripts` hook rather than `admin_footer`
* Fix PHP warnings/notices in widget code
* Full support for localization
* Rename class from `BlogTime` to `c2c_BlogTime`
* Note compatibility with WP 2.9+, 3.0+
* Drop support for versions of WP older than 2.8
* Assign object instance to global variable, `$c2c_blog_time` as global, to allow for external manipulation
* Remove docs from top of plugin file (all that and more are in readme.txt)
* Minor code reformatting (spacing)
* Add PHPDoc documentation
* Add package info
* Remove trailing whitespace in header docs
* Update copyright date
* Add Changelog, Filters, and Upgrade Notice sections to readme.txt
* Add .pot file

## 1.0
* Initial release
