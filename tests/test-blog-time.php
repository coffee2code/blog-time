<?php

class Blog_Time_Test extends WP_UnitTestCase {

	protected $incoming_time_format = '';

	function tearDown() {
		parent::tearDown();

		$this->incoming_time_format = '';
		remove_filter( 'c2c_blog_time_format', array( $this, 'filter_c2c_blog_time_format' ) );
	}

	/**
	 *
	 * HELPER FUNCTIONS
	 *
	 */


	function filter_c2c_blog_time_format( $format = '' ) {
		$this->incoming_time_format = $format;
		return 'G:i Y ||| j n';
	}


	/**
	 *
	 * TESTS
	 *
	 */


	function test_class_name() {
		$this->assertTrue( class_exists( 'c2c_BlogTime' ) );
	}

	function test_widget_class_name() {
		$this->assertTrue( class_exists( 'c2c_BlogTimeWidget' ) );
	}

	function test_widget_base_class_name() {
		$this->assertTrue( class_exists( 'C2C_Widget_008' ) );
	}

	function test_version() {
		$this->assertEquals( '3.3.1', c2c_BlogTime::version() );
	}

	function test_widget_made_available() {
		$this->assertContains( 'c2c_BlogTimeWidget', array_keys( $GLOBALS['wp_widget_factory']->widgets ) );
	}

	function test_return_value_of_c2c_blog_time() {
		$format = 'Y-n-j G:i';
		$time   = date_i18n( $format, strtotime( current_time( 'mysql' ) ) );

		$this->assertEquals( $time, c2c_blog_time( $format, false ) );
	}

	function test_c2c_blog_time_echoes_by_default() {
		$format = 'Y-n-j G:i';

		ob_start();
		$time = date_i18n( $format, strtotime( current_time( 'mysql' ) ) );
		c2c_blog_time( $format );
		$out = ob_get_contents();
		ob_end_clean();

		$this->assertEquals( $time, $out );
	}

	function test_c2c_blog_time_explicit_echo() {
		$format = 'Y-n-j G:i';

		ob_start();
		$time = date_i18n( $format, strtotime( current_time( 'mysql' ) ) );
		c2c_blog_time( $format );
		$out = ob_get_contents();
		ob_end_clean();

		$this->assertEquals( $time, $out );
	}

	function test_invoking_c2c_blog_time_via_filter_approach() {
		$format = 'Y-n-j G:i';
		$time   = date_i18n( $format, strtotime( current_time( 'mysql' ) ) );

		$this->assertEquals( $time, apply_filters( 'c2c_blog_time', $format, false ) );
	}

	function test_c2c_blog_time_format_filter() {
		add_filter( 'c2c_blog_time_format', array( $this, 'filter_c2c_blog_time_format' ) );

		$format = $this->filter_c2c_blog_time_format();
		$time   = date_i18n( $format, strtotime( current_time( 'mysql' ) ) );

		$this->assertEquals( $time, apply_filters( 'c2c_blog_time', '', false ) );
	}

	function test_c2c_blog_time_format_filter_does_not_override_explicit_arg_value() {
		add_filter( 'c2c_blog_time_format', array( $this, 'filter_c2c_blog_time_format' ) );

		$format = 'Y-n-j G:i';
		$time   = date_i18n( $format, strtotime( current_time( 'mysql' ) ) );

		$this->assertEquals( $time, apply_filters( 'c2c_blog_time', $format, false ) );
	}

	function test_default_time_format() {
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
