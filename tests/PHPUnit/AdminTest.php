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
use Patchwork as P;
use WP_Mock as M;

class AdminTest extends McAvoy\TestCase {

	protected $testFiles = array(
		'admin.php',
		'class-list-table.php',
		'core.php',
	);

	public function test_search_page_callback() {
		P\replace('McAvoy\ListTable::prepare_items', function () {} );
		P\replace('McAvoy\ListTable::display', function () {} );

		M::userFunction( __NAMESPACE__ . '\maybe_delete_queries', array(
			'times'  => 1,
		) );

		M::userFunction( 'get_bloginfo', array(
			'times'  => 1,
			'args'   => 'name',
			'return' => 'BLOG_NAME',
		) );

		M::userFunction( 'current_user_can', array(
			'times'  => 1,
			'args'   => array( 'mcavoy_delete_queries' ),
			'return' => true,
		) );

		M::userFunction( 'wp_nonce_field', array(
			'times'  => 1,
			'args'   => array( 'delete-queries', 'mcavoy-nonce' ),
		) );

		M::passthruFunction( '__' );
		M::passthruFunction( 'esc_html_e' );
		M::passthruFunction( 'esc_html' );
		M::passthruFunction( 'esc_attr_e' );

		ob_start();
		search_page_callback();
		$result = ob_get_contents();
		ob_end_clean();

		$this->assertContains( '<div class="wrap">', $result );

		P\undoAll();
	}

	public function test_search_page_callback_checks_delete_cap() {
		P\replace('McAvoy\ListTable::prepare_items', function () {} );
		P\replace('McAvoy\ListTable::display', function () {} );

		M::userFunction( __NAMESPACE__ . '\maybe_delete_queries', array(
			'times'  => 1,
		) );

		M::userFunction( 'get_bloginfo', array(
			'return' => 'BLOG_NAME',
		) );

		M::userFunction( 'current_user_can', array(
			'times'  => 1,
			'args'   => array( 'mcavoy_delete_queries' ),
			'return' => false,
		) );

		M::passthruFunction( '__' );
		M::passthruFunction( 'esc_html_e' );
		M::passthruFunction( 'esc_html' );

		ob_start();
		search_page_callback();
		$result = ob_get_contents();
		ob_end_clean();

		$this->assertNotContains( '<form method="POST" id="mcavoy-delete-queries">', $result );

		P\undoAll();
	}

	public function test_maybe_delete_queries() {
		$_POST['mcavoy-nonce'] = 'foo';

		$logger = Mockery::mock( 'McAvoy\Loggers\TestLogger' )->makePartial();
		$logger->shouldReceive( 'delete_queries' )->once();

		M::userFunction( 'wp_verify_nonce', array(
			'times'  => 1,
			'args'   => array( 'foo', 'delete-queries' ),
			'return' => true,
		) );

		M::userFunction( 'current_user_can', array(
			'times'  => 1,
			'args'   => array( 'mcavoy_delete_queries' ),
			'return' => true,
		) );

		M::userFunction( 'McAvoy\get_logger', array(
			'times'  => 1,
			'return' => $logger,
		) );

		M::passthruFunction( 'esc_html__' );

		ob_start();
		maybe_delete_queries();
		$result = ob_get_contents();
		ob_end_clean();

		$this->assertTrue( false !== strpos( $result, 'notice-success' ) );

		unset( $_POST['mcavoy-nonce'] );
	}

	public function test_maybe_delete_queries_checks_nonce() {

		// $_POST['mcavoy-nonce'] hasn't been set.
		$this->assertNull( maybe_delete_queries() );

		$_POST['mcavoy-nonce'] = 'foo';

		M::userFunction( 'wp_verify_nonce', array(
			'times'  => 1,
			'args'   => array( 'foo', 'delete-queries' ),
			'return' => false,
		) );

		// If the nonce doesn't check out, we should still be coming back empty-handed.
		$this->assertNull( maybe_delete_queries() );

		unset( $_POST['mcavoy-nonce'] );
	}

	public function test_maybe_delete_queries_checks_current_user_caps() {
		$_POST['mcavoy-nonce'] = 'foo';

		M::userFunction( 'wp_verify_nonce', array(
			'return' => true,
		) );

		M::userFunction( 'current_user_can', array(
			'times'  => 1,
			'args'   => array( 'mcavoy_delete_queries' ),
			'return' => false,
		) );

		$this->assertNull( maybe_delete_queries() );

		unset( $_POST['mcavoy-nonce'] );
	}
}
