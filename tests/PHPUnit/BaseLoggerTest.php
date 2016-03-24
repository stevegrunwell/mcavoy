<?php
/**
 * Tests for the base Logger class.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy\Loggers;

use McAvoy;
use ReflectionMethod;
use WP_Mock as M;

require_once PROJECT . 'class-logger.php';
require_once ABSPATH . 'TestLogger.php';

class BaseLoggerTest extends McAvoy\TestCase {

	public function test_get_args() {
		$instance = new TestLogger;
		$method   = new ReflectionMethod( $instance, 'get_args' );
		$method->setAccessible( true );

		M::wpFunction( 'wp_parse_args', array(
			'times'  => 1,
			'args'   => array(
				array( 'foo', 'bar' ),
				array(
					'limit'   => 50,
					'page'    => 1,
					'orderby' => 'created_at',
					'order'   => 'desc',
				),
			),
			'return' => 'PARSED',
		) );

		$this->assertEquals( 'PARSED', $method->invoke( $instance, array( 'foo', 'bar' ) ) );
	}

}
