<?php

require_once PATH_THIRD.'surgeree/pi.surgeree.php';

class Surgeree_unit_test_case extends Testee_unit_test_case {

	/**
	 * @var The module instance being tested
	 */
	protected $_subject;

	/**
	 * @var The name of the method being tested.
	 *
	 * Should be set in the child setUp() method.
	 */
	protected $_methodName;

	/**
	 * Always set up Surgeree as the subject.
	 */
	function setUp() {

		parent::setUp();

		$this->_subject = new Surgeree();

	}

	/**
	 * Allows us to use default and user specified values for fetch_param mock
	 *
	 * @param  array  $params Parameters that would be passed from the template
	 * @return void
	 */
	protected function setParams($params = null) {

		$defaults = $this->_getParamDefaults($this->_methodName);
		$params = (is_array($params)) ? array_merge($defaults, $params) : $defaults;

		// Finally we can set these values as the return
		// for calls to any given plugin parameter.
		foreach ($params as $param => $value) {

			// We need to force values to strings, as that is what
			// will always be actually passed to the method.
			$this->_subject->EE->TMPL->returns('fetch_param', (string) $value, array($param, '*'));

		}

	}

	/**
	 * Shortcut for mocking what's between a tag pair.
	 *
	 * @param  string $data Whatever is supposed to be inside the tag pair.
	 * @return void
	 */
	protected function setTagdata($data) {

		$this->_subject->EE->TMPL->tagdata = $data;

	}

	/**
	 * Shortcut for running the method
	 *
	 * @return mixed Whatever the method returns
	 */
	protected function runMethod() {

		return $this->_subject->{$this->_methodName}();

	}

	/**
	 * Retrieves the default values for all method parameters
	 *
	 * For example, when a tested method has fetch_param('numerator', '1'),
	 * we can use this to set the return value of that call to '1' as is
	 * defined in the second argument. Otherwise our tests would have to
	 * make assumptions about what those defaults that are passed are.
	 *
	 * @param  string $methodName The name of the plugin method to get defaults for
	 * @return array  The default values as key-value pairs.
	 */
	private function _getParamDefaults($methodName) {

		// Here we use reflection to figure out what file
		// contents to get to inspect the plugin method.
		$method = new ReflectionMethod('Surgeree', $methodName);

		$contents = $this->_getMethodContents($method);

		// Then we're using regex to pull out all the defaults
		// and their corresponding parameter names.
		return $this->_parseParamDefaults($contents);

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
	private function _parseParamDefaults($functionBody) {

		preg_match_all('/fetch_param\([\'"]([^, ]+)[\'"](?:, +[\'"]?([^)\'"]*)[\'"]?)\)/', $functionBody, $matches);

		return array_combine($matches[1], $matches[2]);

	}

}
