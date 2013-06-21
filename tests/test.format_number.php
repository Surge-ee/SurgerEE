<?php

require_once PATH_THIRD.'surgeree/classes/surgeree_unit_test_case.php';

class Test_format_number extends Surgeree_unit_test_case {

	function test__returns_nothing_with_all_default_params() {

		$this->_setParamDefaults('format_number');

		$result = $this->_subject->format_number();
		$this->assertEqual($result, '');

	}

	function test__returns_english_currency_format_with_default_params() {

		$this->_setParamDefaults('format_number', array('number'));
		$this->_setParam('number', '2000');

		$result = $this->_subject->format_number();
		$this->assertEqual($result, '2,000.00');

	}

	function test__precision_parameter_respected() {

		$this->_setParamDefaults('format_number', array('number', 'precision'));
		$this->_setParam('number', '2');
		$this->_setParam('precision', '3');

		$result = $this->_subject->format_number();
		$this->assertEqual($result, '2.000');

	}

	function test__decimal_parameter_respected() {

		$this->_setParamDefaults('format_number', array('number', 'decimal'));
		$this->_setParam('number', '2');
		$this->_setParam('decimal', '\'');

		$result = $this->_subject->format_number();
		$this->assertEqual($result, '2\'00');

	}

	function test__separator_parameter_respected() {

		$this->_setParamDefaults('format_number', array('number', 'separator'));
		$this->_setParam('number', '20000');
		$this->_setParam('separator', '.');

		$result = $this->_subject->format_number();
		$this->assertEqual($result, '20.000.00');

	}

	function test__groupsize_parameter_respected() {

		$this->_setParamDefaults('format_number', array('number', 'groupsize'));
		$this->_setParam('number', '20000');
		$this->_setParam('groupsize', '4');

		$result = $this->_subject->format_number();
		$this->assertEqual($result, '2,0000.00');

	}

}
