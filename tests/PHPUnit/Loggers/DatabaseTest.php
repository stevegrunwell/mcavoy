<?php
/**
 * Test the database integration.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy\Loggers;

use McAvoy;
use Mockery;
use ReflectionMethod;
use WP_Mock as M;

class DatabaseTest extends McAvoy\TestCase {

	protected $testFiles = array(
		'class-logger.php',
		'loggers/database.php',
	);

	public function test_save_query() {
		global $wpdb;

		$instance = new DatabaseLogger;
		$method   = new ReflectionMethod( $instance, 'save_query' );
		$method->setAccessible( true );

		$meta = array( 'foo', 'bar' );
		$wpdb = Mockery::mock( '\wpdb' )->makePartial();
		$wpdb->prefix = 'wp_';
		$wpdb->shouldReceive( 'insert' )
			->once()
			->with( 'wp_mcavoy_searches', array(
				'term'       => 'term',
				'metadata'   => 'JSON',
				'created_at' => 'CURRENT_TIME',
			), array( '%s', '%s', '%s' ) );

		M::wpFunction( 'wp_json_encode', array(
			'times'  => 1,
			'args'   => array( $meta ),
			'return' => 'JSON',
		) );

		M::wpFunction( 'current_time', array(
			'times'  => 1,
			'args'   => array( 'mysql', true ),
			'return' => 'CURRENT_TIME',
		) );

		$method->invoke( $instance, 'term', $meta );

		$wpdb = null;
	}

	public function test_create_database_table() {
		global $wpdb;

		$instance = new DatabaseLogger;
		$method   = new ReflectionMethod( $instance, 'create_database_table' );
		$method->setAccessible( true );

		$wpdb = Mockery::mock( '\wpdb' )->makePartial();
		$wpdb->shouldReceive( 'get_charset_collate' )->once()->andReturn( 'utf-8' );
		$wpdb->prefix = 'wp_';

		M::wpFunction( 'dbDelta', array(
			'times'       => 1,
			'returnUsing' => function ( $sql ) {
				if ( false === strpos( $sql, 'wp_mcavoy_searches' ) ) {
					$this->fail( 'Unexpected database table name' );
				}
				return true;
			}
		) );

		M::wpFunction( 'update_option', array(
			'times'  => 1,
			'args'   => array( 'mcavoy_db_version', DatabaseLogger::SCHEMA_VERSION, false ),
		) );

		$method->invoke( $instance );

		$wpdb = null;
	}

	public function test_drop_database_table() {
		global $wpdb;

		$instance = new DatabaseLogger;
		$method   = new ReflectionMethod( $instance, 'drop_database_table' );
		$method->setAccessible( true );

		$wpdb = Mockery::mock( '\wpdb' )->makePartial();
		$wpdb->prefix = 'wp_';
		$wpdb->shouldReceive( 'query' )
			->once()
			->with( 'SQL' );
		$wpdb->shouldReceive( 'prepare' )
			->once()
			->with( 'DROP TABLE IF_EXISTS %s;', 'wp_mcavoy_searches' )
			->andReturn( 'SQL' );

		M::wpFunction( 'delete_option', array(
			'times'  => 1,
			'args'   => array( 'mcavoy_db_version' ),
		) );

		$method->invoke( $instance );

		$wpdb = null;
	}

}
