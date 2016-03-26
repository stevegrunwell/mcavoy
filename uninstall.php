<?php
/**
 * Cleanup script to run when uninstalling the plugin.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy;

require_once __DIR__ . '/inc/caps.php';
require_once __DIR__ . '/inc/core.php';
require_once __DIR__ . '/inc/loggers/autoload.php';

// Verify that we're actually uninstalling the plugin.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// De-register custom capabilities.
do_action( 'mcavoy_deregister_caps' );

// Execute the uninstall() method on the current logger.
$logger = get_logger();
$logger->uninstall();
