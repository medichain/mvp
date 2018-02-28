<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

JLoader::register('EventbookingHelper', JPATH_ROOT . '/components/com_eventbooking/helper/helper.php');

class EventbookingHelperRoute
{
	/**
	 * Menu items look up array
	 *
	 * @var array
	 */
	protected static $lookup;

	/**
	 * Function to get Event Route
	 *
	 * @param int $id
	 * @param int $catId
	 * @param int $itemId
	 *
	 * @return string
	 */
	public static function getEventRoute($id, $catId = 0, $itemId = 0)
	{
		$id      = (int) $id;
		$needles = ['event' => [$id]];
		$link    = 'index.php?option=com_eventbooking&view=event&id=' . $id;

		if (!$catId)
		{
			//Find the main category of this event
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('category_id')
				->from('#__eb_event_categories')
				->where('event_id = ' . $id)
				->where('main_category = 1');
			$db->setQuery($query);
			$catId = (int) $db->loadResult();
		}

		if ($catId)
		{
			$needles['category']   = self::getCategoriesPath($catId, 'id', false);
			$needles['categories'] = $needles['upcomingevents'] = $needles['category'];
			$link .= '&catid=' . $catId;
		}

		if ($item = self::findItem($needles, $itemId))
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	/**
	 * Function to get Category Route
	 *
	 * @param int $id
	 * @param int $itemId
	 *
	 * @return string
	 */
	public static function getCategoryRoute($id, $itemId = 0)
	{
		$link    = 'index.php?option=com_eventbooking&view=category&id=' . $id;
		$catIds  = self::getCategoriesPath($id, 'id', false);
		$needles = array('category' => $catIds, 'upcomingevents' => $catIds, 'categories' => $catIds);

		if ($item = self::findItem($needles, $itemId))
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	/**
	 * Function to get View Route
	 *
	 * @param string $view (cart, checkout)
	 * @param int    $itemId
	 *
	 * @return string
	 */
	public static function getViewRoute($view, $itemId)
	{
		$link = 'index.php?option=com_eventbooking&view=' . $view;

		if ($item = self::findView($view, $itemId))
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	/**
	 * Get event title, used for building the router
	 *
	 * @param $id
	 *
	 * @return string
	 */
	public static function getEventTitle($id)
	{
		static $events;

		if (!isset($events[$id]))
		{
			$config = EventbookingHelper::getConfig();
			$db     = JFactory::getDbo();
			$query  = $db->getQuery(true);

			if ($fieldSuffix = EventbookingHelper::getFieldSuffix())
			{
				$query->select($db->quoteName('alias' . $fieldSuffix, 'alias'));
			}
			else
			{
				$query->select('alias');
			}

			$query->from('#__eb_events')
				->where('id = ' . $id);
			$db->setQuery($query);

			if ($config->insert_event_id)
			{
				$events[$id] = $id . '-' . $db->loadResult();
			}
			else
			{
				$events[$id] = $db->loadResult();
			}
		}

		return $events[$id];
	}

	/**
	 * @param int    $id
	 * @param string $type
	 * @param bool   $reverse
	 *
	 * @return array
	 */
	public static function getCategoriesPath($id, $type = 'id', $reverse = true)
	{
		static $categories;

		if (empty($categories))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id, parent')->from('#__eb_categories');

			if ($fieldSuffix = EventbookingHelper::getFieldSuffix())
			{
				$query->select($db->quoteName('alias' . $fieldSuffix, 'alias'));
			}
			else
			{
				$query->select('alias');
			}

			$db->setQuery($query);
			$categories = $db->loadObjectList('id');
		}

		$config = EventbookingHelper::getConfig();
		$paths  = array();

		if ($type == 'id' || $config->insert_category == 0)
		{
			do
			{
				$paths[] = $categories[$id]->{$type};
				$id      = $categories[$id]->parent;
			} while ($id != 0);

			if ($reverse)
			{
				$paths = array_reverse($paths);
			}
		}
		else
		{
			$paths[] = $categories[$id]->{$type};
		}

		return $paths;
	}

	/**
	 * Find item id variable corresponding to the view
	 *
	 * @param string $view
	 * @param int    $itemId
	 *
	 * @return int
	 */
	public static function findView($view, $itemId = 0)
	{
		$needles = [$view => [0]];

		if ($item = self::findItem($needles, $itemId))
		{
			return $item;
		}

		return 0;
	}

	/**
	 * Function to find Itemid
	 *
	 * @param array $needles
	 * @param int   $itemId
	 *
	 * @return int
	 */
	public static function findItem($needles = array(), $itemId = 0)
	{
		// Prepare the reverse lookup array.
		if (self::$lookup === null)
		{
			self::$lookup = array();
			$component    = JComponentHelper::getComponent('com_eventbooking');
			$menus        = JFactory::getApplication()->getMenu('site');
			$items        = $menus->getItems('component_id', $component->id);

			foreach ($items as $item)
			{
				if (!empty($item->query['view']))
				{
					$view = $item->query['view'];

					if (!isset(self::$lookup[$view]))
					{
						self::$lookup[$view] = array();
					}

					if (isset($item->query['id']))
					{
						self::$lookup[$view][$item->query['id']] = $item->id;
					}
					else
					{
						self::$lookup[$view][0] = $item->id;
					}
				}
			}
		}

		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$view]))
				{
					foreach ($ids as $id)
					{
						$id = (int) $id;

						if (isset(self::$lookup[$view][$id]))
						{
							return self::$lookup[$view][$id];
						}
					}
				}
			}
		}

		//Return default item id
		return $itemId;
	}

	/**
	 * Get default menu item
	 *
	 * @return int
	 */
	public static function getDefaultMenuItem()
	{
		$config   = EventbookingHelper::getConfig();
		$language = JFactory::getLanguage()->getTag();

		if (JLanguageMultilang::isEnabled() && $config->get('default_menu_item_' . $language))
		{
			return $config->get('default_menu_item_' . $language);
		}
		else if ($config->get('default_menu_item') > 0)
		{
			return $config->get('default_menu_item');
		}
		else
		{
			$defaultViews = ['calendar', 'fullcalendar', 'categories', 'upcomingevents', 'category'];
			$component    = JComponentHelper::getComponent('com_eventbooking');
			$menus        = JFactory::getApplication()->getMenu('site');
			$items        = $menus->getItems('component_id', $component->id);

			foreach ($items as $item)
			{
				if (!empty($item->query['view']) && in_array($item->query['view'], $defaultViews))
				{
					return $item->id;
				}
			}
		}

		return 0;
	}
}
