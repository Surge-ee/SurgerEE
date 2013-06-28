<?php

require_once PATH_THIRD.'surgeree/classes/surgeree_unit_test_case.php';

class Test_is_halfway extends Surgeree_unit_test_case {

	protected $_methodName = 'is_halfway';

	function test__returns_empty_with_default_args() {

		$this->setParams();
		$result = $this->runMethod();
		$this->assertIdentical($result, '');

	}

	function test__returns_empty_with_nonnumeric_count() {

		$this->setParams();
		$result = $this->runMethod();
		$this->assertIdentical($result, '');

	}

	function test__returns_empty_with_nonnumeric_total() {

		$this->setParams();
		$result = $this->runMethod();
		$this->assertIdentical($result, '');

	}

	function test__rounds_up_by_default() {

		$this->setParams(array(
			'count' => 1,
			'total' => 3
		));

		$result = $this->runMethod();
		$this->assertEqual($result, 'y');

	}

	function test__rounds_down_when_told_to() {

		$this->setParams(array(
			'count' => 1,
			'total' => 3,
			'round' => 'down'
		));

		$result = $this->runMethod();
		$this->assertEqual($result, 'n');

	}

	function test__rounds_down_when_given_invalid_round_option() {

		$this->setParams(array(
			'count' => 1,
			'total' => 3,
			'round' => 'this is neither the up or down string'
		));

		$result = $this->runMethod();
		$this->assertEqual($result, 'n');

	}

	function test__rounds_up_when_told_to() {

		$this->setParams(array(
			'count' => 1,
			'total' => 3,
			'round' => 'up'
		));

		$result = $this->runMethod();
		$this->assertEqual($result, 'y');

	}

}
