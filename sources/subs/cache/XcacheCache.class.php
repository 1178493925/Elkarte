<?php
/**
 * This file contains functions that deal with getting and setting cache values.
 *
 * @name      ElkArte Forum
 * @copyright ElkArte Forum contributors
 * @license   BSD http://opensource.org/licenses/BSD-3-Clause
 *
 * @version 1.0 Release Candidate 2
 *
 */

if (!defined('ELK'))
	die('No access...');

/**
 * Xcache.
 *
 * Xcache may need auth credentials, depending on how its been set up,
 * these credentials must be passed with the options array during the
 * initialization of the class in the indexes:
 *   - cache_uid
 *   - cache_password
 */
class Xcache_Cache extends Cache_Method_Abstract
{
	/**
	 * {@inheritdoc }
	 */
	public function init()
	{
		// Xcache may need auth credentials, depending on how its been set up
		if (!empty($this->_options['cache_uid']) && !empty($this->_options['cache_password']))
		{
			$_SERVER['PHP_AUTH_USER'] = $this->_options['cache_uid'];
			$_SERVER['PHP_AUTH_PW'] = $this->_options['cache_password'];
		}

		return function_exists('xcache_set') && ini_get('xcache.var_size') > 0;
	}

	/**
	 * {@inheritdoc }
	 */
	public function put($key, $value, $ttl = 120)
	{
		if ($value === null)
			xcache_unset($key);
		else
			xcache_set($key, $value, $ttl);
	}

	/**
	 * {@inheritdoc }
	 */
	public function get($key, $ttl = 120)
	{
		return xcache_get($key);
	}

	/**
	 * {@inheritdoc }
	 */
	public function clean($type = '')
	{
		// Get the counts so we clear each instance
		$pcnt = xcache_count(XC_TYPE_PHP);
		$vcnt = xcache_count(XC_TYPE_VAR);

		// Time to clear the user vars and/or the opcache
		if ($type === '' || $type === 'user')
		{
			for ($i = 0; $i < $vcnt; $i++)
				xcache_clear_cache(XC_TYPE_VAR, $i);
		}

		if ($type === '' || $type === 'data')
		{
			for ($i = 0; $i < $pcnt; $i++)
				xcache_clear_cache(XC_TYPE_PHP, $i);
		}
	}
}