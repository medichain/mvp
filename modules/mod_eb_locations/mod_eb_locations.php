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
require_once JPATH_ROOT . '/components/com_eventbooking/helper/helper.php';

$document = JFactory::getDocument();
$document->addStylesheet(JUri::base(true) . '/media/com_eventbooking/assets/css/style.css', 'text/css', null, null);
$user = JFactory::getUser();
EventbookingHelper::loadLanguage();
$config           = EventbookingHelper::getConfig();
$fieldSuffix      = EventbookingHelper::getFieldSuffix();
$app              = JFactory::getApplication();
$db               = JFactory::getDbo();
$query            = $db->getQuery(true);
$numberLocations  = (int) $params->get('number_locations', 0);
$showNumberEvents = (int) $params->get('show_number_events', 1);

$query->select('a.id, COUNT(b.id) AS total_events')
	->select($db->quoteName('a.name' . $fieldSuffix, 'name'))
	->from('#__eb_locations AS a')
	->innerJoin('#__eb_events AS b ON a.id = b.location_id')
	->where('a.published = 1')
	->where('b.published = 1')
	->where('b.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')')
	->group('a.id')
	->order($db->quoteName('a.name' . $fieldSuffix));

if ($config->hide_past_events)
{
	$currentDate = $db->quote(JHtml::_('date', 'Now', 'Y-m-d'));

	if ($config->show_children_events_under_parent_event)
	{
		$query->where('(DATE(b.event_date) >= ' . $currentDate . ' OR DATE(b.cut_off_date) >= ' . $currentDate . ' OR DATE(b.max_end_date) >= ' . $currentDate . ')');
	}
	else
	{
		if ($config->show_until_end_date)
		{
			$query->where('(DATE(b.event_date) >= ' . $currentDate . ' OR DATE(b.event_end_date) >= ' . $currentDate . ')');
		}
		else
		{
			$query->where('(DATE(b.event_date) >= ' . $currentDate . ' OR DATE(b.cut_off_date) >= ' . $currentDate . ')');
		}
	}
}

if ($numberLocations)
{
	$db->setQuery($query, 0, $numberLocations);
}
else
{
	$db->setQuery($query);
}

$rows   = $db->loadObjectList();
$itemId = (int) $params->get('item_id');

if (!$itemId)
{
	$itemId = EventbookingHelper::getItemid();
}

require JModuleHelper::getLayoutPath('mod_eb_locations', 'default');
