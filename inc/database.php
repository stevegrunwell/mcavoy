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

/**
 * Retrieve search queries from the database.
 *
 * @global $wpdb
 *
 * @param array $args {
 *   Optional. Overrides for default query arguments.
 *
 *   @var int    $limit   The maximum number of (unique) queries to return. Default is 50.
 *   @var int    $page    The page of results to return. Default is 1.
 *   @var string $orderby The column results should be ordered by. Default is created_at.
 *   @var string $order   Either 'asc' or 'desc'. Default is 'desc'.
 * }
 * @return array An array of stdClass objects, each one representing a row.
 */
function get_search_queries( $args = array() ) {
	global $wpdb;

	$args  = wp_parse_args( $args, array(
		'limit'   => 50,
		'page'    => 1,
		'orderby' => 'created_at',
		'order'   => 'desc',
	) );
	$table = $wpdb->prefix . MCAVOY_DB_SEARCHES_TABLE;
	$cols  = array( 'id', 'term', 'metadata', 'created_at' );
	$sort  = in_array( $args['orderby'], $cols ) ? $args['orderby'] : 'created_at';
	$order = strtolower( $args['order'] ) === 'asc' ? 'asc' : 'desc';

	// @codingStandardsIgnoreStart
	return $wpdb->get_results( $wpdb->prepare(
		"SELECT * FROM $table ORDER BY $sort $order LIMIT %d,%d",
		abs( ( $args['page'] - 1 ) * $args['limit'] ),
		$args['limit']
	) );
	// @codingStandardsIgnoreEnd
}
