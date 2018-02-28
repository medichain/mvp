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

/**
 * Class EventbookingViewCouponHtml
 *
 * @property EventbookingModelCoupon $model
 */
class EventbookingViewCouponHtml extends RADViewItem
{
	protected function prepareView()
	{
		parent::prepareView();

		$db                         = JFactory::getDbo();
		$config                     = EventbookingHelper::getConfig();
		$options                    = array();
		$options[]                  = JHtml::_('select.option', 0, JText::_('%'));
		$options[]                  = JHtml::_('select.option', 1, $config->currency_symbol);
		$options[]                  = JHtml::_('select.option', 2, JText::_('EB_VOUCHER'));
		$this->lists['coupon_type'] = JHtml::_('select.genericlist', $options, 'coupon_type', 'class="input-medium"', 'value', 'text', $this->item->coupon_type);

		$options                 = array();
		$options[]               = JHtml::_('select.option', 0, JText::_('EB_EACH_MEMBER'));
		$options[]               = JHtml::_('select.option', 1, JText::_('EB_EACH_REGISTRATION'));
		$this->lists['apply_to'] = JHtml::_('select.genericlist', $options, 'apply_to', '', 'value', 'text', $this->item->apply_to);

		$options                   = array();
		$options[]                 = JHtml::_('select.option', 0, JText::_('EB_BOTH'));
		$options[]                 = JHtml::_('select.option', 1, JText::_('EB_INDIVIDUAL_REGISTRATION'));
		$options[]                 = JHtml::_('select.option', 2, JText::_('EB_GROUP_REGISTRATION'));
		$this->lists['enable_for'] = JHtml::_('select.genericlist', $options, 'enable_for', '', 'value', 'text', $this->item->enable_for);

		$rows = EventbookingHelperDatabase::getAllEvents($config->sort_events_dropdown, $config->hide_past_events_from_events_dropdown);

		if (empty($this->item->id) || $this->item->event_id == -1)
		{
			$selectedEventIds[] = -1;
			$assignment         = 0;
		}
		else
		{
			$query = $db->getQuery(true);
			$query->select('event_id')
				->from('#__eb_coupon_events')
				->where('coupon_id=' . $this->item->id);
			$db->setQuery($query);
			$selectedEventIds = $db->loadColumn();

			if (count($selectedEventIds) && $selectedEventIds[0] < 0)
			{
				$assignment = -1;
			}
			else
			{
				$assignment = 1;
			}

			$selectedEventIds = array_map('abs', $selectedEventIds);
		}

		$this->lists['event_id'] = EventbookingHelperHtml::getEventsDropdown($rows, 'event_id[]', 'class="input-xlarge" multiple="multiple" ', $selectedEventIds);

		$options   = array();
		$options[] = JHtml::_('select.option', 0, JText::_('EB_ALL_EVENTS'));
		$options[] = JHtml::_('select.option', 1, JText::_('EB_ALL_SELECTED_EVENTS'));

		if (!$config->multiple_booking)
		{
			$options[] = JHtml::_('select.option', -1, JText::_('EB_ALL_EXCEPT_SELECTED_EVENTS'));
		}

		$this->lists['assignment'] = JHtml::_('select.genericlist', $options, 'assignment', ' onchange="showHideEventsSelection(this);"', 'value', 'text', $assignment);

		$this->nullDate    = $db->getNullDate();
		$this->config      = $config;
		$this->registrants = $this->model->getRegistrants();
		$this->assignment  = $assignment;
	}

	/**
	 * Override addToolbar function to allow generating custom buttons for import & batch coupon feature
	 */
	protected function addToolbar()
	{
		$layout = $this->getLayout();

		if ($layout == 'default')
		{
			parent::addToolbar();
		}
	}
}
