<?php
/**
 * Tests for the plugin's core functionality.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy;

use Mockery;
use WP_Mock as M;

class UninstallTest extends TestCase {

	public function setup() {
		if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
			define( 'WP_UNINSTALL_PLUGIN', true );
		}

		parent::setup();
	}

	public function test_uninstall() {
		$logger = Mockery::mock( __NAMESPACE__ . '\Logger\DatabaseLogger' )->makePartial();
		$logger->shouldReceive( 'uninstall' )->once();

		M::userFunction( __NAMESPACE__ . '\get_logger', array(
			'times'  => 1,
			'return' => $logger,
		) );
		M::expectAction( 'mcavoy_deregister_caps' );

		// Include the uninstall file.
		include PROJECT . '/../uninstall.php';
	}

}
