# McAvoy Change Log

All notable changes to this project will be documented in this file, according to [the Keep a Changelog standards](http://keepachangelog.com/).

This project adheres to [Semantic Versioning](http://semver.org/).

## [0.1.1] - 2016-03-28

After 0.1.0 was released, I/10up had Lukas Pawlik (@lukaspawlik) audit the plugin before we installed it on a client site. This security release reflects his findings.

* Fixed cross-site scripting (XSS) bug where search terms weren't automatically escaped.
* Fixed fatal error (`Fatal error: Call to undefined function McAvoy\Admin\get_logger()`) when deleting saved queries that resulted as a side-effect of namespace juggling.
* Added method access modifiers to the `ListTable` class.

## [0.1.0] - 2016-03-27

Initial public release.


[Unreleased]: https://github.com/stevegrunwell/mcavoy/compare/master...develop
[0.1.1]: https://github.com/stevegrunwell/mcavoy/compare/v0.1.0...v0.1.1
[0.1.0]: https://github.com/stevegrunwell/mcavoy/releases/tag/v0.1.0