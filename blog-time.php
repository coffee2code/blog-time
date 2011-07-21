<?php
/**
 * @package Blog_Time
 * @author Scott Reilly
 * @version 2.0
 */
/*
Plugin Name: Blog Time
Version: 2.0
Plugin URI: http://coffee2code.com/wp-plugins/blog-time/
Author: Scott Reilly
Author URI: http://coffee2code.com
Text Domain: blog-time
Description: Display the time according to your blog via a widget, admin widget, and/or template tag.

Compatible with WordPress 3.1+, 3.2+.

=>> Read the accompanying readme.txt file for instructions and documentation.
=>> Also, visit the plugin's homepage for additional information and updates.
=>> Or visit: http://wordpress.org/extend/plugins/blog-time/

TODO:
	* Update screenshots for WP 3.2
	* Use C2C_Widget widget framework
	* Time format string doesn't currently apply to dynamic clock. Make it work, or remove option to customize time format
	* No need to bother AJAXifiying clock link when it is dynamic
	* Add support for widget to have dynamic mode
	* Since jqClock already supports it, facilitate displaying server date?

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

if ( ! class_exists( 'c2c_BlogTime' ) ) :

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
		add_action( 'admin_enqueue_scripts',      array( __CLASS__, 'enqueue_js' ) );
		add_action( 'admin_head',                 array( __CLASS__, 'add_css' ) );
		add_action( 'admin_print_footer_scripts', array( __CLASS__, 'add_js' ) );
		add_action( 'in_admin_header',            array( __CLASS__, 'add_widget' ) );
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
	 * Sets JS variable to path necessary for AJAX
	 *
	 * Only needed on front-end for widget since admin already sets this.
	 *
	 * @since 1.2
	 *
	 * @return void
	 */
	function set_js_ajaxurl() {
		echo '<script type="text/javascript">var ajaxurl = \'' . admin_url( 'admin-ajax.php' ) . "';</script>\n";
	}

	/**
	 * Enqueues JS
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	public function enqueue_js() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jqclock' , plugins_url( '/js/jqClock.min.js' , __FILE__ ), array( 'jquery' ), '2.0.1', true );
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
		global $wp_version; // Only for WP3.1 support
		echo '<style type="text/css">#' . self::$span_id . '{float:right;line-height:26px;height:25px;padding:0 2px 0 6px;margin-top:3px;font-size:12px;';
		if ( version_compare( '3.1.99', $wp_version ) > 0 ) // Only for WP3.1 support
			echo 'line-height:46px;height:46px;margin-top:0;';
		echo "}\n";
		echo '.no-js #' . self::$span_id . " {margin-top:2px;}\n";
		echo '#' . self::$span_id . '-time, .clockdate {display:none;}';
		echo "</style>\n";
	}

	/**
	 * Outputs the admin widget
	 *
	 * @return void (Text is echoed.)
	 */
	function add_widget() {
		$span_id = self::$span_id;
		echo "<span id='$span_id-time'>" . (date_i18n('U')+4*60*60) . '</span>';
		echo "<span id='$span_id'><a href='' title='" . __( 'Click to refresh blog time', self::$textdomain ) . "'>" .
			self::display_time() . "</a></span>\n";
	}

	/**
	 * Outputs Javascript
	 *
	 * @since 2.0
	 *
	 * @return void (Text is echoed.)
	 */
	function add_js() {
		$span_id = self::$span_id;
		$action  = apply_filters( 'c2c_blog_time_js_insert_action', 'insertBefore' );
		$target  = apply_filters( 'c2c_blog_time_target', '#user_info' );
		$dynamic = apply_filters( 'c2c_blog_time_active_clock', true ) !== false ? 'true' : 'false';
		echo <<<JS
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('#$span_id').{$action}($('{$target}')).show();
			if ($dynamic) {
				$('#$span_id a').clock({"timestamp":parseFloat($('#$span_id-time').text() * 1000)});
				$('#$span_id a').click(function() { return false; });
			}
			else {
				$('#$span_id a').click(function() {
					$.get(ajaxurl, {action: 'report_time'}, function(data) {
						$('#$span_id a').html(data);
					});
					return false;
				});
			}
		});
		</script>

JS;
	}

} // end c2c_BlogTime


c2c_BlogTime::init();

// Template tag
if ( ! function_exists( 'c2c_blog_time' ) ) {
	/**
	 * Template tag to display the blog's time.
	 *
	 * @since 1.1
	 *
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
if ( ! function_exists( 'blog_time' ) ) {
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