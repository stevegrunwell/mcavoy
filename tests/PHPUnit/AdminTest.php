<?php
/**
 * Tests for the plugin's admin UI.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy\Admin;

use McAvoy;
use Mockery;
use WP_Mock as M;

class AdminTest extends McAvoy\TestCase {

	protected $testFiles = array(
		'admin.php',
		'core.php',
	);

	public function test_search_page_callback() {
		M::wpFunction( __NAMESPACE__ . '\maybe_delete_queries', array(
			'times'  => 1,
		) );

		M::wpFunction( 'get_bloginfo', array(
			'times'  => 1,
			'args'   => 'name',
			'return' => 'BLOG_NAME',
		) );

		M::wpFunction( 'current_user_can', array(
			'times'  => 1,
			'args'   => array( 'mcavoy_delete_queries' ),
			'return' => true,
		) );

		M::wpFunction( 'wp_nonce_field', array(
			'times'  => 1,
			'args'   => array( 'delete-queries', 'mcavoy-nonce' ),
		) );

		M::wpPassthruFunction( '__' );
		M::wpPassthruFunction( 'esc_html_e' );
		M::wpPassthruFunction( 'esc_html' );
		M::wpPassthruFunction( 'esc_attr_e' );

		ob_start();
		search_page_callback();
		$result = ob_get_contents();
		ob_end_clean();

		$this->assertContains( '<div class="wrap">', $result );
	}

	public function test_search_page_callback_checks_delete_cap() {
		M::wpFunction( __NAMESPACE__ . '\maybe_delete_queries', array(
			'times'  => 1,
		) );

		M::wpFunction( 'get_bloginfo', array(
			'return' => 'BLOG_NAME',
		) );

		M::wpFunction( 'current_user_can', array(
			'times'  => 1,
			'args'   => array( 'mcavoy_delete_queries' ),
			'return' => false,
		) );

		M::wpPassthruFunction( '__' );
		M::wpPassthruFunction( 'esc_html_e' );
		M::wpPassthruFunction( 'esc_html' );

		ob_start();
		search_page_callback();
		$result = ob_get_contents();
		ob_end_clean();

		$this->assertNotContains( '<form method="POST" id="mcavoy-delete-queries">', $result );
	}

	public function test_maybe_delete_queries() {
		$_POST['mcavoy-nonce'] = 'foo';

		$logger = Mockery::mock( 'McAvoy\Loggers\TestLogger' )->makePartial();
		$logger->shouldReceive( 'delete_queries' )->once();

		M::wpFunction( 'wp_verify_nonce', array(
			'times'  => 1,
			'args'   => array( 'foo', 'delete-queries' ),
			'return' => true,
		) );

		M::wpFunction( 'current_user_can', array(
			'times'  => 1,
			'args'   => array( 'mcavoy_delete_queries' ),
			'return' => true,
		) );

		M::wpFunction( 'McAvoy\get_logger', array(
			'times'  => 1,
			'return' => $logger,
		) );

		M::wpPassthruFunction( 'esc_html__' );

		ob_start();
		maybe_delete_queries();
		$result = ob_get_contents();
		ob_end_clean();

		$this->assertTrue( false !== strpos( $result, 'notice-success' ) );

		unset( $_POST['mcavoy-nonce'] );
	}
}
