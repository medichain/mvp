<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class EventbookingModelCategories extends RADModelList
{
	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->state->insert('id', 'int', 0)
			->set('filter_order', 'tbl.ordering');

		$listLength = (int) EventbookingHelper::getConfigValue('number_categories');

		if ($listLength)
		{
			$this->state->setDefault('limit', $listLength);
		}
	}

	/**
	 * Method to get the current parent category
	 *
	 * @return stdClass|null
	 * @throws Exception
	 */
	public function getCategory()
	{
		if ($categoryId = (int) $this->getState('id'))
		{
			$category = EventbookingHelperDatabase::getCategory($categoryId);

			if ($category)
			{
				// Process content plugin for category description
				$category->description = JHtml::_('content.prepare', $category->description);
			}

			return $category;
		}

		return null;
	}

	/**
	 * Get additional data for categories before it is returned
	 *
	 * @param array $rows
	 *
	 * @return void
	 */
	protected function beforeReturnData($rows)
	{
		for ($i = 0, $n = count($rows); $i < $n; $i++)
		{
			$row               = $rows[$i];
			$row->total_events = EventbookingHelper::getTotalEvent($row->id);
			$row->description  = JHtml::_('content.prepare', $row->description);
		}
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
		$query->select('tbl.*');

		// Adding support for multilingual site
		$fieldSuffix = EventbookingHelper::getFieldSuffix();

		if ($fieldSuffix)
		{
			EventbookingHelperDatabase::getMultilingualFields($query, array('name', 'description'), $fieldSuffix);
		}

		return $this;
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
		$db          = $this->getDbo();
		$fieldSuffix = EventbookingHelper::getFieldSuffix();

		$query->where('tbl.published=1')
			->where('tbl.parent=' . $this->state->id)
			->where('tbl.access IN (' . implode(',', JFactory::getUser()->getAuthorisedViewLevels()) . ')');

		if ($fieldSuffix)
		{
			$query->where($db->quoteName('tbl.name' . $fieldSuffix) . ' != ""')
				->where($db->quoteName('tbl.name' . $fieldSuffix) . ' IS NOT NULL');
		}

		if (JFactory::getApplication()->getLanguageFilter())
		{
			$query->where('tbl.language IN (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ', "")');
		}

		return $this;
	}
}
