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

class EventbookingViewRegistrantHtml extends RADViewItem
{
	protected function prepareView()
	{
		parent::prepareView();

		$layout = $this->getLayout();

		if ($layout == 'import')
		{
			return;
		}

		// Add necessary javascript library
		$document = JFactory::getDocument();
		$rootUri  = JUri::root(true);
		$document->addScript($rootUri . '/media/com_eventbooking/assets/js/paymentmethods.js');
		$document->addScript($rootUri . '/media/com_eventbooking/assets/js/ajaxupload.js');
		$document->addScriptDeclaration('var siteUrl="' . EventbookingHelper::getSiteUrl() . '";');
		EventbookingHelper::addLangLinkForAjax();

		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		$config = EventbookingHelper::getConfig();

		$rows  = EventbookingHelperDatabase::getAllEvents($config->sort_events_dropdown, $config->hide_past_events_from_events_dropdown);
		$event = EventbookingHelperDatabase::getEvent((int) $this->item->event_id);

		if ($config->hide_past_events_from_events_dropdown && $this->item->id)
		{
			$eventExists = false;

			foreach ($rows as $row)
			{
				if ($row->id == $this->item->event_id)
				{
					$eventExists = true;
					break;
				}
			}

			if (!$eventExists)
			{
				$rows[] = $event;
			}
		}

		$this->lists['event_id'] = EventbookingHelperHtml::getEventsDropdown($rows, 'event_id', '', $this->item->event_id);

		if ($this->item->id)
		{
			if ($this->item->is_group_billing)
			{
				$rowFields = EventbookingHelperRegistration::getFormFields($this->item->event_id, 1, $this->item->language);
			}
			else
			{
				$rowFields = EventbookingHelperRegistration::getFormFields($this->item->event_id, 0, $this->item->language);
			}
		}
		else
		{
			//Default, we just display individual registration form
			$rowFields = EventbookingHelperRegistration::getFormFields($this->item->event_id, 0);
		}
		$form = new RADForm($rowFields);
		if ($this->item->id)
		{
			$data = EventbookingHelperRegistration::getRegistrantData($this->item, $rowFields);
			$form->bind($data, false);
		}
		else
		{
			$data = array();
			$form->bind($data, true);
		}

		$form->setEventId($this->item->event_id);
		$form->prepareFormFields('setRecalculateFee();');
		$form->buildFieldsDependency();

		$options                  = array();
		$options[]                = JHtml::_('select.option', 0, JText::_('EB_PENDING'));
		$options[]                = JHtml::_('select.option', 1, JText::_('EB_PAID'));
		$options[]                = JHtml::_('select.option', 3, JText::_('EB_WAITING_LIST'));
		$options[]                = JHtml::_('select.option', 2, JText::_('EB_CANCELLED'));
		$this->lists['published'] = JHtml::_('select.genericlist', $options, 'published', ' class="inputbox" ', 'value', 'text',
			$this->item->published);
		if ($this->item->id > 0)
		{
			$query->select('*')
				->from('#__eb_registrants')
				->where('group_id=' . $this->item->id)
				->order('id');
			$db->setQuery($query);
			$rowMembers = $db->loadObjectList();
		}
		else
		{
			$rowMembers = array();
		}

		if ($event->collect_member_information !== '')
		{
			$config->collect_member_information = $event->collect_member_information;
		}

		if ($config->collect_member_information && !$rowMembers && $this->item->number_registrants > 1)
		{
			$rowMembers = array();
			for ($i = 0; $i < $this->item->number_registrants; $i++)
			{
				$rowMember           = new RADTable('#__eb_registrants', 'id', $db);
				$rowMember->event_id = $this->item->event_id;
				$rowMember->group_id = $this->item->id;
				$rowMember->store();
				$rowMembers[] = $rowMember;
			}
		}

		$options   = array();
		$options[] = JHtml::_('select.option', -1, JText::_('EB_PAYMENT_STATUS'));
		$options[] = JHtml::_('select.option', 0, JText::_('EB_PARTIAL_PAYMENT'));

		if (strpos($this->item->payment_method, 'os_offline') !== false)
		{
			$options[] = JHtml::_('select.option', 2, JText::_('EB_DEPOSIT_PAID'));
		}

		$options[]                     = JHtml::_('select.option', 1, JText::_('EB_FULL_PAYMENT'));
		$this->lists['payment_status'] = JHtml::_('select.genericlist', $options, 'payment_status', ' class="inputbox" ', 'value', 'text',
			$this->item->payment_status);

		// Payment methods
		$options   = array();
		$options[] = JHtml::_('select.option', '', JText::_('EB_PAYMENT_METHOD'), 'name', 'title');
		$query->clear()
			->select('name, title, params')
			->from('#__eb_payment_plugins')
			->where('published = 1')
			->order('ordering');
		$db->setQuery($query);
		$methods = $db->loadObjectList();

		$options                       = array_merge($options, $methods);
		$this->lists['payment_method'] = JHtml::_('select.genericlist', $options, 'payment_method', ' class="inputbox" ', 'name', 'title',
			$this->item->payment_method ? $this->item->payment_method : 'os_offline');

		if (count($rowMembers))
		{
			$this->memberFormFields = EventbookingHelperRegistration::getFormFields($this->item->event_id, 2, $this->item->language);
		}

		if ($config->activate_checkin_registrants)
		{
			$this->lists['checked_in'] = JHtml::_('select.booleanlist', 'checked_in', ' class="inputbox" ', $this->item->checked_in);
		}

		if ($event->has_multiple_ticket_types)
		{
			$this->ticketTypes = EventbookingHelperData::getTicketTypes($event->id);

			$registrantTickets = array();
			if ($this->item->id)
			{
				$query->clear()
					->select('*')
					->from('#__eb_registrant_tickets')
					->where('registrant_id = ' . (int) $this->item->id);
				$db->setQuery($query);
				$registrantTickets = $db->loadObjectList('ticket_type_id');
			}

			$this->registrantTickets = $registrantTickets;
		}


		$showPaymentFee = false;

		foreach ($methods as $method)
		{
			$params            = new Joomla\Registry\Registry($method->params);
			$paymentFeeAmount  = (float) $params->get('payment_fee_amount');
			$paymentFeePercent = (float) $params->get('payment_fee_percent');

			if ($paymentFeeAmount != 0 || $paymentFeePercent != 0)
			{
				$showPaymentFee = true;
				break;
			}
		}

		$this->config         = $config;
		$this->event          = $event;
		$this->rowMembers     = $rowMembers;
		$this->form           = $form;
		$this->showPaymentFee = $showPaymentFee;
	}

	/**
	 * Override addToolbar function to allow generating custom buttons for import Registrants feature
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
