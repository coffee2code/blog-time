<?php
/**
 * @package Blog_Time
 * @author Scott Reilly
 * @version 1.2
 */
/*
Plugin Name: Blog Time
Version: 1.2
Plugin URI: http://coffee2code.com/wp-plugins/blog-time/
Author: Scott Reilly
Author URI: http://coffee2code.com
Text Domain: blog-time
Description: Display the time according to your blog via a widget, admin widget, and/or template tag.

Compatible with WordPress 2.8+, 2.9+, 3.0+, 3.1+.

=>> Read the accompanying readme.txt file for instructions and documentation.
=>> Also, visit the plugin's homepage for additional information and updates.
=>> Or visit: http://wordpress.org/extend/plugins/blog-time/

*/

/*
Copyright (c) 2009-2011 by Scott Reilly (aka coffee2code)

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

require_once( dirname( __FILE__ ) . '/blog-time.widget.php' );

if ( !class_exists( 'c2c_BlogTime' ) ) :

class c2c_BlogTime {
	private static $config            = array();
	private static $textdomain        = 'blog-time';
	private static $textdomain_subdir = '';
	private static $span_id           = 'blog-time-admin-widget';

	/**
	 * Constructor
	 *
	 */
	public function init() {
		self::$config = array(
			'time_format' => __( 'g:i A', self::$textdomain )
		);

		add_action( 'init', array( __CLASS__, 'do_init' ) );
	}

	/**
	 * Handle initialization
	 */
	public function do_init() {
		self::load_textdomain();
		add_action( 'admin_head',                 array( __CLASS__, 'add_css' ) );
		add_action( 'admin_print_footer_scripts', array( __CLASS__, 'add_widget' ) );
		add_action( 'wp_ajax_report_time',        array( __CLASS__, 'report_time' ) );
		add_action( 'wp_ajax_nopriv_report_time', array( __CLASS__, 'report_time' ) );
		add_action( 'wp_head',                    array( __CLASS__, 'set_js_ajaxurl' ) );
	}

	/**
	 * Loads the localization textdomain for the plugin.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		$subdir = empty( self::$textdomain_subdir ) ? '' : ( '/' . self::$textdomain_subdir );
		load_plugin_textdomain( self::$textdomain, false, basename( dirname( __FILE__ ) ) . $subdir );
	}

	/**
	 * Formats the current time (mysql) to the specified time format.
	 *
	 * @param string $time_format (optional) The format for the time string, if not the default.
	 * @return string The time string
	 */
	public function display_time( $time_format = '' ) {
		if ( empty( $time_format ) )
			$time_format = apply_filters( 'blog_time_format', self::$config['time_format'] );
		return date_i18n( $time_format, strtotime( current_time( 'mysql' ) ) );
	}

	/**
	 * Sets JS variables to paths useful for AJAX
	 *
	 * @since 1.2
	 */
	function set_js_ajaxurl() {
		$ajaxurl = admin_url( 'admin-ajax.php' );
		$wpcontenturl = get_stylesheet_directory_uri();
		echo "<script type='text/javascript'>var ajaxurl = '$ajaxurl'; var wpcontenturl = '$wpcontenturl';</script>\n";
	}

	/**
	 * The AJAX responder to return the blog time.
	 *
	 * @return void
	 */
	function report_time() {
		echo self::display_time();
		exit();
	}

	/**
	 * Outputs CSS
	 *
	 * @return void (Text is echoed.)
	 */
	function add_css() {
		echo '<style type="text/css">#' . self::$span_id . "{display:none;}</style>\n";
	}

	/**
	 * Outputs the admin widget
	 *
	 * @return void (Text is echoed.)
	 */
	function add_widget() {
		$span_id = self::$span_id;
		echo "<span id='$span_id'> | <a href='#' title='" . __( 'Click to refresh blog time', self::$textdomain ) . "'>" .
			self::display_time() . "</a></span>\n";
		echo <<<JS
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('#$span_id').insertAfter($('#user_info p a:first')).show();
			$('#$span_id a').click(function() {
				$.get(ajaxurl, {action: 'report_time'}, function(data) {
					$('#$span_id a').html(data);
				});
				return false;
			});
		});
		</script>

JS;
	}

} // end c2c_BlogTime


c2c_BlogTime::init();

// Template tag
if ( !function_exists( 'c2c_blog_time' ) ) {
	/**
	 * Template tag to display the blog's time.
	 *
	 * @since 1.1
	 * @param string $time_format PHP-style datetime format string. Uses plugin default if not specified.
	 * @param boolean $echo Optional. Echo the time to the page?
	 * @return string The formatted blog time.
	 */
	function c2c_blog_time( $time_format = '', $echo = true ) {
		$val = c2c_BlogTime::display_time( $time_format );
		if ( $echo ) echo $val;
		return $val;
	}
	add_filter( 'c2c_blog_time', 'c2c_blog_time', 10, 2 );
}

// Deprecated
if ( !function_exists( 'blog_time' ) ) {
	/**
	 * @deprecated 1.1 Use c2c_blog_time() instead
	 */
	function blog_time( $time_format = '', $echo = true ) {
		_deprecated_function( __FUNCTION__, '1.1', 'c2c_blog_time()' );
		return c2c_blog_time( $time_format, $echo );
	}
}

endif; // end if !class_exists()
?>