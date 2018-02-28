<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class EventbookingModelArchive extends EventbookingModelList
{
	/**
	 * Method to build where clause for SQL query
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return $this
	 */
	protected function buildQueryWhere(JDatabaseQuery $query)
	{
		$nowDate = $this->getDbo()->quote(EventbookingHelper::getServerTimeFromGMTTime());

		$query->where('tbl.event_date < ' . $nowDate);

		return parent::buildQueryWhere($query);
	}

	/**
	 * Builds a generic ORDER BY clause based on the model's state
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return $this
	 */
	protected function buildQueryOrder(JDatabaseQuery $query)
	{
		$query->order('tbl.event_date DESC');

		return $this;
	}
}
