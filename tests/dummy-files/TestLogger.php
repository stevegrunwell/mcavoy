<?php
/**
 * A non-abstract implementation of the base Logger class.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy\Loggers;

class TestLogger extends Logger {

	public function get_queries( $args = array() ) {

	}

	public function save_query( $term, $metadata ) {

	}
}
