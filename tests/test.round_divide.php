<?php

require_once PATH_THIRD.'surgeree/classes/surgeree_unit_test_case.php';

class Test_round_divide extends Surgeree_unit_test_case {

	public function test__returns_one_with_default_attributes() {

		$this->_setParamDefaults('round_divide');

		$result = $this->_subject->round_divide();
		$this->assertEqual($result, 1);

	}

	public function test__zero_denominator_rewritten_to_1() {

		$this->_setParam('numerator', 11);
		$this->_setParam('denominator', 0);

		$result = $this->_subject->round_divide();
		$this->assertEqual($result, 11);

	}

	public function test__round_up_rounds_up() {

		$this->_setParam('numerator', 11);
		$this->_setParam('denominator', 2);
		$this->_setParam('round', 'up');

		$result = $this->_subject->round_divide();
		$this->assertEqual($result, 6);

	}

	public function test__round_down_rounds_down() {

		$this->_setParam('numerator', 11);
		$this->_setParam('denominator', 2);
		$this->_setParam('round', 'down');

		$result = $this->_subject->round_divide();
		$this->assertEqual($result, 5);

	}

}
