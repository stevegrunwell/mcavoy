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

		M::passthruFunction( '_x', array(
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

		M::userFunction( 'date_i18n', array(
			'times'  => 1,
			'args'   => array( 'FORMAT', 0 ),
			'return' => 'i18n_TIME',
		) );

		M::userFunction( 'get_date_from_gmt', array(
			'times'  => 1,
			'args'   => array( 'DATE' ),
			'return' => 'GMT_DATE',
		) );

		$this->assertEquals( 'i18n_TIME', $method->invoke( $instance, $item, 'created_at' ) );
	}

	public function test_column_default_term() {
		$instance = new ListTable;
		$method   = new ReflectionMethod( $instance, 'column_default' );
		$method->setAccessible( true );
		$item     = new \stdClass;
		$item->term = uniqid();

		$this->assertEquals( $item->term, $method->invoke( $instance, $item, 'term' ) );
	}

	public function test_column_default_empty_term() {
		$instance = new ListTable;
		$method   = new ReflectionMethod( $instance, 'column_default' );
		$method->setAccessible( true );
		$item     = new \stdClass;
		$item->term = '';

		M::userFunction( '_x', array(
			'times'  => 1,
			'return' => 'EMPTY_TERM',
		) );

		M::onFilter( 'mcavoy_empty_term_placeholder' )
			->with( 'EMPTY_TERM' )
			->reply( 'EMPTY!!' );

		$this->assertEquals( 'EMPTY!!', $method->invoke( $instance, $item, 'term' ) );
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
		$_GET['orderby'] = 'foo';
		$_GET['order']   = 'asc';
		$_GET['paged']   = 1;

		$instance = Mockery::mock( __NAMESPACE__ . '\ListTable' )
			->shouldAllowMockingProtectedMethods()
			->makePartial();
		$instance->shouldReceive( 'get_columns' )
			->once()
			->andReturn( array( 'foo' => 'Foo' ) );
		$instance->shouldReceive( 'get_sortable_columns' )
			->once()
			->andReturn( array( 'column-a' ) );
		$instance->shouldReceive( 'set_pagination_args' )
			->once()
			->with( 'PAGINATION_ARGS' );
		$queries  = Mockery::mock( 'McAvoy_Query' )
			->makePartial();
		$queries->shouldReceive( 'get_items' )
			->once()
			->andReturn( array( 'foo', 'bar' ) );
		$queries->shouldReceive( 'get_pagination_args' )
			->once()
			->andReturn( 'PAGINATION_ARGS' );
		$logger   = Mockery::mock( __NAMESPACE__ . '\Loggers\TestLogger' )->makePartial();
		$logger->shouldReceive( 'get_queries' )
			->once()
			->with( array(
				'orderby' => 'foo',
				'order'   => 'asc',
				'page'    => 1,
			) )
			->andReturn( $queries );
		$prop     = new ReflectionProperty( $instance, '_column_headers' );
		$prop->setAccessible( true );

		M::userFunction( __NAMESPACE__ . '\get_logger', array(
			'times'  => 1,
			'return' => $logger,
		) );

		M::passthruFunction( 'absint', array(
			'times'  => 1,
		) );

		$instance->prepare_items();

		$this->assertEquals(
			array( array( 'foo' => 'Foo' ), array(), array( 'column-a' ) ),
			$prop->getValue( $instance )
		);

		unset( $_GET['orderby'], $_GET['order'], $_GET['paged'] );
	}

	public function test_prepare_items_validates_orderby() {
		$_GET['orderby'] = 'foo';

		$instance = Mockery::mock( __NAMESPACE__ . '\ListTable' )
			->shouldAllowMockingProtectedMethods()
			->makePartial();
		$instance->shouldReceive( 'get_columns' );
		$instance->shouldReceive( 'get_sortable_columns' )
			->andReturn( array( 'bar' => 'Bar' ) );
		$instance->shouldReceive( 'set_pagination_args' );
		$queries  = Mockery::mock( 'McAvoy_Query' )->makePartial();
		$queries->shouldReceive( 'get_items' );
		$queries->shouldReceive( 'get_pagination_args' );
		$logger   = Mockery::mock( __NAMESPACE__ . '\Loggers\TestLogger' )->makePartial();
		$logger->shouldReceive( 'get_queries' )
			->once()
			->with( array(
				'orderby' => null,
				'order'   => null,
				'page'    => 1,
			) )
			->andReturn( $queries );

		M::userFunction( __NAMESPACE__ . '\get_logger', array(
			'return' => $logger,
		) );

		M::passthruFunction( 'absint' );

		$instance->prepare_items();

		unset( $_GET['orderby'] );
	}

	public function test_prepare_items_validates_order() {
		$_GET['order'] = 'desc';

		$instance = Mockery::mock( __NAMESPACE__ . '\ListTable' )
			->shouldAllowMockingProtectedMethods()
			->makePartial();
		$instance->shouldReceive( 'get_columns' );
		$instance->shouldReceive( 'get_sortable_columns' )
			->andReturn( array( 'bar' => 'Bar' ) );
		$instance->shouldReceive( 'set_pagination_args' );
		$queries  = Mockery::mock( 'McAvoy_Query' )->makePartial();
		$queries->shouldReceive( 'get_items' );
		$queries->shouldReceive( 'get_pagination_args' );
		$logger   = Mockery::mock( __NAMESPACE__ . '\Loggers\TestLogger' )->makePartial();
		$logger->shouldReceive( 'get_queries' )
			->once()
			->with( array(
				'orderby' => null,
				'order'   => 'desc',
				'page'    => 1,
			) )
			->andReturn( $queries );

		M::userFunction( __NAMESPACE__ . '\get_logger', array(
			'return' => $logger,
		) );

		M::passthruFunction( 'absint' );

		$instance->prepare_items();

		unset( $_GET['order'] );
	}

	public function test_prepare_items_validates_order_with_bad_value() {
		$_GET['order'] = 'foo';

		$instance = Mockery::mock( __NAMESPACE__ . '\ListTable' )
			->shouldAllowMockingProtectedMethods()
			->makePartial();
		$instance->shouldReceive( 'get_columns' );
		$instance->shouldReceive( 'get_sortable_columns' )
			->andReturn( array( 'bar' => 'Bar' ) );
		$instance->shouldReceive( 'set_pagination_args' );
		$queries  = Mockery::mock( 'McAvoy_Query' )->makePartial();
		$queries->shouldReceive( 'get_items' );
		$queries->shouldReceive( 'get_pagination_args' );
		$logger   = Mockery::mock( __NAMESPACE__ . '\Loggers\TestLogger' )->makePartial();
		$logger->shouldReceive( 'get_queries' )
			->once()
			->with( array(
				'orderby' => null,
				'order'   => null,
				'page'    => 1,
			) )
			->andReturn( $queries );

		M::userFunction( __NAMESPACE__ . '\get_logger', array(
			'return' => $logger,
		) );

		M::passthruFunction( 'absint' );

		$instance->prepare_items();

		unset( $_GET['order'] );
	}

	public function test_prepare_items_validates_page() {
		$_GET['paged'] = '3';

		$instance = Mockery::mock( __NAMESPACE__ . '\ListTable' )
			->shouldAllowMockingProtectedMethods()
			->makePartial();
		$instance->shouldReceive( 'get_columns' );
		$instance->shouldReceive( 'get_sortable_columns' )
			->andReturn( array( 'bar' => 'Bar' ) );
		$instance->shouldReceive( 'set_pagination_args' );
		$queries  = Mockery::mock( 'McAvoy_Query' )->makePartial();
		$queries->shouldReceive( 'get_items' );
		$queries->shouldReceive( 'get_pagination_args' );
		$logger   = Mockery::mock( __NAMESPACE__ . '\Loggers\TestLogger' )->makePartial();
		$logger->shouldReceive( 'get_queries' )
			->once()
			->with( array(
				'orderby' => null,
				'order'   => null,
				'page'    => 3,
			) )
			->andReturn( $queries );

		M::userFunction( __NAMESPACE__ . '\get_logger', array(
			'return' => $logger,
		) );

		M::passthruFunction( 'absint', array(
			'times'  => 1,
			'args'   => array( 3 ),
		) );

		$instance->prepare_items();

		unset( $_GET['paged'] );
	}

	public function test_get_datetime_format() {
		$instance = new ListTable;
		$method   = new ReflectionMethod( $instance, 'get_datetime_format' );
		$method->setAccessible( true );
		$format   = uniqid();

		M::userFunction( 'get_option', array(
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

		M::userFunction( 'get_option', array(
			'times'  => 0,
		) );

		$this->assertEquals( $format, $method->invoke( $instance ) );
	}

}
