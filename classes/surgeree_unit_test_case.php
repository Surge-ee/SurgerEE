<?php

require_once PATH_THIRD.'surgeree/pi.surgeree.php';

class Surgeree_unit_test_case extends Testee_unit_test_case {

	protected $_subject;

	/**
	 * Always set up Surgeree as the subject.
	 */
	function setUp() {
		parent::setUp();

		$this->_subject = new Surgeree();

	}

	/**
	 * This is a shortcut for mocking the value of a parameter for a plugin tag.
	 *
	 * @param  string $param The parameter name.
	 * @param  string $value The value to set the parameter to.
	 * @return void
	 */
	protected function _setParam($param, $value) {

		$this->_subject->EE->TMPL->returns('fetch_param', $value, array($param, '*'));

	}

	/**
	 * Mocks fetch_param to return the passed default value.
	 *
	 * For example, when a tested method has fetch_param('numerator', '1'),
	 * we can use this to set the return value of that call to '1' as is
	 * defined in the second argument. Otherwise our tests would have to
	 * make assumptions about what those defaults that are passed are.
	 *
	 * @param  string $methodName The name of the plugin method to get defaults for
	 * @return void
	 */
	protected function _setParamDefaults($methodName, $exceptions = array()) {

		// Here we use reflection to figure out what file
		// contents to get to inspect the plugin method.
		$method = new ReflectionMethod('Surgeree', $methodName);

		$contents = $this->_getMethodContents($method);

		// Then we're using regex to pull out all the defaults
		// and their corresponding parameter names.
		$defaults = $this->_getFetchParamDefaults($contents);

		// Finally we can set these values as the return
		// for calls to any given plugin parameter.
		foreach ($defaults as $param => $value) {
			if (!in_array($param, $exceptions)) {

				$this->_setParam($param, $value);

			}
		}

	}

	/**
	 * Shortcut for mocking what's between a tag pair.
	 *
	 * @param  string $data Whatever is supposed to be inside the tag pair.
	 * @return void
	 */
	protected function _setTagdata($data) {

		$this->_subject->EE->TMPL->tagdata = $data;

	}

	/**
	 * Grab the actual text of the method
	 *
	 * @param  ReflectionMethod $reflectionMethod The method as represented by it's reflection.
	 * @return string           The method body.
	 */
	private function _getMethodContents(ReflectionMethod $reflectionMethod) {

		$filename = $reflectionMethod->getFileName();
		$start_line = $reflectionMethod->getStartLine() - 1; // it's actually - 1, otherwise you wont get the function() block
		$end_line = $reflectionMethod->getEndLine();
		$length = $end_line - $start_line;

		$source = file($filename);
		$body = implode("", array_slice($source, $start_line, $length));

		return $body;

	}

	/**
	 * Use regex to extract any and all param/default pairs from a method body
	 *
	 * @param  String $functionBody The method's contents.
	 * @return Array  An array with param names as keys, defaults as values.
	 */
	private function _getFetchParamDefaults($functionBody) {

		preg_match_all('/fetch_param\([\'"]([^, ]+)[\'"](?:, +[\'"]?([^)\'"]*)[\'"]?)\)/', $functionBody, $matches);

		return array_combine($matches[1], $matches[2]);

	}

}
