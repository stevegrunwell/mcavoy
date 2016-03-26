<?php
/**
 * Tests for the plugin's core functionality.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy\Caps;

use McAvoy;
use Mockery;
use WP_Mock as M;

class CapsTest extends McAvoy\TestCase {

	protected $testFiles = array(
		'caps.php',
	);

	public function test_get_caps() {
		$result = get_caps();

		$this->assertInternalType( 'array', $result['view_queries'] );
		$this->assertInternalType( 'array', $result['delete_queries'] );
	}

	public function test_register_caps() {
		$caps = array(
			'foo' => array( 'administrator', 'editor' ),
			'bar' => array( 'administrator' ),
		);

		$role = Mockery::mock( 'WP_Role' )->makePartial();
		$role->shouldReceive( 'add_cap' )->times( 2 )->with( 'mcavoy_foo', true );
		$role->shouldReceive( 'add_cap' )->once()->with( 'mcavoy_bar', true );

		M::wpFunction( __NAMESPACE__ . '\get_caps', array(
			'times'  => 1,
			'return' => $caps,
		) );

		M::wpFunction( 'get_role', array(
			'return' => $role,
		) );

		M::onFilter( 'mcavoy_foo_roles' )
			->with( $caps['foo'], 'foo' )
			->reply( $caps['foo'] );

		M::onFilter( 'mcavoy_bar_roles' )
			->with( $caps['bar'], 'bar' )
			->reply( $caps['bar'] );

		register_caps();
	}

}
