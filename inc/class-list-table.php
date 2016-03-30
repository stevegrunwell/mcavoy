<?php
/**
 * A custom implementation of WP_List_Table for showing search queries.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy;

if ( ! class_exists( '\WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Display a list table of search queries.
 */
class ListTable extends \WP_List_Table {

	/**
	 * The localized datetime format.
	 *
	 * @var string
	 */
	protected $datetime_format;

	/**
	 * Get a list of columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'created_at' => _x( 'Date', 'table column header', 'mcavoy' ),
			'term'       => _x( 'Search query', 'table column header', 'mcavoy' ),
		);
	}

	/**
	 * Populate each column's content.
	 *
	 * @param object $item        The current row's item.
	 * @param string $column_name The current column name.
	 */
	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'created_at':
				return date_i18n(
					$this->get_datetime_format(),
					strtotime( get_date_from_gmt( $item->created_at ) )
				);

			case 'term':
				if ( '' === $item->term ) {

					/**
					 * Filter the placeholder used for empty search terms.
					 *
					 * @param string $placeholder The text that will be displayed in McAvoy reports when the
					 *                            search term is empty.
					 */
					$item->term = apply_filters(
						'mcavoy_empty_term_placeholder',
						_x( '<empty>', 'placeholder for empty search term in McAvoy report', 'mcavoy' )
					);
				}
				return $item->term;
		}
	}

	/**
	 * Get a list of sortable columns.
	 *
	 * @return array Array of sortable columns.
	 */
	protected function get_sortable_columns() {
		return array(
			'created_at' => array( 'created_at', false ),
			'term'       => array( 'term', false ),
		);
	}

	/**
	 * Prepares the list of items for displaying.
	 */
	public function prepare_items() {
		$logger   = get_logger();
		$sortable = $this->get_sortable_columns();
		$orderby  = null;
		$order    = null;
		$page     = 1;

		// Build column headers (all, hidden, sortable).
		$this->_column_headers = array(
			$this->get_columns(),
			array(),
			$sortable,
		);

		// Determine what column we're ordering by.
		if ( isset( $_GET['orderby'] ) && in_array( $_GET['orderby'], array_keys( $sortable ) ) ) {
			$orderby = $_GET['orderby'];
		}

		if ( isset( $_GET['order'] ) && in_array( $_GET['order'], array( 'asc', 'desc' ) ) ) {
			$order = $_GET['order'];
		}

		if ( isset( $_GET['paged'] ) ) {
			$page = absint( $_GET['paged'] );
		}

		// Get the items from the logger.
		$queries     = $logger->get_queries( array(
			'orderby' => $orderby,
			'order'   => $order,
			'page'    => $page,
		) );
		$this->items = $queries->get_items();
		$this->set_pagination_args( $queries->get_pagination_args() );
	}

	/**
	 * Get the datetime format to use when displaying dates.
	 *
	 * @return string A PHP date() format string.
	 */
	protected function get_datetime_format() {
		if ( ! $this->datetime_format ) {
			$this->datetime_format = get_option( 'links_updated_date_format' );
		}

		return $this->datetime_format;
	}
}
