<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class EventbookingModelUpcomingevents extends EventbookingModelList
{
	/**
	 * Builds a WHERE clause for the query
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return $this
	 */
	protected function buildQueryWhere(JDatabaseQuery $query)
	{
		parent::buildQueryWhere($query);

		$this->applyHidePastEventsFilter($query);

		return $this;
	}

	/**
	 * Builds a generic ORDER BY clause. For upcoming event, event is already ordered by event_date ASC direction
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return $this
	 */
	protected function buildQueryOrder(JDatabaseQuery $query)
	{
		$config = EventbookingHelper::getConfig();

		// Display featured events at the top if configured
		if ($config->display_featured_events_on_top)
		{
			$query->order('tbl.featured DESC');
		}

		$query->order('tbl.event_date');

		return $this;
	}
}
