# ![McAvoy banner](plugin-repo-assets/banner-1544x500.png) McAvoy

[![Build Status](https://travis-ci.org/stevegrunwell/mcavoy.svg?branch=master)](https://travis-ci.org/stevegrunwell/mcavoy)
[![Code Climate](https://codeclimate.com/github/stevegrunwell/mcavoy/badges/gpa.svg)](https://codeclimate.com/github/stevegrunwell/mcavoy)

McAvoy is a simple WordPress plugin that logs site searches (and information about the people performing them) so you can get a better sense of what your audience is looking for. Is your navigation unclear? Are people regularly getting lost in your infinitely-scrolling homepage when they're just trying to find the latest news on a topic? McAvoy is there, collecting the facts you need to make informed decisions!

Best of all, McAvoy is meant to grow with you, sending search query data anywhere you need to in order to get the most meaningful results.


## Frequently-asked questions

### Eww, this plugin adds a new database table? Can't I put the data somewhere else?

Absolutely! Creating a new database table isn't ideal in a lot of situations, so McAvoy has been built from the ground-up to be flexible in the way the data is handled.

For example, if you want to send the data to something like [Firebase](https://www.firebase.com/), you can easily do so by creating a new callback attached to the `mcavoy_save_search_query` action:

```php
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
```

### What's all this about requiring at least PHP 5.3?

McAvoy has been written using [PHP Namespaces](http://php.net/manual/en/language.namespaces.php), which is super common in the larger PHP community but rather rare in WordPress (as WordPress strives to support as many people as possible). For most users, this minimum requirement shouldn't be of any concern (after all, [security patches stopped being delivered for PHP 5.3 in mid-2014](http://php.net/supported-versions.php)).

If you **are** affected, however, I urge you to *please* upgrade your server (or change hosts) as soon as humanely possible. Besides the obvious benefits of having current security patches, newer versions of PHP are more performant than ever.


### Who the heck is McAvoy?

This plugin was designed to answer five questions about your site's audience, specifically those searching on it: "who", "what", "when", "where", and "why." Those even somewhat familiar with journalism probably recognize the importance of those five questions, and as such I found it fitting to name the plugin after a journalist. [Edward R Murrow](https://en.wikipedia.org/wiki/Edward_R._Murrow), [Walter Cronkite](https://en.wikipedia.org/wiki/Walter_Cronkite), and [Dan Rather](https://en.wikipedia.org/wiki/Dan_Rather) were all contenders, but ultimately [ACN Anchor Will McAvoy](https://en.wikipedia.org/wiki/The_Newsroom_(U.S._TV_series)) won out.

![Will McAvoy (Jeff Daniels) proclaiming that he will "single-handedly fix the internet!"](http://images.complex.com/complex/image/upload/qmizflfilz5xi04nd9rt.gif)


## Contributing to McAvoy

Suggestions, bug reports, and pull requests for McAvoy are welcome, please [read the project's contribution guidelines](https://github.com/stevegrunwell/mcavoy/blob/develop/CONTRIBUTING.md) to get started!


## License

McAvoy is freely available under [the MIT License](https://opensource.org/licenses/MIT), a copy of which is included in this repository.
