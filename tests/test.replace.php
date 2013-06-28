<?php

require_once PATH_THIRD.'surgeree/classes/surgeree_unit_test_case.php';

class Test_replace extends Surgeree_unit_test_case {

	protected $_methodName = 'replace';

	function test__works_when_slashes_provided_by_user() {

		$this->setTagdata('This is something to replace stuff with.');
		$this->setParams(array(
			'regex' => '/is/',
			'replace' => 'iz'
		));

		$result = $this->runMethod();
		$this->assertEqual($result, 'Thiz iz something to replace stuff with.');

	}

	function test__works_when_slashes_not_provided_by_user() {

		$this->setTagdata('This is something to replace stuff with.');
		$this->setParams(array(
			'regex' => 'is',
			'replace' => 'iz'
		));

		$result = $this->runMethod();
		$this->assertEqual($result, 'Thiz iz something to replace stuff with.');

	}

	function test__throws_error_when_slashes_occur_in_regex() {

		$this->setTagdata('/path/to/something');
		$this->setParams(array(
			'regex' => 'path/to',
			'replace' => 'longer/path/to'
		));

		$this->expectError();
		$result = $this->runMethod();

	}

}
