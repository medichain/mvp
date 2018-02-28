<?php
/**
 * @package        Joomla
 * @subpackage     Events Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2015 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

class plgEventbookingContactenhanced extends JPlugin
{
	public function __construct(& $subject, $config = array())
	{
		parent::__construct($subject, $config);

		$this->canRun = file_exists(JPATH_ROOT . '/components/com_contactenhanced/contactenhanced.php');
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
			$db     = JFactory::getDbo();
			$fields = array_keys($db->getTableColumns('#__ce_details'));

			// Remove some system fields
			$fields = array_diff($fields, array('id', 'alias', 'ordering', 'checked_out', 'checked_out_time', 'user_id', 'catid', 'hits', 'params'));

			foreach ($fields as $field)
			{
				$options[] = JHtml::_('select.option', $field, $field);
			}

			return $options;
		}
	}

	/**
	 * Method to get data stored in Contact Enhanced data of the given user
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
			$synchronizer = new RADSynchronizerContactenhanced();

			return $synchronizer->getData($userId, $mappings);
		}
	}
}
