<?php

require_once PATH_THIRD.'surgeree/classes/surgeree_unit_test_case.php';

class Test_format_number extends Surgeree_unit_test_case {

	protected $_methodName = 'format_number';

	function test__returns_nothing_with_all_default_params() {

		$this->setParams();

		$result = $this->runMethod();
		$this->assertEqual($result, '');

	}

	function test__returns_english_currency_format_with_default_params() {

		$this->setParams(array(
			'number' => '2000'
		));

		$result = $this->runMethod();
		$this->assertEqual($result, '2,000.00');

	}

	function test__precision_parameter_respected() {

		$this->setParams(array(
			'number' => 2,
			'precision' => 3
		));

		$result = $this->runMethod();
		$this->assertEqual($result, '2.000');

	}

	function test__decimal_parameter_respected() {

		$this->setParams(array(
			'number' => 2,
			'decimal' => "'"
		));

		$result = $this->runMethod();
		$this->assertEqual($result, "2'00");

	}

	function test__separator_parameter_respected() {

		$this->setParams(array(
			'number' => '20000',
			'separator' => '.'
		));

		$result = $this->runMethod();
		$this->assertEqual($result, '20.000.00');

	}

	function test__groupsize_parameter_respected() {

		$this->setParams(array(
			'number' => '20000',
			'groupsize' => '4'
		));

		$result = $this->runMethod();
		$this->assertEqual($result, '2,0000.00');

	}

}
