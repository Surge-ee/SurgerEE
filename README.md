SurgerEE
--------

A collection of simple tags for expressionengine that make template surgery easier.

## Installation

### Simple Installation

Simply copy the `pi.surgeree.php` file into a directory called `surgeree` inside the third_party folder of your ExpressionEngine installation.

### Fancy Installation (for git users)

Make this repo into a submodule for your project's private ExpressionEngine git repo.

	git submodule add git://github.com/dsurgeons/SurgerEE.git system/expressionengine/third_party/surgeree

For added flexiblity, fork this repo and make the submodule to your fork instead. Don't forget to submit pull requests after you've added stuff :).

## Documentation

Please see the [wiki](https://github.com/dsurgeons/SurgerEE/wiki) for in depth documentation.

## Changelog

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
