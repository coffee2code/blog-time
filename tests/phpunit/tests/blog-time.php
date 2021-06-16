<?php

defined( 'ABSPATH' ) or die();

class Blog_Time_Test extends WP_UnitTestCase {

	protected $incoming_time_format = '';
	protected $incoming_context = '';
	protected static $default_time_format  = 'g:i A';
	protected static $setting_name = 'c2c_blog_time';

	public function tearDown() {
		parent::tearDown();

		$this->unset_current_user();
		unregister_setting( 'general', self::$setting_name );

		$this->incoming_time_format = '';
		$this->incoming_context = '';

		unset( $GLOBALS['wp_admin_bar'] );
		unset( $GLOBALS['wp_settings_fields'] );
		unset( $GLOBALS['wp_registered_settings'] );

		unset( $GLOBALS['wp_styles']);
		$GLOBALS['wp_styles'] = new WP_Styles;

		unset( $GLOBALS['wp_scripts'] );
		$GLOBALS['wp_scripts'] = new WP_Scripts;

		wp_dequeue_style( 'c2c_BlogTime' );
		wp_dequeue_script( 'jquery' );
		wp_dequeue_script( 'moment' );
		wp_dequeue_script( 'c2c_BlogTime' );
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
			array( array( 'T', '[' . c2c_BlogTime::display_time( 'T' ) . ']' ) ),
		);
	}


	//
	//
	// HELPER FUNCTIONS
	//
	//


	// Unsets current user globally. Taken from post.php test.
	private function unset_current_user() {
			global $current_user, $user_ID;

			$current_user = $user_ID = null;
	}

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
		$this->assertEquals( '3.6.2', c2c_BlogTime::version() );
	}

	public function test_hooks_plugins_loaded() {
		$this->assertEquals( 10, has_action( 'plugins_loaded', array( 'c2c_BlogTime', 'init' ) ) );
	}

	public function test_hooks_admin_bar_menu() {
		$this->assertEquals( 500, has_action( 'admin_bar_menu', array( 'c2c_BlogTime', 'admin_bar_menu' ) ) );
	}

	public function test_hooks_admin_enqueue_scripts() {
		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', array( 'c2c_BlogTime', 'enqueue_js' ) ) );
	}

	public function test_hooks_wp_enqueue_scripts() {
		$this->assertEquals( 10, has_action( 'wp_enqueue_scripts', array( 'c2c_BlogTime', 'enqueue_js' ) ) );
	}

	public function test_hooks_wp_ajax_report_time() {
		$this->assertEquals( 10, has_action( 'wp_ajax_report_time', array( 'c2c_BlogTime', 'report_time' ) ) );
	}

	public function test_hooks_wp_ajax_nopriv_report_time() {
		$this->assertEquals( 10, has_action( 'wp_ajax_nopriv_report_time', array( 'c2c_BlogTime', 'report_time' ) ) );
	}


	/*
	 * Widget
	 */


	public function test_widget_class_name() {
		$this->assertTrue( class_exists( 'c2c_BlogTimeWidget' ) );
	}

	public function test_widget_version() {
		$this->assertEquals( '008', c2c_BlogTimeWidget::version() );
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


	/*
	 * c2c_blog_time()
	 */


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

	public function test_get_time_format_with_default_time_format_and_default_context() {
		$this->assertEquals( self::$default_time_format, c2c_BlogTime::get_time_format( '', 'default' ) );
	}

	public function test_get_time_format_with_setting_unset_and_raw_context() {
		$this->assertEmpty( c2c_BlogTime::get_time_format( '', 'raw' ) );
	}

	public function test_get_time_format_with_setting_set_and_raw_context() {
		$format = 'F js Y';
		update_option( self::$setting_name, $format );

		$this->assertEquals( $format, c2c_BlogTime::get_time_format( '', 'raw' ) );
	}

	public function test_get_time_format_with_explicit_arg_and_setting_set_and_filtered_and_raw_context() {
		add_filter( 'c2c_blog_time_format', array( $this, 'filter_c2c_blog_time_format' ), 10, 2 );
		$format = 'F js Y';
		update_option( self::$setting_name, $format );

		$this->assertEquals( 'Y s', c2c_BlogTime::get_time_format( 'Y s', 'raw' ) );
	}

	public function test_get_time_format_with_setting_set_and_filtered_and_raw_context() {
		add_filter( 'c2c_blog_time_format', array( $this, 'filter_c2c_blog_time_format' ), 10, 2 );
		$format = 'F js Y';
		update_option( self::$setting_name, $format );

		$this->assertEquals( $format, c2c_BlogTime::get_time_format( '', 'raw' ) );
	}

	public function test_get_time_format_with_default_time_format_and_momentjs_context() {
		$this->assertEquals( 'h:mm A', c2c_BlogTime::get_time_format( '', 'momentjs' ) );
	}

	public function test_get_time_format_with_custom_time_format_and_momentjs_context() {
		$this->assertEquals( 'HH:mm:ss z', c2c_BlogTime::get_time_format( 'H:i:s e', 'momentjs' ) );
	}


	/*
	 * filter: c2c_blog_time_format
	 */


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

	public function test_get_time_format_returns_default_time_format_when_filter_c2c_blog_time_format_returns_empty_string() {
		add_filter( 'c2c_blog_time_format', '__return_empty_string' );

		$this->assertEquals( self::$default_time_format, c2c_BlogTime::get_time_format( '' ) );
	}

	public function test_get_time_format_returns_default_time_format_when_filter_c2c_blog_time_format_returns_empty_array() {
		add_filter( 'c2c_blog_time_format', '__return_empty_array' );

		$this->assertEquals( self::$default_time_format, c2c_BlogTime::get_time_format( '' ) );
	}

	public function test_get_time_format_returns_default_time_format_when_filter_c2c_blog_time_format_returns_boolean_true() {
		add_filter( 'c2c_blog_time_format', '__return_true' );

		$this->assertEquals( self::$default_time_format, c2c_BlogTime::get_time_format( '' ) );
	}

	public function test_get_time_format_returns_default_time_format_when_filter_c2c_blog_time_format_returns_boolean_false() {
		add_filter( 'c2c_blog_time_format', '__return_false' );

		$this->assertEquals( self::$default_time_format, c2c_BlogTime::get_time_format( '' ) );
	}

	public function test_get_time_format_returns_default_time_format_when_filter_c2c_blog_time_format_returns_null() {
		add_filter( 'c2c_blog_time_format', '__return_null' );

		$this->assertEquals( self::$default_time_format, c2c_BlogTime::get_time_format( '' ) );
	}


	/*
	 * c2c_BlogTime::display_time()
	 */


	public function test_display_time() {
		$this->assertEquals(
			date_i18n( c2c_BlogTime::get_time_format(), strtotime( current_time( 'mysql' ) ) ),
			c2c_BlogTime::display_time()
		);
	}

	public function test_display_time_with_explicit_time_format() {
		$time_format = 'm d, Y h:i';

		$this->assertEquals(
			date_i18n( c2c_BlogTime::get_time_format( $time_format ), strtotime( current_time( 'mysql' ) ) ),
			c2c_BlogTime::display_time( $time_format )
		);
	}

	public function test_display_time_with_non_time_format_string() {
		$this->assertEquals(
			'QQQQ',
			c2c_BlogTime::display_time( 'QQQQ' )
		);
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
	 * c2c_BlogTime::add_widget()
	 */


	public function test_add_widget() {
		$expected = '/<span class="c2c-blog-time-widget"><span class="ab-icon"><\/span><span class="c2c-blog-time-widget-display ab-label c2c-blog-time-dynamic"><a class="ab-item" href="" title="Click to refresh blog time">';
		$expected .= '(1?[0-9]:[0-5][0-9] [AP]M)';
		$expected .= '<\/a><\/span><\/span>' . "\\n/";

		$this->assertRegExp( $expected, c2c_BlogTime::add_widget() );
	}

	public function test_filter_c2c_blog_time_active_clock() {
		add_filter( 'c2c_blog_time_active_clock', '__return_false' );

		$expected = '/<span class="c2c-blog-time-widget"><span class="ab-icon"><\/span><span class="c2c-blog-time-widget-display ab-label "><a class="ab-item" href="" title="Click to refresh blog time">';
		$expected .= '(1?[0-9]:[0-5][0-9] [AP]M)';
		$expected .= '<\/a><\/span><\/span>' . "\\n/";

		$this->assertRegExp( $expected, c2c_BlogTime::add_widget() );
	}


	/*
	 * c2c_BlogTime::show_in_toolbar_for_user()
	 */


	public function test_show_in_toolbar_for_user() {
		add_filter( 'show_admin_bar', '__return_true' );

		$this->assertTrue( c2c_BlogTime::show_in_toolbar_for_user() );
	}

	public function test_show_in_toolbar_for_user_when_admin_bar_not_showing() {
		add_filter( 'show_admin_bar', '__return_false' );

		$this->assertFalse( c2c_BlogTime::show_in_toolbar_for_user() );
	}


	public function test_filter_c2c_blog_time_toolbar_widget_for_user() {
		add_filter( 'show_admin_bar', '__return_true' );
		add_filter( 'c2c_blog_time_toolbar_widget_for_user', '__return_false' );

		$this->assertFalse( c2c_BlogTime::show_in_toolbar_for_user() );
	}


	/*
	 * enqueue_js()
	 */

	public function test_enqueue_js_when_admin_bar_is_showing( $momentjs_time_format = 'h:mm A' ) {
		$this->test_show_in_toolbar_for_user();

		c2c_BlogTime::enqueue_js();

		$this->assertTrue( wp_style_is( 'c2c_BlogTime', 'enqueued' ) );
		$this->assertTrue( wp_script_is( 'jquery', 'enqueued' ) );
		$this->assertTrue( wp_script_is( 'moment', 'enqueued' ) );
		$this->assertTrue( wp_script_is( 'c2c_BlogTime', 'enqueued' ) );

		$inline_scripts = wp_scripts()->get_data( 'c2c_BlogTime', 'before' );

		$this->assertIsArray( $inline_scripts );
		$this->assertEquals( 2, count( $inline_scripts ) );
		$this->assertEquals(
			'const c2c_BlogTime = {"ajaxurl":"http:\/\/example.org\/wp-admin\/admin-ajax.php","time_format":"' . $momentjs_time_format . '","utc_offset":"+0000"}',
			$inline_scripts[1]
		);
		$this->assertEmpty( wp_scripts()->get_data( 'c2c_BlogTime', 'after' ) );
	}

	public function test_enqueue_js_when_admin_bar_is_not_showing() {
		$this->test_show_in_toolbar_for_user_when_admin_bar_not_showing();

		c2c_BlogTime::enqueue_js();

		$this->assertFalse( wp_style_is( 'c2c_BlogTime', 'enqueued' ) );
		$this->assertFalse( wp_script_is( 'jquery', 'enqueued' ) );
		$this->assertFalse( wp_script_is( 'moment', 'enqueued' ) );
		$this->assertFalse( wp_script_is( 'c2c_BlogTime', 'enqueued' ) );

		$this->assertEmpty( wp_scripts()->get_data( 'c2c_BlogTime', 'before' ) );
		$this->assertEmpty( wp_scripts()->get_data( 'c2c_BlogTime', 'after' ) );
	}

	public function test_enqueue_js_when_admin_bar_is_not_showing_but_force_is_true() {
		$this->test_show_in_toolbar_for_user_when_admin_bar_not_showing();

		c2c_BlogTime::enqueue_js( true );

		$this->assertTrue( wp_style_is( 'c2c_BlogTime', 'enqueued' ) );
		$this->assertTrue( wp_script_is( 'jquery', 'enqueued' ) );
		$this->assertTrue( wp_script_is( 'moment', 'enqueued' ) );
		$this->assertTrue( wp_script_is( 'c2c_BlogTime', 'enqueued' ) );

		$inline_scripts = wp_scripts()->get_data( 'c2c_BlogTime', 'before' );

		$this->assertIsArray( $inline_scripts );
		$this->assertEquals( 2, count( $inline_scripts ) );
		$this->assertEquals(
			'const c2c_BlogTime = {"ajaxurl":"http:\/\/example.org\/wp-admin\/admin-ajax.php","time_format":"h:mm A","utc_offset":"+0000"}',
			$inline_scripts[1]
		);
		$this->assertEmpty( wp_scripts()->get_data( 'c2c_BlogTime', 'after' ) );
	}

	public function test_enqueue_js_added_inline_script_reflects_momentjs_formatted_current_blog_time_format() {
		add_filter( 'c2c_blog_time_format', function ( $f ) { return 'H:i:s A F'; } );

		$this->test_enqueue_js_when_admin_bar_is_showing( 'HH:mm:ss A MMMM' );
	}


	/*
	 * admin_bar_menu()
	 */


	public function test_admin_bar_menu_renders() {
		global $wp_admin_bar;

		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		add_filter( 'show_admin_bar', '__return_true' );
		_wp_admin_bar_init();

		//do_action( 'admin_bar_menu' );
		//c2c_BlogTime::admin_bar_menu();

		ob_start();

		// Reproduce necessary parts of `wp_admin_bar_render()`, which only
		// renders once.
		do_action_ref_array( 'admin_bar_menu', array( &$wp_admin_bar ) );
		do_action( 'wp_before_admin_bar_render' );
		$wp_admin_bar->render();
		do_action( 'wp_after_admin_bar_render' );

		$output = ob_get_contents();

		ob_end_clean();

		$this->assertGreaterThan( 0, strpos( $output, '<span class="c2c-blog-time-widget">' ) );
	}

	// Note: This essentially obtains the formatted time via two separate
	// function calls which are then compared, so depending on the time
	// format in use (such as seconds, which isn't the current default) or
	// the actual moment of invocation, the test could fail if the time/date
	// happens to change in the microseconds between function calls.
	public function test_admin_bar_menu() {
		global $wp_admin_bar;

		require_once ABSPATH . WPINC . '/class-wp-admin-bar.php';
		$wp_admin_bar = new WP_Admin_Bar;

		$expected = (object) array(
			'id'     => 'c2c-blog-time',
			'parent' => 'top-secondary',
			'title'  => c2c_BlogTime::add_widget(),
			'meta'   => array( 'class' => '', 'title' => __( 'Current blog time', 'blog-time' ) ),
			'href'   => false,
			'group'  => false,
		);

		c2c_BlogTime::admin_bar_menu();

		$this->assertEquals( $expected, $wp_admin_bar->get_node( 'c2c-blog-time' ) );
	}


	/*
	 * report_time()
	 */

	// Note: This essentially obtains the formatted time via two separate
	// function calls which are then compared, so depending on the time
	// format in use (such as seconds, which isn't the current default) or
	// the actual moment of invocation, the test could fail if the time/date
	// happens to change in the microseconds between function calls.
	public function test_report_time() {
		$this->expectOutputRegex( '~^' . preg_quote( c2c_BlogTime::display_time( '', 'default' ) ) . '$~', c2c_BlogTime::report_time( false ) );
	}

	public function test_report_time_with_custom_format() {
		$format = 'H:i:s A F';
		add_filter( 'c2c_blog_time_format', function ( $f ) use ( $format ) { return $format; } );

		$this->expectOutputRegex( '~^' . preg_quote( c2c_BlogTime::display_time( $format ) ) . '$~', c2c_BlogTime::report_time( false ) );
	}


	/*
	 * is_wp_55_or_later()
	 */


	public function test_is_wp_55_or_later_when_wp_is_lower() {
		global $wp_version;
		$orig_wp_verion = $wp_version;
		$wp_version = '5.4.2';

		$this->assertFalse( c2c_BlogTime::is_wp_55_or_later() );

		$wp_version = $orig_wp_verion;
	}

	public function test_is_wp_55_or_later() {
		global $wp_version;
		$orig_wp_verion = $wp_version;
		$wp_version = '5.5.0';

		$this->assertTrue( c2c_BlogTime::is_wp_55_or_later() );

		$wp_version = $orig_wp_verion;
	}


	/*
	 * plugin_action_links()
	 */


	public function test_plugin_action_links() {
		$links = array(
			'<a href="/test">Test</a>',
		);

		$expected = array_merge( array(
			'<a href="http://example.org/wp-admin/options-general.php#c2c_blog_time">Settings</a>',
		), $links );

		$this->assertEquals( $expected, c2c_BlogTime::plugin_action_links( $links ) );
	}

	public function test_plugin_action_links_does_not_get_hooked_when_setting_is_not_initialized() {
		$this->test_initialize_setting_does_not_register_setting_for_user_who_cannot_manage_options();

		$this->assertEmpty( has_filter( 'plugin_action_links_blog-time/blog-time.php', array( 'c2c_BlogTime', 'plugin_action_links' ) ) );
	}

	public function test_plugin_action_links_gets_hooked_when_setting_is_initialized() {
		$this->test_initialize_setting_registers_setting_for_user_who_can_manage_options();

		$this->assertEquals( 10, has_filter( 'plugin_action_links_blog-time/blog-time.php', array( 'c2c_BlogTime', 'plugin_action_links' ) ) );
	}

	/*
	 * initialize_setting()
	 */

	public function test_initialize_setting_does_not_register_setting_for_user_who_cannot_manage_options() {
		c2c_BlogTime::initialize_setting();
		$this->assertArrayNotHasKey( self::$setting_name, get_registered_settings() );
	}

	public function test_initialize_setting_registers_setting_for_user_who_can_manage_options() {
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		c2c_BlogTime::initialize_setting();

		$this->assertArrayHasKey( self::$setting_name, get_registered_settings() );
	}

	public function test_does_not_hook_allowed_options_for_unauthorized_user_for_post_WP55() {
		$user_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $user_id );

		global $wp_version;
		$orig_wp_verion = $wp_version;
		$wp_version = '5.5';

		c2c_BlogTime::initialize_setting();

		$this->assertFalse( has_filter( 'allowed_options', array( 'c2c_BlogTime', 'allowed_options' ) ) );

		$wp_version = $orig_wp_verion;
	}

	public function test_hooks_allowed_options_for_authorized_user_for_post_WP55() {
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		global $wp_version;
		$orig_wp_verion = $wp_version;
		$wp_version = '5.5';

		c2c_BlogTime::initialize_setting();

		$this->assertEquals( 10, has_filter( 'allowed_options', array( 'c2c_BlogTime', 'allowed_options' ) ) );

		$wp_version = $orig_wp_verion;
	}

	public function test_does_not_hook_whitelist_options_for_unauthorized_user_for_pre_WP55() {
		$user_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $user_id );

		global $wp_version;
		$orig_wp_verion = $wp_version;
		$wp_version = '5.4.2';

		c2c_BlogTime::initialize_setting();

		$this->assertFalse( has_filter( 'whitelist_options', array( 'c2c_BlogTime', 'allowed_options' ) ) );

		$wp_version = $orig_wp_verion;
	}

	public function test_hooks_whitelist_options_for_authorized_user_for_pre_WP55() {
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		global $wp_version;
		$orig_wp_verion = $wp_version;
		$wp_version = '5.4.2';

		c2c_BlogTime::initialize_setting();

		$this->assertEquals( 10, has_filter( 'whitelist_options', array( 'c2c_BlogTime', 'allowed_options' ) ) );

		$wp_version = $orig_wp_verion;
	}

	public function test_does_not_add_setting_field_for_unauthorized_user() {
		$user_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $user_id );
		c2c_BlogTime::initialize_setting();

		$this->assertFalse( isset( $GLOBALS['wp_settings_fields'] ) );
	}

	public function test_adds_setting_field_for_authorized_user() {
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		c2c_BlogTime::initialize_setting();

		$expected = array( 'general' => array(
			'default' => array(
				'c2c_blog_time' => array(
					'id'       => 'c2c_blog_time',
					'title'    => 'Blog Time Format',
					'callback' => array( 'c2c_BlogTime', 'display_option' ),
					'args'     => array(),
				),
			),
		) );

		$this->assertSame( $expected, $GLOBALS['wp_settings_fields'] );
	}


	/*
	 * allowed_options()
	 */


	public function test_allowed_options() {
		$this->assertSame(
			array( 'example' => array( 'sample' ), 'c2c_blog_time' => array( 'c2c_blog_time' ) ),
			c2c_BlogTime::allowed_options( array( 'example' => array( 'sample' ) ) )
		);
	}


	/*
	 * display_option()
	 */


	public function test_display_option_with_no_saved_setting_value() {
		c2c_BlogTime::display_option();

		$expected = '<input name="c2c_blog_time" type="text" id="c2c_blog_time" value="" class="short-text">' . "\n";
		$expected .= '<p class="description">Used by the <strong>Blog Time</strong> plugin. See <a href="https://www.php.net/manual/en/datetime.format.php">Documentation on date and time formatting</a> for formatting syntax.</p>' . "\n";
		$expected .= '<p class="blog-time-info">Default (used when setting is blank): <code>' . self::$default_time_format . '</code></p>' . "\n";

		$this->expectOutputRegex( '~^' . preg_quote( $expected ) . '$~' );
	}

	public function test_display_option_with_saved_setting_value() {
		$value = 'H:i:s T';
		update_option( 'c2c_blog_time', $value );
		c2c_BlogTime::display_option();

		$expected = '<input name="c2c_blog_time" type="text" id="c2c_blog_time" value="' . $value . '" class="short-text">' . "\n";
		$expected .= '<p class="description">Used by the <strong>Blog Time</strong> plugin. See <a href="https://www.php.net/manual/en/datetime.format.php">Documentation on date and time formatting</a> for formatting syntax.</p>' . "\n";
		$expected .= '<p class="blog-time-info">Default (used when setting is blank): <code>' . self::$default_time_format . '</code></p>' . "\n";

		$this->expectOutputRegex( '~^' . preg_quote( $expected ) . '$~' );
	}

	public function test_display_option_with_no_saved_setting_value_and_filtered() {
		$filtered_format = 'T s:i:h';
		add_filter( 'c2c_blog_time_format', function ( $format ) use ( $filtered_format ) { return $filtered_format; } );

		c2c_BlogTime::display_option();

		$expected = '<input name="c2c_blog_time" type="text" id="c2c_blog_time" value="" class="short-text">' . "\n";
		$expected .= '<p class="description">Used by the <strong>Blog Time</strong> plugin. See <a href="https://www.php.net/manual/en/datetime.format.php">Documentation on date and time formatting</a> for formatting syntax.</p>' . "\n";
		$expected .= '<p class="blog-time-info">Default (used when setting is blank): <code>' . self::$default_time_format . '</code></p>' . "\n";
		$expected .= '<p class="c2c_blog_time blog-time-info notice notice-warning">The blog time format is currently configured via the \'c2c_blog_time_format\' filter, which takes precedence over this setting. The filtered blog time format value is: <code>' . $filtered_format . '</code></p>' . "\n";

		$this->expectOutputRegex( '~^' . preg_quote( $expected ) . '$~' );
	}

	public function test_display_option_with_saved_setting_value_and_filtered() {
		$filtered_format = 'T s:i:h';
		add_filter( 'c2c_blog_time_format', function ( $format ) use ( $filtered_format ) { return $filtered_format; } );

		$value = 'H:i:s T';
		update_option( 'c2c_blog_time', $value );
		c2c_BlogTime::display_option();

		$expected = '<input name="c2c_blog_time" type="text" id="c2c_blog_time" value="' . $value . '" class="short-text">' . "\n";
		$expected .= '<p class="description">Used by the <strong>Blog Time</strong> plugin. See <a href="https://www.php.net/manual/en/datetime.format.php">Documentation on date and time formatting</a> for formatting syntax.</p>' . "\n";
		$expected .= '<p class="blog-time-info">Default (used when setting is blank): <code>' . self::$default_time_format . '</code></p>' . "\n";
		$expected .= '<p class="c2c_blog_time blog-time-info notice notice-warning">The blog time format is currently configured via the \'c2c_blog_time_format\' filter, which takes precedence over this setting. The filtered blog time format value is: <code>' . $filtered_format . '</code></p>' . "\n";

		$this->expectOutputRegex( '~^' . preg_quote( $expected ) . '$~' );
	}


	/*
	 * TEST TODO:
	 * - JS is enqueued
	 * - CSS is enqueue
	 * - Admin toolbar widget is added (and has necessary data and format)
	 * - filter 'c2c_blog_time_active_clock' outputs (or doesn't output) appropriately
	 */

}
