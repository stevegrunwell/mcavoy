<?php
/**
 * Plugin Name: McAvoy
 * Plugin URI:  https://github.com/stevegrunwell/mcavoy
 * Description: Discover what visitors are searching for on your WordPress site.
 * Version:     0.1.0
 * Author:      Steve Grunwell
 * Author URI:  https://stevegrunwell.com
 * License:     MIT
 * Text Domain: mcavoy
 * Domain Path: /languages
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy;

require_once __DIR__ . '/inc/admin.php';
require_once __DIR__ . '/inc/database.php';
require_once __DIR__ . '/inc/logger.php';

/**
 * Procedure to run when the plugin is first activated.
 */
function activate_plugin() {
	create_database_table();
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\activate_plugin' );
