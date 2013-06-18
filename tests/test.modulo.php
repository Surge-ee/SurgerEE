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

}
