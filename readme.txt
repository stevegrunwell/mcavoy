=== McAvoy ===
Contributors:      stevegrunwell
Donate link:       https://stevegrunwell.com/donate
Tags:              search, query, tracking, site search
Requires at least: 4.4
Tested up to:      4.4.2
Stable tag:        0.1.2
License:           MIT
License URI:       https://opensource.org/licenses/MIT

Discover what visitors are searching for on your WordPress site.

== Description ==

McAvoy is a simple WordPress that logs site searches (and information about the people performing them) so you can get a better sense of what your audience is looking for. Is your navigation unclear? Are people regularly getting lost in your infinitely-scrolling homepage when they're just trying to find the latest news on a topic? McAvoy is there, collecting the facts you need to make informed decisions!

Best of all, McAvoy is meant to grow with you, sending search query data anywhere you need to in order to get the most meaningful results.

**Notice:** In the interest of writing the best software possible, McAvoy requires a *minimum* of PHP 5.3. For more information, please see the Frequently Asked Questions.

To keep up with the latest developments (or to contribute to ongoing development), please [keep up with McAvoy on GitHub](https://github.com/stevegrunwell/mcavoy)!


== Installation ==

1. Upload the plugin files to `/wp-content/plugins/mcavoy` or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.


== Frequently Asked Questions ==

= Eww, this plugin adds a new database table? Can't I put the data somewhere else? =

Absolutely! Creating a new database table isn't ideal in a lot of situations, so McAvoy has been built from the ground-up to be flexible in the way the data is handled.

For example, if you want to send the data to something like [Firebase](https://www.firebase.com/), you can easily do so by creating a new callback attached to the `mcavoy_save_search_query` action:

		/**
		 * Save a search query to Firebase.
		 *
		 * @param string $term     The search term.
		 * @param array  $metadata Meta data that should be saved with the query.
		 */
		function save_search_query_to_firebase( $term, $metadata ) {
			// do something with this data!
		}
		add_action( 'mcavoy_save_search_query', 'save_search_query_to_firebase', 10, 2 );


= What's all this about requiring at least PHP 5.3? =

McAvoy has been written using [PHP Namespaces](http://php.net/manual/en/language.namespaces.php), which is super common in the larger PHP community but rather rare in WordPress (as WordPress strives to support as many people as possible). For most users, this minimum requirement shouldn't be of any concern (after all, [security patches stopped being delivered for PHP 5.3 in mid-2014](http://php.net/supported-versions.php)).

If you **are** affected, however, I urge you to *please* upgrade your server (or change hosts) as soon as humanely possible. Besides the obvious benefits of having current security patches, newer versions of PHP are more performant than ever.


= Who the heck is McAvoy? =

This plugin was designed to answer five questions about your site's audience, specifically those searching on it: "who", "what", "when", "where", and "why." Those even somewhat familiar with journalism probably recognize the importance of those five questions, and as such I found it fitting to name the plugin after a journalist. [Edward R Murrow](https://en.wikipedia.org/wiki/Edward_R._Murrow), [Walter Cronkite](https://en.wikipedia.org/wiki/Walter_Cronkite), and [Dan Rather](https://en.wikipedia.org/wiki/Dan_Rather) were all contenders, but ultimately [ACN Anchor Will McAvoy](https://en.wikipedia.org/wiki/The_Newsroom_(U.S._TV_series)) won out.


== Screenshots ==

1. A list of recent searches made on a site – judging by the terms, it sounds like someone was hungry!


== Changelog ==

For a complete changelog, please see [McAvoy's GitHub repository](https://github.com/stevegrunwell/mcavoy/blob/master/CHANGELOG.md).

= 0.1.2 =
* Fixed issue with WordPress Multisite wherein McAvoy would not properly set up the `DatabaseLogger` dependencies when network activated.

= 0.1.1 =
* Fixed cross-site scripting (XSS) bug where search terms weren't automatically escaped.
* Fixed fatal error (`Fatal error: Call to undefined function McAvoy\Admin\get_logger()`) when deleting saved queries that resulted as a side-effect of namespace juggling.
* Added method access modifiers to the `ListTable` class.

= 0.1.0 =
* Initial public release.


== Upgrade Notice ==

= 0.1.2 =
Fixes issues that arise when McAvoy is network activated within WordPress Multisite.

= 0.1.1 =
This release fixes a Cross-site Scripting (XSS) vulnerability and fixes a fatal error when trying to delete saved queries and is a recommended upgrade for all users.