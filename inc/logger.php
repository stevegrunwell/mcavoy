<?php
/**
 * Log searches made through WordPress.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy;

/**
 * Listen for search queries and send them to save_search_query() if they're valid.
 */
function capture_search_query() {
	if ( ! is_search() || is_paged() ) {
		return;
	}

	// Prepare data about this query.
	$term = get_query_var( 's', false );
	$meta = prepare_query_metadata();

	// Save this search query.
	save_search_query( $term, $meta );
}
add_action( 'template_redirect', __NAMESPACE__ . '\capture_search_query' );

/**
 * Prepare meta data about a query that should be saved.
 *
 * @global $wp_query
 *
 * @return array An array of meta data.
 */
function prepare_query_metadata() {
	global $wp_query;

	$user = wp_get_current_user();
	$data = array(
		'ip_address'   => sanitize_text_field( $_SERVER['REMOTE_ADDR'] ),
		'referrer'     => wp_get_referer(),
		'user_agent'   => sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ),
		'results'      => $wp_query->found_posts,
		'current_user' => 0 === $user->ID ? null : $user->user_login,
	);

	/**
	 * Customize the meta data being saved along with search data.
	 *
	 * @param array $data Search meta data.
	 */
	return apply_filters( 'mcavoy_prepare_query_metadata', $data );
}

/**
 * Save a search query.
 *
 * @param string $term     The search term.
 * @param array  $metadata Optional. Additional metadata to save with the query. Default is an
 *                         empty array.
 */
function save_search_query( $term, $metadata = array() ) {

	/**
	 * Pass the prepared search data off to be saved.
	 *
	 * @param string $term     The search term.
	 * @param array  $metadata Meta data that should be saved with the query.
	 */
	do_action( 'mcavoy_save_search_query', $term, $metadata );
}
