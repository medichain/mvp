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

class EventbookingViewCouponsHtml extends RADViewList
{
	protected function prepareView()
	{
		parent::prepareView();

		$config = EventbookingHelper::getConfig();
		$rows   = EventbookingHelperDatabase::getAllEvents($config->sort_events_dropdown, $config->hide_past_events_from_events_dropdown);

		$this->lists['filter_event_id'] = EventbookingHelperHtml::getEventsDropdown($rows, 'filter_event_id', 'onchange="submit();"', $this->state->filter_event_id);

		$discountTypes       = array(0 => '%', 1 => $config->get('currency_symbol', '$'));
		$this->discountTypes = $discountTypes;
		$this->nullDate      = JFactory::getDbo()->getNullDate();
		$this->dateFormat    = $config->get('date_format', 'Y-m-d');
	}
}
