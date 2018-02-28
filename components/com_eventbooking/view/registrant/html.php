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

class EventbookingViewRegistrantHtml extends RADViewHtml
{
	public function display()
	{
		$rootUri  = JUri::root(true);
		$document = JFactory::getDocument();
		$user     = JFactory::getUser();
		$config   = clone EventbookingHelper::getConfig();
		$db       = JFactory::getDbo();
		$query    = $db->getQuery(true);

		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$userId      = $user->get('id');
		$lists       = array();

		EventbookingHelper::addLangLinkForAjax();
		$document->addScriptDeclaration('var siteUrl="' . EventbookingHelper::getSiteUrl() . '";');
		$document->addScript($rootUri . '/media/com_eventbooking/assets/js/paymentmethods.js');
		$document->addScript($rootUri . '/media/com_eventbooking/assets/js/ajaxupload.js');
		$customJSFile = JPATH_ROOT . '/media/com_eventbooking/assets/js/custom.js';

		if (file_exists($customJSFile) && filesize($customJSFile) > 0)
		{
			$document->addScript($rootUri . '/media/com_eventbooking/assets/js/custom.js');
		}

		$item = $this->model->getData();

		EventbookingHelper::checkEditRegistrant($item);

		if ($item->id)
		{
			$query->select('*')
				->from('#__eb_events')
				->where('id=' . $item->event_id);

			if ($fieldSuffix)
			{
				$query->select($db->quoteName('title' . $fieldSuffix, 'title'));
			}

			$db->setQuery($query);
			$event       = $db->loadObject();
			$this->event = $event;

			if ($event->collect_member_information !== '')
			{
				$config->collect_member_information = $event->collect_member_information;
			}

			if ($item->is_group_billing)
			{
				$rowFields = EventbookingHelperRegistration::getFormFields($item->event_id, 1, $item->language);
			}
			else
			{
				$rowFields = EventbookingHelperRegistration::getFormFields($item->event_id, 0, $item->language);
			}

			$data = EventbookingHelperRegistration::getRegistrantData($item, $rowFields);

			$query->clear()
				->select('*')
				->from('#__eb_registrants')
				->where('group_id=' . $item->id)
				->order('id');
			$db->setQuery($query, 0, $item->number_registrants);
			$rowMembers = $db->loadObjectList();

			$useDefault = false;
		}
		else
		{
			$rowFields = EventbookingHelperRegistration::getFormFields($item->event_id, 0);

			$useDefault = true;
			$data       = array();
			$rowMembers = array();
		}

		if ($userId && $user->authorise('eventbooking.registrantsmanagement', 'com_eventbooking'))
		{
			$canChangeStatus = true;

			$options   = array();
			$options[] = JHtml::_('select.option', 0, JText::_('EB_PENDING'));
			$options[] = JHtml::_('select.option', 1, JText::_('EB_PAID'));
			$options[] = JHtml::_('select.option', 3, JText::_('EB_WAITING_LIST'));
			$options[] = JHtml::_('select.option', 2, JText::_('EB_CANCELLED'));

			$lists['published'] = JHtml::_('select.genericlist', $options, 'published', ' class="inputbox" ', 'value', 'text', $item->published);
		}
		else
		{
			$canChangeStatus = false;
		}

		$form = new RADForm($rowFields);
		$form->bind($data, $useDefault);

		$form->setEventId($item->event_id);

		if ($canChangeStatus)
		{
			$form->prepareFormFields('setRecalculateFee();');
		}

		$form->buildFieldsDependency();

		if (empty($item->id))
		{
			$rows              = EventbookingHelperDatabase::getAllEvents($config->sort_events_dropdown, $config->hide_past_events_from_events_dropdown);
			$lists['event_id'] = EventbookingHelperHtml::getEventsDropdown($rows, 'event_id', 'class="inputbox validate[required]"', $item->event_id);
		}

		if ($config->collect_member_information && !$rowMembers && $item->number_registrants > 1)
		{
			$rowMembers = array();

			for ($i = 0; $i < $item->number_registrants; $i++)
			{
				$rowMember           = new RADTable('#__eb_registrants', 'id', $db);
				$rowMember->event_id = $item->event_id;
				$rowMember->group_id = $item->id;
				$rowMember->store();
				$rowMembers[] = $rowMember;
			}
		}

		if (count($rowMembers))
		{
			$this->memberFormFields = EventbookingHelperRegistration::getFormFields($item->event_id, 2, $item->language);
		}

		if ($config->activate_checkin_registrants)
		{
			$lists['checked_in'] = JHtml::_('select.booleanlist', 'checked_in', ' class="inputbox" ', $item->checked_in);
		}

		$options   = array();
		$options[] = JHtml::_('select.option', -1, JText::_('EB_PAYMENT_STATUS'));
		$options[] = JHtml::_('select.option', 0, JText::_('EB_PARTIAL_PAYMENT'));

		if (strpos($item->payment_method, 'os_offline') !== false)
		{
			$options[] = JHtml::_('select.option', 2, JText::_('EB_DEPOSIT_PAID'));
		}

		$options[]               = JHtml::_('select.option', 1, JText::_('EB_FULL_PAYMENT'));
		$lists['payment_status'] = JHtml::_('select.genericlist', $options, 'payment_status', ' class="inputbox" ', 'value', 'text',
			$item->payment_status);

		if ($user->authorise('eventbooking.registrantsmanagement', 'com_eventbooking') || empty($item->published))
		{
			$canChangeFeeFields = true;
		}
		else
		{
			$canChangeFeeFields = false;
		}

		$event = EventbookingHelperDatabase::getEvent($item->event_id);

		if ($event->has_multiple_ticket_types)
		{
			$this->ticketTypes = EventbookingHelperData::getTicketTypes($event->id);

			if ($item->id)
			{
				$query->clear()
					->select('*')
					->from('#__eb_registrant_tickets')
					->where('registrant_id = ' . (int) $item->id);
				$db->setQuery($query);
				$registrantTickets = $db->loadObjectList('ticket_type_id');
			}
			else
			{
				$registrantTickets = array();
			}

			$this->registrantTickets = $registrantTickets;

			if ($user->authorise('eventbooking.registrantsmanagement', 'com_eventbooking'))
			{
				$canChangeTicketsQuantity = true;
			}
			else
			{
				$canChangeTicketsQuantity = false;
			}

			$this->canChangeTicketsQuantity = $canChangeTicketsQuantity;
		}

		$this->item               = $item;
		$this->config             = $config;
		$this->lists              = $lists;
		$this->canChangeStatus    = $canChangeStatus;
		$this->form               = $form;
		$this->rowMembers         = $rowMembers;
		$this->return             = $this->input->get->getBase64('return');
		$this->canChangeFeeFields = $canChangeFeeFields;

		$this->addToolbar();

		$this->setLayout('default');

		parent::display();
	}

	/**
	 * Build Form Toolbar
	 */
	protected function addToolbar()
	{
		require_once JPATH_ADMINISTRATOR . '/includes/toolbar.php';

		$user = JFactory::getUser();

		if ($user->authorise('core.edit', 'com_eventbooking') || $user->authorise('core.create', 'com_eventbooking'))
		{
			JToolbarHelper::save('registrant.save', 'JTOOLBAR_SAVE');
		}

		if ($this->item->id &&
			$this->item->published != 2 &&
			EventbookingHelperAcl::canCancelRegistration($this->item->event_id)
		)
		{
			JToolbarHelper::custom('registrant.cancel', 'delete', 'delete', JText::_('EB_CANCEL_REGISTRATION'), false);
		}

		JToolbarHelper::cancel('registrant.cancel_edit', 'JTOOLBAR_CLOSE');
	}
}
