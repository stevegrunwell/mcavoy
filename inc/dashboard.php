<?php
/**
 * Admin dashboard widgets.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy\Admin\Dashboard;

use McAvoy;

/**
 * Register custom admin dashboard widgets.
 */
function register_widgets() {
	wp_add_dashboard_widget(
		'mcavoy-recent-searches',
		_x( 'Recent Searches', 'dashboard widget title', 'mcavoy' ),
		__NAMESPACE__ . '\recent_searches_widget'
	);
}
add_action( 'wp_dashboard_setup', __NAMESPACE__ . '\register_widgets' );

/**
 * Generate the output for the "Recent Searches" widget.
 */
function recent_searches_widget() {
	$logger  = McAvoy\get_logger();
	$queries = $logger->get_queries( array(
		'limit' => 5,
	) );

	if ( empty( $queries->get_items() ) ) {
		printf( '<p class="no-items">%s</p>', esc_html__( 'There have not been any recent queries.', 'mcavoy' ) );
		return;
	}
?>

	<table class="mcavoy-searches-table">
		<thead>
			<tr>
				<th class="created_at" scope="col"><?php echo esc_html( _x( 'Time', 'time since query was last run', 'mcavoy' ) ); ?></th>
				<th class="term" scope="col"><?php echo esc_html( _x( 'Term', 'search term', 'mcavoy' ) ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $queries->get_items() as $item ) : ?>

				<tr>
					<td>
						<?php
							printf(
								'<time title="%s">%s</time>',
								esc_attr( $item->created_at ),
								esc_html( human_time_diff( strtotime( $item->created_at ) ) )
							);
						?>
					</td>
					<td><?php echo esc_html( $item->term ); ?></td>
				</tr>

			<?php endforeach; ?>
		</tbody>
	</table>

<?php
}
