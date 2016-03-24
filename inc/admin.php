<?php
/**
 * Views for WP-Admin.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy;

/**
 * Register the "Site Searches" page under the Tools heading.
 */
function register_menu_page() {
	add_submenu_page(
		'tools.php',
		__( 'McAvoy Site Searches', 'mcavoy' ),
		__( 'Site Searches', 'mcavoy' ),
		'manage_options',
		'mcavoy-searches',
		__NAMESPACE__ . '\search_page_callback'
	);
}
add_action( 'admin_menu', __NAMESPACE__ . '\register_menu_page' );

/**
 * Generate the content for the "Site Searches" page.
 */
function search_page_callback() {
	$queries     = get_search_queries();
	$date_format = get_option( 'links_updated_date_format' );
?>

	<div class="wrap">
		<div id="icon-tools" class="icon32"></div>
		<h1><?php esc_html_e( 'McAvoy Site Searches', 'mcavoy' ); ?></h1>
		<p><?php echo esc_html( sprintf(
			/** Translators: %s is the site name. */
			__( 'These are the most recent queries on %s:', 'mcavoy' ),
			get_bloginfo( 'name' )
		) ); ?></p>

		<?php if ( empty( $queries ) ) : ?>

			<p class="no-items"><?php esc_html_e( 'No matching queries were found', 'mcavoy' ); ?></p>

		<?php else : ?>

			<table class="wp-list-table widefat striped">
				<thead>
					<tr>
						<th><?php echo esc_html( _x( 'Date', 'table column header', 'mcavoy' ) ); ?></th>
						<th><?php echo esc_html( _x( 'Search query', 'table column header', 'mcavoy' ) ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $queries as $query ) : ?>

						<tr>
							<td>
								<?php echo esc_html( date_i18n( $date_format, strtotime( $query->created_at ) ) ); ?>
							</td>
							<td>
								<?php echo esc_html( $query->term ); ?>
							</td>
						</tr>

					<?php endforeach; ?>
				</tbody>
			</table>

		<?php endif; ?>
	</div>

<?php
}
