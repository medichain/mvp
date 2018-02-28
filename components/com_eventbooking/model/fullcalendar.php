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

class EventbookingModelFullcalendar extends RADModel
{
	/**
	 * Fields which will be returned from SQL query
	 *
	 * @var array
	 */
	protected static $fields = array(
		'a.id',
		'a.title',
		'a.event_date AS `start`',
		'a.event_end_date AS `end`',
		'a.thumb'
	);

	/**
	 * The view parameter
	 *
	 * @var \Joomla\Registry\Registry
	 */
	protected $params;

	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 */

	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->state->insert('start', 'string', '')
			->insert('end', 'string');

		$this->params = EventbookingHelper::getViewParams(JFactory::getApplication()->getMenu()->getActive(), array('fullcalendar'));
	}

	/**
	 * Get monthly events
	 *
	 * @return array|mixed
	 */
	public function getData()
	{
		$config      = EventbookingHelper::getConfig();
		$db          = $this->getDbo();
		$query       = $db->getQuery(true);
		$date        = JFactory::getDate('now', JFactory::getConfig()->get('offset'));
		$params      = $this->params;
		$categoryIds = $params->get('category_ids');
		$categoryIds = array_filter(ArrayHelper::toInteger($categoryIds));
		$year        = $params->get('default_year') ?: $date->format('Y');
		$month       = $params->get('default_month') ?: $date->format('m');

		// Calculate start date and end date of the given month
		if (EventbookingHelper::isValidDate($this->state->start))
		{
			$startDate = $this->state->start;
		}
		else
		{
			$date->setDate($year, $month, 1);
			$date->setTime(0, 0, 0);
			$startDate = $db->quote($date->toSql(true));
		}

		if (EventbookingHelper::isValidDate($this->state->end))
		{
			$endDate = $this->state->end;
		}
		else
		{
			$date->setDate($year, $month, $date->daysinmonth);
			$date->setTime(23, 59, 59);
			$endDate = $db->quote($date->toSql(true));
		}

		$query->select($db->quoteName(['a.id', 'a.title', 'a.thumb', 'a.event_date', 'a.event_end_date'], [null, null, null, 'start', 'end']))
			->select($db->quoteName(['c.color_code', 'c.text_color']))
			->from('#__eb_events AS a')
			->innerJoin('#__eb_event_categories AS b ON (a.id = b.event_id AND b.main_category=1)')
			->innerJoin('#__eb_categories as c ON b.category_id = c.id')
			->where('a.published = 1')
			->where('a.access in (' . implode(',', JFactory::getUser()->getAuthorisedViewLevels()) . ')')
			->order('a.event_date ASC, a.ordering ASC');

		if ($fieldSuffix = EventbookingHelper::getFieldSuffix())
		{
			EventbookingHelperDatabase::getMultilingualFields($query, ['a.title'], $fieldSuffix);
		}

		if ($this->params->get('hide_children_events', 0))
		{
			$query->where('a.parent_id = 0');
		}

		if ($categoryIds)
		{
			$query->where('a.id IN (SELECT event_id FROM #__eb_event_categories WHERE category_id IN (' . implode(',', $categoryIds) . '))');
		}

		$startDate = $db->quote($startDate);
		$endDate   = $db->quote($endDate);
		$query->where("`event_date` BETWEEN $startDate AND $endDate");

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

		return $db->loadObjectList();
	}
}
