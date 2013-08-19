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
 * SurgerEE Plugin
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Plugin
 * @author		Daniel Poulin
 * @author 		Chris Fidao
 * @link 		http://github.com/dsurgeons/SurgerEE Homepage
 * @license
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

$plugin_info = array(
	'pi_name'		=> 'SurgerEE',
	'pi_version'	=> '1.5.3',
	'pi_author'		=> 'Digital Surgeons',
	'pi_author_url'	=> 'http://github.com/dsurgeons/SurgerEE',
	'pi_description'=> 'Various commonly needed items that make us want to use php in templates.',
	'pi_usage'		=> Surgeree::usage()
);


class Surgeree {

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
		$denominator = $this->EE->TMPL->fetch_param('denominator', '1');
		$denominator = ($denominator == 0) ? 1 : $denominator;
		$this->return_data = $numerator % $denominator;
		return $this->return_data;
	}

	/** Generic divide-then-round. */
	function round_divide() {
		$numerator = (int) $this->EE->TMPL->fetch_param('numerator', '1');
		$denominator = (int) $this->EE->TMPL->fetch_param('denominator', '1');
		$round = $this->EE->TMPL->fetch_param('round', 'up');
		$denominator = ($denominator === 0) ? 1 : $denominator;
		$this->return_data = ($round === 'up') ? ceil($numerator / $denominator) : floor($numerator / $denominator);
		return $this->return_data;
	}

	/** Grab value of a get variable */
	function get() {
		$key = $this->EE->TMPL->fetch_param('varname');
		$this->return_data = $this->EE->input->get($key);
		return $this->return_data;
	}

	/** Grab value of a post variable */
	function post() {
		$key = $this->EE->TMPL->fetch_param('varname');
		$this->return_data = $this->EE->input->get($key);
		return $this->return_data;
	}

	/** Helper function replacing number_format accounting for groupings other than thousands.
	 *
	 * Taken from php documentation comments. @see http://php.net/manual/en/function.number-format.php#95293
	 */
	protected function _betterNumberFormat($number, $precision, $decimal, $separator, $groupsize) {
		$number = sprintf("%0.{$precision}f",$number);
		$number = explode('.',$number);
		while (strlen($number[0]) % $groupsize) $number[0]= ' '.$number[0];
		$number[0] = str_split($number[0],$groupsize);
		$number[0] = join($separator[0],$number[0]);
		$number[0] = trim($number[0]);
		$number = join($decimal[0],$number);

		return $number;
	}

	/** Formats a passed number in specified format. Useful for localization. */
	function format_number() {
		// Get the number to apply this to.
		$number = trim($this->EE->TMPL->fetch_param('number', ''));
		// Need to resolve issues with optional tag pairing before
		// accepting numbers from within.
		//$tagdata = trim($this->EE->TMPL->tagdata);
		//$number = (is_numeric($tagdata) && $param == '') ? $tagdata : $param ;
		if (!is_numeric($number)) return '';

		// Get settings for number_format
		$precision = $this->EE->TMPL->fetch_param('precision', '2');
		$decimal = $this->EE->TMPL->fetch_param('decimal', '.');
		$separator = $this->EE->TMPL->fetch_param('separator', ',');
		$groupsize = $this->EE->TMPL->fetch_param('groupsize', '3');

		$this->return_data = $this->_betterNumberFormat($number, $precision, $decimal, $separator, $groupsize);

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
				FROM `exp_channel_titles` AS t
					JOIN `exp_channels` AS c
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

	/** Checks if an integer is halfway rounded up or down through the total.
	 *
	 * Useful for determining if you are halfway through a loop. */
	function is_halfway() {
		$count = $this->EE->TMPL->fetch_param('count');
		$total = $this->EE->TMPL->fetch_param('total');
		$round = $this->EE->TMPL->fetch_param('round', 'up');

		if (!is_numeric($count) || !is_numeric($total)) return '';

		if ( ($round == 'up' && $count == ceil($total / 2)) || ($round == 'down' && $count == floor($total / 2)) ) {
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

		$total = floor($iters/$increment);

		$variables = array();
		$j = 1;
		for ($i = 1; $i <= $iters; $i += $increment) {
			$variables[] = array(
				'current' => $j,
				'total' => $total
			);
			$j++;
		}

		return $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables);
	}

	/** Loops enough times to make a completed parent loop divisible by a number.
	 *
	 * Useful for instance in a carousel which is getting dummy slides filled in
	 * to make each page have a certain number of slides.  Say the carousel takes
	 * 10 slides, but only 7 are present. This loop will run 3 times in that
	 * instance to allow you to output dummy slides.
	 *
	 * @param int total Total number of items that exist.
	 * @param int make_divisible_by Number that the padded total will be divisible by.
	 */
	function loop_fill() {
		$numerator = (int) $this->EE->TMPL->fetch_param('total', '1');
		$denominator = (int) $this->EE->TMPL->fetch_param('make_divisible_by', '1');
		$denominator = ($denominator === 0) ? 1 : $denominator;
		$needed_iterations = $denominator - ($numerator % $denominator);
		$needed_iterations = ($needed_iterations === $denominator) ? 0 : $needed_iterations;

		$variables = array();
		$j = 1;
		for ($i = 1; $i <= $needed_iterations; $i += 1) {
			$variables[] = array(
				'current' => $j,
				'total' => $needed_iterations
			);
			$j++;
		}

		$this->return_data = (empty($variables)) ? '' : $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables);

		return $this->return_data;
	}

	function url_title_2_entry_id() {
		$url_title = $this->EE->TMPL->fetch_param('url_title', '');

		$sql = "SELECT `entry_id` FROM `exp_channel_titles` WHERE `url_title`=?;";
		$q = $this->EE->db->query($sql, array($url_title));

		if ($q->num_rows() > 0) {
			$this->return_data = $q->row()->entry_id;
		} else {
			$this->return_data = '';
		}

		return $this->return_data;
	}

	/*
		Strip HTML out of content. Can optionally allow html tags
		Wrapper for @Link: http://us.php.net/strip_tags
	 */
	function strip_tags() {
		$allowed_tags = $this->EE->TMPL->fetch_param('allowed_tags', '');
		$this->return_data = strip_tags($this->EE->TMPL->tagdata, $allowed_tags);

		return $this->return_data;
	}

	/** Attempts to make a proper title out of a url_title not associated with a entry. */
	function proper_title() {
		$url_title = $this->EE->TMPL->fetch_param('url_title', '');

		$this->return_data = ucwords(preg_replace('/[_-]/', ' ', $url_title));
		return $this->return_data;
	}

	/** Returns entire uri string (instead of having to check for each segment). */
	function all_segments() {
		return $this->EE->uri->uri_string();
	}

	/** Returns the number of segments in the current page's url. */
	function total_segments() {
		return $this->EE->uri->total_segments();
	}

	/** Returns the entire url for the current page, including domain and protocol. */
	function current_url() {
		$this->EE->load->helper('url');
		return current_url();
	}

	/** Ensures presence of http or https in a url, to prevent urls from pointing to wrong domain. */
	function ensure_http() {
		$this->return_data = $this->EE->TMPL->tagdata;
		if ($this->return_data == '') return '';

		if ((strpos($this->return_data, 'http://') === FALSE) || (strpos($this->return_data, 'https://') === FALSE)) {
			$this->return_data = 'http://'.$this->return_data;
		}

		return $this->return_data;
	}

	// -- Plugin Usage -- //
	public static function usage() {
		$buffer = 'See documentation on <a href="https://github.com/dsurgeons/SurgerEE/wiki">github</a>.';
		/*$readme_file = ltrim(dirname(__FILE__), '/').'/README.md';
		$buffer = file_get_contents($readme_file);*/

		return $buffer;
	}
}


/* End of file pi.surgeree.php */
/* Location: /system/expressionengine/third_party/surgeree/pi.surgeree.php */
