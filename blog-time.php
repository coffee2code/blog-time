<?php
/**
 * Plugin Name: Blog Time
 * Version:     4.0.1
 * Plugin URI:  https://coffee2code.com/wp-plugins/blog-time/
 * Author:      Scott Reilly
 * Author URI:  https://coffee2code.com/
 * Text Domain: blog-time
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Description: Display the time according to your blog via admin toolbar widget, a sidebar widget, and/or template tag.
 *
 * Compatible with WordPress 4.6 through 5.8+.
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/blog-time/
 *
 * @package Blog_Time
 * @author  Scott Reilly
 * @version 4.0.1
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
	 * The name used for the plugin's setting.
	 *
	 * @ccess private
	 * @since 4.0
	 * @var string
	 */
	private static $setting_name = 'c2c_blog_time';

	/**
	 * Internally stored configuration settings.
	 * @var array
	 */
	private static $config = array();

	/**
	 * Internally used flag to indicate if inline JS script has been added.
	 *
	 * @access private
	 * @since 4.0.1
	 * @var bool
	 */
	private static $added_inline = false;

	/**
	 * Returns version of the plugin.
	 *
	 * @since 3.0
	 */
	public static function version() {
		return '4.0.1';
	}

	/**
	 * Initialization.
	 *
	 */
	public static function init() {
		// Load textdomain.
		load_plugin_textdomain( 'blog-time' );

		self::reset();

		// Register hooks.
		add_action( 'admin_init',                 array( __CLASS__, 'initialize_setting' ) );
		add_action( 'admin_bar_menu',             array( __CLASS__, 'admin_bar_menu' ), 500 );
		add_action( 'admin_enqueue_scripts',      array( __CLASS__, 'enqueue_js' ) );
		add_action( 'wp_enqueue_scripts',         array( __CLASS__, 'enqueue_js' ) );
		add_action( 'wp_ajax_report_time',        array( __CLASS__, 'report_time' ) );
		add_action( 'wp_ajax_nopriv_report_time', array( __CLASS__, 'report_time' ) );
	}

	/**
	 * Resets the class.
	 *
	 * Primarily restores memoization variables to default values.
	 *
	 * @since 4.0.1
	 */
	public static function reset() {
		self::$config = array(
			'time_format' => __( 'g:i A', 'blog-time' )
		);

		self::$added_inline = false;
	}

	/**
	 * Determines if the running WordPress is WP 5.5 or later.
	 *
	 * @since 4.0
	 *
	 * @return bool True if WP is 5.5 or later, else false.
	 */
	public static function is_wp_55_or_later() {
		return version_compare( $GLOBALS['wp_version'], '5.5', '>=' );
	}

	/**
	 * Adds a 'Settings' link to the plugin action links.
	 *
	 * @since 4.0
	 *
	 * @param string[] $action_links An array of plugin action links.
	 * @return array
	 */
	public static function plugin_action_links( $action_links ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'options-general.php' ) . '#c2c_blog_time' ),
			__( 'Settings', 'blog-time' )
		);
		array_unshift( $action_links, $settings_link );

		return $action_links;
	}

	/**
	 * Initializes setting.
	 *
	 * @since 4.0
	 */
	public static function initialize_setting() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		register_setting( 'general', self::$setting_name );

		add_filter(
			self::is_wp_55_or_later() ? 'allowed_options' : 'whitelist_options',
			array( __CLASS__, 'allowed_options' )
		);

		add_settings_field(
			self::$setting_name,
			__( 'Blog Time Format', 'blog-time' ),
			array( __CLASS__, 'display_option' ),
			'general'
		);

		// Add link to settings page from the plugin's action links on plugin page.
		add_filter( 'plugin_action_links_blog-time/blog-time.php', array( __CLASS__, 'plugin_action_links' ) );
	}

	/**
	 * Allows the plugin's option(s).
	 *
	 * @since 4.0
	 *
	 * @param array $options Array of options.
	 * @return array The amended options array.
	 */
	public static function allowed_options( $options ) {
		$added = array( self::$setting_name => array( self::$setting_name ) );

		return self::is_wp_55_or_later()
			? add_allowed_options( $added, $options )
			: add_option_whitelist( $added, $options );
	}

	/**
	 * Outputs markup for the plugin setting on the Reading Settings page.
	 *
	 * @since 4.0
	 *
	 * @param array $args Arguments.
	 */
	public static function display_option( $args = array() ) {
		printf(
			'<input name="%s" type="text" id="%s" value="%s" class="short-text">' . "\n" . '<p class="description">%s</p>' . "\n" . '<p class="blog-time-info">%s</p>' . "\n",
			esc_attr( self::$setting_name ),
			esc_attr( self::$setting_name ),
			esc_attr( self::get_time_format( '', 'raw' ) ),
			sprintf(
				/* translators: %s: URL to PHP documentation for data and time formatting. */
				__( 'Used by the <strong>Blog Time</strong> plugin. See <a href="%s">Documentation on date and time formatting</a> for formatting syntax.', 'blog-time' ),
				'https://www.php.net/manual/en/datetime.format.php'
			),
			sprintf(
				/* translators: %s: Default blog time format. */
				__( 'Default (used when setting is blank): %s', 'blog-time' ),
				'<code>' . self::$config['time_format'] . '</code>'
			)
		);

		// Provide a warning notice when setting value is being overridden via filter.
		$time_format =self::get_time_format();
		if ( self::get_time_format( '', 'nofilter' ) !== $time_format ) {
			// Note: If a filter callback sets time format to the default (or to the setting value if one is set),
			// then this warning will not appear. That's fine since the interface conveys the time format in use,
			// just not technically accurate by implying the default/setting value is being used. If the default or
			// setting is changed, the notice will then appear, which might be surprising since there was no mention
			// of a filter being used just prior.
			printf(
				'<p class="%s blog-time-info notice notice-warning">%s</p>' . "\n",
				esc_attr(self::$setting_name ),
				sprintf(
					/* translators: 1: Filter name, 2: Filtered time format. */
					__( 'The blog time format is currently configured via the %1$s filter, which takes precedence over this setting. The filtered blog time format value is %2$s (which may differ, depending on the logic used in the filter callback).', 'blog-time' ),
					"'c2c_blog_time_format'",
					'<code>' . $time_format . '</code>'
				)
			);
		}
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
		wp_enqueue_script( 'moment', plugins_url( 'js/moment.min.js', __FILE__ ), array(), '2.29.1', true );
		wp_enqueue_script( __CLASS__, plugins_url( 'js/blog-time.js', __FILE__ ), array( 'jquery', 'moment' ), self::version(), true );

		if ( ! self::$added_inline ) {
			wp_add_inline_script( __CLASS__, 'const ' . __CLASS__ . ' = ' . json_encode( array(
				'ajaxurl'     => admin_url( 'admin-ajax.php' ),
				'time_format' => self::get_time_format( '', 'momentjs' ),
				'utc_offset'  => self::display_time( 'O', 'utc-offset' ),
			) ), 'before' );
			self::$added_inline = true;
		}
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
	 * Recognized contexts (though not all have special handling):
	 * - ajax:     When time is being returned via AJAX. Functionally equivalent
	 *             to 'default'.
	 * - default:  The default context. Returns the true blog time format with
	 *             everything (default value, parameter, setting, filter) taken
	 *             into account.
	 * - momentjs: When time is to be ultimately used by MomentJS. Functionally
	 *             equivalent to 'default', but with format tokens converted from
	 *             PHP-syntax to MomentJS-syntax.
	 * - nofilter: Takes default value, parameter, and setting into account, but
	 *             does not pass the blog time format through any filters.
	 * - raw:      Takes parameter and setting into account, but ignores default
	 *             value and filter. If parameter is set, then returns that value.
	 *             Else returns value of setting, even if empty.
	 * - template_tag: When time is being retrieved or displayed via the
	 *             template tag. Functionally equivalent to 'default'.
	 * - widget:   When time is being displayed within a widget. Functionally
	 *             equivalent to 'default'.
	 *
	 * @since 3.5
	 * @since 4.0 Add support for 'nofilter' and 'raw' contexts.
	 *
	 * @param  string $time_format Optional. The format for the time string, if
	 *                             being explicitly set. Default ''.
	 * @param  string $context.    Optional. The context for the time being
	 *                             displayed. Can be any custom value, but has
	 *                             special handling for 'momentjs' 'nofilter',.
	 *                             or 'raw'. Default 'default'.
	 * @return string The time format string.
	 */
	public static function get_time_format( $time_format = '', $context = 'default' ) {
		if ( ! $context ) {
			$context = 'default';
		}

		// If no time format has been explicitly specified, use setting value.
		if ( $time_format ) {
			$explicit = true;
		} else {
			$explicit = false;
			$time_format = get_option( self::$setting_name );
		}

		// Don't proceed any further if context is 'raw'.
		if ( 'raw' === $context ) {
			return $time_format;
		}

		// If no time format at this point, use default.
		if ( ! $time_format ) {
			$time_format = self::$config['time_format'];
		}

		// Filter blog time format unless context is 'nofilter'.
		if ( ! $explicit && 'nofilter' !== $context ) {
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

		// If no time format still, use default.
		if ( ! $time_format || ! is_string( $time_format ) ) {
			$time_format = self::$config['time_format'];
		}

		// If the context is momentjs, then convert time format to Moment's format.
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
	 * Echoes the blog time and optionally exits (for use as AJAX responder).
	 *
	 * @since 4.0 Added `$exit` arg.
	 *
	 * @param bool $exit Optional. Exit after echoing time? Default true.
	 */
	public static function report_time( $exit = true ) {
		echo self::display_time( '', 'ajax' );

		if ( $exit ) {
			exit;
		}
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
			'T' => '[' . self::display_time( 'T' ) . ']',
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
