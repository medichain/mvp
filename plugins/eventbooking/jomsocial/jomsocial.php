<?php
/**
 * @package        Joomla
 * @subpackage     Events Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 - 2017 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

class plgEventbookingJomSocial extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param   object &$subject   The object to observe
	 * @param   array  $config     An optional associative array of configuration settings.
	 *                             Recognized key values include 'name', 'group', 'params', 'language'
	 *                             (this list is not meant to be comprehensive).
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		$this->canRun = file_exists(JPATH_ROOT . '/components/com_community/community.php');
	}

	/**
	 * Method to get list of custom fields in Jomsocial used to map with fields in Membership Pro
	 *
	 * Method is called on custom field add / edit page from backend of Membership Pro
	 *
	 * @return mixed
	 */
	public function onGetFields()
	{
		if ($this->canRun)
		{
			$db  = JFactory::getDbo();
			$sql = 'SELECT fieldcode AS `value`, fieldcode AS `text` FROM #__community_fields WHERE published=1 AND fieldcode != ""';
			$db->setQuery($sql);

			return $db->loadObjectList();
		}
	}

	/**
	 * Method to get data stored in Jomsocial profile of the given user
	 *
	 * @param int   $userId
	 * @param array $mappings
	 *
	 * @return array
	 */
	public function onGetProfileData($userId, $mappings = array())
	{
		if ($this->canRun)
		{
			$synchronizer = new RADSynchronizerJomsocial();

			return $synchronizer->getData($userId, $mappings);
		}
	}

	/**
	 * Method to create Jomsocial account for registrants when they register for an event in Events Booking
	 *
	 * @param $row
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
			$db  = JFactory::getDbo();
			$sql = 'SELECT COUNT(*) FROM #__community_users WHERE userid=' . $row->user_id;
			$db->setQuery($sql);
			$count = $db->loadResult();
			if ($count)
			{
				return;
			}

			$sql = 'INSERT INTO #__community_users(userid) VALUES(' . $row->user_id . ')';
			$db->setQuery($sql);
			$db->execute();

			$sql = 'SELECT id, fieldcode FROM #__community_fields WHERE published=1 AND fieldcode != ""';
			$db->setQuery($sql);
			$fieldList = $db->loadObjectList('fieldcode');

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

				if ($rowField->field_mapping && isset($rowField->field_mapping, $fieldList) && isset($data[$rowField->name]))
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
			if (count($fieldValues))
			{
				foreach ($fieldValues as $fieldCode => $fieldValue)
				{
					$fieldId = $fieldList[$fieldCode]->id;
					if ($fieldId)
					{
						$fieldValue = $db->quote($fieldValue);
						$sql        = "INSERT INTO #__community_fields_values(user_id, field_id, `value`, `access`) VALUES($row->user_id, $fieldId, $fieldValue, 1)";
						$db->setQuery($sql);
						$db->execute();
					}
				}
			}

		}

		return true;
	}
}
