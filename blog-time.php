<?php
/**
 * Plugin Name: Blog Time
 * Version:     3.6.2
 * Plugin URI:  https://coffee2code.com/wp-plugins/blog-time/
 * Author:      Scott Reilly
 * Author URI:  https://coffee2code.com/
 * Text Domain: blog-time
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Description: Display the time according to your blog via admin toolbar widget, a sidebar widget, and/or template tag.
 *
 * Compatible with WordPress 4.6 through 5.4+.
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/blog-time/
 *
 * @package Blog_Time
 * @author  Scott Reilly
 * @version 3.6.2
 */

/*
	Copyright (c) 2009-2021 by Scott Reilly (aka coffee2code)

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
		return '3.6.2';
	}

	/**
	 * Initialization.
	 *
	 */
	public static function init() {
		// Load textdomain.
		load_plugin_textdomain( 'blog-time' );

		self::$config = array(
			'time_format' => __( 'g:i A', 'blog-time' )
		);

		add_action( 'admin_bar_menu',             array( __CLASS__, 'admin_bar_menu' ), 500 );
		add_action( 'admin_enqueue_scripts',      array( __CLASS__, 'enqueue_js' ) );
		add_action( 'wp_enqueue_scripts',         array( __CLASS__, 'enqueue_js' ) );
		add_action( 'wp_ajax_report_time',        array( __CLASS__, 'report_time' ) );
		add_action( 'wp_ajax_nopriv_report_time', array( __CLASS__, 'report_time' ) );
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
	 * @return bool True if enabled, false if not.
	 */
	public static function show_in_toolbar_for_user() {
		/**
		 * Filters if the blog time admin toolbar widget should be enabled for user.
		 *
		 * @since 3.0
		 *
		 * @param bool $enabled_for_user Is the blog time admin toolbar widget
		 *                               enabled for user? Default true.
		 */
		return is_admin_bar_showing() ? (bool) apply_filters( 'c2c_blog_time_toolbar_widget_for_user', true ) : false;
	}

	/**
	 * Enqueues JavaScript.
	 *
	 * @since 2.0
	 *
	 * @param bool $force Optional. Should scripts get enqueued regardless of
	 *                    admin toolbar check? (e.g. when widget is displayed)
	 */
	public static function enqueue_js( $force = false ) {
		if ( ! $force && ! self::show_in_toolbar_for_user() ) {
			return;
		}

		wp_enqueue_style( __CLASS__, plugins_url( 'css/blog-time.css', __FILE__ ), array(), self::version() );

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'moment', plugins_url( 'js/moment.min.js', __FILE__ ), array(), '2.26.0', true );
		wp_enqueue_script( __CLASS__, plugins_url( 'js/blog-time.js', __FILE__ ), array( 'jquery', 'moment' ), self::version(), true );

		$text = array(
			'ajaxurl'     => admin_url( 'admin-ajax.php' ),
			'time_format' => self::get_time_format( '', 'momentjs' ),
			'utc_offset'  => self::display_time( 'O', 'utc-offset' ),
		);
		wp_localize_script( __CLASS__, __CLASS__, $text );
	}

	/**
	 * Adds time to admin toolbar.
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
	 * Determines the time format string for the given context.
	 *
	 * @since 3.5
	 *
	 * @param  string $time_format Optional. The format for the time string, if being explicitly set. Default ''.
	 * @param  string $context.    Optional. The context for the time being displayed. Default 'default'.
	 * @return string The time string.
	 */
	public static function get_time_format( $time_format = '', $context = 'default' ) {
		if ( ! $context ) {
			$context = 'default';
		}

		if ( ! $time_format ) {
			$time_format = apply_filters_deprecated(
				'blog_time_format',
				array( self::$config['time_format'] ),
				'3.2',
				'c2c_blog_time_format'
			);
			/**
			 * Filters the time format string for a given context.
			 *
			 * @since 3.1
			 *
			 * @param string $time_format The format for the time string.
			 * @param string $context     The context.
			 */
			$time_format = apply_filters( 'c2c_blog_time_format', $time_format, $context );
		}

		if ( 'momentjs' === $context ) {
			$time_format = self::map_php_time_format_to_momentjs( $time_format );
		}

		return $time_format;
	}

	/**
	 * Formats the current time (mysql) to the specified time format.
	 *
	 * @param  string $time_format Otional. The format for the time string, if not the default.
	 * @param  string $context.    Optional. The context for the time being displayed. Default ''.
	 * @return string The time string.
	 */
	public static function display_time( $time_format = '', $context = '' ) {
		$time_format = self::get_time_format( $time_format, $context );

		return date_i18n( $time_format, strtotime( current_time( 'mysql' ) ) );
	}

	/**
	 * Echoes the blog time and exists (for use as AJAX responder).
	 */
	public static function report_time() {
		echo self::display_time( '', 'ajax' );
		exit();
	}

	/**
	 * Converts a PHP time format string into a Moment.js time format string.
	 *
	 * @since 3.5
	 *
	 * @param  string $format PHP time format string.
	 * @return string
	 */
	public static function map_php_time_format_to_momentjs( $format ) {
		// Preprocess format string to handle exceptions.
		$pre_mapping = array(
			'js' => '~~', // Moment doesn't have ordinal suffix as a separate token
		);
		foreach ( $pre_mapping as $pre_map => $remap ) {
			$format = str_replace( $pre_map, $remap, $format );
		}

		// Mappings for PHP time format tokens to Moment.js tokens.
		$mapping = array(
			// Day
			'd' => 'DD',
			'D' => 'ddd',
			'j' => 'D',
			'l' => 'dddd',
			'N' => 'E',
			'S' => '',
			'w' => 'd',
			'z' => 'DDD',
			// Week
			'W' => 'w',
			// Month
			'F' => 'MMMM',
			'm' => 'MM',
			'M' => 'MMM',
			'n' => 'M',
			't' => '',
			// Year
			'L' => '',
			'o' => 'gggg',
			'Y' => 'YYYY',
			'y' => 'YY',
			// Time
			'a' => 'a',
			'A' => 'A',
			'B' => 'SSS',
			'g' => 'h',
			'G' => 'H',
			'h' => 'hh',
			'H' => 'HH',
			'i' => 'mm',
			's' => 'ss',
			'u' => 'SSSSSS',
			'v' => 'SSS',
			// Timezone
			'e' => 'z',
			'I' => '',
			'O' => 'ZZ',
			'P' => 'Z',
			'T' => '',
			'Z' => '',
			// Full date/time
			'c' => 'YYYY-MM-DDTHH:mm:ssZ',
			'r' => 'ddd, DD MMM YYYY HH:mm:ss ZZ',
			'U' => 'x',
		);

		$parts = str_split( $format );

		if ( ! $parts ) {
			return $format;
		}

		$moment = array();
		foreach ( $parts as $token ) {
			$moment[] = isset( $mapping[ $token ] ) ? $mapping[ $token ] : $token;
		}
		$format = implode( '', $moment );

		// Finish mapping exceptions.
		$post_mapping = array(
			'~~' => 'Do',
		);
		foreach ( $post_mapping as $post_map => $remap ) {
			$format = str_replace( $post_map, $remap, $format );
		}

		return $format;
	}

	/**
	 * Returns the markup for the admin widget.
	 *
	 * @param array $args {
	 *     Optional. Configuration array. Default empty array.
	 *
	 *.    @type string.   $context Context for the widget, e.g. "widget". Default "admin-widget".
	 *     @type bool|null $dynamic Should the clock by dynamic? Default true, unless filtered via
	 *                              the 'c2c_blog_time_active_clock' filter.
	 *.    @type string.   $format  An explicit PHP time format string. Default ''.
	 * }
	 */
	public static function add_widget( $args = array() ) {
		$defaults = array(
			'context' => 'admin-widget',
			'dynamic' => null,
			'format'  => '',
		);
		$args = wp_parse_args( $args, $defaults );

		if ( is_null( $args['dynamic'] ) ) {
			/**
			 * Filters if the Javascript-powered dynamic clock introduced in v2.0 should
			 * be enabled or if instead the v1.x era behavior of a static timestamp that
			 * can be clicked to update the timestamp via AJAX should be enabled. By
			 * default the dynamic clock is enabled.
			 *
			 * @since 2.0
			 *
			 * @param bool $active_clock Is the blog time clock active? Default true.
			 */
			$is_dynamic = (bool) apply_filters( 'c2c_blog_time_active_clock', true );
		} else {
			$is_dynamic = true == $args['dynamic'];
		}

		$dynamic_class = $is_dynamic ? 'c2c-blog-time-dynamic' : '';

		$time = self::display_time( $args['format'], $args['context'] );

		// Data to encode into data attributes.
		$data_atts = '';
		if ( $args['format'] ) {
			$data_atts .= ' data-time-format="' . esc_attr( self::get_time_format( $args['format'], 'momentjs' ) ) . '"';
		}

		$out  = '<span class="c2c-blog-time-widget"' . $data_atts . '>';

		$out .= '<span class="ab-icon"></span>';

		$out .= '<span class="c2c-blog-time-widget-display ab-label ' . $dynamic_class . '">';
		$out .= sprintf(
			'<a class="ab-item" href="" title="%s">%s</a>',
			esc_attr__( 'Click to refresh blog time', 'blog-time' ),
			$time
		);
		$out .= "</span></span>\n";

		return $out;
	}

} // end c2c_BlogTime

add_action( 'plugins_loaded', array( 'c2c_BlogTime', 'init' ) );


// Template tag
if ( ! function_exists( 'c2c_blog_time' ) ) {
	/**
	 * Template tag to display the blog's time.
	 *
	 * @since 1.1
	 *
	 * @param  string $time_format PHP-style datetime format string. Uses plugin default if not specified.
	 * @param  bool   $echo        Optional. Echo the time to the page?
	 * @return string The formatted blog time.
	 */
	function c2c_blog_time( $time_format = '', $echo = true ) {
		$val = c2c_BlogTime::display_time( $time_format, 'template-tag' );
		if ( $echo ) { echo $val; }
		return $val;
	}
	add_filter( 'c2c_blog_time', 'c2c_blog_time', 10, 2 );
}

endif; // end if !class_exists()
