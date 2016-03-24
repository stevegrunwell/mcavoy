<?php
/**
 * Tests for the plugin's logger.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy;

use WP_Mock as M;

class LoggerTest extends TestCase {

	protected $testFiles = array(
		'logger.php',
	);

	public function test_capture_search_query() {
		$this->markTestSkipped( 'Namespace conflicts with WP_Mock that need solving.' );
		M::wpFunction( 'is_search', array(
			'times'  => 1,
			'return' => true,
		) );

		M::wpFunction( 'is_paged', array(
			'times'  => 1,
			'return' => false,
		) );

		M::wpFunction( 'get_query_var', array(
			'times'  => 1,
			'args'   => array( 's', false ),
			'return' => 'foo',
		) );

		M::wpFunction( __NAMESPACE__ . '\prepare_query_metadata', array(
			'times'  => 1,
			'return' => array( 'meta', 'data' ),
		) );

		M::wpFunction( __NAMESPACE__ . '\save_query', array(
			'times'  => 1,
			'args'   => array( 'foo', array( 'meta', 'data' ) ),
			'return' => true,
		) );

		capture_search_query();
	}

	public function test_capture_search_query_checks_if_is_search() {
		M::wpFunction( 'is_search', array(
			'times'  => 1,
			'return' => false,
		) );

		M::wpFunction( 'is_paged', array(
			'times'  => 0,
		) );

		M::wpFunction( 'get_query_var', array(
			'times'  => 0,
		) );

		capture_search_query();
	}

	// We're not concerned about logging a search query if it's page 2+ of results.
	public function test_capture_search_query_checks_if_is_paged() {
		M::wpFunction( 'is_search', array(
			'times'  => 1,
			'return' => true,
		) );

		M::wpFunction( 'is_paged', array(
			'times'  => 1,
			'return' => true,
		) );

		M::wpFunction( 'get_query_var', array(
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
			'ip_address' => $server['REMOTE_ADDR'],
			'referrer'   => 'http://example.com',
			'user_agent' => $server['HTTP_USER_AGENT'],
			'results'    => 5,
		);
		$backup   = $_SERVER;
		$wp_query = new \stdClass;
		$wp_query->found_posts = 5;

		M::wpFunction( 'wp_get_referer', array(
			'times'  => 1,
			'return' => 'http://example.com',
		) );

		M::wpPassthruFunction( 'sanitize_text_field', array(
			'times' => 1,
			'args'  => array( $server['REMOTE_ADDR'] ),
		) );

		M::wpPassthruFunction( 'sanitize_text_field', array(
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

	public function test_save_search_query() {
		$term = uniqid();
		$meta = array( uniqid() );

		M::expectAction( 'mcavoy_save_search_query', $term, $meta );

		save_search_query( $term, $meta );
	}

}
