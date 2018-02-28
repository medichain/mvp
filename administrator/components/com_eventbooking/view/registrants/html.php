<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class EventbookingViewRegistrantsHtml extends RADViewList
{
	protected function prepareView()
	{
		parent::prepareView();

		$config = EventbookingHelper::getConfig();
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);

		$rows                           = EventbookingHelperDatabase::getAllEvents($config->sort_events_dropdown, $config->hide_past_events_from_events_dropdown);
		$this->lists['filter_event_id'] = EventbookingHelperHtml::getEventsDropdown($rows, 'filter_event_id', 'onchange="submit();"', $this->state->filter_event_id);

		$options   = array();
		$options[] = JHtml::_('select.option', -1, JText::_('EB_REGISTRATION_STATUS'));
		$options[] = JHtml::_('select.option', 0, JText::_('EB_PENDING'));
		$options[] = JHtml::_('select.option', 1, JText::_('EB_PAID'));

		if ($config->activate_waitinglist_feature)
		{
			$options[] = JHtml::_('select.option', 3, JText::_('EB_WAITING_LIST'));
		}

		$options[] = JHtml::_('select.option', 2, JText::_('EB_CANCELLED'));

		$this->lists['filter_published'] = JHtml::_('select.genericlist', $options, 'filter_published', ' class="input-medium" onchange="submit()" ', 'value', 'text',
			$this->state->filter_published);

		if ($config->activate_checkin_registrants)
		{
			$options                          = array();
			$options[]                        = JHtml::_('select.option', -1, JText::_('EB_CHECKIN_STATUS'));
			$options[]                        = JHtml::_('select.option', 1, JText::_('EB_CHECKED_IN'));
			$options[]                        = JHtml::_('select.option', 0, JText::_('EB_NOT_CHECKED_IN'));
			$this->lists['filter_checked_in'] = JHtml::_('select.genericlist', $options, 'filter_checked_in', ' class="input-medium" onchange="submit()" ', 'value', 'text',
				$this->state->filter_checked_in);
		}


		$rowFields = EventbookingHelperRegistration::getAllEventFields($this->state->filter_event_id);
		$fields    = [];

		foreach ($rowFields as $rowField)
		{
			if ($rowField->show_on_registrants != 1 || in_array($rowField->name, ['first_name', 'last_name', 'email']))
			{
				continue;
			}

			$fields[$rowField->id] = $rowField;
		}

		if (count($fields))
		{
			$this->fieldsData = $this->model->getFieldsData(array_keys($fields));
		}

		list($ticketTypes, $tickets) = $this->model->getTicketsData();

		$query->select('COUNT(*)')
			->from('#__eb_payment_plugins')
			->where('published=1');
		$db->setQuery($query);
		$totalPlugins = (int) $db->loadResult();

		$this->config       = $config;
		$this->totalPlugins = $totalPlugins;
		$this->coreFields   = EventbookingHelperRegistration::getPublishedCoreFields();
		$this->fields       = $fields;
		$this->ticketTypes  = $ticketTypes;
		$this->tickets      = $tickets;
	}

	/**
	 * Override addToolbar method to add custom csv export function
	 * @see RADViewList::addToolbar()
	 */
	protected function addToolbar()
	{
		parent::addToolbar();

		// Instantiate a new JLayoutFile instance and render the batch button
		$layout = new JLayoutFile('joomla.toolbar.batch');

		$bar   = JToolbar::getInstance('toolbar');
		$dhtml = $layout->render(array('title' => JText::_('EB_MASS_MAIL')));
		$bar->appendButton('Custom', $dhtml, 'batch');

		JToolbarHelper::custom('resend_email', 'envelope', 'envelope', 'EB_RESEND_EMAIL', true);
		JToolbarHelper::custom('export', 'download', 'download', 'EB_EXPORT_REGISTRANTS', false);

		$config = EventbookingHelper::getConfig();

		if ($config->activate_certificate_feature)
		{
			JToolbarHelper::custom('download_certificates', 'download', 'download', 'EB_DOWNLOAD_CERTIFICATES', true);
		}

		if ($config->activate_waitinglist_feature)
		{
			JToolbarHelper::custom('request_payment', 'envelope', 'envelope', 'EB_REQUEST_PAYMENT', true);
		}
	}
}
