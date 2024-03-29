<?php
/**
 * Blog Time plugin widget code
 *
 * Copyright (c) 2009-2020 by Scott Reilly (aka coffee2code)
 *
 * @package c2c_Blog_Time_Widget
 * @author  Scott Reilly
 * @version 008
 */

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'c2c_BlogTimeWidget' ) ) :

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'c2c-widget.php' );

class c2c_BlogTimeWidget extends c2c_Widget_013 {

	/**
	 * Returns version of the widget.
	 *
	 * @since 005
	 *
	 * @return string
	 */
	public static function version() {
		return '008';
	}

	/**
	 * Registers the widget.
	 *
	 * @since 007
	 */
	public static function register_widget() {
		register_widget( __CLASS__ );
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct( 'blog-time', __FILE__ );
	}

	/**
	 * Initializes the plugin's configuration and localizable text variables.
	 */
	public function load_config() {
		$this->title       = __( 'Blog Time', 'blog-time' );
		$this->description = __( 'The current time according to your site.', 'blog-time' );

		$this->config = array(
			'title'   => array(
				'input'   => 'text',
				'default' => $this->title,
				'label'   => __( 'Title', 'blog-time' ),
			),
			'format'  => array(
				'input'   => 'text',
				'default' => 'g:i A',
				'label'   => __( 'Time format', 'blog-time' ),
				'help'    => sprintf( __( 'PHP-style time format string. See %s for more info.', 'blog-time' ),
							'<a href="https://www.php.net/manual/en/datetime.format.php" title="">https://www.php.net/manual/en/datetime.format.php</a>' ),
			),
			'dynamic' => array(
				'input'   => 'checkbox',
				'default' => true,
				'label'   => __( 'Use dynamic clock?', 'blog-time' ),
				'help'    => __( 'If checked, the widget will function like a regular clock, updating itself every minute.', 'blog-time' ),
			),
			'before'  => array(
				'input'   => 'text',
				'default' => '',
				'label'   => __( 'Before text', 'blog-time' ),
				'help'    => __( 'Text to display before the time.', 'blog-time' ),
			),
			'after'   => array(
				'input'   => 'text',
				'default' => '',
				'label'   => __( 'After text', 'blog-time' ),
				'help'    => __( 'Text to display after the time.', 'blog-time' ),
			),
		);
	}

	/**
	 * Outputs the body of the widget.
	 *
	 * @param array $args     Widget args.
	 * @param array $instance Widget instance.
	 * @param array $settings Widget settings.
	 * @return string
	 */
	public function widget_body( $args, $instance, $settings ) {
		extract( $args );
		extract( $settings );

		// Ensure JS is enqueued
		c2c_BlogTime::enqueue_js( true );

		$content = '';

		// Widget content
		if ( $before ) {
			$content .= $before;
		}

		$content .= "<div id='user_info'>";

		$content .= c2c_BlogTime::add_widget( array(
			'context' => 'widget',
			'dynamic' => ! empty( $dynamic ),
			'format'  => $format
		) );

		$content .= "</div>";

		if ( $after ) {
			$content .= $after;
		}

		return $content;
	}

} // end class c2c_BlogTimeWidget

add_action( 'widgets_init', array( 'c2c_BlogTimeWidget', 'register_widget' ) );

endif; // end if !class_exists()
