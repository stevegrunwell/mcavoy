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

require_once __DIR__ . '/inc/database.php';
require_once __DIR__ . '/inc/logger.php';

/**
 * Load the plugin textdomain.
 */
function load_textdomain() {
	load_plugin_textdomain( 'mcavoy', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\load_textdomain' );
