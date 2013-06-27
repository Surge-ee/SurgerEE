<?php

require_once PATH_THIRD.'surgeree/classes/surgeree_unit_test_case.php';

class Test_has_category extends Surgeree_unit_test_case {

	function test__returns_n_with_no_params() {

		$result = $this->_subject->has_category();
		$this->assertEqual($result, 'n');

	}

	function test__returns_y_if_entry_id_has_category_id() {

		$this->_setParam('entry_id', 1);
		$this->_setParam('category_id', 1);

		$this->EE->db->returns('count_all_results', 1, array('*'));

		$result = $this->_subject->has_category();
		$this->assertEqual($result, 'y');

	}

	function test__returns_n_if_entry_id_doesnt_have_category_id() {

		$this->_setParam('entry_id', 1);
		$this->_setParam('category_id', 1);

		$this->EE->db->returns('count_all_results', 0, array('*'));

		$result = $this->_subject->has_category();
		$this->assertEqual($result, 'n');

	}

	function test__returns_n_if_entry_doesnt_exist() {

		$this->_setParam('entry_id', 1);
		$this->_setParam('category_id', 1);

		$this->EE->db->returns('count_all_results', 0, array('*'));

		$result = $this->_subject->has_category();
		$this->assertEqual($result, 'n');

	}

	function test__returns_n_if_category_doesnt_exist() {

		$this->_setParam('entry_id', 1);
		$this->_setParam('category_id', 1);

		$this->EE->db->returns('count_all_results', 0, array('*'));

		$result = $this->_subject->has_category();
		$this->assertEqual($result, 'n');

	}

	function test__returns_n_if_either_or_both_params_arent_int() {

		$this->EE->db->returns('count_all_results', 1, array('*'));

		$this->_setParam('entry_id', 'blah');
		$this->_setParam('category_id', 1);

		$result = $this->_subject->has_category();
		$this->assertEqual($result, 'n');

		$this->_setParam('entry_id', 1);
		$this->_setParam('category_id', 'blah');

		$result = $this->_subject->has_category();
		$this->assertEqual($result, 'n');

		$this->_setParam('entry_id', 'blah');
		$this->_setParam('category_id', 'blah');

		$result = $this->_subject->has_category();
		$this->assertEqual($result, 'n');

	}

}
