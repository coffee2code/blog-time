<?php

defined( 'ABSPATH' ) or die();

class Blog_Time_Test extends WP_UnitTestCase {

	protected $incoming_time_format = '';
	protected $incoming_context = '';
	protected static $default_time_format  = 'g:i A';

	public function tearDown() {
		parent::tearDown();

		$this->incoming_time_format = '';
		$this->incoming_context = '';
		remove_filter( 'c2c_blog_time_format', array( $this, 'filter_c2c_blog_time_format' ), 10, 2 );
	}


	//
	//
	// DATA PROVIDERS
	//
	//


	public static function php_to_momentjs_mappings() {
		return array(
			array( array( 'c', 'YYYY-MM-DDTHH:mm:ssZ' ) ),
			array( array( 'r', 'ddd, DD MMM YYYY HH:mm:ss ZZ' ) ),
			array( array( 'M js, Y', 'MMM Do, YYYY' ) ),
		);
	}


	//
	//
	// HELPER FUNCTIONS
	//
	//


	public function filter_c2c_blog_time_format( $format = '', $context = '' ) {
		$this->incoming_time_format = $format;
		$this->incoming_context = $context;

		return ( 'widget' === $context ) ? 'Y,m,dTH:i:s A' : 'G:i Y ||| j n';
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
		$this->assertEquals( '3.5.1', c2c_BlogTime::version() );
	}

	public function test_widget_class_name() {
		$this->assertTrue( class_exists( 'c2c_BlogTimeWidget' ) );
	}

	public function test_widget_version() {
		$this->assertEquals( '007', c2c_BlogTimeWidget::version() );
	}

	public function test_widget_base_class_name() {
		$this->assertTrue( class_exists( 'c2c_Widget_013' ) );
	}

	public function test_widget_parent_class() {
		$this->assertEquals( 'c2c_Widget_013', get_parent_class( 'c2c_BlogTimeWidget' ) );
	}

	public function test_widget_framework_version() {
		$this->assertEquals( '013', c2c_Widget_013::version() );
	}

	public function test_widget_hooks_widgets_init() {
		$this->assertEquals( 10, has_filter( 'widgets_init', array ( 'c2c_BlogTimeWidget', 'register_widget' ) ) );
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
		add_filter( 'c2c_blog_time_format', array( $this, 'filter_c2c_blog_time_format' ), 10, 2 );

		$format = $this->filter_c2c_blog_time_format();
		$time   = date_i18n( $format, strtotime( current_time( 'mysql' ) ) );

		$this->assertEquals( $time, apply_filters( 'c2c_blog_time', '', false ) );
	}

	public function test_c2c_blog_time_format_filter_does_not_override_explicit_arg_value() {
		add_filter( 'c2c_blog_time_format', array( $this, 'filter_c2c_blog_time_format' ), 10, 2 );

		$format = 'Y-n-j G:i';
		$time   = date_i18n( $format, strtotime( current_time( 'mysql' ) ) );

		$this->assertEquals( $time, apply_filters( 'c2c_blog_time', $format, false ) );
	}

	public function test_default_time_format() {
		add_filter( 'c2c_blog_time_format', array( $this, 'filter_c2c_blog_time_format' ), 10, 2 );

		c2c_blog_time( '', false );

		$this->assertEquals( self::$default_time_format, $this->incoming_time_format );
		$this->assertEquals( 'template-tag', $this->incoming_context );
	}


	/*
	 * c2c_BlogTime::get_time_format()
	 */


	public function test_get_time_format() {
		$this->assertEquals( self::$default_time_format, c2c_BlogTime::get_time_format() );
	}

	public function test_get_time_format_with_explicit_time_format() {
		$time_formats = array(
			'F js Y, H:i:s',
			'c',
			'Y-n-j G:i',
		);

		foreach ( $time_formats as $format ) {
			$this->assertEquals( $format, c2c_BlogTime::get_time_format( $format ) );
		}
	}

	public function test_get_time_format_filtered_via_c2c_blog_time_format() {
		add_filter( 'c2c_blog_time_format', array( $this, 'filter_c2c_blog_time_format' ), 10, 2 );

		$this->assertEquals( 'G:i Y ||| j n', c2c_BlogTime::get_time_format() );
		$this->assertEquals( 'default', $this->incoming_context );
	}

	public function test_get_time_format_explicit_format_ignores_filter_c2c_blog_time_format() {
		add_filter( 'c2c_blog_time_format', array( $this, 'filter_c2c_blog_time_format' ), 10, 2 );

		$format = 'F js Y, H:i:s';

		$this->assertEquals( $format, c2c_BlogTime::get_time_format( $format ) );
	}

	public function test_get_time_format_with_context_for_filter_c2c_blog_time_format() {
		add_filter( 'c2c_blog_time_format', array( $this, 'filter_c2c_blog_time_format' ), 10, 2 );

		$this->assertEquals( 'Y,m,dTH:i:s A', c2c_BlogTime::get_time_format( '', 'widget' ) );
		$this->assertEquals( 'widget', $this->incoming_context );
	}


	/*
	 * c2c_BlogTime::map_php_time_format_to_momentjs()
	 */


	/**
	 * @dataProvider php_to_momentjs_mappings
	 */
	public function test_map_php_time_format_to_momentjs( $mappings ) {
		list( $php, $moment ) = $mappings;

		$this->assertEquals( $moment, c2c_BlogTime::map_php_time_format_to_momentjs( $php ) );
	}

	public function test_map_php_time_format_to_momentjs_ignores_unknown_chars() {
		$str = 'X? CpqQE[];,./0987654321!@#$%^&*()_+-=<>``""';
		$this->assertEquals( $str, c2c_BlogTime::map_php_time_format_to_momentjs( $str ) );
	}

	public function test_map_php_time_format_to_momentjs_doesnt_error_with_blank_format() {
		$this->assertEquals( '', c2c_BLogTime::map_php_time_format_to_momentjs( '' ) );
	}


	/*
	 * TEST TODO:
	 * - JS is enqueued
	 * - CSS is enqueue
	 * - Admin toolbar widget is added (and has necessary data and format)
	 * - filter 'c2c_blog_time_active_clock' outputs (or doesn't output) appropriately
	 */

}
