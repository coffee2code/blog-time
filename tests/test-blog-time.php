<?php

defined( 'ABSPATH' ) or die();

class Blog_Time_Test extends WP_UnitTestCase {

	protected $incoming_time_format = '';

	public function tearDown() {
		parent::tearDown();

		$this->incoming_time_format = '';
		remove_filter( 'c2c_blog_time_format', array( $this, 'filter_c2c_blog_time_format' ) );
	}


	//
	//
	// HELPER FUNCTIONS
	//
	//


	public function filter_c2c_blog_time_format( $format = '' ) {
		$this->incoming_time_format = $format;
		return 'G:i Y ||| j n';
	}


	//
	//
	// TESTS
	//
	//


	public function test_class_name() {
		$this->assertTrue( class_exists( 'c2c_BlogTime' ) );
	}

	public function test_version() {
		$this->assertEquals( '3.4', c2c_BlogTime::version() );
	}

	public function test_widget_class_name() {
		$this->assertTrue( class_exists( 'c2c_BlogTimeWidget' ) );
	}

	public function test_widget_version() {
		$this->assertEquals( '006', c2c_BlogTimeWidget::version() );
	}

	public function test_widget_base_class_name() {
		$this->assertTrue( class_exists( 'c2c_BlogTime_Widget_011' ) );
	}

	public function test_widget_framework_version() {
		$this->assertEquals( '011', c2c_BlogTime_Widget_011::version() );
	}

	public function test_widget_hooks_widgets_init() {
		$this->assertEquals( 10, has_filter( 'widgets_init', 'register_c2c_BlogTimeWidget' ) );
	}

	public function test_widget_made_available() {
		$this->assertContains( 'c2c_BlogTimeWidget', array_keys( $GLOBALS['wp_widget_factory']->widgets ) );
	}

	public function test_return_value_of_c2c_blog_time() {
		$format = 'Y-n-j G:i';
		$time   = date_i18n( $format, strtotime( current_time( 'mysql' ) ) );

		$this->assertEquals( $time, c2c_blog_time( $format, false ) );
	}

	public function test_c2c_blog_time_echoes_by_default() {
		$format = 'Y-n-j G:i';

		ob_start();
		$time = date_i18n( $format, strtotime( current_time( 'mysql' ) ) );
		c2c_blog_time( $format );
		$out = ob_get_contents();
		ob_end_clean();

		$this->assertEquals( $time, $out );
	}

	public function test_c2c_blog_time_explicit_echo() {
		$format = 'Y-n-j G:i';

		ob_start();
		$time = date_i18n( $format, strtotime( current_time( 'mysql' ) ) );
		c2c_blog_time( $format );
		$out = ob_get_contents();
		ob_end_clean();

		$this->assertEquals( $time, $out );
	}

	public function test_invoking_c2c_blog_time_via_filter_approach() {
		$format = 'Y-n-j G:i';
		$time   = date_i18n( $format, strtotime( current_time( 'mysql' ) ) );

		$this->assertEquals( $time, apply_filters( 'c2c_blog_time', $format, false ) );
	}

	public function test_c2c_blog_time_format_filter() {
		add_filter( 'c2c_blog_time_format', array( $this, 'filter_c2c_blog_time_format' ) );

		$format = $this->filter_c2c_blog_time_format();
		$time   = date_i18n( $format, strtotime( current_time( 'mysql' ) ) );

		$this->assertEquals( $time, apply_filters( 'c2c_blog_time', '', false ) );
	}

	public function test_c2c_blog_time_format_filter_does_not_override_explicit_arg_value() {
		add_filter( 'c2c_blog_time_format', array( $this, 'filter_c2c_blog_time_format' ) );

		$format = 'Y-n-j G:i';
		$time   = date_i18n( $format, strtotime( current_time( 'mysql' ) ) );

		$this->assertEquals( $time, apply_filters( 'c2c_blog_time', $format, false ) );
	}

	public function test_default_time_format() {
		add_filter( 'c2c_blog_time_format', array( $this, 'filter_c2c_blog_time_format' ) );

		c2c_blog_time( '', false );

		$this->assertEquals( 'g:i A', $this->incoming_time_format );
	}

	/*
	 * TEST TODO:
	 * - JS is enqueued
	 * - CSS is enqueue
	 * - Admin toolbar widget is added (and has necessary data and format)
	 * - filter 'c2c_blog_time_active_clock' outputs (or doesn't output) appropriately
	 */

}
