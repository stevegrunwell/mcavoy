<?php
/**
 * Functionality to manage the plugin's custom database table.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy;

define( 'MCAVOY_DB_SEARCHES_TABLE', 'mcavoy_searches' );
define( 'MCAVOY_DB_VERSION', 1 );

/**
 * Create the custom database table.
 *
 * @global $wpdb
 */
function create_database_table() {
	global $wpdb;

	$table   = $wpdb->prefix . MCAVOY_DB_SEARCHES_TABLE;
	$charset = $wpdb->get_charset_collate();
	$sql     = "CREATE TABLE IF NOT EXISTS $table (
		`id` tinyint(11) unsigned NOT NULL AUTO_INCREMENT,
		`term` varchar(255) NOT NULL DEFAULT '',
		`metadata` text,
		`created_at` datetime NOT NULL,
		PRIMARY KEY (`id`),
		KEY `term` (`term`),
		KEY `created_at` (`created_at`)
	) $charset;";

	// Load the necessary libraries and run the schema migration.
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );

	// Store the current database version in the options table.
	update_option( 'mcavoy_db_version', MCAVOY_DB_VERSION, false );
}

/**
 * Drop the custom database table.
 *
 * @global $wpdb
 */
function drop_database_table() {
	global $wpdb;

	$wpdb->query( $wpdb->prepare(
		'DROP TABLE IF_EXISTS %s;',
		$wpdb->prefix . MCAVOY_DB_SEARCHES_TABLE
	) );

	// Remove the database version from the options table.
	delete_option( 'mcavoy_db_version' );
}

/**
 * Save a search query to the database.
 *
 * @global $wpdb
 *
 * @param string $term     The search term.
 * @param array  $metadata Meta data that should be saved with the query.
 */
function save_search_to_database( $term, $metadata ) {
	global $wpdb;

	$wpdb->insert( $wpdb->prefix . MCAVOY_DB_SEARCHES_TABLE, array(
		'term'     => $term,
		'metadata' => wp_json_encode( $metadata ),
		'created_at' => current_time( 'mysql', true ),
	), array( '%s', '%s', '%s' ) );
}
add_action( 'mcavoy_save_search_query', __NAMESPACE__ . '\save_search_to_database', 10, 2 );
