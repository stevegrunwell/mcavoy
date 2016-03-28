<?php
/**
 * Tests for the base Logger class.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy\Loggers;

use McAvoy;
use Mockery;
use ReflectionMethod;
use WP_Mock as M;

require_once PROJECT . 'loggers/class-logger.php';
require_once ABSPATH . 'TestLogger.php';

class BaseLoggerTest extends McAvoy\TestCase {

	public function test_init() {
		$instance = Mockery::mock( __NAMESPACE__ . '\TestLogger' )
			->shouldAllowMockingProtectedMethods()
			->makePartial();
		$instance->shouldReceive( 'add_hooks' )->once();

		$instance->init();
	}

	public function test_add_hooks() {
		$instance = new TestLogger;
		$method   = new ReflectionMethod( $instance, 'add_hooks' );
		$method->setAccessible( true );

		M::expectActionAdded( 'mcavoy_save_search_query', array( $instance, 'save_query' ), 10, 2 );

		$method->invoke( $instance );
	}

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
