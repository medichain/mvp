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

class EventbookingViewMassmailHtml extends RADViewHtml
{
	public function display()
	{
		// Only users with registrants management permission can access to massmail function
		$user = JFactory::getUser();

		if (!$user->authorise('eventbooking.registrantsmanagement', 'com_eventbooking'))
		{
			$app = JFactory::getApplication();

			if ($user->get('guest'))
			{
				$active = $app->getMenu()->getActive();
				$option = isset($active->query['option']) ? $active->query['option'] : '';
				$view   = isset($active->query['view']) ? $active->query['view'] : '';

				if ($option == 'com_eventbooking' && $view == 'massmail')
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


		$config      = EventbookingHelper::getConfig();
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$fieldSuffix = EventbookingHelper::getFieldSuffix();

		if ($fieldSuffix)
		{
			$query->select($db->quoteName(['id', 'title' . $fieldSuffix, 'event_date'], [null, 'title', null]));
		}
		else
		{
			$query->select($db->quoteName(['id', 'title', 'event_date']));
		}

		$query->from('#__eb_events')
			->where('published = 1')
			->order($config->sort_events_dropdown);

		if ($config->hide_past_events_from_events_dropdown)
		{
			$currentDate = $db->quote(JHtml::_('date', 'Now', 'Y-m-d'));
			$query->where('(DATE(event_date) >= ' . $currentDate . ' OR DATE(event_end_date) >= ' . $currentDate . ')');
		}

		if ($config->only_show_registrants_of_event_owner)
		{
			$query->where('created_by = ' . (int) $user->id);
		}

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$options   = array();
		$options[] = JHtml::_('select.option', 0, JText::_('EB_SELECT_EVENT'), 'id', 'title');

		if ($config->show_event_date)
		{
			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$row = $rows[$i];

				if ($row->event_date == EB_TBC_DATE)
				{
					$options[] = JHtml::_('select.option', $row->id, $row->title, 'id', 'title');
				}
				else
				{
					$options[] = JHtml::_('select.option', $row->id,
						$row->title . ' (' . JHtml::_('date', $row->event_date, $config->date_format, null) . ')', 'id', 'title');
				}
			}
		}
		else
		{
			$options = array_merge($options, $rows);
		}

		$lists             = array();
		$lists['event_id'] = JHtml::_('select.genericlist', $options, 'event_id', ' class="input-xlarge" ', 'id', 'title');
		$this->lists       = $lists;

		parent::display();
	}
}
