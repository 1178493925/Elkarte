<?php

/**
 * The main theme class
 *
 * @name      ElkArte Forum
 * @copyright ElkArte Forum contributors
 * @license   BSD http://opensource.org/licenses/BSD-3-Clause
 *
 * This file contains code covered by:
 * copyright:    2011 Simple Machines (http://www.simplemachines.org)
 * license:        BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version 1.1 beta 1
 *
 */

if (!defined('ELK'))
{
	die('No access...');
}

/**
 * Class Theme
 */
abstract class Theme
{
	const STANDARD = 'standard';
	const DEFERRED = 'defer';
	const ALL = -1;

	protected $id;

	protected $templates;
	protected $layers;

	protected $html_headers = array();
	protected $links = array();
	protected $js_files = array();
	protected $js_inline = array(
		'standard' => array(),
		'defer' => array()
	);
	protected $js_vars = array();
	protected $css_rules = array();
	protected $css_files = array();

	protected $rtl;

	/**
	 * Theme constructor.
	 *
	 * @param int $id
	 */
	public function __construct($id)
	{
		$this->layers = Template_Layers::getInstance();
		$this->templates = Templates::getInstance();

		$this->css_files = &$GLOBALS['context']['css_files'];
		$this->js_files = &$GLOBALS['context']['js_files'];
		$this->css_rules = &$GLOBALS['context']['css_rules'];
		if (empty($this->css_rules))
		{
			$this->css_rules = array(
				'all' => '',
				'media' => array(),
			);
		}
	}

	/**
	 * Add a Javascript variable for output later (for feeding text strings and similar to JS)
	 *
	 * @param mixed[] $vars array of vars to include in the output done as 'varname' => 'var value'
	 * @param bool $escape = false, whether or not to escape the value
	 */
	public function addJavascriptVar($vars, $escape = false)
	{
		if (empty($vars) || !is_array($vars))
		{
			return;
		}

		foreach ($vars as $key => $value)
			$this->js_vars[$key] = !empty($escape) ? JavaScriptEscape($value) : $value;
	}

	/**
	 * Add a CSS rule to a style tag in head.
	 *
	 * @param string $rules the CSS rule/s
	 * @param null|string $media = null, the media query the rule belongs to
	 */
	public function addCSSRules($rules, $media = null)
	{
		if (empty($rules))
		{
			return;
		}

		if ($media === null)
		{
			if (!isset($this->css_rules['all']))
			{
				$this->css_rules['all'] = '';
			}
			$this->css_rules['all'] .= '
		' . $rules;
		}
		else
		{
			if (!isset($this->css_rules['media'][$media]))
			{
				$this->css_rules['media'][$media] = '';
			}

			$this->css_rules['media'][$media] .= '
		' . $rules;
		}
	}

	/**
	 * Returns javascript vars loaded with addJavascriptVar function
	 *
	 * @return array
	 */
	public function getJavascriptVars()
	{
		return $this->js_vars;
	}

	/**
	 * Returns inline javascript of a give type that was added with addInlineJavascript function
	 *
	 * @param int $type One of ALL, SELF, DEFERRED class constants
	 *
	 * @return array
	 * @throws Exception if the type is not known
	 */
	public function getInlineJavascript($type = self::ALL)
	{
		switch ($type)
		{
			case self::ALL:
				return $this->js_inline;
			case self::DEFERRED:
				return $this->js_inline[self::DEFERRED];
			case self::STANDARD:
				return $this->js_inline[self::STANDARD];
		}

		throw new \Exception('Unknown inline Javascript type');
	}

	/**
	 * Add a block of inline Javascript code to be executed later
	 *
	 * What it does:
	 * - only use this if you have to, generally external JS files are better, but for very small scripts
	 *   or for scripts that require help from PHP/whatever, this can be useful.
	 * - all code added with this function is added to the same <script> tag so do make sure your JS is clean!
	 *
	 * @param string $javascript
	 * @param bool $defer = false, define if the script should load in <head> or before the closing <html> tag
	 */
	function addInlineJavascript($javascript, $defer = false)
	{
		if (!empty($javascript))
		{
			$this->js_inline[(!empty($defer) ? self::DEFERRED : self::STANDARD)][] = $javascript;
		}
	}

	/**
	 * Turn on/off RTL language support
	 *
	 * @param $toggle
	 *
	 * @return $this
	 */
	public function setRTL($toggle)
	{
		$this->rtl = (bool) $toggle;

		return $this;
	}
}