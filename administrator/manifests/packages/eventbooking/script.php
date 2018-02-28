<?php

/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
class Pkg_EventbookingInstallerScript
{
	protected $installType;

	public function preflight($type, $parent)
	{
		if (!version_compare(JVERSION, '3.5.0', 'ge'))
		{
			JError::raiseWarning(null, 'Cannot install Events Booking in a Joomla release prior to 3.5.0');

			return false;
		}

		if (version_compare(PHP_VERSION, '5.4.0', '<'))
		{
			JError::raiseWarning(null, 'Events Booking requires PHP 5.4.0+ to work. Please contact your hosting provider, ask them to update PHP version for your hosting account.');

			return false;
		}
	}

	/**
	 * method to install the component
	 *
	 * @return void
	 */
	public function install($parent)
	{
		$this->installType = 'install';
	}

	public function update($parent)
	{
		$this->installType = 'update';
	}

	public function postflight($type, $parent)
	{
		// Clear the cached extension data and menu cache
		$this->cleanCache('_system', 0);
		$this->cleanCache('_system', 1);
		$this->cleanCache('com_modules', 0);
		$this->cleanCache('com_modules', 1);
		$this->cleanCache('com_plugins', 0);
		$this->cleanCache('com_plugins', 1);
		$this->cleanCache('mod_menu', 0);
		$this->cleanCache('mod_menu', 1);

		JFactory::getApplication()->redirect(
			JRoute::_('index.php?option=com_eventbooking&task=update.update&install_type=' . $this->installType, false));
	}

	/**
	 * Clean the cache
	 *
	 * @param   string  $group     The cache group
	 * @param   integer $client_id The ID of the client
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		$conf = JFactory::getConfig();

		$options = array(
			'defaultgroup' => ($group) ? $group : JFactory::getApplication()->input->get('option'),
			'cachebase'    => ($client_id) ? JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache'),
			'result'       => true,
		);

		try
		{
			/** @var JCacheControllerCallback $cache */
			$cache = JCache::getInstance('callback', $options);
			$cache->clean();
		}
		catch (JCacheException $exception)
		{

		}
	}
}