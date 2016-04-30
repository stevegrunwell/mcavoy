<?php
/**
 * Views for WP-Admin.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy\Admin;

use McAvoy;

/**
 * Register the "Site Searches" page under the Tools heading.
 */
function register_menu_page() {
	add_submenu_page(
		'tools.php',
		__( 'McAvoy Site Searches', 'mcavoy' ),
		__( 'Site Searches', 'mcavoy' ),
		'mcavoy_view_queries',
		'mcavoy-searches',
		__NAMESPACE__ . '\search_page_callback'
	);
}
add_action( 'admin_menu', __NAMESPACE__ . '\register_menu_page' );

/**
 * Generate the content for the "Site Searches" page.
 */
function search_page_callback() {
	maybe_delete_queries();

	$tabs   = array(
		'recent' => array(
			'label'    => _x( 'Recent Searches', 'tab label', 'mcavoy' ),
			'callback' => __NAMESPACE__ . '\recent_queries',
		),
		'report' => array(
			'label'    => _x( 'Reports', 'tab label', 'mcavoy' ),
			'callback' => '__return_false',
		),
	);

	/**
	 * Filter the tabs available on the McAvoy screen.
	 *
	 * @param array $tabs An array of tabs, with a unique identifier as the key and a an array
	 *                    containing a label and callback that outputs the tab contents.
	 */
	$tabs = apply_filters( 'mcavoy_admin_tabs', $tabs );

	// Set a default tab.
	$active = key( $tabs );
	if ( isset( $_GET['tab'] ) && isset( $tabs[ $_GET['tab'] ] ) ) {
		$active = sanitize_text_field( $_GET['tab'] );
	}
?>

	<div class="wrap">
		<div id="icon-tools" class="icon32"></div>
		<h1><?php esc_html_e( 'McAvoy Site Searches', 'mcavoy' ); ?></h1>

		<?php if ( $tabs ) : ?>
			<h2 class="nav-tab-wrapper">
				<?php foreach ( $tabs as $slug => $tab ) : ?>

					<a href="<?php echo esc_url( add_query_arg( 'tab', $slug ) ); ?>" class="nav-tab <?php echo $slug === $active ? 'nav-tab-active' : ''; ?>">
						<?php echo esc_html( $tab['label'] ); ?>
					</a>

				<?php endforeach; ?>
			</h2>
		<?php endif; ?>

		<?php $tabs[ $active ]['callback'](); ?>

	</div>

<?php
}

/**
 * Generate the markup for the "Recent Searches" tab.
 */
function recent_queries() {
	$table = new McAvoy\ListTable;
	$table->prepare_items();
?>

	<h2><?php esc_html_e( 'Recent Searches', 'mcavoy' ); ?></h2>
	<p><?php echo esc_html( sprintf(
		/** Translators: %s is the site name. */
		__( 'These are the most recent searches on %s:', 'mcavoy' ),
		get_bloginfo( 'name' )
	) ); ?></p>

	<?php $table->display(); ?>

	<?php if ( current_user_can( 'mcavoy_delete_queries' ) ) : ?>
		<form method="POST" id="mcavoy-delete-queries">
			<h2><?php esc_html_e( 'Delete saved queries?', 'macavoy' ); ?></h2>
			<p><?php esc_html_e( 'Remove all of the saved search queries?', 'mcavoy' ); ?></p>
			<?php wp_nonce_field( 'delete-queries', 'mcavoy-nonce' ); ?>
			<input type="submit" class="button delete" value="<?php esc_attr_e( 'Delete queries', 'mcavoy' ); ?>" />
		</form>
	<?php endif; ?>

<?php
}

/**
 * Delete searches if the user has requested (and has permission) to do so.
 */
function maybe_delete_queries() {
	if ( ! isset( $_POST['mcavoy-nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['mcavoy-nonce'], 'delete-queries' ) ) {
		return;
	}

	if ( ! current_user_can( 'mcavoy_delete_queries' ) ) {
		return;
	}

	$logger = McAvoy\get_logger();
	$logger->delete_queries();

	printf(
		'<div class="notice notice-success is-dismissable"><p>%s</p></div>',
		esc_html__( 'Saved queries have been deleted!', 'mcavoy' )
	);
}
