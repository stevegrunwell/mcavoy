# McAvoy

[![Build Status](https://travis-ci.org/stevegrunwell/mcavoy.svg?branch=master)](https://travis-ci.org/stevegrunwell/mcavoy)
[![Code Climate](https://codeclimate.com/github/stevegrunwell/mcavoy/badges/gpa.svg)](https://codeclimate.com/github/stevegrunwell/mcavoy)

McAvoy is a simple WordPress plugin that logs site searches (and information about the people performing them) so you can get a better sense of what your audience is looking for. Is your navigation unclear? Are people regularly getting lost in your infinitely-scrolling homepage when they're just trying to find the latest news on a topic? McAvoy is there, collecting the facts you need to make informed decisions!

> **Note:** McAvoy is still in a _very_ early state and is likely to change quite rapidly in the near future. Until an official release is tagged, please be **very** careful about depending on any actions, filters, or functions not explicitly documented in this README file!


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

To prevent logging to the WordPress database, simply unhook the corresponding action:

```php
remove_action( 'mcavoy_save_search_query', 'McAvoy\save_search_to_database', 10 );
```


### Who the heck is McAvoy?

This plugin was designed to answer five questions about your site's audience, specifically those searching on it: who, what, when, where, and why. Those even somewhat familiar with journalism probably recognize the importance of those five questions, and as such I found it fitting to name the plugin after a journalist. [Edward R Murrow](https://en.wikipedia.org/wiki/Edward_R._Murrow), [Walter Cronkite](https://en.wikipedia.org/wiki/Walter_Cronkite), and [Dan Rather](https://en.wikipedia.org/wiki/Dan_Rather) were all contenders, but ultimately [ACN Anchor Will McAvoy](https://en.wikipedia.org/wiki/The_Newsroom_(U.S._TV_series)) won out.

![Will McAvoy (Jeff Daniels) proclaiming that he will "single-handedly fix the internet!"](http://images.complex.com/complex/image/upload/qmizflfilz5xi04nd9rt.gif)

## License

McAvoy is freely available under [the MIT License](https://opensource.org/licenses/MIT), a copy of which is included in this repository.
