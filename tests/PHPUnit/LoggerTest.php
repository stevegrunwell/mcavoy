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

	public function test_prepare_query_metadata() {
		$server   = array(
			'REMOTE_ADDR'     => '192.168.1.1',
			'HTTP_USER_AGENT' => 'Chrome, I guess?',
		);
		$expected = array(
			'ip_address' => $server['REMOTE_ADDR'],
			'user_agent' => $server['HTTP_USER_AGENT'],
		);
		$backup   = $_SERVER;

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
	}

}
