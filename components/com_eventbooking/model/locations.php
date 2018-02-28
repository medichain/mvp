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

class EventbookingModelLocations extends RADModelList
{
	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->state->setDefault('filter_order_Dir', 'DESC');
	}
 	/**
	 * Builds SELECT columns list for the query
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return $this
	 */
	protected function buildQueryColumns(JDatabaseQuery $query)
	{
		$query->select('tbl.*')
			->select('c.name AS country_name');

		return $this;
	}

   	/**
	 * Builds JOINS clauses for the query
	 *
	 * @param JDatabaseQuery $query
	 *
	 * @return $this
	 */
	protected function buildQueryJoins(JDatabaseQuery $query)
	{
		$query->leftJoin('#__eb_countries AS c ON tbl.country = c.id ');

		return $this;
	}
	/**
	 * Builds a WHERE clause for the query
	 */
	protected function buildQueryWhere(JDatabaseQuery $query)
	{
		$query->where('tbl.user_id=' . (int) JFactory::getUser()->id);

		return $this;
	}
}
