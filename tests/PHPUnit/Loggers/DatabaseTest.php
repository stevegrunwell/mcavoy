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
		'loggers/class-logger.php',
		'loggers/database.php',
	);

	public function test_activate() {
		$instance = Mockery::mock( __NAMESPACE__ . '\DatabaseLogger' )
			->shouldAllowMockingProtectedMethods()
			->makePartial();
		$instance->shouldReceive( 'create_database_table' )->once();

		$instance->activate();
	}

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

		M::userFunction( 'wp_json_encode', array(
			'times'  => 1,
			'args'   => array( $meta ),
			'return' => 'JSON',
		) );

		M::userFunction( 'current_time', array(
			'times'  => 1,
			'args'   => array( 'mysql', true ),
			'return' => 'CURRENT_TIME',
		) );

		$method->invoke( $instance, 'term', $meta );

		$wpdb = null;
	}

	public function test_uninstall() {
		$instance = Mockery::mock( __NAMESPACE__ . '\DatabaseLogger' )
			->shouldAllowMockingProtectedMethods()
			->makePartial();
		$instance->shouldReceive( 'drop_database_table' )->once();

		$instance->uninstall();
	}

	public function test_create_database_table() {
		global $wpdb;

		$instance = new DatabaseLogger;
		$method   = new ReflectionMethod( $instance, 'create_database_table' );
		$method->setAccessible( true );

		$wpdb = Mockery::mock( '\wpdb' )->makePartial();
		$wpdb->shouldReceive( 'get_charset_collate' )->once()->andReturn( 'utf-8' );
		$wpdb->prefix = 'wp_';

		M::userFunction( 'dbDelta', array(
			'times'       => 1,
			'returnUsing' => function ( $sql ) {
				if ( false === strpos( $sql, 'wp_mcavoy_searches' ) ) {
					$this->fail( 'Unexpected database table name' );

				/** @ticket #26 */
				} elseif ( false === strpos( $sql, '`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT' ) ) {
					$this->fail( 'The ID column in the database logger table should be an unsigned BIGINT' );
				}
				return true;
			}
		) );

		M::userFunction( 'update_option', array(
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
			->with( 'DROP TABLE IF EXISTS wp_mcavoy_searches' );

		M::userFunction( 'delete_option', array(
			'times'  => 1,
			'args'   => array( 'mcavoy_db_version' ),
		) );

		$method->invoke( $instance );

		$wpdb = null;
	}

	public function test_maybe_trigger_activation() {
		$instance = Mockery::mock( __NAMESPACE__ . '\DatabaseLogger' )->makePartial();
		$instance->shouldReceive( 'activate' )->once();

		M::userFunction( 'get_option', array(
			'times'  => 1,
			'args'   => array( 'mcavoy_db_version', false ),
			'return' => false,
		) );

		$method = new ReflectionMethod( $instance, 'maybe_trigger_activation' );
		$method->setAccessible( true );
		$method->invoke( $instance );
	}

	public function test_maybe_trigger_activation_bails_if_db_is_current() {
		$instance = Mockery::mock( __NAMESPACE__ . '\DatabaseLogger' )->makePartial();
		$instance->shouldReceive( 'activate' )->never();

		M::userFunction( 'get_option', array(
			'return' => 99999999,
		) );

		$method = new ReflectionMethod( $instance, 'maybe_trigger_activation' );
		$method->setAccessible( true );
		$method->invoke( $instance );
	}

	public function test_maybe_trigger_activation_upgrade_schema() {
		$instance = Mockery::mock( __NAMESPACE__ . '\DatabaseLogger' )->makePartial();
		$instance->shouldReceive( 'activate' )->once();

		M::userFunction( 'get_option', array(
			'return' => -1,
		) );

		$method = new ReflectionMethod( $instance, 'maybe_trigger_activation' );
		$method->setAccessible( true );
		$method->invoke( $instance );
	}

}
