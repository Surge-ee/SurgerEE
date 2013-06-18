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
	'pi_version'	=> '1.5.4',
	'pi_author'		=> 'Digital Surgeons',
	'pi_author_url'	=> 'http://github.com/dsurgeons/SurgerEE',
	'pi_description'=> 'Various commonly needed items that make us want to use php in templates.',
	'pi_usage'		=> Surgeree::usage()
);


class Surgeree {

	/**
	 * @var Return value for EE template parsing.
	 */
	public $return_data;

	/**
	 * @var The prefix parameter passed to any method.
	 */
	private $prefix = '';

	/**
	 * Always-run code.
	 */
	public function __construct() {

		// Superglobal
		$this->EE =& get_instance();

		// Set prefix if in params.
		$this->prefix = $this->EE->TMPL->fetch_param('prefix', '');

	}

	// ------------------------------------------------------------------------
	//  Helpers
	// ------------------------------------------------------------------------

	/**
	 * Recursively apply the prefix to a "parse variables" array's keys.
	 *
	 * @param  array $variables An array to be passed to EE->TMPL->parse_variables.
	 * @return array The same array with prefixed keys.
	 */
	private function _prefixify($variables) {

		$return = array();

		foreach($variables as $key => $value) {
			// We only want to rewrite string keys.
			$newkey = is_integer($key) ? $key : $this->prefix.':'.$key;

			// We will want to recurse through values as well.
			$return[$newkey] = is_array($value) ? $this->_prefixify($value) : $value;
		}

		return $return;

	}

	/**
	 * Helper function replacing number_format accounting for groupings other than thousands.
	 *
	 * Taken from php documentation comments. @see http://php.net/manual/en/function.number-format.php#95293
	 */
	private function _betterNumberFormat($number, $precision, $decimal, $separator, $groupsize) {
		$number = sprintf("%0.{$precision}f",$number);
		$number = explode('.',$number);
		while (strlen($number[0]) % $groupsize) $number[0]= ' '.$number[0];
		$number[0] = str_split($number[0],$groupsize);
		$number[0] = join($separator[0],$number[0]);
		$number[0] = trim($number[0]);
		$number = join($decimal[0],$number);

		return $number;
	}

	/**
	 * Abstracts out the process of determining Yes/No, Y/N, True/False string booleans.
	 *
	 * @param  string $value String to be interpreted in boolean context.
	 * @return bool   Authentic boolean representation.
	 */
	private function _processYesNo($value) {
		$lowered = strtolower($value);

		return ($lowered === 'yes' || $lowered === 'y' || $lowered === 'true');
	}

	private function _get_post($type = 'get_post') {
		$var		= $this->EE->TMPL->fetch_param('varname', '');
		$td			= ltrim($this->EE->TMPL->tagdata);
		$sanitize 	= $this->EE->TMPL->fetch_param('sanitize', 'xss');
		$check_XID	= $this->_processYesNo($this->EE->TMPL->fetch_param( 'check_xid', $this->EE->config->item('secure_forms')) );// CP -> Admin -> Security and Privacy -> Security and Sessions
		$glue		= $this->EE->TMPL->fetch_param('glue', '');
		$delimiter 	= $this->EE->TMPL->fetch_param('delimiter', '');

		if (trim($var) === '')
		{
			$this->EE->TMPL->log_item("surgeree:".$type.":parameter varname empty");
			return $this->EE->TMPL->no_results();
		}

		if ( $this->EE->input->$type($var, TRUE) === FALSE )
		{
			$this->EE->TMPL->log_item("surgeree:".$type.":".$var.":empty");
			return $this->EE->TMPL->no_results();
		}

		if ($check_XID === TRUE )
		{
			/* to save on session cache because XID just can be checked once and the user can be grabing more than one var */
			if ( !isset($this->EE->session->cache['surgeree'][$type]['valid_XID']) ) {
				$valid_XID =
				$this->EE->session->cache['surgeree'][$type]['valid_XID'] =
				$this->EE->security->secure_forms_check($this->EE->input->$type('XID'));
			} else {
				$valid_XID = $this->EE->session->cache['surgeree'][$type]['valid_XID'];
			}

			if ($valid_XID === FALSE) {
				$this->EE->TMPL->log_item("surgeree:".$type.":invalid or old XID");
				return $this->EE->TMPL->no_results();
			}
		}

		$varvalue	= ( $sanitize !== 'filename' AND $sanitize !== 'search')
					? $this->EE->input->$type($var, TRUE)
					: $this->EE->input->$type($var);

		$varvalues =
		$vartags = array();

		$varvalues	= is_array($varvalue)
					? $varvalue
					: ( $delimiter !== ''
					? explode($delimiter, $varvalue)
					: array($varvalue) );

		foreach ($varvalues as $value)
		{
			if( !is_array( $value ) )// prevent multidimensional arrays, at least at the first level
			{
				$value = (string)$value;

				$value = $this->_sanitize($value, $sanitize);

				$vartags[] =  array('surgeree:'.$type.':value' => $value);
				$this->EE->TMPL->log_item("surgeree:".$type.":".$var.":value: ".$value);
			}
		}

		if ( empty($td) ) {
			$varvalue = implode($glue, $varvalues);

			$this->EE->TMPL->log_item("surgeree:".$type.":".$var.":value: ".$varvalue);

			return $varvalue;
		}
		if ( empty($vartags) ) {
			$this->EE->TMPL->log_item("surgeree:".$type.":".$var.":value:no_results");
			return $this->EE->TMPL->no_results();
		}

		return $this->EE->TMPL->parse_variables( $td, $vartags );
	}

	private function _sanitize($value, $type = 'xss')
	{
		switch($type) {
			case 'filename':
				$value = $this->EE->security->sanitize_filename( $value );
				break;

			case  'search':
				$this->EE->load->helper('search');
				$value = sanitize_search_terms( $value );
				break;

			default:
				$value = $this->EE->security->xss_clean( $value );
				switch($type) {
					case  'sql':
						$value = $this->EE->db->escape_str($value);
						break;

					case  'html':
						$value = htmlspecialchars($value, ENT_QUOTES);
						break;

				}
		}

		return $value;
	}

	// ------------------------------------------------------------------------
	//  Tag Methods
	// ------------------------------------------------------------------------

	/**
	 * Applies the modulo operator to a numerator and denominator.
	 *
	 * Useful for outputting stuff every third, fourth, etc entry in
	 * a loop.
	 */
	function modulo() {
		$numerator = $this->EE->TMPL->fetch_param('numerator', '1');
		$denominator = $this->EE->TMPL->fetch_param('denominator', '1');
		$denominator = ($denominator == 0) ? 1 : $denominator;
		$this->return_data = $numerator % $denominator;
		return $this->return_data;
	}

	/**
	 * Generic divide-then-round.
	 */
	function round_divide() {
		$numerator = (int) $this->EE->TMPL->fetch_param('numerator', '1');
		$denominator = (int) $this->EE->TMPL->fetch_param('denominator', '1');
		$round = $this->EE->TMPL->fetch_param('round', 'up');
		$denominator = ($denominator === 0) ? 1 : $denominator;
		$this->return_data = ($round === 'up') ? ceil($numerator / $denominator) : floor($numerator / $denominator);
		return $this->return_data;
	}

	/**
	 * Grab value of a get variable
	 */
	function get() {
		return $this->return_data = $this->_get_post('get');
	}

	/**
	 * Grab value of a post variable
	 */
	function post() {
		return $this->return_data = $this->_get_post('post');
	}

	/** Try to grab value of a variable, looking first in post, and then in get */
	function get_post() {
		return $this->return_data = $this->_get_post();
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

	/**
	 * Formats a passed number in specified format. Useful for localization.
	 */
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

	/**
	 * A looping tag returning all of the years for which there are entries.
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
		$exclude_current_year = $this->_processYesNo($this->EE->TMPL->fetch_param('exclude_current_year', ''));

		$channels = explode('|', $this->EE->TMPL->fetch_param('channel', ''));
		$num_channels = count($channels);

		$statuses = explode('|', $this->EE->TMPL->fetch_param('status', 'open'));
		$num_statuses = count($statuses);

		$sort = (strtoupper($this->EE->TMPL->fetch_param('sort', 'desc')) === 'DESC') ? 'DESC' : 'ASC';

		$this->EE->db->select('channel_titles.year')
			->from('channel_titles')
			->join('channels', 'channels.channel_id = channel_titles.channel_id');

		// Add in current year exclusion if applicable
		if ($exclude_current_year) {
			$current_year = date('Y');
			$this->EE->db->where('channel_titles.year !=', $current_year);
		}

		// Add which channel(s) it should come from
		if ($num_channels > 0) {
			$channels_group = "(";
			for ($i = 0; $i < $num_channels; $i++) {
				$channel = $this->EE->db->escape($channels[$i]);
				$channels_group .= "`exp_channels`.`channel_name` = $channel";
				if ($i != ($num_channels - 1)) { $channels_group .= " OR ";}
			}
			$channels_group .= ")";

			$this->EE->db->where($channels_group, null, false);
		}

		// Add which statuses it should pull.
		$status_group = "(";
		for ($i = 0; $i < $num_statuses; $i++) {
			$status = $this->EE->db->escape($statuses[$i]);
			$status_group .= "`exp_channel_titles`.`status` = $status";
			if ($i != ($num_statuses - 1)) { $status_group .= " OR "; }
		}
		$status_group .= ")";

		$this->EE->db->where($status_group, null, false);

		// Run the query, order it properly.
		$query = $this->EE->db->group_by('channel_titles.year')
			->order_by('channel_titles.entry_date', $sort)
			->get();

		$variables = array();
		foreach($query->result() as $row) {
			$variables[] = array(
				'year' => $row->year
			);
		}

		return $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $this->_prefixify($variables));
	}

	/**
	 * Checks if an integer is halfway rounded up or down through the total.
	 *
	 * Useful for determining if you are halfway through a loop.
	 */
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

	/**
	 * Splits a string by a delimiter.
	 */
	function split_string() {
		$delimiter = $this->EE->TMPL->fetch_param('delimiter', '');
		$string = $this->EE->TMPL->fetch_param('string', '');

		if ( $delimiter == '' OR $string == '') {
			return $this->return_data = $this->EE->TMPL->no_results();
		}

		$vartags = array();
		$a = explode($delimiter, $string);

		foreach ($a as $v) {
			$vartags[] =  array('item' => $v);
		}

		return $this->return_data = $this->EE->TMPL->parse_variables(ltrim($this->EE->TMPL->tagdata), $this->_prefixify($vartags));
	}

	/**
	 * Performs a regex replace on a string
	 */
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

	/**
	 * Performs a multiple regex replace on a string
	 */
	function replace_multiple() {
		$param = $this->EE->TMPL->fetch_param('string', '');
		$tagdata = $this->EE->TMPL->tagdata;
		$string = ($tagdata != '' && $param == '') ? $tagdata : $param ;
		$regex   = $this->EE->TMPL->fetch_param('regex', '');
		$replace  = $this->EE->TMPL->fetch_param('replace', '');

		$i=0;

		$regex_array = explode("|",$regex);
		$replace_array = explode("|",$replace);

		foreach($regex_array as $loop) {
			$string = preg_replace("/$loop/",$replace_array[$i],$string);
			$i++;
		}

		//Output transformed string
		$this->return_data = $string;
		return $this->return_data;
	}


	/**
	 * Searches for a regex in a string
	 */
	function match() {
		$string = $this->EE->TMPL->fetch_param('string', '');
		$regex   = $this->EE->TMPL->fetch_param('regex', '');

		//Output transformed string
		$this->return_data = (preg_match("/$regex/", $string)) ? 'y' : 'n';
		return $this->return_data;
	}

	/**
	 * Just loops a certain number of times.
	 */
	function loop() {

		$zero_based = $this->_processYesNo($this->EE->TMPL->fetch_param('zero_based', 'no'));
		$iters      = (int) $this->EE->TMPL->fetch_param('iterations', '1');
		$increment  = (int) $this->EE->TMPL->fetch_param('increment', '1');
		$start      = (int) $this->EE->TMPL->fetch_param('start', '1');
		$total      = floor($iters/$increment);
		$variables  = array();

		$j = $start;
		for ($i = 0; $i < $iters; $i += $increment) {
			$variables[] = array(
				'index'	=> ($zero_based) ? $i - 1 : $i,
				'current' => ($zero_based) ? $j - 1 : $j,
				'total' => $total
			);
			$j++;
		}

		return $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $this->_prefixify($variables));

	}

	/**
	 * Loops enough times to make a completed parent loop divisible by a number.
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

		$this->return_data = (empty($variables)) ? '' : $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $this->_prefixify($variables));

		return $this->return_data;
	}

	function url_title_2_entry_id() {
		$url_title = $this->EE->TMPL->fetch_param('url_title', '');

		$q = $this->EE->db->select('entry_id')
			->from('channel_titles')
			->where('url_title', $url_title)
			->get();

		if ($q->num_rows() > 0) {
			$this->return_data = $q->row('entry_id');
		} else {
			$this->return_data = '';
		}

		return $this->return_data;
	}

	function entry_id_2_url_title() {
		$entry_id = $this->EE->TMPL->fetch_param('entry_id', '');

		$this->return_data = '';

		if ( intval($entry_id) !== 0 ) {
			$q = $this->EE->db->select('url_title')
				->from('channel_titles')
				->where('entry_id', $entry_id)
				->get();

			if ($q->num_rows() > 0) {
				$this->return_data = $q->row('url_title');
			}
		}

		return $this->return_data;
	}

	function entry_id_2_title() {
		$entry_id = $this->EE->TMPL->fetch_param('entry_id', '');

		$q = $this->EE->db->select('title')
			->from('channel_titles')
			->where('entry_id', $entry_id)
			->get();

		if ($q->num_rows() > 0) {
			$this->return_data = $q->row('title');
		} else {
			$this->return_data = '';
		}

		return $this->return_data;
	}

	/**
	 * Strip HTML out of content. Can optionally allow html tags
	 *
	 * Wrapper for @link: http://us.php.net/strip_tags
	 */
	function strip_tags() {

		$allowed_tags = $this->EE->TMPL->fetch_param('allowed_tags', '');
		$chars = $this->EE->TMPL->fetch_param('chars');
		$words = $this->EE->TMPL->fetch_param('words');

		$this->return_data = strip_tags($this->EE->TMPL->tagdata, $allowed_tags);
		if ( !empty($chars) && is_numeric($chars) ) {
			$this->return_data = $this->EE->functions->char_limiter($this->return_data, $chars);
		} elseif ( !empty($words) && is_numeric($words) ) {
			$this->return_data = $this->EE->functions->word_limiter($this->return_data, $chars);
		}

		return $this->return_data;
	}

	function url_encode() {
		$use_raw_method = $this->_processYesNo($this->EE->TMPL->fetch_param('raw','yes'));
		$tagdata = $this->_processYesNo( $this->EE->TMPL->fetch_param('parse_conditionals', 'no') )
				? $this->EE->TMPL->advanced_conditionals($this->EE->TMPL->tagdata)
				: $this->EE->TMPL->tagdata;
		if ($use_raw_method) {
			$this->return_data = rawurlencode($tagdata);
		} else {
			$this->return_data = urlencode($tagdata);
		}
		return $this->return_data;
	}

	function url_decode() {
		$use_raw_method = $this->_processYesNo($this->EE->TMPL->fetch_param('raw','yes'));

		if ($use_raw_method) {
			$this->return_data = rawurldecode($this->EE->TMPL->tagdata);
		} else {
			$this->return_data = urldecode($this->EE->TMPL->tagdata);
		}
		return $this->return_data;
	}

	/**
	 * Ensure valid urls.
	 *
	 * @see  https://github.com/EllisLab/Valid-Url/blob/master/valid_url/pi.valid_url.php
	 */
	function url_fix() {
		$protected = array('&' => 'AMPERSANDMARKER', '/' => 'SLASHMARKER', '=' => 'EQUALSMARKER');

		$str = str_replace(SLASH, '/', trim(urldecode(str_replace('&amp;', '&', $this->EE->TMPL->tagdata))));

		// really, really bad URL
		if (($url = @parse_url($str)) === FALSE || (! isset($url['scheme']) && ($url = @parse_url("http://{$str}")) === FALSE) ) {
			$this->EE->TMPL->log_item('Surgeree:url_fix Plugin error: unable to parse URL '.htmlentities($str));
			return;
		}

		foreach ($url as $k => $v) {
			switch($k) {
				case 'path':
					$url[$k] = urlencode(str_replace(array_keys($protected), $protected, $v));
				break;
				case 'query':
					$url[$k] = '?'.urlencode(str_replace(array_keys($protected), $protected, $v));
				break;
				case 'scheme':
					$url[$k] .= ($v == 'file') ? ':///' : '://';
				break;
			}
		}

		return $this->return_data = implode('', str_replace('&', '&amp;', str_replace($protected, array_keys($protected), $url)));
	}

	function redirect(){
		/* using code igniter, since EE->redirect doesn't allow response_code */

		$location			= $this->EE->TMPL->fetch_param('location', '');
		$response_code		= $this->EE->TMPL->fetch_param('response_code', 302);
		$redirect_method 	= $this->EE->config->item('redirect_method') == 'refresh'
							? 'refresh'
							: 'location';
		if ( !empty($location) ) {
			$this->EE->load->helper('url');
			return redirect($location,$redirect_method,$response_code);
		}
	}
	/**
	 * Attempts to make a proper title out of a url_title not associated with a entry.
	 */
	function proper_title() {
		$url_title = $this->EE->TMPL->fetch_param('url_title', '');

		$this->return_data = ucwords(preg_replace('/[_-]/', ' ', $url_title));
		return $this->return_data;
	}

	/**
	 * Returns the number of segments in the current page's url.
	 */
	function total_segments() {
		return $this->EE->uri->total_segments();
	}

	/**
	 * Returns the entire url for the current page, including domain and protocol.
	 */
	function current_url() {
		$this->EE->load->helper('url');
		return current_url();
	}

	/**
	 * Returns the entire uri for the current page
	 *
	 * Includes the first /, to match the behavior of the page_uri of exp:channel:entries.
	 */
	function current_uri() {
		$uri = $this->EE->uri->uri_string();
		return (substr($uri, 0) !== '/') ? '/'.$uri : $uri;
	}

	function referer() {
		return $this->EE->input->server('HTTP_REFERER', TRUE);
	}

	function previous_url() {
		$default = $this->EE->TMPL->fetch_param('default', '');

		$this->return_data = isset($this->EE->session->tracker[1]) ?
			($this->EE->session->tracker[1] == 'index' ? '/' : $this->EE->session->tracker[1])
			: $default;

		return $this->return_data;
	}

	/**
	 * Ensures presence of http in a url, to prevent urls from pointing to wrong domain.
	 */
	function ensure_http() {
		$this->return_data = $this->EE->TMPL->tagdata;
		if ($this->return_data == '') return '';

		if (strpos($this->return_data, 'http://') === FALSE) {
			$this->return_data = 'http://'.$this->return_data;
		}

		return $this->return_data;
	}

	/**
	 * Ensures that a trailing slash in a url is either present or not.
	 */
	function trailing_slash() {

		$trim = $this->_processYesNo($this->EE->TMPL->fetch_param('trim', 'no'));
		$has_trailing_slash = substr($this->EE->TMPL->tagdata, -1) === '/';
		$this->return_data = $this->EE->TMPL->tagdata;

		if ($trim && $has_trailing_slash) {
			$this->return_data = substr($this->return_data, 0, -1);
		} elseif (!$trim && !$has_trailing_slash) {
			$this->return_data = $this->return_data . '/';
		}

		return $this->return_data;
	}

	function parse_file_paths() {
		if ($td = $this->EE->TMPL->tagdata)
		{
			$this->EE->load->library('typography');
			$this->EE->typography->initialize();
			$this->EE->typography->parse_images = TRUE;

			return $this->return_data = $this->EE->typography->parse_file_paths($td);
		}
	}

	/**
	 * Allows us to read the value of any dynamic variables being set on the page.
	 */
	function read_dynamic_variable() {
		$variable = $this->EE->TMPL->fetch_param('variable');

		$this->return_data = '';

		if (isset($_POST[$variable])) {
			$this->return_data = $_POST[$variable];
		}

		return $this->return_data;
	}

	// ------------------------------------------------------------------------
	//  Usage
	// ------------------------------------------------------------------------

	/**
	 * Usage to be displayed on the control panel documentation page.
	 */
	public static function usage() {
		$buffer = 'See documentation on <a href="https://github.com/dsurgeons/SurgerEE/wiki">github</a>.';
		/*$readme_file = ltrim(dirname(__FILE__), '/').'/README.md';
		$buffer = file_get_contents($readme_file);*/

		return $buffer;
	}

}

/* End of file pi.surgeree.php */
/* Location: /system/expressionengine/third_party/surgeree/pi.surgeree.php */
