<?php
/**
 * Cleanup script to run when uninstalling the plugin.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy;

require_once __DIR__ . '/inc/database.php';

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

drop_database_table();
