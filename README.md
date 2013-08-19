SurgerEE
--------

A collection of simple tags for expressionengine that make template surgery easier.

## Installation

Simply copy the `pi.surgeree.php` file into a directory called `surgeree` inside the third_party folder of your ExpressionEngine installation.

> Note that we no longer recommend using submodules to install this add-on (after having been bit by that over and over again), though this add-on is still compatible with that method.

## Documentation

Please see the [wiki](https://github.com/dsurgeons/SurgerEE/wiki) for in depth documentation.

## Tests

This addon comes with a test suite that can be run by installing the [testee](http://devot-ee.com/add-ons/testee) addon and running from it's module page.

## Contributors

The following community members have made contributions to this project:

 - [cwcrawley](https://github.com/cwcrawley) (of Made by Hippo)
 	- Variation on regex replace allowing multiple regexes to be applied
 - [robsonsobral](https://github.com/robsonsobral)
 	- Start parameter on Loop tag
 	- Add chars and words parameters to Strip_tags
 	- Added url_decode, url_encode, url_fix
 - [ads1018](https://github.com/ads1018)
 	- Add entry_id_2_title
 - [fideloper](https://github.com/fideloper)
 	- Add Strip_tags

 Maintained by [EpocSquadron](https://github.com/epocsquadron).

## Changelog

Version 1.5.6

- Fix issue with ensure_http. [Carl Crawley]

Version 1.5.5

- Version number was not bumped in previous release.

Version 1.5.4

- Modified ensure_http to notice https:// hyperlinks. [Carl Crawley]

Version 1.5.3

 - Add two new methods for accessing get and post variables from within templates.

Version 1.5.2

 - Add method for ensuring http is in a url.

Version 1.5.1

 - More comprehensive documentation added and moved to the github wiki.
 - Fix a bug with loop method reporting total incorrectly.

Version 1.5

 - round_divide replaces ceil_divide with a more general implementation, allowing you to specify which way to round.
 - Added format_number method.
 - Added loop_fill method.
 - Added url helper methods all_segments, total_segments and current_url.

Version 1.4.2

 - Move repo to organization.

Version 1.4.1

 - Add MPL license to make this code usable by others.

Version 1.4.0

 - Rename plugin to SurgerEE.
 - Rework readme into Markdown.

Version 1.3.0

 - Add url_title2entry_id method.

Version 1.2.0

 - Numerous bug fixes.

Version <=1.1.0

 - Initial plugin development
