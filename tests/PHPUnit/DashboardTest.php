<?php
/**
 * Tests for admin dashboard widgets.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy\Admin\Dashboard;

use McAvoy;
use Mockery;
use WP_Mock as M;

class DashboardTest extends McAvoy\TestCase {

	protected $testFiles = array(
		'dashboard.php',
	);

	public function test_register_widgets() {
		M::wpFunction( 'wp_add_dashboard_widget', array(
			'times'  => 1,
			'args'   => array( 'mcavoy-recent-searches', '*', __NAMESPACE__ . '\recent_searches_widget' )
		) );

		M::wpPassthruFunction( '_x' );

		register_widgets();
	}

	public function test_recent_searches_widget() {
		$time    = time();
		$item    = new \stdClass;
		$item->created_at = date( 'Y-m-d H:m:s', $time );
		$item->term       = 'SEARCH_TERM';
		$queries = Mockery::mock( __NAMESPACE__ . '\McAvoy_Query' )->makePartial();
		$queries->shouldReceive( 'get_items' )
			->times( 2 )
			->andReturn( array( $item ) );

		$logger  = Mockery::mock( 'McAvoy\Loggers\TestLogger' )->makePartial();
		$logger->shouldReceive( 'get_queries' )
			->once()
			->with( array(
				'limit' => 5,
			) )
			->andReturn( $queries );

		M::wpFunction( 'McAvoy\get_logger', array(
			'times'  => 1,
			'return' => $logger,
		) );

		M::wpFunction( 'human_time_diff', array(
			'times'  => 1,
			'return' => 'TIMEDIFF',
		) );

		M::wpPassthruFunction( '_x' );
		M::wpPassthruFunction( 'esc_attr' );
		M::wpPassthruFunction( 'esc_html', array(
			'times' => 4,
		) );

		ob_start();
		recent_searches_widget();
		$result = ob_get_contents();
		ob_end_clean();
	}

	public function test_recent_searches_widget_with_empty_set() {
		$queries = Mockery::mock( __NAMESPACE__ . '\McAvoy_Query' )->makePartial();
		$queries->shouldReceive( 'get_items' )->once()->andReturn( array() );

		$logger  = Mockery::mock( 'Mockery\Loggers\TestLogger' )->makePartial();
		$logger->shouldReceive( 'get_queries' )
			->once()
			->with( array(
				'limit' => 5,
			) )
			->andReturn( $queries );

		M::wpFunction( 'McAvoy\get_logger', array(
			'times'  => 1,
			'return' => $logger,
		) );

		M::wpPassthruFunction( 'esc_html__', array(
			'times'  => 1,
		) );

		ob_start();
		recent_searches_widget();
		$result = ob_get_contents();
		ob_end_clean();

		$this->assertEquals( 0, strpos( $result, '<p class="no-items">') );
	}

}
