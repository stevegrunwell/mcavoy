<?php
/**
 * Tests for the custom WP_List_Table implementation.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy;

use Mockery;
use ReflectionMethod;
use ReflectionProperty;
use WP_Mock as M;

class ListTableTest extends TestCase {

	protected $testFiles = array(
		'class-list-table.php',
	);

	public function test_get_columns() {
		$keys = array( 'created_at', 'term' );

		M::wpPassthruFunction( '_x', array(
			'times'  => count( $keys ),
		) );

		$instance = new ListTable;
		$result   = $instance->get_columns();

		foreach ( $keys as $key ) {
			$this->assertNotEmpty( $result[ $key ] );
		}
	}

	public function test_column_default() {
		$instance = new ListTable;
		$method   = new ReflectionMethod( $instance, 'column_default' );
		$method->setAccessible( true );

		$this->assertNull( $method->invoke( $instance, null, 'a-column-that-does-not-exist' ) );
	}

	public function test_column_default_created_at() {
		$instance = Mockery::mock( __NAMESPACE__ . '\ListTable' )
			->shouldAllowMockingProtectedMethods()
			->makePartial();
		$instance->shouldReceive( 'get_datetime_format' )->once()->andReturn( 'FORMAT' );
		$method   = new ReflectionMethod( $instance, 'column_default' );
		$method->setAccessible( true );
		$item     = new \stdClass;
		$item->created_at = 'DATE';

		M::wpFunction( 'date_i18n', array(
			'times'  => 1,
			'args'   => array( 'FORMAT', 0 ),
			'return' => 'i18n_TIME',
		) );

		M::wpFunction( 'get_date_from_gmt', array(
			'times'  => 1,
			'args'   => array( 'DATE' ),
			'return' => 'GMT_DATE',
		) );

		$this->assertEquals( 'i18n_TIME', $method->invoke( $instance, $item, 'created_at' ) );
	}

	public function test_column_term() {
		$instance = new ListTable;
		$method   = new ReflectionMethod( $instance, 'column_default' );
		$method->setAccessible( true );
		$item     = new \stdClass;
		$item->term = uniqid();

		$this->assertEquals( $item->term, $method->invoke( $instance, $item, 'term' ) );
	}

	public function test_get_sortable_columns() {
		$keys = array( 'created_at', 'term' );

		$instance = new ListTable;
		$method   = new ReflectionMethod( $instance, 'get_sortable_columns' );
		$method->setAccessible( true );
		$result   = $method->invoke( $instance );

		foreach ( $keys as $key ) {
			$this->assertInternalType( 'array', $result[ $key ] );
		}
	}

	public function test_prepare_items() {
		$this->markTestIncomplete( 'Incomplete' );
	}

	public function test_get_datetime_format() {
		$instance = new ListTable;
		$method   = new ReflectionMethod( $instance, 'get_datetime_format' );
		$method->setAccessible( true );
		$format   = uniqid();

		M::wpFunction( 'get_option', array(
			'times'  => 1,
			'args'   => array( 'links_updated_date_format' ),
			'return' => $format,
		) );
		$this->assertEquals( $format, $method->invoke( $instance ) );
	}

	public function test_get_datetime_format_caches_result() {
		$instance = new ListTable;
		$method   = new ReflectionMethod( $instance, 'get_datetime_format' );
		$method->setAccessible( true );
		$format   = uniqid();
		$prop     = new ReflectionProperty( $instance, 'datetime_format' );
		$prop->setAccessible( true );
		$prop->setValue( $instance, $format );

		M::wpFunction( 'get_option', array(
			'times'  => 0,
		) );

		$this->assertEquals( $format, $method->invoke( $instance ) );
	}

}
