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

class EventbookingViewDiscountHtml extends RADViewItem
{
	protected function prepareView()
	{
		parent::prepareView();

		$config = EventbookingHelper::getConfig();

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, title, event_date')
			->from('#__eb_events')
			->where('published=1')
			->order($config->sort_events_dropdown);

		if ($config->hide_past_events_from_events_dropdown)
		{
			$currentDate = $db->quote(JHtml::_('date', 'Now', 'Y-m-d'));

			if ($this->item->event_ids)
			{
				$query->where('(id IN(' . $this->item->event_ids . ') OR DATE(event_date) >= ' . $currentDate . ' OR DATE(event_end_date) >= ' . $currentDate . ')');
			}
			else
			{
				$query->where('(DATE(event_date) >= ' . $currentDate . ' OR DATE(event_end_date) >= ' . $currentDate . ')');
			}
		}

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$selectedEventIds = array();

		if ($this->item->id)
		{
			$query->clear()
				->select('event_id')
				->from('#__eb_discount_events')
				->where('discount_id=' . $this->item->id);
			$db->setQuery($query);
			$selectedEventIds = $db->loadColumn();
		}

		$this->lists['event_id'] = EventbookingHelperHtml::getEventsDropdown($rows, 'event_id[]', 'class="input-xlarge" multiple="multiple" ', $selectedEventIds);
		$this->nullDate          = $db->getNullDate();
		$this->config            = $config;
	}
}
