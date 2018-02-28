<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class EventbookingModelCommonRegistrants extends RADModelList
{
	/**
	 * The selected registrants to export
	 *
	 * @var array
	 */
	protected $registrantIds = array();

	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 */
	public function __construct($config = array())
	{
		$config['search_fields'] = array('tbl.first_name', 'tbl.last_name', 'tbl.email', 'tbl.transaction_id');

		$config['table'] = '#__eb_registrants';

		if (!isset($config['remember_states']))
		{
			$config['remember_states'] = true;
		}

		parent::__construct($config);

		$this->state->insert('filter_event_id', 'int', 0)
			->insert('filter_published', 'int', -1)
			->insert('filter_checked_in', 'int', -1)
			->insert('filter_from_date', 'string', '')
			->insert('filter_to_date', 'string', '')
			->setDefault('filter_order_Dir', 'DESC');

		// Dynamic searchable fields
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('name')
			->from('#__eb_fields')
			->where('published = 1')
			->where('is_searchable = 1');
		$db->setQuery($query);
		$searchableFields = $db->loadColumn();

		foreach ($searchableFields as $field)
		{
			$field = 'tbl.' . $field;

			if (!in_array($field, $this->searchFields))
			{
				$this->searchFields[] = $field;
			}
		}
	}

	/**
	 * Get list group name for group members records
	 *
	 * @param array $rows
	 */
	protected function beforeReturnData($rows)
	{
		if (count($rows))
		{
			// Get group billing records
			$billingIds = array();

			foreach ($rows as $row)
			{
				if ($row->group_id)
				{
					$billingIds[] = $row->group_id;
				}
			}

			if (count($billingIds))
			{
				$db    = $this->getDbo();
				$query = $db->getQuery(true);

				$query->select('id, first_name, last_name')
					->from('#__eb_registrants')
					->where('id IN (' . implode(',', $billingIds) . ')');
				$db->setQuery($query);
				$billingRecords = $db->loadObjectList('id');

				foreach ($rows as $row)
				{
					if ($row->group_id > 0)
					{
						$billingRecord   = $billingRecords[$row->group_id];
						$row->group_name = $billingRecord->first_name . ' ' . $billingRecord->last_name;
					}
				}
			}
		}
	}

	/**
	 * Get registrants custom fields data
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function getFieldsData($fields)
	{
		$fieldsData = array();
		$rows       = $this->data;

		if (count($rows) && count($fields))
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true);

			$query->select('id, fieldtype')
				->from('#__eb_fields')
				->where('id IN (' . implode(',', $fields) . ')');
			$db->setQuery($query);
			$rowFields = $db->loadObjectList('id');

			$registrantIds = array();

			foreach ($rows as $row)
			{
				$registrantIds[] = $row->id;
			}

			$query->clear()
				->select('registrant_id, field_id, field_value')
				->from('#__eb_field_values')
				->where('registrant_id IN (' . implode(',', $registrantIds) . ')')
				->where('field_id IN (' . implode(',', $fields) . ')');
			$db->setQuery($query);
			$rowFieldValues = $db->loadObjectList();

			$config = EventbookingHelper::getConfig();

			foreach ($rowFieldValues as $rowFieldValue)
			{
				$fieldValue = $rowFieldValue->field_value;

				if ($rowFields[$rowFieldValue->field_id]->fieldtype == 'Date')
				{
					try
					{
						$dateTime   = new DateTime($fieldValue);
						$fieldValue = $dateTime->format($config->date_format);
					}
					catch (Exception $e)
					{
						$fieldValue = $rowFieldValue->field_value;
					}
				}
				elseif (is_string($fieldValue) && is_array(json_decode($fieldValue)))
				{
					$fieldValue = implode(', ', json_decode($fieldValue));
				}

				$fieldsData[$rowFieldValue->registrant_id][$rowFieldValue->field_id] = $fieldValue;
			}

			// Get data from core fields
			$query->clear()
				->select('id, name')
				->from('#__eb_fields')
				->where('id IN (' . implode(',', $fields) . ')')
				->where('is_core = 1');
			$db->setQuery($query);
			$coreFields = $db->loadObjectList();

			if (count($coreFields))
			{
				foreach ($rows as $row)
				{
					foreach ($coreFields as $coreField)
					{
						$fieldsData[$row->id][$coreField->id] = $row->{$coreField->name};
					}
				}
			}
		}

		return $fieldsData;
	}

	/**
	 * Get tickets data for the selected events
	 *
	 * @return array
	 */
	public function getTicketsData()
	{
		$ticketTypes = array();
		$tickets     = array();

		if ($this->state->filter_event_id || $this->state->id)
		{
			$eventId = $this->state->filter_event_id ? $this->state->filter_event_id : $this->state->id;

			$event = EventbookingHelperDatabase::getEvent($eventId);

			if ($event->has_multiple_ticket_types)
			{
				$ticketTypes = EventbookingHelperData::getTicketTypes($eventId);

				$ticketTypeIds = array();

				foreach ($ticketTypes as $ticketType)
				{
					$ticketTypeIds[] = $ticketType->id;
				}

				$db    = $this->getDbo();
				$query = $db->getQuery(true);
				$query->select('registrant_id, ticket_type_id, quantity')
					->from('#__eb_registrant_tickets')
					->where('ticket_type_id IN (' . implode(',', $ticketTypeIds) . ')');
				$db->setQuery($query);
				$registrantTickets = $db->loadObjectList();

				foreach ($registrantTickets as $registrantTicket)
				{
					$tickets[$registrantTicket->registrant_id][$registrantTicket->ticket_type_id] = $registrantTicket->quantity;
				}
			}
		}

		return array($ticketTypes, $tickets);
	}

	/**
	 * Builds SELECT columns list for the query
	 */
	protected function buildQueryColumns(JDatabaseQuery $query)
	{
		$currentDate = $this->getDbo()->quote(EventbookingHelper::getServerTimeFromGMTTime());
		$query->select('tbl.*, ev.title, ev.event_date, ev.event_end_date, ev.ticket_prefix, u.username, cp.code AS coupon_code, cp.id AS coupon_id')
			->select("TIMESTAMPDIFF(MINUTE, ev.event_end_date, $currentDate) AS event_end_date_minutes");

		return $this;
	}

	/**
	 * Builds LEFT JOINS clauses for the query
	 */
	protected function buildQueryJoins(JDatabaseQuery $query)
	{
		$query->leftJoin('#__eb_events AS ev ON tbl.event_id = ev.id')
			->leftJoin('#__users AS u ON tbl.user_id = u.id')
			->leftJoin('#__eb_coupons AS cp ON tbl.coupon_id = cp.id');

		return $this;
	}

	/**
	 * Build where clase of the query
	 *
	 * @see RADModelList::buildQueryWhere()
	 */
	protected function buildQueryWhere(JDatabaseQuery $query)
	{
		$app    = JFactory::getApplication();
		$config = EventbookingHelper::getConfig();
		$user   = JFactory::getUser();
		$db     = JFactory::getDbo();

		if (!empty($this->registrantIds))
		{
			$query->where('tbl.id IN (' . implode(',', $this->registrantIds) . ')');
		}

		if ($this->state->filter_published != -1)
		{
			$query->where(' tbl.published = ' . $this->state->filter_published);
		}

		if ($this->state->filter_checked_in != -1)
		{
			$query->where(' tbl.checked_in = ' . $this->state->filter_checked_in);
		}

		if ($this->state->filter_event_id || $this->state->id)
		{
			$eventId = $this->state->filter_event_id ? $this->state->filter_event_id : $this->state->id;

			$query->where(' tbl.event_id = ' . $eventId);
		}

		if (!$config->show_pending_registrants || strtolower($this->getName()) == 'registrantlist')
		{
			$query->where('(tbl.published >= 1 OR tbl.payment_method LIKE "os_offline%")');
		}

		if (!$config->get('include_group_billing_in_registrants', 1))
		{
			$query->where(' tbl.is_group_billing = 0 ');
		}

		if (!$config->include_group_members_in_registrants)
		{
			$query->where(' tbl.group_id = 0 ');
		}

		if ($this->state->filter_from_date && EventbookingHelper::isValidDate($this->state->filter_from_date))
		{
			$date = JFactory::getDate($this->state->filter_from_date, JFactory::getConfig()->get('offset'));
			$query->where('DATE(tbl.register_date) >= ' . $db->quote($date->format('Y-m-d')));
		}

		if ($this->state->filter_to_date && EventbookingHelper::isValidDate($this->state->filter_to_date))
		{
			$date = JFactory::getDate($this->state->filter_to_date, JFactory::getConfig()->get('offset'));
			$query->where('DATE(tbl.register_date) <= ' . $db->quote($date->format('Y-m-d')));
		}

		return parent::buildQueryWhere($query);
	}

	/**
	 * Setter method to set selected registrantIds for exporting
	 *
	 * @param array $registrantIds
	 */
	public function setRegistrantIds($registrantIds)
	{
		$this->registrantIds = $registrantIds;
	}
}
