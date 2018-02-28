<?php
/**
 * @package        Joomla
 * @subpackage     Events Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 - 2017 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

class plgEventbookingEasyprofile extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param   object &$subject   The object to observe
	 * @param   array  $config     An optional associative array of configuration settings.
	 *                             Recognized key values include 'name', 'group', 'params', 'language'
	 *                             (this list is not meant to be comprehensive).
	 */

	public function __construct(& $subject, $config = array())
	{
		parent::__construct($subject, $config);

		$this->canRun = file_exists(JPATH_ROOT . '/components/com_jsn/jsn.php');
	}

	/**
	 * Method to get data stored in EasyProfile of the given user
	 *
	 * @param int   $userId
	 * @param array $mappings
	 *
	 * @return array
	 */
	public function onGetProfileData($userId, $mappings)
	{
		if ($this->canRun)
		{
			$synchronizer = new RADSynchronizerEasyprofile();

			return $synchronizer->getData($userId, $mappings);
		}
	}

	/**
	 * Method to get list of custom fields in Easyprofile used to map with fields in Membership Pro
	 *
	 * Method is called on custom field add / edit page from backend of Membership Pro
	 *
	 * @return mixed
	 */
	public function onGetFields()
	{
		if ($this->canRun)
		{
			$db     = JFactory::getDbo();
			$fields = array_keys($db->getTableColumns('#__jsn_users'));
			$fields = array_diff($fields, array('id', 'params'));

			$options = array();

			foreach ($fields as $field)
			{
				$options[] = JHtml::_('select.option', $field, $field);
			}

			return $options;
		}
	}

	/**
	 * Method to create a CB account for subscriber if it does not exist yet
	 *
	 * @param SubscriberOSMembership $row
	 *            The subscription record
	 *
	 * @return bool
	 */
	public function onAfterStoreRegistrant($row)
	{
		if (!$this->canRun)
		{
			return;
		}

		if ($row->user_id)
		{
			$db = JFactory::getDbo();

			// Check if user exist
			$query = $db->getQuery(true);
			$query->select('a.id')->from('#__jsn_users AS a')->where('a.id = ' . $row->user_id);
			$db->setQuery($query);
			$profileId = $db->loadResult();

			// Get list of fields in #__jsn_users table
			$fieldList = array_keys($db->getTableColumns('#__jsn_users'));

			$config = EventbookingHelper::getConfig();
			if ($config->multiple_booking)
			{
				$rowFields = EventbookingHelperRegistration::getFormFields($row->id, 4);
			}
			elseif ($row->is_group_billing)
			{
				$rowFields = EventbookingHelperRegistration::getFormFields($row->event_id, 1);
			}
			else
			{
				$rowFields = EventbookingHelperRegistration::getFormFields($row->event_id, 0);
			}

			$data = EventbookingHelperRegistration::getRegistrantData($row, $rowFields);

			$fieldValues = array();

			foreach ($rowFields as $rowField)
			{
				if ($rowField->field_mapping && in_array($rowField->field_mapping, $fieldList) && isset($data[$rowField->name]))
				{
					$fieldValue = $data[$rowField->name];

					if (is_string($fieldValue) && is_array(json_decode($fieldValue)))
					{
						$fieldValues[$rowField->field_mapping] = implode('|*|', json_decode($fieldValue));
					}
					else
					{
						$fieldValues[$rowField->field_mapping] = $fieldValue;
					}
				}
			}

			if (!count($fieldValues))
			{
				return;
			}

			// Write Jsn User
			if ($profileId)
			{
				// Update User
				$query = $db->getQuery(true);
				$query->update("#__jsn_users");

				foreach ($fieldValues as $key => $value)
				{
					$query->set($db->quoteName($key) . ' = ' . $db->quote($value));
				}

				$query->where('id = ' . $row->user_id);
				$db->setQuery($query);
				$db->execute();
			}
			else
			{
				// New User
				$fields = array();
				$values = array();

				foreach ($fieldValues as $key => $value)
				{
					$fields[] = $db->quoteName($key);
					$values[] = $db->quote($value);
				}

				$query = "INSERT INTO #__jsn_users(id," . implode(', ', $fields) . ") VALUES(" . $row->user_id . ", " . implode(', ', $values) . ")";
				$db->setQuery($query);
				$db->execute();
			}
		}
	}
}
