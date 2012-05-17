<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * DSUtils Plugin
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Plugin
 * @author		Daniel Poulin
 * @link 		http://github.com/EpocSquadron/dsutils Homepage
 */

$plugin_info = array(
	'pi_name'		=> 'DSUtils',
	'pi_version'	=> '1.3.0',
	'pi_author'		=> 'Daniel Poulin',
	'pi_author_url'	=> 'http://github.com/EpocSquadron/dsutils',
	'pi_description'=> 'Various commonly needed items that make us want to use php in templates.',
	'pi_usage'		=> Dsutils::usage()
);


class Dsutils {

	public $return_data;

	/** Constructor */
	public function __construct() {
		$this->EE =& get_instance();
	}

	/** Applies the modulo operator to a numerator and denominator.
	 *
	 * Useful for outputting stuff every third, fourth, etc entry in
	 * a loop. */
	function modulo() {
		$numerator = $this->EE->TMPL->fetch_param('numerator', '1');
		$denomenator = $this->EE->TMPL->fetch_param('denomenator', '1');
		$denomenator = ($denomenator == 0) ? 1 : $denomenator;
		$this->return_data = $numerator % $denomenator;
		return $this->return_data;
	}

	/** Applies division to passed parameters, then rounds up. */
	function ceil_divide() {
		$numerator = $this->EE->TMPL->fetch_param('numerator', '1');
		$denomenator = $this->EE->TMPL->fetch_param('denomenator', '1');
		$denomenator = ($denomenator == 0) ? 1 : $denomenator;
		$this->return_data = ceil($numerator / $denomenator);
		return $this->return_data;
	}

	/** A looping tag returning all of the years for which there are entries.
	 *
	 * Very useful for generating archive links based on calendar year.
	 *
	 * @param exclude_current_year y|n Whether or not to include the current year in the results.
	 * @param channel <channel_name[|channel_name...]> Channel(s) to restrict search to.
	 * @param status <open[|closed...]> Status of entries to restrict search to.
	 * @return {year} Variable containing year for each iteration of the loop.
	 */
	function years() {
		// Prepare parameters
		$exclude_current_year = ($this->EE->TMPL->fetch_param('exclude_current_year', '') == 'yes');

		$channels = explode('|', $this->EE->TMPL->fetch_param('channel', ''));
		$num_channels = count($channels);

		$statuses = explode('|', $this->EE->TMPL->fetch_param('status', 'open'));
		$num_statuses = count($statuses);

		$sort = (strtoupper($this->EE->TMPL->fetch_param('sort', 'desc')) === 'DESC') ? 'DESC' : 'ASC';

		// Prepare query
		$sql = "SELECT t.`year`
				FROM `{$this->EE->db->dbprefix}channel_titles` AS t
					JOIN `{$this->EE->db->dbprefix}channels` AS c
						ON (c.`channel_id` = t.`channel_id`) WHERE ";
		// If excluding year, do it now
		if ($exclude_current_year) {
			$current_year = date('Y');
			$sql .= " t.`year` != '$current_year' AND ";
		}
		// Add which channel it should come from
		if ($num_channels > 0) {
			for ($i = 0; $i < $num_channels; $i++) {
				$sql .= "c.`channel_name`='{$channels[$i]}' AND ";
			}
		}
		// Add which statuses it should pull.
		$sql .= "(";
		for ($i = 0; $i < $num_statuses; $i++) {
			$sql .= "t.`status`='{$statuses[$i]}'";
			if ($i != ($num_statuses - 1)) {
				$sql .= " OR ";
			}
		}
		// Add what order to pull in.
		$sql .= ") GROUP BY t.`year` ORDER BY t.`entry_date` $sort";

		// Execute query, build results
		$variables = array();
		$query = $this->EE->db->query($sql);

		foreach($query->result() as $row) {
			$variables[] = array(
				'year' => $row->year
			);
		}

		return $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables);
	}

	/** Checks if an integer is halfway rounded up through the total.
	 *
	 * Useful for determining if you are halfway through a loop. */
	function is_halfway() {
		$count = $this->EE->TMPL->fetch_param('count');
		$total = $this->EE->TMPL->fetch_param('total');

		if (is_numeric($count) && is_numeric($total) && $count == ceil($total / 2)) {
			$this->return_data = 'y';
		} else {
			$this->return_data = 'n';
		}
		return $this->return_data;
	}

	/** Performs a regex replace on a string */
	function replace() {
		$param = $this->EE->TMPL->fetch_param('string', '');
		$tagdata = $this->EE->TMPL->tagdata;
		$string = ($tagdata != '' && $param == '') ? $tagdata : $param ;
		$regex   = $this->EE->TMPL->fetch_param('regex', '');
		$replace  = $this->EE->TMPL->fetch_param('replace', '');

		//Output transformed string
		$this->return_data = preg_replace("/$regex/", $replace, $string);
		return $this->return_data;
	}

	/** Searches for a regex in a string */
	function match() {
		$string = $this->EE->TMPL->fetch_param('string', '');
		$regex   = $this->EE->TMPL->fetch_param('regex', '');

		//Output transformed string
		$this->return_data = (preg_match("/$regex/", $string)) ? 'y' : 'n';
		return $this->return_data;
	}

	/** Just loops a certain number of times. */
	function loop() {
		$iters = (int) $this->EE->TMPL->fetch_param('iterations', '1');
		$increment = (int) $this->EE->TMPL->fetch_param('increment', '1');

		$variables = array();
		$j = 1;
		for ($i = 1; $i <= $iters; $i += $increment) {
			$variables[] = array(
				'current' => $j,
				'total' => $iters
			);
			$j++;
		}

		return $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables);
	}

	function url_title_2_entry_id() {
		$url_title = $this->EE->TMPL->fetch_param('url_title', '');

		$sql = "SELECT `entry_id` FROM `{$this->EE->db->dbprefix}channel_titles` WHERE `url_title`=?;";
		$q = $this->EE->db->query($sql, array($url_title));

		if ($q->num_rows() > 0) {
			$this->return_data = $q->row()->entry_id;
		} else {
			$this->return_data = '';
		}

		return $this->return_data;
	}

	// -- Plugin Usage -- //
	public static function usage() {
		ob_start();
?>
Mathematical
------------

	=== Modulo ===
		{exp:dsutils:modulo numerator="4" denominator="3"}
	Output:
		1

	=== Ceil Divide ===
		{exp:dsutils:ceil_divide numerator="4" denomenator="3"}
	Output:
		2

Logical
-------

	=== Is Halfway ===
		{exp:dsutils:is_halfway count="5" total="9"}
	Output:
		y

	=== Years ===
		{exp:dsutils:years channel="blog"}
			<li>{year}</li>
		{/exp:dsutils:years}
	Output:
		<li>2011</li>
		<li>2010</li>
		...

	=== Loop ===
		{exp:dsutils:loop iterations="8" increment="4"}
			{current}
		{/exp:dsutils:loop}
	Output:
		1
		2

	=== Url_title to Entry_id ===
		{exp:dsutils:url_title_2_entry_id url_title="{segment_3}"}
	Output:
		14

String Manipulation
-------------------

	=== Replace ===
		{exp:dsutils:replace regex="foo" reaplce="bar"}
			Something foo.
		{/exp:dsutils:replace}
		OR
		{exp:dsutils:replace string="Something foo." regex="foo" reaplce="bar"}
	Output:
		Something bar.

	=== Match ===
		{exp:dsutils:match string="foo" regex="^[f]"}
	Output:
		y


<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
}


/* End of file pi.dsutils.php */
/* Location: /system/expressionengine/third_party/dsutils/pi.dsutils.php */
