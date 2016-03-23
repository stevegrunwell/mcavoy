<?php
/**
 * Log searches made through WordPress.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy;

/**
 * Prepare meta data about a query that should be saved.
 *
 * @return array An array of meta data.
 */
function prepare_query_metadata() {
	$data = array(
		'ip_address' => sanitize_text_field( $_SERVER['REMOTE_ADDR'] ),
		'user_agent' => sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ),
	);

	/**
	 * Customize the meta data being saved along with search data.
	 *
	 * @param array $data Search meta data.
	 */
	return apply_filters( 'mcavoy_prepare_query_metadata', $data );
}
