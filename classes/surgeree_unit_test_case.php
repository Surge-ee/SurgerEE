<?php

require_once PATH_THIRD.'surgeree/pi.surgeree.php';

class Surgeree_unit_test_case extends Testee_unit_test_case {

	protected $_subject;

	/**
	 * Always set up Surgeree as the subject.
	 */
	function setUp() {
		parent::setUp();

		$this->_subject = new Surgeree();
	}

	/**
	 * This is a shortcut for setting the value of a parameter for a plugin tag.
	 *
	 * @param  string $param The parameter name.
	 * @param  string $value The value to set the parameter to.
	 * @return void
	 */
	protected function _setParam($param, $value) {

		$this->_subject->EE->TMPL->returns('fetch_param', $value, array($param, '*'));

	}

}
