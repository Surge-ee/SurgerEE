SurgerEE
--------

A collection of simple tags for expressionengine that make template surgery easier.

## Mathematical Helpers

### Modulo
Applies the modulo operator to a numerator and denominator. Useful for outputting stuff every third, fourth, etc entry in a loop.

	{exp:surgeree:modulo numerator="4" denominator="3"}

Output:

	1

### Ceil Divide
Applies division to passed parameters, then rounds up.

	{exp:surgeree:ceil_divide numerator="4" denomenator="3"}

Output:

	2

## Logical Helpers

### Halfway
Checks if an integer is halfway rounded up through the total. Useful for determining if you are halfway through a loop.

	{exp:surgeree:is_halfway count="5" total="9"}

Output:

	y

### Years
A looping tag returning all of the years for which there are entries. Very useful for generating archive links based on calendar year.

	{exp:surgeree:years channel="blog"}
		<li>{year}</li>
	{/exp:surgeree:years}

Output:

	<li>2011</li>
	<li>2010</li>
	...

### Loop
Essentially a for loop implementation. Increment has a different meaning, and will essentially integer divide the number of iterations to determine the actual number of iterations.  This is useful when you know you want to do that integer division but don't want to calculate it in the template.

	{exp:surgeree:loop iterations="8" increment="4"}
		{current}
	{/exp:surgeree:loop}

Output:

	1
	2

### Url_title to Entry_id
Simply outputs the entry_id associated with a url_title.

	{exp:surgeree:url_title_2_entry_id url_title="{segment_3}"}

Output:

	14

## String Manipulation Helpers

### Replace
Simply calls a regex replace. Regex's should have all /'s escaped as they are automatically added to the regex parameter to form a valid regex for php.

	{exp:surgeree:replace regex="foo" reaplce="bar"}
		Something foo.
	{/exp:surgeree:replace}

OR

	{exp:surgeree:replace string="Something foo." regex="foo" reaplce="bar"}

Output:

	Something bar.

### Match
Just looks for something in a string and returns 'y' or 'n'. As noted for the replace method, all forward slashes should be escaped.

	{exp:surgeree:match string="foo" regex="^[f]"}

Output:

	y

### Proper Title
Attempts to make a proper title out of a url_title not associated with a entry. Best used with simple url_titles, not titles of articles and the like.

	{exp:surgeree:proper_title url_title="some_title"}

Output:

	Some Title

### Strip Tags
A simple wrapper around strip_tags allowing you to remove html tags from output.

Example 1: Strip HTML

	{exp:surgeree:strip_tags}
		<p>Some HTML Content. These P tag will be taken out.</p>
	{/exp:surgeree:strip_tags}

Example 2: Keep certain HTML tags:

	{exp:surgeree:strip_tags allowed_tags="<img>"}
		<p>Some HTML Content. ONLY the image tag only will be kept.</p>
		<p><img src="http://placehold.it/300x300" alt="" /></p>
	{/exp:surgeree:strip_tags}

Example 3: Keep multiple HTML tags:

	{exp:surgeree:strip_tags allowed_tags="<img> <iframe>"}
		<p>Some HTML Content. ONLY the image tag only will be kept.</p>
		<p><img src="http://placehold.it/300x300" alt="" /></p>
		<iframe src="http://example.com">This stays too!</iframe>
	{/exp:surgeree:strip_tags}
