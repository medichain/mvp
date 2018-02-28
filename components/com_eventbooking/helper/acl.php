<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2016 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

class EventbookingHelperAcl
{
	/**
	 * Check to see whether the current users can access View List function
	 *
	 * @return bool
	 */
	public static function canViewRegistrantList()
	{
		return JFactory::getUser()->authorise('eventbooking.viewregistrantslist', 'com_eventbooking');
	}

	/**
	 * Method to check whether the current user can edit the given registration record
	 *
	 * @param EventbookingTableRegistrant $rowRegistrant
	 *
	 * @return bool
	 */
	public static function canEditRegistrant($rowRegistrant)
	{
		$user = JFactory::getUser();

		if ($user->id && ($user->authorise('eventbooking.registrantsmanagement', 'com_eventbooking')
				|| $user->get('id') == $rowRegistrant->user_id
				|| $user->get('email') == $rowRegistrant->email)
		)
		{
			return true;
		}

		return false;
	}

	/**
	 * Check to see whether this event can be cancelled
	 *
	 * @param int $eventId
	 *
	 * @return bool
	 */
	public static function canCancel($eventId)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(*)')
			->from('#__eb_events')
			->where('id = ' . $eventId)
			->where(' enable_cancel_registration = 1')
			->where('(DATEDIFF(cancel_before_date, NOW()) >=0)');
		$db->setQuery($query);
		$total = $db->loadResult();

		if ($total)
		{
			return true;
		}

		return false;
	}

	/**
	 * Method to check whether the current user can export registrants of certain events or all events
	 *
	 * @param int $eventId
	 *
	 * @return bool
	 */
	public static function canExportRegistrants($eventId = 0)
	{
		$user = JFactory::getUser();

		if ($eventId)
		{
			$config = EventbookingHelper::getConfig();
			$db     = JFactory::getDbo();
			$query  = $db->getQuery(true);
			$query->select('created_by')
				->from('#__eb_events')
				->where('id = ' . (int) $eventId);
			$db->setQuery($query);
			$createdBy = (int) $db->loadResult();

			if ($config->only_show_registrants_of_event_owner)
			{
				return $createdBy > 0 && $createdBy == $user->id;
			}
			else
			{
				return ($createdBy > 0 && $createdBy == $user->id) || $user->authorise('eventbooking.registrantsmanagement', 'com_eventbooking');
			}

		}
		else
		{
			return $user->authorise('eventbooking.registrantsmanagement', 'com_eventbooking');
		}
	}

	/**
	 * Check to see whether the current user can change status (publish/unpublish) of the given event
	 *
	 * @param $eventId
	 *
	 * @return bool
	 */
	public static function canChangeEventStatus($eventId)
	{
		$user = JFactory::getUser();

		if ($user->get('guest'))
		{
			return false;
		}

		if ($user->authorise('core.admin', 'com_eventbooking'))
		{
			return true;
		}

		if ($user->authorise('core.edit.state', 'com_eventbooking'))
		{
			if (empty($eventId))
			{
				return true;
			}
			
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('created_by')
				->from('#__eb_events')
				->where('id = ' . (int) $eventId);
			$db->setQuery($query);
			$createdBy = (int) $db->loadResult();

			if ($createdBy == $user->id)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Check to see whether the user can cancel registration for the given event
	 *
	 * @param $eventId
	 *
	 * @return bool|int
	 */
	public static function canCancelRegistration($eventId)
	{
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		$user   = JFactory::getUser();
		$userId = $user->get('id');
		$email  = $user->get('email');

		if (!$userId)
		{
			return false;
		}

		$query->select('id')
			->from('#__eb_registrants')
			->where('event_id = ' . $eventId)
			->where('(user_id = ' . $userId . ' OR email = ' . $db->quote($email) . ')')
			->where('(published=1 OR (payment_method LIKE "os_offline%" AND published NOT IN (2,3)))');

		$db->setQuery($query);
		$registrantId = $db->loadResult();

		if (!$registrantId)
		{
			return false;
		}

		$query->clear()
			->select('COUNT(*)')
			->from('#__eb_events')
			->where('id = ' . $eventId)
			->where('enable_cancel_registration = 1')
			->where('DATEDIFF(cancel_before_date, NOW()) >= 0');
		$db->setQuery($query);
		$total = $db->loadResult();

		if (!$total)
		{
			return false;
		}

		return $registrantId;
	}

	/**
	 * Check to see whether the current users can add events from front-end
	 */
	public static function checkAddEvent()
	{
		return JFactory::getUser()->authorise('eventbooking.addevent', 'com_eventbooking');
	}

	/**
	 * Check to see whether the current user can edit registrant
	 *
	 * @param int $eventId
	 *
	 * @return boolean
	 */
	public static function checkEditEvent($eventId)
	{
		$user = JFactory::getUser();

		if ($user->get('guest'))
		{
			return false;
		}

		if (!$eventId)
		{
			return false;
		}

		$rowEvent = EventbookingHelperDatabase::getEvent($eventId);

		if (!$rowEvent)
		{
			return false;
		}

		if ($user->authorise('core.edit', 'com_eventbooking') || ($user->authorise('core.edit.own', 'com_eventbooking') && ($rowEvent->created_by == $user->get('id'))))
		{
			return true;
		}

		return false;
	}

	/**
	 * Check to see whether the current user can delete the given registrant
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public static function canDeleteRegistrant($id = 0)
	{
		$user   = JFactory::getUser();
		$config = EventbookingHelper::getConfig();

		if (!$user->authorise('eventbooking.registrantsmanagement', 'com_eventbooking'))
		{
			return false;
		}

		if ($user->authorise('core.delete', 'com_eventbooking'))
		{
			return true;
		}

		if ($config->get('only_show_registrants_of_event_owner') && $config->get('enable_delete_registrants', 1))
		{
			if ($id == 0)
			{
				return true;
			}

			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('b.created_by')
				->from('#__eb_registrants AS a')
				->innerJoin('#__eb_events AS b ON a.event_id = b.id')
				->where('a.id = ' . $id);
			$db->setQuery($query);
			$eventCreatorID = $db->loadObject();

			if ($eventCreatorID == $user->id)
			{
				return true;
			}
		}

		return false;
	}
}