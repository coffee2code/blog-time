<?php
/**
 * @package Blog_Time
 * @author Scott Reilly
 * @version 1.1
 */
/*
Plugin Name: Blog Time
Version: 1.1
Plugin URI: http://coffee2code.com/wp-plugins/blog-time/
Author: Scott Reilly
Author URI: http://coffee2code.com
Text Domain: blog-time
Description: Display the time according to your blog via a widget, admin widget, and/or template tag.

Compatible with WordPress 2.8+, 2.9+, 3.0+.

=>> Read the accompanying readme.txt file for instructions and documentation.
=>> Also, visit the plugin's homepage for additional information and updates.
=>> Or visit: http://wordpress.org/extend/plugins/blog-time/

*/

/*
Copyright (c) 2009-2010 by Scott Reilly (aka coffee2code)

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
	var $config = array();
	var $textdomain = 'blog-time';
	var $textdomain_subdir = '';

	/**
	 * Constructor
	 *
	 */
	function c2c_BlogTime() {
		$this->config = array(
			'time_format' => __( 'g:i A', $this->textdomain )
		);

		add_action( 'plugins_loaded', array( &$this, 'report_time' ), 1 );
		add_action( 'init', array( &$this, 'init' ) );
	}

	/**
	 * Handle initialization
	 */
	function init() {
		$this->load_textdomain();
		add_action( 'admin_head', array( &$this, 'add_css' ) );
		add_action( 'admin_print_footer_scripts', array( &$this, 'add_widget' ) );
	}

	/**
	 * Loads the localization textdomain for the plugin.
	 *
	 * @return void
	 */
	function load_textdomain() {
		$subdir = empty( $this->textdomain_subdir ) ? '' : '/'.$this->textdomain_subdir;
		load_plugin_textdomain( $this->textdomain, false, basename( dirname( __FILE__ ) ) . $subdir );
	}

	/**
	 * Formats the current time (mysql) to the specified time format.
	 *
	 * @param string $time_format (optional) The format for the time string, if not the default.
	 * @return string The time string
	 */
	function display_time( $time_format = '' ) {
		if ( empty( $time_format ) )
			$time_format = apply_filters( 'blog_time_format', $this->config['time_format'] );
		return date_i18n( $time_format, strtotime( current_time( 'mysql' ) ) );
	}

	/**
	 * The AJAX responder to return the blog time.
	 *
	 * @return void
	 */
	function report_time() {
		if ( is_admin() && isset( $_GET['blog_time'] ) && $_GET['blog_time'] == '1' ) {
			echo $this->display_time();
			exit();
		}
	}

	/**
	 * Outputs CSS
	 *
	 * @return void (Text is echoed.)
	 */
	function add_css() {
		echo <<<CSS
		<style type="text/css">
		#blog-time {display:none;}
		</style>

CSS;
	}

	/**
	 * Outputs the admin widget
	 *
	 * @return void (Text is echoed.)
	 */
	function add_widget() {
		echo "<span id='blog-time'> | <a href='#' title='" . __( 'Click to refresh blog time', $this->textdomain ) . "'>" .
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

} // end c2c_BlogTime

endif; // end if !class_exists()

if ( class_exists( 'c2c_BlogTime' ) ) {
	$GLOBALS['c2c_blog_time'] = new c2c_BlogTime();

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
			$val = $GLOBALS['c2c_blog_time'] ? $GLOBALS['c2c_blog_time']->display_time( $time_format ) : '';
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
			return c2c_blog_time( $time_format, $echo );
		}
	}
}

?>