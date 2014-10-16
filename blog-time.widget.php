<?php
/**
 * @package Blog_Time_Widget
 * @author Scott Reilly
 * @version 004
 */
/*
 * Blog Time plugin widget code
 *
 * Copyright (c) 2009-2014 by Scott Reilly (aka coffee2code)
 *
 */

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'c2c_BlogTimeWidget' ) ) :

require_once( 'c2c-widget.php' );

class c2c_BlogTimeWidget extends C2C_Widget_008 {

	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct( 'blog-time', __FILE__ );
	}

	/**
	 * Initializes the plugin's configuration and localizable text variables.
	 *
	 * @return void
	 */
	function load_config() {
		$this->title       = __( 'Blog Time', 'blog-time' );
		$this->description = __( 'The current time according to your site.', 'blog-time' );

		$this->config = array(
			'title'   => array( 'input' => 'text', 'default' => $this->title,
					'label' => __( 'Title', 'blog-time' ) ),
			'format'  => array( 'input' => 'text', 'default' => 'g:i A',
					'label' => __( 'Time format', $this->textdomain ),
					'help'  => sprintf( __( 'PHP-style time format string. See %s for more info. <em>Does not apply to dynamic clock.</em>', 'blog-time' ),
						'<a href="http://php.net/date" title="">http://php.net/date</a>' ) ),
			'dynamic' => array( 'input' => 'checkbox', 'default' => true,
					'label' => __( 'Use dynamic clock?', 'blog-time' ),
					'help'  => __( 'If checked, the widget will function like a regular clock, updating itself every minute.', 'blog-time' ) ),
			'before'  => array( 'input' => 'text', 'default' => '',
					'label' => __( 'Before text', 'blog-time' ),
					'help'  => __( 'Text to display before the time.', 'blog-time' ) ),
			'after'   => array( 'input' => 'text', 'default' => '',
					'label' => __( 'After text', 'blog-time' ),
					'help'  => __( 'Text to display after the time.', 'blog-time' ) )
		);
	}

	/**
	 * Outputs the body of the widget
	 *
	 * @param array $args Widget args
	 * @param array $instance Widget instance
	 * @param array $settings Widget settings
	 * @return void (Text is echoed.)
	 */
	function widget_body( $args, $instance, $settings ) {
		extract( $args );
		extract( $settings );

		// Ensure JS is enqueued
		c2c_BlogTime::enqueue_js( true );

		// Widget content
		if ( $before ) {
			echo $before;
		}

		echo "<div id='user_info'>";

		echo c2c_BlogTime::add_widget( array( 'dynamic' => ! empty( $dynamic ), 'format' => $format ) );

		echo "</div>";

		if ( $after ) {
			echo $after;
		}
	}

} // end class c2c_BlogTimeWidget

function register_c2c_BlogTimeWidget() {
	register_widget( 'c2c_BlogTimeWidget' );
}
add_action( 'widgets_init', 'register_c2c_BlogTimeWidget' );

endif; // end if !class_exists()
?>