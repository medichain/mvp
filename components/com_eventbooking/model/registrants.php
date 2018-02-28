<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

JLoader::register('EventbookingModelCommonRegistrants', JPATH_ADMINISTRATOR . '/components/com_eventbooking/model/common/registrants.php');

class EventbookingModelRegistrants extends EventbookingModelCommonRegistrants
{
	/**
	 * Build where clase of the query
	 *
	 * @see RADModelList::buildQueryWhere()
	 */
	protected function buildQueryWhere(JDatabaseQuery $query)
	{
		$user   = JFactory::getUser();
		$config = EventbookingHelper::getConfig();

		if ($config->only_show_registrants_of_event_owner && !$user->authorise('core.admin', 'com_eventbooking'))
		{
			$query->where('tbl.event_id IN (SELECT id FROM #__eb_events WHERE created_by =' . $user->id . ')');
		}

		return parent::buildQueryWhere($query);
	}
}
