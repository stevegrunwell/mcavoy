<?php
/**
 * Set up custom capabilities used by McAvoy.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy\Caps;

/**
 * Get an array of plugin-specific capabilities and the default WordPress roles that they should be
 * granted to.
 *
 * @return array An associative array where the key represents the (non-prefixed) capability slug
 *               and the value is an array of WordPress role names.
 */
function get_caps() {
	return array(
		'view_queries'   => array( 'administrator', 'editor' ),
		'delete_queries' => array( 'administrator', 'editor' ),
	);
}

/**
 * Register custom capabilities used by McAvoy.
 */
function register_caps() {
	$caps = get_caps();

	foreach ( $caps as $cap => $roles ) {
		$roles_filter = sprintf( 'mcavoy_%s_roles', $cap );

		/**
		 * Filter the WordPress user roles that should be granted access to $cap.
		 *
		 * @param array  $roles The roles that should be granted $cap.
		 * @param string $cap   The WordPress capability.
		 */
		$roles = apply_filters( $roles_filter, $roles, $cap );

		// Iterate through the roles and grant them $cap.
		foreach ( $roles as $role_name ) {
			$role = get_role( $role_name );

			if ( $role ) {
				$role->add_cap( sprintf( 'mcavoy_%s', $cap ), true );
			}
		}
	}
}
add_action( 'mcavoy_register_caps', __NAMESPACE__ . '\register_caps' );
