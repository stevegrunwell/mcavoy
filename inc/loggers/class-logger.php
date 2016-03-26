<?php
/**
 * Base Logger class for all other logging methods to inherit from.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy\Loggers;

/**
 * Basic logger definition.
 */
abstract class Logger {

	/**
	 * Operations to run when the plugin is activated.
	 */
	public function activate() {}

	/**
	 * Flush the saved queries.
	 */
	abstract public function delete_queries();

	/**
	 * Initialize this logger.
	 *
	 * This method should call any internal methods necessary to prepare this logger.
	 */
	public function init() {
		$this->add_hooks();
	}

	/**
	 * Retrieve search queries.
	 *
	 * @param array $args Arguments to override the query defaults. For a full list, please
	 *                    see Logger::get_args().
	 * @return McAvoy_Query A McAvoy_Query object representing the query.
	 */
	abstract public function get_queries( $args = array() );

	/**
	 * Save a search query.
	 *
	 * @param string $term     The search term.
	 * @param array  $metadata Meta data that should be saved with the query.
	 */
	abstract public function save_query( $term, $metadata );

	/**
	 * Operations to run when the plugin is uninstalled.
	 */
	public function uninstall() {}

	/**
	 * Hook this logger into WordPress.
	 */
	protected function add_hooks() {
		add_action( 'mcavoy_save_search_query', array( $this, 'save_query' ), 10, 2 );
	}

	/**
	 * Retrieve all possible parameters for displaying search terms.
	 *
	 * @param array $args {
	 *   Optional. Arguments to merge into the defaults. Default is an empty array.
	 *
	 *   @var int    $limit   The maximum number of (unique) queries to return. Default is 50.
	 *   @var int    $page    The page of results to return. Default is 1.
	 *   @var string $orderby The column results should be ordered by. Default is created_at.
	 *   @var string $order   Either 'asc' or 'desc'. Default is 'desc'.
	 * }
	 * @return array An array of parameters.
	 */
	protected function get_args( $args = array() ) {
		return wp_parse_args( (array) $args, array(
			'limit'   => 50,
			'page'    => 1,
			'orderby' => 'created_at',
			'order'   => 'desc',
		) );
	}
}
