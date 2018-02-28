<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;

use Joomla\Registry\Registry;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class com_eventbookingInstallerScript
{

	public static $languageFiles = array('en-GB.com_eventbooking.ini');

	protected $installType;

	/**
	 * Method to run before installing the component
	 */
	public function preflight($type, $parent)
	{

		//Backup the old language files
		foreach (self::$languageFiles as $languageFile)
		{
			if (JFile::exists(JPATH_ROOT . '/language/en-GB/' . $languageFile))
			{
				JFile::copy(JPATH_ROOT . '/language/en-GB/' . $languageFile, JPATH_ROOT . '/language/en-GB/bak.' . $languageFile);
			}
		}

		//Delete the css files which are now moved to themes folder
		$files = array('default.css', 'fire.css', 'leaf.css', 'ocean.css', 'sky.css', 'tree.css');
		$path  = JPATH_ROOT . '/components/com_eventbooking/assets/css/';
		foreach ($files as $file)
		{
			$filePath = $path . $file;
			if (JFile::exists($filePath))
			{
				JFile::delete($filePath);
			}
		}

		//Backup files which need to be keep 
		if (JFile::exists(JPATH_ROOT . '/components/com_eventbooking/fields.xml'))
		{
			JFile::copy(JPATH_ROOT . '/components/com_eventbooking/fields.xml', JPATH_ROOT . '/components/com_eventbooking/bak.fields.xml');
		}

		if (JFolder::exists(JPATH_ROOT . '/components/com_eventbooking/views'))
		{
			JFolder::delete(JPATH_ROOT . '/components/com_eventbooking/views');
		}

		if (JFolder::exists(JPATH_ROOT . '/administrator/components/com_eventbooking/controller'))
		{
			JFolder::delete(JPATH_ROOT . '/administrator/components/com_eventbooking/controller');
		}

		// Backup css file
		if (JFile::exists(JPATH_ROOT . '/components/com_eventbooking/assets/css/custom.css'))
		{
			JFile::copy(JPATH_ROOT . '/components/com_eventbooking/assets/css/custom.css', JPATH_ROOT . '/components/com_eventbooking/custom.css');
		}

		if (JFolder::exists(JPATH_ROOT . '/components/com_eventbooking/view/common'))
		{
			JFolder::delete(JPATH_ROOT . '/components/com_eventbooking/view/common');
		}

		if (JFolder::exists(JPATH_ROOT . '/components/com_eventbooking/emailtemplates'))
		{
			JFolder::delete(JPATH_ROOT . '/components/com_eventbooking/emailtemplates');
		}

		// Fix mistake causes by a bug in version 2.9.0
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('extension_id, manifest_cache')
			->from('#__extensions')
			->where('`type` = "plugin"')
			->where('`folder` = "eventbooking"')
			->where('`element` = "system"');
		$db->setQuery($query);
		$plugins = $db->loadObjectList();

		if (count($plugins) > 1)
		{
			$processedPlugins = array();

			$installer = new JInstaller();

			foreach ($plugins as $plugin)
			{
				$params   = new Registry($plugin->manifest_cache);
				$filename = $params->get('filename');
				if ($filename && !in_array($filename, $processedPlugins))
				{
					$processedPlugins[] = $filename;
					$query->clear()
						->update('#__extensions')
						->set('`element` = ' . $db->quote($filename))
						->where('extension_id = ' . $plugin->extension_id);
					$db->setQuery($query);
					$db->execute();
				}
				else
				{
					try
					{
						$installer->uninstall('plugin', $plugin->extension_id, 0);
					}
					catch (\Exception $e)
					{

					}
				}
			}

			// Clear messages queue
			JFactory::getApplication()->getMessageQueue();
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

	/**
	 * Method to run after installing the component
	 */
	public function postflight($type, $parent)
	{
		//Restore the modified language strings by merging to language files
		$registry = new Registry();
		foreach (self::$languageFiles as $languageFile)
		{
			$backupFile  = JPATH_ROOT . '/language/en-GB/bak.' . $languageFile;
			$currentFile = JPATH_ROOT . '/language/en-GB/' . $languageFile;

			if (JFile::exists($currentFile) && JFile::exists($backupFile))
			{
				$registry->loadFile($currentFile, 'INI');
				$currentItems = $registry->toArray();
				$registry->loadFile($backupFile, 'INI');
				$backupItems = $registry->toArray();
				$items       = array_merge($currentItems, $backupItems);
				$content     = "";
				foreach ($items as $key => $value)
				{
					$content .= "$key=\"$value\"\n";
				}
				JFile::write($currentFile, $content);
				//Delete the backup file
				JFile::delete($backupFile);
			}
		}
		//Restore the renamed files
		if (JFile::exists(JPATH_ROOT . '/components/com_eventbooking/bak.fields.xml'))
		{
			JFile::copy(JPATH_ROOT . '/components/com_eventbooking/bak.fields.xml', JPATH_ROOT . '/components/com_eventbooking/fields.xml');
			JFile::delete(JPATH_ROOT . '/components/com_eventbooking/bak.fields.xml');
		}

		if (JFile::exists(JPATH_ROOT . '/components/com_eventbooking/custom.css'))
		{
			JFile::move(JPATH_ROOT . '/components/com_eventbooking/custom.css', JPATH_ROOT . '/media/com_eventbooking/assets/css/custom.css');
		}

		$customCss = JPATH_ROOT . '/media/com_eventbooking/assets/css/custom.css';
		if (!file_exists($customCss))
		{
			$fp = fopen($customCss, 'w');
			fclose($fp);
			@chmod($customCss, 0777);
		}

		if ($this->installType == 'install')
		{
			$db  = JFactory::getDbo();
			$sql = 'SELECT COUNT(*) FROM #__eb_messages';
			$db->setQuery($sql);
			$total = $db->loadResult();
			if (!$total)
			{
				$configSql = JPATH_ADMINISTRATOR . '/components/com_eventbooking/sql/messages.eventbooking.sql';
				$sql       = JFile::read($configSql);
				$queries   = $db->splitSql($sql);
				if (count($queries))
				{
					foreach ($queries as $query)
					{
						$query = trim($query);
						if ($query != '' && $query{0} != '#')
						{
							$db->setQuery($query);
							$db->execute();
						}
					}
				}
			}
		}
		
		// Create folders to store categories and events images
		if (!JFolder::exists(JPATH_ROOT . '/images/com_eventbooking'))
		{
			JFolder::create(JPATH_ROOT . '/images/com_eventbooking');
		}

		if (!JFolder::exists(JPATH_ROOT . '/images/com_eventbooking/categories'))
		{
			JFolder::create(JPATH_ROOT . '/images/com_eventbooking/categories');
		}

		if (!JFolder::exists(JPATH_ROOT . '/images/com_eventbooking/categories/thumb'))
		{
			JFolder::create(JPATH_ROOT . '/images/com_eventbooking/thumb');
		}
	}
}