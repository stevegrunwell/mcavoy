<?php
/**
 * Plugin Name: McAvoy
 * Plugin URI:  https://wordpress.org/plugins/mcavoy
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

define( 'MCAVOY_VERSION', '0.1.0' );

require_once __DIR__ . '/inc/admin.php';
require_once __DIR__ . '/inc/caps.php';
require_once __DIR__ . '/inc/core.php';
require_once __DIR__ . '/inc/class-list-table.php';
require_once __DIR__ . '/inc/class-mcavoy-query.php';

// Loggers.
require_once __DIR__ . '/inc/class-logger.php';
require_once __DIR__ . '/inc/loggers/database.php';

/**
 * Initialize the plugin.
 */
function init() {
	$logger = get_logger();
	$logger->init();
}
add_action( 'init', __NAMESPACE__ . '\init' );

/**
 * Procedure to run when the plugin is first activated.
 */
function activate_plugin() {
	$logger = get_logger();
	$logger->activate();

	/**
	 * Trigger custom capabilities required by the plugin to be registered.
	 */
	do_action( 'mcavoy_register_caps' );
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\activate_plugin' );

/**
 * Load the plugin textdomain.
 */
function load_textdomain() {
	load_plugin_textdomain( 'mcavoy', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\load_textdomain' );
