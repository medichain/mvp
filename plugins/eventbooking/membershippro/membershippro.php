<?php
/**
 * @package        Joomla
 * @subpackage     Events Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2015 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

class plgEventbookingMembershippro extends JPlugin
{
	public function __construct(& $subject, $config = array())
	{
		parent::__construct($subject, $config);

		$this->canRun = file_exists(JPATH_ROOT . '/components/com_osmembership/osmembership.php');
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
			require_once JPATH_ROOT . '/components/com_osmembership/helper/helper.php';
			$fields = OSMembershipHelper::getProfileFields(0);

			$options = array();
			foreach ($fields as $field)
			{
				$options[] = JHtml::_('select.option', $field->name, $field->title);
			}

			return $options;
		}
	}

	/**
	 * Method to get data stored in Membership Pro profile of the given user
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
			$synchronizer = new RADSynchronizerMembershippro();

			return $synchronizer->getData($userId, $mappings);
		}
	}
}
