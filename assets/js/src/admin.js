/**
 * Admin-area scripting for McAvoy.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

( function ( $, McAvoy ) {
	'use strict';

	var $deleteQueriesForm = $( document.getElementById( 'mcavoy-delete-queries' ) );

	/**
	 * Alert the user before they delete saved queries.
	 */
	$deleteQueriesForm.submit( function () {
		return confirm( McAvoy.i18n.deleteQueriesConfig );
	} );

} ( jQuery, McAvoy, undefined ) );
