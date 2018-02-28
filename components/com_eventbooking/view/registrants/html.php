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

class EventbookingViewRegistrantsHtml extends RADViewList
{
	protected function prepareView()
	{
		parent::prepareView();

		$user = JFactory::getUser();

		if (!$user->authorise('eventbooking.registrantsmanagement', 'com_eventbooking'))
		{
			$app = JFactory::getApplication();

			if ($user->get('guest'))
			{
				$active = $app->getMenu()->getActive();
				$option = isset($active->query['option']) ? $active->query['option'] : '';
				$view   = isset($active->query['view']) ? $active->query['view'] : '';

				if ($option == 'com_eventbooking' && $view == 'registrants')
				{
					$returnUrl = 'index.php?Itemid=' . $active->id;
				}
				else
				{
					$returnUrl = JUri::getInstance()->toString();
				}

				$redirectUrl = JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($returnUrl), false);
				$app->redirect($redirectUrl);
			}
			else
			{
				$app->redirect(JUri::root(), JText::_('NOT_AUTHORIZED'));
			}
		}

		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$config      = EventbookingHelper::getConfig();

		//Get list of events
		$query->select('id, event_date')
			->select($db->quoteName('title' . $fieldSuffix, 'title'))
			->from('#__eb_events')
			->where('published = 1')
			->order($config->sort_events_dropdown);

		if ($config->hide_past_events_from_events_dropdown)
		{
			$currentDate = $db->quote(JHtml::_('date', 'Now', 'Y-m-d'));
			$query->where('(DATE(event_date) >= ' . $currentDate . ' OR DATE(event_end_date) >= ' . $currentDate . ')');
		}

		if ($config->only_show_registrants_of_event_owner && !$user->authorise('core.admin', 'com_eventbooking'))
		{
			$query->where('created_by = ' . (int) $user->id);
		}

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$this->lists['filter_event_id'] = EventbookingHelperHtml::getEventsDropdown($rows, 'filter_event_id', 'onchange="submit();"', $this->state->filter_event_id);

		$options   = array();
		$options[] = JHtml::_('select.option', -1, JText::_('EB_REGISTRATION_STATUS'));
		$options[] = JHtml::_('select.option', 0, JText::_('EB_PENDING'));
		$options[] = JHtml::_('select.option', 1, JText::_('EB_PAID'));

		if ($config->activate_waitinglist_feature)
		{
			$options[] = JHtml::_('select.option', 3, JText::_('EB_WAITING_LIST'));
		}

		$options[]                       = JHtml::_('select.option', 2, JText::_('EB_CANCELLED'));
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

		$query->clear()
			->select('id, name, title, is_core')
			->from('#__eb_fields')
			->where('published = 1')
			->where('show_on_registrants = 1')
			->where('name != "first_name"')
			->order('ordering');
		$db->setQuery($query);
		$fields = $db->loadObjectList('id');

		if (count($fields))
		{
			$this->fieldsData = $this->model->getFieldsData(array_keys($fields));
		}

		$this->findAndSetActiveMenuItem();

		$this->config     = $config;
		$this->coreFields = EventbookingHelperRegistration::getPublishedCoreFields();
		$this->fields     = $fields;

		$this->addToolbar();
	}

	/**
	 * Override addToolbar method to add custom csv export function
	 * @see RADViewList::addToolbar()
	 */
	protected function addToolbar()
	{
		require_once JPATH_ADMINISTRATOR . '/includes/toolbar.php';

		if (!EventbookingHelperAcl::canDeleteRegistrant())
		{
			$this->hideButtons[] = 'delete';
		}

		parent::addToolbar();

		JToolbarHelper::custom('resend_email', 'envelope', 'envelope', 'EB_RESEND_EMAIL', true);
		JToolbarHelper::custom('export', 'download', 'download', 'EB_EXPORT_REGISTRANTS', false);
	}
}
