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
	$table = new ListTable;
	$table->prepare_items();
?>

	<div class="wrap">
		<div id="icon-tools" class="icon32"></div>
		<h1><?php esc_html_e( 'McAvoy Site Searches', 'mcavoy' ); ?></h1>
		<p><?php echo esc_html( sprintf(
			/** Translators: %s is the site name. */
			__( 'These are the most recent queries on %s:', 'mcavoy' ),
			get_bloginfo( 'name' )
		) ); ?></p>

		<?php $table->display(); ?>

	</div>

<?php
}
