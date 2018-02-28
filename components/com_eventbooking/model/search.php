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

class EventbookingModelSearch extends EventbookingModelList
{
	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->state->remove('id')
			->insert('category_id', 'int', 0)
			->insert('location_id', 'int', '0')
			->insert('created_by', 'int', 0)
			->insert('search', 'string', '')
			->insert('filter_city', 'string', '')
			->insert('filter_state', 'string', '');
	}

	/**
	 * Builds a WHERE clause for the query
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return $this
	 */
	protected function buildQueryWhere(JDatabaseQuery $query)
	{
		$config = EventbookingHelper::getConfig();

		if ($config->hide_past_events)
		{
			$this->applyHidePastEventsFilter($query);
		}

		return parent::buildQueryWhere($query);
	}
}
