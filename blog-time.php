<?php
/**
 * Plugin Name: Blog Time
 * Version:     3.4
 * Plugin URI:  http://coffee2code.com/wp-plugins/blog-time/
 * Author:      Scott Reilly
 * Author URI:  http://coffee2code.com/
 * Text Domain: blog-time
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Description: Display the time according to your blog via admin toolbar widget, a sidebar widget, and/or template tag.
 *
 * Compatible with WordPress 3.3 through 4.4+.
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/blog-time/
 *
 * @package Blog_Time
 * @author  Scott Reilly
 * @version 3.4
 */

/*
 * TODO:
 * - Document template tag
 * - Time format string doesn't currently apply to dynamic clock. Make it work, or remove option to customize time format
 * - Add support for per-user setting for controlling admin toolbar widget (and if not shown, don't enqueue JS or CSS)
 *
 */

/*
	Copyright (c) 2009-2016 by Scott Reilly (aka coffee2code)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or die();

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'blog-time.widget.php' );

if ( ! class_exists( 'c2c_BlogTime' ) ) :

class c2c_BlogTime {

	/**
	 * Internally stored configuration settings.
	 * @var array
	 */
	private static $config = array();

	/**
	 * Returns version of the plugin.
	 *
	 * @since 3.0
	 */
	public static function version() {
		return '3.4';
	}

	/**
	 * Constructor
	 *
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'do_init' ) );
	}

	/**
	 * Handle initialization
	 */
	public static function do_init() {
		// Load textdomain.
		load_plugin_textdomain( 'blog-time' );

		self::$config = array(
			'time_format' => __( 'g:i A', 'blog-time' )
		);

		add_action( 'admin_bar_menu',             array( __CLASS__, 'admin_bar_menu' ), 500 );
		add_action( 'admin_enqueue_scripts',      array( __CLASS__, 'enqueue_js' ) );
		add_action( 'wp_enqueue_scripts',         array( __CLASS__, 'enqueue_js' ) );
		add_action( 'admin_head',                 array( __CLASS__, 'add_css' ) );
		add_action( 'wp_head',                    array( __CLASS__, 'add_css' ) );
		add_action( 'wp_ajax_report_time',        array( __CLASS__, 'report_time' ) );
		add_action( 'wp_ajax_nopriv_report_time', array( __CLASS__, 'report_time' ) );
		add_action( 'wp_head',                    array( __CLASS__, 'set_js_ajaxurl' ) );
	}

	/**
	 * Sets JS variable to path necessary for AJAX
	 *
	 * Only needed on front-end for widget since admin already sets this.
	 *
	 * @since 1.2
	 */
	public static function set_js_ajaxurl() {
		echo '<script type="text/javascript">var ajaxurl = \'' . admin_url( 'admin-ajax.php' ) . "';</script>\n";
	}

	/**
	 * Are we on the wp-login.php page?
	 *
	 * We can get here while logged in and break the page as the admin bar
	 * isn't shown and other things the js relies on aren't available.
	 *
	 * @since 3.0
	 *
	 * @return boolean
	 */
	protected static function is_wp_login() {
		return 'wp-login.php' == basename( $_SERVER['SCRIPT_NAME'] );
	}

	/**
	 * Is the blog time admin toolbar widget enabled for the specified user?
	 *
	 * TODO: This is mostly a placeholder for future functionality whereby
	 * the admin toolbar widget is controlled on a per-user basis via a
	 * user option.
	 *
	 * @since 3.0
	 *
	 * @return boolean True if enabled, false if not
	 */
	public static function show_in_toolbar_for_user() {
		return is_admin_bar_showing() ?
			apply_filters( 'c2c_blog_time_toolbar_widget_for_user', true ) :
			false;
	}

	/**
	 * Enqueues JS
	 *
	 * @since 2.0
	 *
	 * @param boolean $force Optional. Enqueue scripts regardless of admin toolbar check? (Typically when widget is displayed)
	 */
	public static function enqueue_js( $force = false ) {
		if ( ! $force && ( ! is_admin_bar_showing() || self::is_wp_login() || ! self::show_in_toolbar_for_user() ) ) {
			return;
		}

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( __CLASS__, plugins_url( 'js/blog-time.js', __FILE__ ), array( 'jquery' ), self::version(), true );
	}

	/**
	 * Adds time to admin toolbar
	 *
	 * @since 3.0
	 */
	public static function admin_bar_menu() {
		global $wp_admin_bar;

		$wp_admin_bar->add_menu( array(
			'id'     => 'c2c-blog-time',
			'parent' => 'top-secondary',
			'title'  => self::add_widget(),
			'meta'   => array( 'class' => '', 'title' => __( 'Current blog time', 'blog-time' ) )
		) );
	}

	/**
	 * Outputs CSS
	 */
	public static function add_css() {
		echo '<style type="text/css">';
		echo '.c2c-blog-time-widget-time { display:none; }';
		echo '#wpadminbar .c2c-blog-time-widget-display a { padding:0; }';
		echo "</style>\n";
	}

	/**
	 * Formats the current time (mysql) to the specified time format.
	 *
	 * @param  string $time_format (optional) The format for the time string, if not the default.
	 * @return string The time string
	 */
	public static function display_time( $time_format = '' ) {
		if ( empty( $time_format ) ) {
			$time_format = apply_filters( 'blog_time_format', self::$config['time_format'] ); // deprecated as of v3.1
			$time_format = apply_filters( 'c2c_blog_time_format', $time_format );
		}

		return date_i18n( $time_format, strtotime( current_time( 'mysql' ) ) );
	}

	/**
	 * The AJAX responder to return the blog time.
	 */
	public static function report_time() {
		echo self::display_time();
		exit();
	}

	/**
	 * Outputs the admin widget
	 *
	 * @param  $args array (optional) Configuration array. Currently supports:
	 *   dynamic (boolean|null) Should the clock by dynamic? Default is possibly filter 'true'.
	 *   format (string) PHP time string format for the time. (Doesn't apply for dynamic clock.)
	 */
	public static function add_widget( $args = array() ) {
		$defaults = array(
			'dynamic' => null,
			'format'  => '',
		);
		$args = wp_parse_args( $args, $defaults );

		if ( is_null( $args['dynamic'] ) ) {
			$dynamic = apply_filters( 'c2c_blog_time_active_clock', true ) !== false ? 'c2c-blog-time-dynamic' : '';
		} else {
			$dynamic = $args['dynamic'] == true ? 'c2c-blog-time-dynamic' : '';
		}

		$out  = "<span class='c2c-blog-time-widget'>";
		$out .= "<span class='c2c-blog-time-widget-time'>" . self::display_time( 'Y,n,j,G,i,s' ) . '</span>';
		$out .= "<span class='c2c-blog-time-widget-display $dynamic'>" .
			"<a href='' title='" . __( 'Click to refresh blog time', 'blog-time' ) . "'>" .
			self::display_time( $args['format'] ) . "</a></span></span>\n";

		return $out;
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
		if ( $echo ) { echo $val; }
		return $val;
	}
	add_filter( 'c2c_blog_time', 'c2c_blog_time', 10, 2 );
}

endif; // end if !class_exists()
