<?php

require_once PATH_THIRD.'surgeree/classes/surgeree_unit_test_case.php';

class Test_replace extends Surgeree_unit_test_case {

	function test__works_when_slashes_provided_by_user() {

		$this->_setTagdata('This is something to replace stuff with.');
		$this->_setParam('regex', '/is/');
		$this->_setParam('replace', 'iz');

		$result = $this->_subject->replace();
		$this->assertEqual($result, 'Thiz iz something to replace stuff with.');

	}

	function test__works_when_slashes_not_provided_by_user() {

		$this->_setTagdata('This is something to replace stuff with.');
		$this->_setParam('regex', 'is');
		$this->_setParam('replace', 'iz');

		$result = $this->_subject->replace();
		$this->assertEqual($result, 'Thiz iz something to replace stuff with.');

	}

	function test__throws_error_when_slashes_occur_in_regex() {

		$this->_setTagdata('/path/to/something');
		$this->_setParam('regex', 'path/to');
		$this->_setParam('replace', 'longer/path/to');

		$this->expectError();
		$result = $this->_subject->replace();

	}

}
