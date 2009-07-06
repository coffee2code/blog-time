<?php
/*
Plugin Name: Blog Time
Version: 1.0
Plugin URI: http://coffee2code.com/wp-plugins/blog-time
Author: Scott Reilly
Author URI: http://coffee2code.com
Description: Show a timestamp of your blog's time via a widget, admin widget, and/or template tag.

This plugin adds a timestamp string to the top of all admin pages to show the server time for the blog.  This admin time widget
is AJAX-ified so that if you click the timestamp, it updates in place (without a page reload) to show the new current server time.

Also provided is a "Blog Time" widget (for WP2.8+) providing the same functionality as the admin widget, but for your sidebars.
You may also utilize the plugin's capabilities directly within a theme template via use of the template tag "blog_time()".

NOTE: This plugin generates a timestamp and NOT a clock.  The time being displayed is the time of the page load, or if clicked, the
time when the widget last retrieved the time.  It does not actively increment time on the display.

This is most useful to see the server/blog time to judge when a time sensitive post, comment, or action would be dated by the blog (i.e. such
as monitoring for when to close comments on a contest post, or just accounting for the server being hosted in a different timezone).

Compatible with WordPress 2.6+, 2.7+, 2.8+.

=>> Read the accompanying readme.txt file for more information.  Also, visit the plugin's homepage
=>> for more information and the latest updates

Installation:

1. Download the file http://coffee2code.com/wp-plugins/blog-time.zip and unzip it into your 
/wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' admin menu in WordPress
3. Optionally use the 'Blog Time' widget (only in WP2.8+) or the template tag 'blog_time()' in a theme template file, to display the blog's time at the time of the page's rendering.
*/

/*
Copyright (c) 2009 by Scott Reilly (aka coffee2code)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation 
files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, 
modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the 
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

require_once(dirname(__FILE__) . '/blog-time.widget.php');

if ( !class_exists('BlogTime') ) :
class BlogTime {
	var $config = array();

	function BlogTime() {
		$this->config = array(
			'time_format' => __('g:i A')
		);

		add_action('plugins_loaded', array(&$this, 'report_time'), 1);
		add_action('admin_footer', array(&$this, 'add_widget'));
		add_action('admin_head', array(&$this, 'add_css'));
	}

	function display_time( $time_format = '' ) {
		if ( empty($time_format) )
			$time_format = apply_filters('blog_time_format', $this->config['time_format']);
		return date_i18n( $time_format, strtotime( current_time('mysql') ) );
	}

	// AJAX responder
	function report_time() {
		if ( is_admin() && isset($_GET['blog_time']) && $_GET['blog_time'] == '1' ) {
			echo $this->display_time();
			exit();
		}
	}

	function add_css() {
		echo <<<CSS
		<style type="text/css">
		#blog-time {display:none;}
		</style>

CSS;
	}

	function add_widget() {
		echo "<span id='blog-time'> | <a href='#' title='" . __('Click to refresh blog time') . "'>" .
			$this->display_time() . "</a></span>\n";
		echo <<<JS
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('#blog-time').insertAfter($('#user_info p a:first')).show();
			$('#blog-time a').click( function() {
				$(this).load('/wp-admin/?blog_time=1');
				return false;
			});
		});
		</script>
JS;
	}

} // end BlogTime

endif; // end if !class_exists()

if ( class_exists('BlogTime') ) {
	$c2c_blog_time = new BlogTime();

	// Template tag
	if ( !function_exists('blog_time') ) {
		/**
		 * Template tag to display the blog's time.
		 *	
		 *	@param string $time_format PHP-style datetime format string. Uses plugin default if not specified.
		 *	@param boolean $echo Optional. Echo the time to the page?
		 *	@return string The formatted blog time.
		 */
		function blog_time( $time_format = '', $echo = true ) {
			$val = $GLOBALS['c2c_blog_time'] ? $GLOBALS['c2c_blog_time']->display_time($time_format) : '';
			if ( $echo ) echo $val;
			return $val;
		}
	}
}

?>