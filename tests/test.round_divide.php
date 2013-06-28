<?php

require_once PATH_THIRD.'surgeree/classes/surgeree_unit_test_case.php';

class Test_round_divide extends Surgeree_unit_test_case {

	protected $_methodName = 'round_divide';

	public function test__returns_one_with_default_attributes() {

		$this->setParams();

		$result = $this->runMethod();
		$this->assertEqual($result, 1);

	}

	public function test__zero_denominator_rewritten_to_1() {

		$this->setParams(array(
			'numerator' => 11,
			'denominator' => 0
		));

		$result = $this->runMethod();
		$this->assertEqual($result, 11);

	}

	public function test__round_up_rounds_up() {

		$this->setParams(array(
			'numerator' => 11,
			'denominator' => 2,
			'round' => 'up'
		));

		$result = $this->runMethod();
		$this->assertEqual($result, 6);

	}

	public function test__round_down_rounds_down() {

		$this->setParams(array(
			'numerator' => 11,
			'denominator' => 2,
			'round' => 'down'
		));

		$result = $this->runMethod();
		$this->assertEqual($result, 5);

	}

}
