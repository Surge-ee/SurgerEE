<?php

require_once PATH_THIRD.'surgeree/classes/surgeree_unit_test_case.php';

class Test_modulo extends Surgeree_unit_test_case {

	public function test__returns_zero_with_default_attributes() {

		$result = $this->_subject->modulo();
		$this->assertEqual($result, 0);

	}

	public function test__returns_a_remainder() {

		$this->_setParam('numerator', 11);
		$this->_setParam('denominator', 2);

		$result = $this->_subject->modulo();
		$this->assertEqual($result, 1);

	}

	public function test__returns_zero_with_non_number_args() {

		// Numerator fed string
		$this->_setParam('numerator', 'hey there');
		$this->_setParam('denominator', 2);
		$result = $this->_subject->modulo();
		$this->assertEqual($result, 0);

		// Denominator fed string
		$this->_setParam('numerator', 11);
		$this->_setParam('denominator', 'hey there');
		$result = $this->_subject->modulo();
		$this->assertEqual($result, 0);

		// Both fed string
		$this->_setParam('numerator', 'hey there');
		$this->_setParam('denominator', 'hey there');
		$result = $this->_subject->modulo();
		$this->assertEqual($result, 0);

	}

	public function test__zero_denominator_rewritten_to_1() {

		$this->_setParam('numerator', 11);
		$this->_setParam('denominator', 0);

		$result = $this->_subject->modulo();
		$this->assertEqual($result, 0);

	}

}
