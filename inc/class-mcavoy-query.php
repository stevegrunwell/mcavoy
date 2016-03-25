<?php
/**
 * Standard format for the various loggers to return data.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy;

/**
 * Represents a query returned by a McAvoy Logger class.
 */
class McAvoyQuery {

	/**
	 * The total number of results found.
	 *
	 * @var int
	 */
	protected $found = 0;

	/**
	 * The current result set.
	 *
	 * @var array
	 */
	protected $items = array();

	/**
	 * Initialize the query instance.
	 *
	 * @param array $items An array of stdClass objects representing the current result set.
	 * @param int   $found Optional. The total number of items found. Default is the size of $items.
	 */
	public function __construct( $items, $found = 0 ) {
		$this->items = $items;
		$this->found = 0 === $found ? count( $items ) : intval( $found );
	}
}
