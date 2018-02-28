<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class EventbookingControllerTool extends RADController
{
	/**
	 * Reset the urls table
	 */
	public function reset_urls()
	{
		JFactory::getDbo()->truncateTable('#__eb_urls');
		$this->setRedirect('index.php?option=com_eventbooking&view=dashboard', JText::_('Urls have successfully reset'));
	}

	/**
	 * Setup multilingual fields
	 */
	public function setup_multilingual_fields()
	{
		EventbookingHelper::setupMultilingual();
	}

	/**
	 * Method to allow sharing language files for Events Booking
	 */
	public function share_translation()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('lang_code')
			->from('#__languages')
			->where('published = 1')
			->where('lang_code != "en-GB"')
			->order('ordering');
		$db->setQuery($query);
		$languages = $db->loadObjectList();

		if (count($languages))
		{
			$mailer   = JFactory::getMailer();
			$jConfig  = JFactory::getConfig();
			$mailFrom = $jConfig->get('mailfrom');
			$fromName = $jConfig->get('fromname');
			$mailer->setSender(array($mailFrom, $fromName));
			$mailer->addRecipient('tuanpn@joomdonation.com');
			$mailer->setSubject('Language Packages for Events Booking shared by ' . JUri::root());
			$mailer->setBody('Dear Tuan \n. I am happy to share my language packages for Events Booking.\n Enjoy!');
			foreach ($languages as $language)
			{
				$tag = $language->lang_code;
				if (file_exists(JPATH_ROOT . '/language/' . $tag . '/' . $tag . '.com_eventbooking.ini'))
				{
					$mailer->addAttachment(JPATH_ROOT . '/language/' . $tag . '/' . $tag . '.com_eventbooking.ini', $tag . '.com_eventbooking.ini');
				}

				if (file_exists(JPATH_ADMINISTRATOR . '/language/' . $tag . '/' . $tag . '.com_eventbooking.ini'))
				{
					echo JPATH_ADMINISTRATOR . '/language/' . $tag . '/' . $tag . '.com_eventbooking.ini';
					$mailer->addAttachment(JPATH_ADMINISTRATOR . '/language/' . $tag . '/' . $tag . '.com_eventbooking.ini', 'admin.' . $tag . '.com_eventbooking.ini');
				}
			}

			require_once JPATH_COMPONENT . '/libraries/vendor/dbexporter/dumper.php';

			$tables = array($db->replacePrefix('#__eb_fields'), $db->replacePrefix('#__eb_messages'));

			try
			{

				$sqlFile = $tag . '.com_eventbooking.sql';
				$options = array(
					'host'           => $jConfig->get('host'),
					'username'       => $jConfig->get('user'),
					'password'       => $jConfig->get('password'),
					'db_name'        => $jConfig->get('db'),
					'include_tables' => $tables,
				);
				$dumper  = Shuttle_Dumper::create($options);
				$dumper->dump(JPATH_ROOT . '/tmp/' . $sqlFile);

				$mailer->addAttachment(JPATH_ROOT . '/tmp/' . $sqlFile, $sqlFile);

			}
			catch (Exception $e)
			{
				//Do nothing
			}

			$mailer->Send();

			$msg = 'Thanks so much for sharing your language files to Events Booking Community';
		}
		else
		{
			$msg = 'Thanks so willing to share your language files to Events Booking Community. However, you don"t have any none English langauge file to share';
		}

		$this->setRedirect('index.php?option=com_eventbooking&view=dashboard', $msg);
	}

	/**
	 * Method to make a given field search and sortable easier
	 */
	public function make_field_search_sort_able()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$fieldId = $this->input->getInt('field_id');

		$query->select('*')
			->from('#__eb_fields')
			->where('id = ' . (int) $fieldId);
		$db->setQuery($query);
		$field = $db->loadObject();

		if (!$field)
		{
			throw new Exception('The field does not exist');
		}

		// Add new field to #__eb_registrants
		$fields = array_keys($db->getTableColumns('#__eb_registrants'));

		if (!in_array($field->name, $fields))
		{
			$sql = "ALTER TABLE  `#__eb_registrants` ADD  `$field->name` VARCHAR( 255 ) NULL;";
			$db->setQuery($sql);
			$db->execute();

			$query->clear()
				->select('*')
				->from('#__eb_field_values')
				->where('field_id = '. $fieldId);
			$db->setQuery($query);
			$rows = $db->loadObjectList();

			$fieldName = $db->quoteName($field->name);

			foreach ($rows as $row)
			{
				$query->clear()
					->update('#__eb_registrants')
					->set($fieldName . ' = ' . $db->quote($row->field_value))
					->where('id = ' . $row->registrant_id);
				$db->setQuery($query);
				$db->execute();
			}
		}

		// Mark the field as searchable
		$query->clear()
			->update('#__eb_fields')
			->set('is_searchable = 1')
			->where('id = ' . (int) $fieldId);
		$db->setQuery($query);
		$db->execute();

		echo 'Done !';
	}

	/**
	 * Fix "Row size too large" issue
	 */
	public function fix_row_size()
	{
		$db = JFactory::getDbo();
		$db->setQuery('ALTER TABLE `#__eb_events` ENGINE = MYISAM ROW_FORMAT = DYNAMIC');
		$db->execute();
	}
}
