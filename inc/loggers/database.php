<?php
/**
 * Save searches into a custom database table.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy\Loggers;

/**
 * Database logger definition.
 */
class DatabaseLogger extends Logger {

	/**
	 * The current database schema version.
	 */
	const SCHEMA_VERSION = 1;

	/**
	 * The database table that holds search terms.
	 */
	const SEARCHES_TABLE = 'mcavoy_searches';

	/**
	 * Operations to run when the plugin is activated.
	 */
	public function activate() {
		$this->create_database_table();
	}

	/**
	 * Retrieve search queries.
	 *
	 * @global $wpdb
	 *
	 * @param array $args Arguments to override the query defaults. For a full list, please
	 *                    see Logger::get_args().
	 * @return McAvoy_Query A McAvoy_Query object representing the query.
	 */
	public function get_queries( $args = array() ) {
		global $wpdb;

		$args  = $this->get_args( $args );
		$table = $wpdb->prefix . self::SEARCHES_TABLE;
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

	/**
	 * Save a search query.
	 *
	 * @global $wpdb
	 *
	 * @param string $term     The search term.
	 * @param array  $metadata Meta data that should be saved with the query.
	 */
	function save_query( $term, $metadata ) {
		global $wpdb;

		$wpdb->insert( $wpdb->prefix . self::SEARCHES_TABLE, array(
			'term'     => $term,
			'metadata' => wp_json_encode( $metadata ),
			'created_at' => current_time( 'mysql', true ),
		), array( '%s', '%s', '%s' ) );
	}

	/**
	 * Create the custom database table.
	 *
	 * @global $wpdb
	 */
	protected function create_database_table() {
		global $wpdb;

		$table   = $wpdb->prefix . self::SEARCHES_TABLE;
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
		update_option( 'mcavoy_db_version', self::SCHEMA_VERSION, false );
	}

	/**
	 * Drop the custom database table.
	 *
	 * @global $wpdb
	 */
	protected function drop_database_table() {
		global $wpdb;

		$wpdb->query( $wpdb->prepare(
			'DROP TABLE IF_EXISTS %s;',
			$wpdb->prefix . self::SEARCHES_TABLE
		) );

		// Remove the database version from the options table.
		delete_option( 'mcavoy_db_version' );
	}
}
