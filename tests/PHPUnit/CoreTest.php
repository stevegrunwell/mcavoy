<?php
/**
 * Tests for the plugin's core functionality.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy;

use WP_Mock as M;

class CoreTest extends TestCase {

	protected $testFiles = array(
		'core.php',
	);

	public function test_capture_search_query() {
		M::userFunction( 'is_search', array(
			'times'  => 1,
			'return' => true,
		) );

		M::userFunction( 'is_paged', array(
			'times'  => 1,
			'return' => false,
		) );

		M::userFunction( 'get_query_var', array(
			'times'  => 1,
			'args'   => array( 's', false ),
			'return' => 'foo',
		) );

		M::userFunction( __NAMESPACE__ . '\prepare_query_metadata', array(
			'times'  => 1,
			'return' => array( 'meta', 'data' ),
		) );

		M::userFunction( __NAMESPACE__ . '\save_search_query', array(
			'times'  => 1,
			'args'   => array( 'foo', array( 'meta', 'data' ) ),
			'return' => true,
		) );

		capture_search_query();
	}

	public function test_capture_search_query_checks_if_is_search() {
		M::userFunction( 'is_search', array(
			'times'  => 1,
			'return' => false,
		) );

		M::userFunction( 'is_paged', array(
			'times'  => 0,
		) );

		M::userFunction( 'get_query_var', array(
			'times'  => 0,
		) );

		capture_search_query();
	}

	// We're not concerned about logging a search query if it's page 2+ of results.
	public function test_capture_search_query_checks_if_is_paged() {
		M::userFunction( 'is_search', array(
			'times'  => 1,
			'return' => true,
		) );

		M::userFunction( 'is_paged', array(
			'times'  => 1,
			'return' => true,
		) );

		M::userFunction( 'get_query_var', array(
			'times'  => 0,
		) );

		capture_search_query();
	}


	public function test_prepare_query_metadata() {
		global $wp_query;

		$server   = array(
			'REMOTE_ADDR'     => '192.168.1.1',
			'HTTP_USER_AGENT' => 'Chrome, I guess?',
		);
		$expected = array(
			'ip_address'   => $server['REMOTE_ADDR'],
			'referrer'     => 'http://example.com',
			'user_agent'   => $server['HTTP_USER_AGENT'],
			'results'      => 5,
			'current_user' => 'foobar',
		);
		$backup   = $_SERVER;
		$wp_query = new \stdClass;
		$wp_query->found_posts = 5;

		$user = new \stdClass;
		$user->ID         = 1;
		$user->user_login = 'foobar';

		M::userFunction( 'wp_get_current_user', array(
			'times'  => 1,
			'return' => $user,
		) );

		M::userFunction( 'wp_get_referer', array(
			'times'  => 1,
			'return' => 'http://example.com',
		) );

		M::passthruFunction( 'sanitize_text_field', array(
			'times' => 1,
			'args'  => array( $server['REMOTE_ADDR'] ),
		) );

		M::passthruFunction( 'sanitize_text_field', array(
			'times' => 1,
			'args'  => array( $server['HTTP_USER_AGENT'] ),
		) );

		M::onFilter( 'mcavoy_prepare_query_metadata' )
			->with( $expected )
			->reply( 'filtered' );

		// Add our values to the $_SERVER superglobal.
		$_SERVER = array_merge( $_SERVER, $server );

		// Execute the function.
		$this->assertEquals( 'filtered', prepare_query_metadata() );

		// Restore our backup of the superglobal.
		$_SERVER = $backup;
		$wp_query = null;
	}

	public function test_prepare_query_metadata_checks_user_id() {
		global $wp_query;

		$server   = array(
			'REMOTE_ADDR'     => '',
			'HTTP_USER_AGENT' => '',
		);
		$backup   = $_SERVER;
		$wp_query = new \stdClass;
		$wp_query->found_posts = 5;

		$user = new \stdClass;
		$user->ID         = 0;

		M::userFunction( 'wp_get_current_user', array(
			'times'  => 1,
			'return' => $user,
		) );

		M::userFunction( 'wp_get_referer', array(
			'return' => 'http://example.com',
		) );

		M::passthruFunction( 'sanitize_text_field' );

		// Add our values to the $_SERVER superglobal.
		$_SERVER = array_merge( $_SERVER, $server );

		$response = prepare_query_metadata();
		$this->assertEmpty( $response['current_user'] );

		$_SERVER  = $backup;
		$wp_query = null;
	}

	public function test_save_search_query() {
		$term = uniqid();
		$meta = array( uniqid() );

		M::passthruFunction( 'sanitize_text_field', array(
			'times'  => 1,
			'args'   => $term,
		) );
		M::expectAction( 'mcavoy_save_search_query', $term, $meta );

		save_search_query( $term, $meta );
	}

	public function test_save_search_query_sanitizes_value() {
		$term = '<a href="#" onclick="doEvilStuff">XSS link</a>';
		$meta = array( uniqid() );

		M::userFunction( 'sanitize_text_field', array(
			'times'  => 1,
			'args'   => array( $term ),
			'return' => 'XSS link',
		) );

		M::expectAction( 'mcavoy_save_search_query', 'XSS link', $meta );

		save_search_query( $term, $meta );
	}

}
