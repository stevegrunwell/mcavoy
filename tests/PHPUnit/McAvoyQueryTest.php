<?php
/**
 * Tests for the McAvoy_Query class.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy;

use ReflectionProperty;
use WP_Mock as M;

class McAvoyQueryTest extends TestCase {

	protected $testFiles = array(
		'class-mcavoy-query.php',
	);

	public function test__construct() {
		$items    = array( 'foo', 'bar' );
		$instance = new McAvoy_Query( $items, array( 'args' ), 5 );

		$items_prop = new ReflectionProperty( $instance, 'items' );
		$items_prop->setAccessible( true );
		$this->assertEquals( $items, $items_prop->getValue( $instance ) );

		$args_prop = new ReflectionProperty( $instance, 'query_args' );
		$args_prop->setAccessible( true );
		$this->assertEquals( array( 'args' ), $args_prop->getValue( $instance ) );

		$found_prop = new ReflectionProperty( $instance, 'found' );
		$found_prop->setAccessible( true );
		$this->assertEquals( 5, $found_prop->getValue( $instance ) );
	}

	public function test__construct_sets_default_found() {
		$instance = new McAvoy_Query( array( 'foo', 'bar' ) );

		$found_prop = new ReflectionProperty( $instance, 'found' );
		$found_prop->setAccessible( true );
		$this->assertEquals( 2, $found_prop->getValue( $instance ) );
	}

}
