<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class EventbookingModelRegistrants extends EventbookingModelCommonRegistrants
{
	/**
	 * Get statistic data
	 *
	 * @return array
	 */
	public static function getStatisticsData()
	{
		$data   = array();
		$config = JFactory::getConfig();
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);

		$query->select('SUM(number_registrants) AS total_registrants, SUM(amount) AS total_amount')
			->from('#__eb_registrants');

		// Today
		$date = JFactory::getDate('now', $config->get('offset'));
		$date->setTime(0, 0, 0);
		$date->setTimezone(new DateTimeZone('UCT'));
		$fromDate = $date->toSql(true);
		$date     = JFactory::getDate('now', $config->get('offset'));
		$date->setTime(23, 59, 59);
		$date->setTimezone(new DateTimeZone('UCT'));
		$toDate = $date->toSql(true);

		$query->where('group_id = 0')
			->where('(published = 1 OR (payment_method LIKE "os_offline%" AND published = 0))')
			->where('register_date >= ' . $db->quote($fromDate))
			->where('register_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['today'] = array(
			'total_registrants' => (int) $row->total_registrants,
			'total_amount'      => floatval($row->total_amount),
		);

		// Yesterday
		$date = JFactory::getDate('now', $config->get('offset'));
		$date->modify('-1 day');
		$date->setTime(0, 0, 0);
		$date->setTimezone(new DateTimeZone('UCT'));
		$fromDate = $date->toSql(true);
		$date     = JFactory::getDate('now', $config->get('offset'));
		$date->modify('-1 day');
		$date->setTime(23, 59, 59);
		$date->setTimezone(new DateTimeZone('UCT'));
		$toDate = $date->toSql(true);

		$query->clear('where');
		$query->where('group_id = 0')
			->where('(published = 1 OR (payment_method LIKE "os_offline%" AND published = 0))')
			->where('register_date >= ' . $db->quote($fromDate))
			->where('register_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['yesterday'] = array(
			'total_registrants' => (int) $row->total_registrants,
			'total_amount'      => floatval($row->total_amount),
		);

		// This week
		$date   = JFactory::getDate('now', $config->get('offset'));
		$monday = clone $date->modify(('Sunday' == $date->format('l')) ? 'Monday last week' : 'Monday this week');
		$monday->setTime(0, 0, 0);
		$monday->setTimezone(new DateTimeZone('UCT'));
		$fromDate = $monday->toSql(true);
		$sunday   = clone $date->modify('Sunday this week');
		$sunday->setTime(23, 59, 59);
		$sunday->setTimezone(new DateTimeZone('UCT'));
		$toDate = $sunday->toSql(true);

		$query->clear('where');
		$query->where('group_id = 0')
			->where('(published = 1 OR (payment_method LIKE "os_offline%" AND published = 0))')
			->where('register_date >= ' . $db->quote($fromDate))
			->where('register_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['this_week'] = array(
			'total_registrants' => (int) $row->total_registrants,
			'total_amount'      => floatval($row->total_amount),
		);

		// Last week, re-use data from this week
		$monday->modify('-7 day');
		$sunday->modify('-7 day');
		$fromDate = $monday->toSql(true);
		$toDate   = $sunday->toSql(true);

		$query->clear('where');
		$query->where('group_id = 0')
			->where('(published = 1 OR (payment_method LIKE "os_offline%" AND published = 0))')
			->where('register_date >= ' . $db->quote($fromDate))
			->where('register_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['last_week'] = array(
			'total_registrants' => (int) $row->total_registrants,
			'total_amount'      => floatval($row->total_amount),
		);

		// This month
		$date = JFactory::getDate('now', $config->get('offset'));
		$date->setDate($date->year, $date->month, 1);
		$date->setTime(0, 0, 0);
		$date->setTimezone(new DateTimeZone('UCT'));
		$fromDate = $date->toSql(true);
		$date     = JFactory::getDate('now', $config->get('offset'));
		$date->setDate($date->year, $date->month, $date->daysinmonth);
		$date->setTime(23, 59, 59);
		$date->setTimezone(new DateTimeZone('UCT'));
		$toDate = $date->toSql(true);

		$query->clear('where');
		$query->where('group_id = 0')
			->where('(published = 1 OR (payment_method LIKE "os_offline%" AND published = 0))')
			->where('register_date >= ' . $db->quote($fromDate))
			->where('register_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['this_month'] = array(
			'total_registrants' => (int) $row->total_registrants,
			'total_amount'      => floatval($row->total_amount),
		);

		// Last month
		$date = JFactory::getDate('first day of last month', $config->get('offset'));
		$date->setTime(0, 0, 0);
		$date->setTimezone(new DateTimeZone('UCT'));
		$fromDate = $date->toSql(true);
		$date     = JFactory::getDate('last day of last month', $config->get('offset'));
		$date->setTime(23, 59, 59);
		$date->setTimezone(new DateTimeZone('UCT'));
		$toDate = $date->toSql(true);

		$query->clear('where');
		$query->where('group_id = 0')
			->where('(published = 1 OR (payment_method LIKE "os_offline%" AND published = 0))')
			->where('register_date >= ' . $db->quote($fromDate))
			->where('register_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['last_month'] = array(
			'total_registrants' => (int) $row->total_registrants,
			'total_amount'      => floatval($row->total_amount),
		);

		// This year
		$date = JFactory::getDate('now', $config->get('offset'));
		$date->setDate($date->year, 1, 1);
		$date->setTime(0, 0, 0);
		$date->setTimezone(new DateTimeZone('UCT'));
		$fromDate = $date->toSql(true);
		$date     = JFactory::getDate('now', $config->get('offset'));
		$date->setDate($date->year, 12, 31);
		$date->setTime(23, 59, 59);
		$date->setTimezone(new DateTimeZone('UCT'));
		$toDate = $date->toSql(true);

		$query->clear('where');
		$query->where('group_id = 0')
			->where('(published = 1 OR (payment_method LIKE "os_offline%" AND published = 0))')
			->where('register_date >= ' . $db->quote($fromDate))
			->where('register_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['this_year'] = array(
			'total_registrants' => (int) $row->total_registrants,
			'total_amount'      => floatval($row->total_amount),
		);

		// Last year
		$date = JFactory::getDate('now', $config->get('offset'));
		$date->setDate($date->year - 1, 1, 1);
		$date->setTime(0, 0, 0);
		$date->setTimezone(new DateTimeZone('UCT'));
		$fromDate = $date->toSql(true);
		$date     = JFactory::getDate('now', $config->get('offset'));
		$date->setDate($date->year - 1, 12, 31);
		$date->setTime(23, 59, 59);
		$date->setTimezone(new DateTimeZone('UCT'));
		$toDate = $date->toSql(true);

		$query->clear('where');
		$query->where('group_id = 0')
			->where('(published = 1 OR (payment_method LIKE "os_offline%" AND published = 0))')
			->where('register_date >= ' . $db->quote($fromDate))
			->where('register_date <=' . $db->quote($toDate));
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['last_year'] = array(
			'total_registrants' => (int) $row->total_registrants,
			'total_amount'      => floatval($row->total_amount),
		);

		// Total registration
		$query->clear();
		$query->select('SUM(number_registrants) AS total_registrants, SUM(amount) AS total_amount')
			->from('#__eb_registrants')
			->where('group_id = 0')
			->where('(published = 1 OR (payment_method LIKE "os_offline%" AND published = 0))');
		$db->setQuery($query);
		$row = $db->loadObject();

		$data['total_registration'] = array(
			'total_registrants' => (int) $row->total_registrants,
			'total_amount'      => floatval($row->total_amount),
		);

		return $data;
	}
}
