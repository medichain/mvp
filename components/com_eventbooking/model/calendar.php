<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

class EventbookingModelCalendar extends RADModel
{
	/**
	 * Fields which will be returned from SQL query
	 *
	 * @var array
	 */
	protected static $fields = array(
		'a.id',
		'a.parent_id',
		'a.location_id',
		'a.event_capacity',
		'a.title',
		'a.event_date',
		'a.event_end_date',
		'a.thumb',
		'a.alias',
		'a.featured',
		'a.short_description',
		'a.individual_price',
		'a.price_text',
		'a.discount_type',
		'a.discount',
		'a.early_bird_discount_type',
		'a.early_bird_discount_date',
		'a.early_bird_discount_amount',
		'a.discount_groups',
		'a.discount_amounts',
		'a.fixed_group_price',
		'a.late_fee_type',
		'a.late_fee_date',
		'a.late_fee_amount',
		'a.registration_start_date',
		'a.cut_off_date',
		'a.currency_symbol',
		'a.currency_code',
		'a.registration_type'
	);

	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 */

	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->state->insert('year', 'int', 0)
			->insert('month', 'int', 0)
			->insert('date', 'string', '')
			->insert('day', 'string', '')
			->insert('id', 'int', 0)
			->insert('mini_calendar', 'int', 0);

		$this->params = EventbookingHelper::getViewParams(JFactory::getApplication()->getMenu()->getActive(), array('calendar'));
	}

	/**
	 * Get monthly events
	 *
	 * @return array|mixed
	 */
	public function getData()
	{
		$config          = EventbookingHelper::getConfig();
		$db              = $this->getDbo();
		$query           = $db->getQuery(true);
		$fieldSuffix     = EventbookingHelper::getFieldSuffix();
		$date            = JFactory::getDate('now', JFactory::getConfig()->get('offset'));
		$currentDateData = self::getCurrentDateData();

		$params             = EventbookingHelper::getViewParams(JFactory::getApplication()->getMenu()->getActive(), array('calendar'));
		$categoryIds        = $params->get('category_ids');
		$excludeCategoryIds = $params->get('exclude_category_ids');
		$year               = $this->state->get('year') ?: $params->get('default_year');
		$month              = $this->state->get('month') ?: $params->get('default_month');


		$categoryIds        = array_filter(ArrayHelper::toInteger($categoryIds));
		$excludeCategoryIds = array_filter(ArrayHelper::toInteger($excludeCategoryIds));

		if (!$year)
		{
			$year = $currentDateData['year'];
		}

		if (!$month)
		{
			$month = $currentDateData['month'];
		}

		$this->state->set('month', $month)
			->set('year', $year);

		// Calculate start date and end date of the given month
		$date->setDate($year, $month, 1);
		$date->setTime(0, 0, 0);
		$startDate = $db->quote($date->toSql(true));

		$date->setDate($year, $month, $date->daysinmonth);
		$date->setTime(23, 59, 59);
		$endDate = $db->quote($date->toSql(true));

		$query->select(static::$fields)
			->select('SUM(b.number_registrants) AS total_registrants')
			->from('#__eb_events AS a')
			->leftJoin('#__eb_registrants AS b ON (a.id = b.event_id ) AND b.group_id = 0 AND (b.published=1 OR (b.payment_method LIKE "os_offline%" AND b.published NOT IN (2,3)))')
			->where('a.published = 1')
			->where('a.access in (' . implode(',', JFactory::getUser()->getAuthorisedViewLevels()) . ')')
			->group('a.id')
			->order('a.event_date ASC, a.ordering ASC');

		if ($fieldSuffix)
		{
			EventbookingHelperDatabase::getMultilingualFields($query, array('a.title', 'a.short_description'), $fieldSuffix);
		}

		if ($categoryId = $this->state->get('id'))
		{
			$query->where('a.id IN (SELECT event_id FROM #__eb_event_categories WHERE category_id = ' . $categoryId . ')');
		}

		if ($this->params->get('hide_children_events', 0))
		{
			$query->where('tbl.parent_id = 0');
		}

		if ($categoryIds)
		{
			$query->where('a.id IN (SELECT event_id FROM #__eb_event_categories WHERE category_id IN (' . implode(',', $categoryIds) . '))');
		}

		if ($excludeCategoryIds && !$this->state->mini_calendar)
		{
			$query->where('a.id NOT IN (SELECT event_id FROM #__eb_event_categories WHERE category_id IN (' . implode(',', $excludeCategoryIds) . '))');
		}

		if ($config->show_multiple_days_event_in_calendar && !$this->state->mini_calendar)
		{
			$query->where("((`event_date` BETWEEN $startDate AND $endDate) OR (MONTH(event_end_date) = $month AND YEAR(event_end_date) = $year ))");
		}
		else
		{
			$query->where("`event_date` BETWEEN $startDate AND $endDate");
		}

		$hidePastEventsParam = $this->params->get('hide_past_events', 2);

		if ($hidePastEventsParam == 1 || ($hidePastEventsParam == 2 && $config->hide_past_events))
		{
			$currentDate = $db->quote(JHtml::_('date', 'Now', 'Y-m-d'));

			if ($config->show_until_end_date)
			{
				$query->where('(DATE(a.event_date) >= ' . $currentDate . ' OR DATE(a.event_end_date) >= ' . $currentDate . ')');
			}
			else
			{
				$query->where('(DATE(a.event_date) >= ' . $currentDate . ' OR DATE(a.cut_off_date) >= ' . $currentDate . ')');
			}
		}

		$db->setQuery($query);

		if ($config->show_multiple_days_event_in_calendar && !$this->state->mini_calendar)
		{
			$rows      = $db->loadObjectList();
			$rowEvents = array();

			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$row      = $rows[$i];
				$arrDates = explode('-', $row->event_date);

				if ($arrDates[0] == $year && $arrDates[1] == $month)
				{
					$rowEvents[] = $row;
				}

				$startDateParts = explode(' ', $row->event_date);
				$startTime      = strtotime($startDateParts[0]);
				$startDateTime  = strtotime($row->event_date);
				$endDateParts   = explode(' ', $row->event_end_date);
				$endTime        = strtotime($endDateParts[0]);
				$count          = 0;

				while ($startTime < $endTime)
				{
					$count++;
					$rowNew             = clone $row;
					$rowNew->event_date = date('Y-m-d H:i:s', $startDateTime + $count * 24 * 3600);
					$arrDates           = explode('-', $rowNew->event_date);

					if ($arrDates[0] == $year && $arrDates[1] == $month)
					{
						$rowEvents[] = $rowNew;
					}

					$startTime += 24 * 3600;
				}
			}

			$rows = $rowEvents;
		}
		else
		{
			$rows = $db->loadObjectList();
		}

		// Uncomment the code below if you want the system auto-run to the next month having events automatically
		/*if (empty($rows) && $app->input->getMethod() == 'GET' && !$this->state->mini_calendar)
		{
			$query->clear()
				->select('MONTH(event_date) AS next_event_month')
				->select('YEAR(event_date) AS next_event_year')
				->from('#__eb_events AS a')
				->where('published = 1')
				->where('access in (' . implode(',', JFactory::getUser()->getAuthorisedViewLevels()) . ')')
				->where('MONTH(a.event_date) > ' . (int) $this->state->get('month'))
				->where('YEAR(a.event_date) >= ' . (int) $this->state->get('year'))
				->order('event_date');

			if ($this->state->id)
			{
				$catId = $this->state->id;
				$query->where("a.id IN (SELECT event_id FROM #__eb_event_categories WHERE category_id = $catId)");
			}

			$db->setQuery($query, 0, 1);
			$rowNextEvent = $db->loadObject();

			if ($rowNextEvent)
			{
				$this->state->set('month', $rowNextEvent->next_event_month)
					->set('year', $rowNextEvent->next_event_year);

				$rows = $this->getData();
			}
			elseif ($app->input->get->getInt('next'))
			{
				$app->enqueueMessage(JText::_('EB_NO_UPCOMING_EVENTS'));
			}
		}*/

		return $rows;
	}

	/**
	 * Get events of the given week
	 *
	 * @return array
	 */
	public function getEventsByWeek()
	{
		$config      = EventbookingHelper::getConfig();
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$db          = $this->getDbo();
		$query       = $db->getQuery(true);
		$startDay    = (int) $config->calendar_start_date;

		// get first day of week of today
		$currentDateData = self::getCurrentDateData();
		$startWeekDate   = $this->state->date;

		if (!EventbookingHelper::isValidDate($startWeekDate))
		{
			$startWeekDate = '';
		}

		if ($startWeekDate)
		{
			$date = JFactory::getDate($startWeekDate, JFactory::getConfig()->get('offset'));
		}
		else
		{
			$date = JFactory::getDate($currentDateData['start_week_date'], JFactory::getConfig()->get('offset'));
			$this->state->set('date', $date->format('Y-m-d', true));
		}

		$date->setTime(0, 0, 0);
		$startDate = $db->quote($date->toSql(true));
		$date->modify('+6 day');
		$date->setTime(23, 59, 59);
		$endDate = $db->quote($date->toSql(true));
		$query->select(static::$fields)
			->select('a.short_description')
			->select($db->quoteName('b.name' . $fieldSuffix, 'location_name'))
			->from('#__eb_events AS a')
			->leftJoin('#__eb_locations AS b ON b.id = a.location_id')
			->where('a.published = 1')
			->where("(a.event_date BETWEEN $startDate AND $endDate)")
			->where('a.access IN (' . implode(',', JFactory::getUser()->getAuthorisedViewLevels()) . ')');


		if ($fieldSuffix)
		{
			EventbookingHelperDatabase::getMultilingualFields($query, array('a.title', 'a.short_description'), $fieldSuffix);
		}

		if ($config->hide_past_events)
		{
			$currentDate = $db->quote($currentDateData['current_date']);

			if ($config->show_until_end_date)
			{
				$query->where('(DATE(a.event_date) >=' . $currentDate . ' OR DATE(a.event_end_date) >=' . $currentDate . ')');
			}
			else
			{
				$query->where('(DATE(a.event_date) >=' . $currentDate . ' OR DATE(a.cut_off_date) >=' . $currentDate . ')');
			}
		}

		$query->order('a.event_date ASC, a.ordering ASC');

		$db->setQuery($query);
		$events   = $db->loadObjectList();
		$eventArr = array();

		foreach ($events as $event)
		{
			$event->short_description = JHtml::_('content.prepare', $event->short_description);
			$weekDay                  = (date('w', strtotime($event->event_date)) - $startDay + 7) % 7;
			$eventArr[$weekDay][]     = $event;
		}

		return $eventArr;
	}

	/**
	 * Get events of the given date
	 *
	 * @return mixed
	 */
	public function getEventsByDaily()
	{
		$config      = EventbookingHelper::getConfig();
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$db          = $this->getDbo();
		$query       = $db->getQuery(true);
		$day         = $this->state->day;

		if (!EventbookingHelper::isValidDate($day))
		{
			$day = '';
		}

		if (!$day)
		{
			$currentDateData = self::getCurrentDateData();
			$day             = $currentDateData['current_date'];
			$this->state->set('day', $day);
		}

		$startDate = $db->quote($day . " 00:00:00");
		$endDate   = $db->quote($day . " 23:59:59");
		$query->select(static::$fields)
			->select('a.short_description, a.location_id')
			->select($db->quoteName('b.name' . $fieldSuffix, 'location_name'))
			->from('#__eb_events AS a')
			->leftJoin('#__eb_locations AS b ON b.id = a.location_id')
			->where('a.published = 1')
			->where("(a.event_date BETWEEN $startDate AND $endDate)")
			->where('a.access IN (' . implode(',', JFactory::getUser()->getAuthorisedViewLevels()) . ')');

		if ($fieldSuffix)
		{
			EventbookingHelperDatabase::getMultilingualFields($query, array('a.title', 'a.short_description'), $fieldSuffix);
		}

		if ($config->hide_past_events)
		{
			$currentDate = $db->quote(JHtml::_('date', 'Now', 'Y-m-d'));

			if ($config->show_until_end_date)
			{
				$query->where('(DATE(a.event_date) >=' . $currentDate . ' OR DATE(a.event_end_date) >=' . $currentDate . ')');
			}
			else
			{
				$query->where('(DATE(a.event_date) >=' . $currentDate . ' OR DATE(a.cut_off_date) >=' . $currentDate . ')');
			}
		}

		$query->order('a.event_date ASC, a.ordering ASC');
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		for ($i = 0, $n = count($rows); $i < $n; $i++)
		{
			$row                    = $rows[$i];
			$row->short_description = JHtml::_('content.prepare', $row->short_description);
		}

		return $rows;
	}

	/**
	 * Get data of current date
	 *
	 * @return array
	 */
	public static function getCurrentDateData($currentDate = 'now')
	{
		static $data;

		if (empty($data))
		{
			$config               = EventbookingHelper::getConfig();
			$startDay             = (int) $config->calendar_start_date;
			$data                 = array();
			$date                 = new DateTime($currentDate, new DateTimeZone(JFactory::getConfig()->get('offset')));
			$data['year']         = $date->format('Y');
			$data['month']        = $date->format('m');
			$data['current_date'] = $date->format('Y-m-d');

			if ($startDay == 0)
			{
				$date->modify('Sunday last week');
			}
			else
			{
				$date->modify(('Sunday' == $date->format('l')) ? 'Monday last week' : 'Monday this week');
			}

			$data['start_week_date'] = $date->format('Y-m-d');
			$data['end_week_date']   = $date->modify('+6 day')->format('Y-m-d');
		}

		return $data;
	}
}
