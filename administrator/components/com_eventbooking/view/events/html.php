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

class EventbookingViewEventsHtml extends RADViewList
{
	protected function prepareView()
	{
		parent::prepareView();

		$this->lists['filter_category_id'] = EventbookingHelperHtml::buildCategoryDropdown($this->state->filter_category_id, 'filter_category_id',
			'onchange="submit();"');

		$options                           = array();
		$options[]                         = JHtml::_('select.option', 0, JText::_('EB_SELECT_LOCATION'), 'id', 'name');
		$options                           = array_merge($options, EventbookingHelperDatabase::getAllLocations());
		$this->lists['filter_location_id'] = JHtml::_('select.genericlist', $options, 'filter_location_id', ' class="inputbox" onchange="submit();" ',
			'id', 'name', $this->state->filter_location_id);

		$options                           = array();
		$options[]                         = JHtml::_('select.option', 0, JText::_('EB_EVENTS_FILTER'));
		$options[]                         = JHtml::_('select.option', 1, JText::_('EB_HIDE_PAST_EVENTS'));
		$options[]                         = JHtml::_('select.option', 2, JText::_('EBH_HIDE_CHILDREN_EVENTS'));
		$this->lists['filter_events'] = JHtml::_('select.genericlist', $options, 'filter_events', ' class="input-medium" onchange="submit();" ',
			'value', 'text', $this->state->filter_events);

		$this->config = EventbookingHelper::getConfig();
	}
}
