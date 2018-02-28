<?php
/**
 * @package        Joomla
 * @subpackage     Event Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 - 2017 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

require_once JPATH_ROOT . '/components/com_eventbooking/helper/helper.php';
require_once JPATH_ROOT . '/components/com_eventbooking/helper/route.php';

EventbookingHelper::loadLanguage();

$config           = EventbookingHelper::getConfig();
$fieldSuffix      = EventbookingHelper::getFieldSuffix();
$db               = JFactory::getDbo();
$query            = $db->getQuery(true);
$numberCategories = (int) $params->get('number_categories', 0);
$parentId         = (int) $params->get('parent_id', 0);

$query->select('a.id')
	->select($db->quoteName('a.name' . $fieldSuffix, 'name'))
	->from('#__eb_categories AS a')
	->where('a.parent = ' . $parentId)
	->where('a.published = 1')
	->where('a.access IN (' . implode(',', JFactory::getUser()->getAuthorisedViewLevels()) . ')')
	->order('a.ordering');

if ($fieldSuffix)
{
	$query->where($db->quoteName('a.name' . $fieldSuffix) . ' != ""')
		->where($db->quoteName('a.name' . $fieldSuffix) . ' IS NOT NULL ');
}

if (JFactory::getApplication()->getLanguageFilter())
{
	$query->where('a.language IN (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ', "")');
}

if ($numberCategories)
{
	$db->setQuery($query, 0, $numberCategories);
}
else
{
	$db->setQuery($query);
}

$rows = $db->loadObjectList();

if ($config->show_number_events)
{
	for ($i = 0, $n = count($rows); $i < $n; $i++)
	{
		$row               = $rows[$i];
		$row->total_events = EventbookingHelper::getTotalEvent($row->id);
	}
}

$itemId = (int) $params->get('item_id');

if (!$itemId)
{
	$itemId = EventbookingHelper::getItemid();
}


require JModuleHelper::getLayoutPath('mod_eb_category', 'default');
