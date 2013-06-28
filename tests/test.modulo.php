<?php

require_once PATH_THIRD.'surgeree/classes/surgeree_unit_test_case.php';

class Test_modulo extends Surgeree_unit_test_case {

	protected $_methodName = 'modulo';

	public function test__returns_zero_with_default_attributes() {

		$this->setParams();

		$result = $this->runMethod();
		$this->assertEqual($result, 0);

	}

	public function test__returns_a_remainder() {

		$this->setParams(array(
			'numerator' => 11,
			'denominator' => 2
		));

		$result = $this->runMethod();
		$this->assertEqual($result, 1);

	}

	public function test__returns_zero_with_non_number_numerator() {

		$this->setParams(array(
			'numerator' => 'hey there',
			'denominator' => 2
		));

		$result = $this->runMethod();
		$this->assertEqual($result, 0);

	}

	public function test__returns_zero_with_non_number_denominator() {

		$this->setParams(array(
			'numerator' => 11,
			'denominator' => 'hey there'
		));

		$result = $this->runMethod();
		$this->assertEqual($result, 0);

	}

	public function test__returns_zero_with_non_number_args() {

		$this->setParams(array(
			'numerator' => 'hey there',
			'denominator' => 'hey there'
		));

		$result = $this->runMethod();
		$this->assertEqual($result, 0);

	}

	public function test__zero_denominator_rewritten_to_1() {

		$this->setParams(array(
			'numerator' => 11,
			'denominator' => 0
		));

		$result = $this->runMethod();
		$this->assertEqual($result, 0);

	}

}
