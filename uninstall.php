<?php
/**
 * Cleanup script to run when uninstalling the plugin.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy;

require_once __DIR__ . '/inc/caps.php';

// Verify that we're actually uninstalling the plugin.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// De-register custom capabilities.
do_action( 'mcavoy_deregister_caps' );
