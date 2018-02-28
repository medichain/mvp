<?php
/**
 * @package        Joomla
 * @subpackage     Events Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2015 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

class plgEventbookingUserprofile extends JPlugin
{
	/**
	 * Flag to determine whether the plugin need to run or not
	 *
	 * @var bool
	 */
	protected $canRun;

	/**
	 * Constructor.
	 *
	 * @param object $subject
	 * @param array  $config
	 */
	public function __construct(& $subject, $config = array())
	{
		parent::__construct($subject, $config);

		$this->canRun = JPluginHelper::isEnabled('user', 'profile') || count(self::getUserFields());
	}

	/**
	 * Get list of profile fields used for mapping with fields in Events Booking
	 *
	 * @return array
	 */
	public function onGetFields()
	{
		if ($this->canRun)
		{
			$options = array();

			if (JPluginHelper::isEnabled('user', 'profile'))
			{
				$fields = array('address1', 'address2', 'city', 'region', 'country', 'postal_code', 'phone', 'website', 'favoritebook', 'aboutme', 'dob');

				foreach ($fields as $field)
				{
					$options[] = JHtml::_('select.option', $field, $field);
				}
			}

			foreach (self::getUserFields() as $field)
			{
				$options[] = JHtml::_('select.option', $field->name, $field->title);
			}


			return $options;
		}
	}

	/**
	 * Method to get data stored in CB profile of the given user
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

			$synchronizer = new RADSynchronizerJoomla();

			$data = $synchronizer->getData($userId, $mappings);

			$fields = self::getUserFields();

			if (count($fields))
			{
				JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_fields/models', 'FieldsModel');

				/* @var FieldsModelField $model */
				$model = JModelLegacy::getInstance('Field', 'FieldsModel', array('ignore_request' => true));

				$fieldIds = array();

				foreach ($mappings as $fieldName => $mappingFieldName)
				{
					if ($mappingFieldName && isset($fields[$mappingFieldName]))
					{
						$fieldIds[] = $fields[$mappingFieldName]->id;
					}
				}

				$fieldValues = $model->getFieldValues($fieldIds, $userId);

				foreach ($mappings as $fieldName => $mappingFieldName)
				{
					if ($mappingFieldName && isset($fields[$mappingFieldName]))
					{
						$fieldId = $fields[$mappingFieldName]->id;

						if (isset($fieldValues[$fieldId]))
						{
							$data[$fieldName] = $fieldValues[$fieldId];
						}
					}
				}
			}

			return $data;
		}
	}

	/**
	 * Run when a membership activated
	 *
	 * @param EventbookingTableRegistrant $row
	 */
	public function onAfterStoreRegistrant($row)
	{
		if ($row->user_id)
		{
			$config = EventbookingHelper::getConfig();
			$userId = $row->user_id;

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

			$fieldMappings = array();

			foreach ($rowFields as $rowField)
			{
				if ($rowField->field_mapping)
				{
					$fieldMappings[$rowField->field_mapping] = $rowField->name;
				}
			}

			// Store user profile data
			if (JPluginHelper::isEnabled('user', 'profile'))
			{
				$db = JFactory::getDbo();

				// Update Name of users based on first name and last name from profile
				$user = JFactory::getUser($userId);
				$user->set('name', $row->first_name . ' ' . $row->last_name);
				$user->save(true);

				$deleteFields = array(
					'profile.address1',
					'profile.address2',
					'profile.city',
					'profile.region',
					'profile.country',
					'profile.postal_code',
					'profile.phone',
					'profile.website',
					'profile.favoritebook',
					'profile.aboutme',
					'profile.dob',
				);

				//Delete old profile data
				$db->setQuery(
					'DELETE FROM #__user_profiles WHERE user_id = ' . $userId .
					' AND profile_key IN ("' . implode('","', $deleteFields) . '")'
				);
				$db->execute();


				$fields = array(
					'address1',
					'address2',
					'city',
					'region',
					'country',
					'postal_code',
					'phone',
					'website',
					'favoritebook',
					'aboutme',
					'dob',
				);

				$tuples = array();
				$order  = 1;

				foreach ($fields as $field)
				{
					$value = '';

					if (isset($fieldMappings[$field]))
					{
						$fieldMapping = $fieldMappings[$field];

						if (isset($data[$fieldMapping]))
						{
							$value = $data[$fieldMapping];
						}
					}

					$tuples[] = '(' . $userId . ', ' . $db->quote('profile.' . $field) . ', ' . $db->quote(json_encode($value)) . ', ' . $order++ . ')';
				}

				$db->setQuery('INSERT INTO #__user_profiles VALUES ' . implode(', ', $tuples));
				$db->execute();
			}

			// Store user custom fields
			$fields = self::getUserFields();

			if (count($fields))
			{
				JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_fields/models', 'FieldsModel');

				/* @var FieldsModelField $model */
				$model = JModelLegacy::getInstance('Field', 'FieldsModel', array('ignore_request' => true));

				foreach ($fields as $field)
				{
					$fieldName = $field->name;

					if (isset($fieldMappings[$fieldName]))
					{
						$fieldMapping = $fieldMappings[$fieldName];

						if (isset($data[$fieldMapping]))
						{
							$fieldValue = $data[$fieldMapping];
						}
						else
						{
							$fieldValue = '';
						}

						$model->setFieldValue($field->id, $userId, $fieldValue);
					}
				}
			}
		}
	}

	/**
	 * Get list of custom fields belong to com_users
	 *
	 * @return array
	 */
	public static function getUserFields()
	{
		if (version_compare(JVERSION, '3.7.0', 'ge'))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id, name')
				->from('#__fields')
				->where($db->quoteName('context') . '=' . $db->quote('com_users.user'))
				->where($db->quoteName('state') . ' = 1');
			$db->setQuery($query);

			return $db->loadObjectList('name');
		}

		return array();
	}
}
