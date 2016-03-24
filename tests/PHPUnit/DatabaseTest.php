<?php
/**
 * Test the database integration.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy;

use WP_Mock as M;
use Mockery;

class DatabaseTest extends TestCase {

	protected $testFiles = array(
		'database.php',
	);

	public function test_create_database_table() {
		global $wpdb;

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
			'args'   => array( 'mcavoy_db_version', MCAVOY_DB_VERSION, false ),
		) );

		create_database_table();

		$wpdb = null;
	}

	public function test_drop_database_table() {
		global $wpdb;

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

		drop_database_table();

		$wpdb = null;
	}

}
