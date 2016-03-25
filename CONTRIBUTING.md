# Contributing to McAvoy

Thank you for taking an interest in contributing the ongoing development of McAvoy!

This plugin is designed around the idea that there's lot of insight to be gleaned from knowing what users are searching for, and McAvoy aims to provide site owners with the "Who", "What", "When", and "Where" so owners can figure out the "Why".

More importantly than analyzing the data, McAvoy needs to make it as easy as possible for owners to **collect** data, then have ways to get that data out of WordPress for better analysis (if needed).


## Getting started

After cloning the Git repository, you'll need to run the following commands to get yourself set up for development:

```sh
$ composer install && npm install
```

Naturally, for those to work, you'll need both [Composer](https://getcomposer.org/) and [Node.js](https://nodejs.org/) in order for those to work.

### Building assets

As compiled assets are **not** kept under version control, you will need to build these assets yourself; fortunately, the process is entirely automated via Grunt:

```sh
$ grunt
```


## Branching strategy

> **Note:** This workflow will take effect after the first public release of the plugin.

This project follows [the Gitflow workflow](https://www.atlassian.com/git/tutorials/comparing-workflows/gitflow-workflow):

* `master` represents the latest stable release
* `develop` houses the current development version
* All new feature branches should be branched off of `develop` and will be merged back into that branch once approved.


## Coding standards

McAvoy is a WordPress plugin, and thus follows the [WordPress Coding Standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/). There's also a pre-commit hook, [WP Enforcer](https://github.com/stevegrunwell/wp-enforcer) that will run your staged files through [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) to ensure everything's up to par.

Furthermore, please adhere to [10up's Engineering Best Practices](https://10up.github.io/Engineering-Best-Practices/); if anyone knows how to write great WordPress code, it's [10up](http://10up.com) (full disclosure: I work at 10up).

### PHP versions

The latest versions of PHPUnit require PHP 5.6 or above, but officially McAvoy attempts to support [any version of PHP that is still receiving security updates](http://php.net/supported-versions.php). At an *absolute minimum* users must be on PHP 5.3 or higher, as versions prior to 5.3 have no support for namespaces, but PHP 5.3 itself is not necessarily supported (as it stopped receiving security updates in mid-2014).


## Unit testing

Unit tests are included with this repository, written using [PHPUnit](https://phpunit.de/), [Mockery](http://docs.mockery.io/en/latest/), and [WP_Mock](https://github.com/10up/wp_mock). Whenever submitting a code-related change, *please* provide a corresponding unit test. Only you can prevent regressions!
