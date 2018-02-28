<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

JLoader::register('RADConfig', JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/config/config.php');
JLoader::register('EventbookingHelper', JPATH_ROOT . '/components/com_eventbooking/helper/helper.php');
JLoader::register('EventbookingHelperRoute', JPATH_ROOT . '/components/com_eventbooking/helper/route.php');

/**
 * Routing class from com_eventbooking
 *
 * @since  2.8.1
 */
class EventbookingRouter extends JComponentRouterBase
{
	/**
	 * Build the route for the com_eventbooking component
	 *
	 * @param   array &$query An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   2.8.1
	 */
	public function build(&$query)
	{
		$segments    = array();
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$db          = JFactory::getDbo();
		$dbQuery     = $db->getQuery(true);

		//Store the query string to use in the parseRouter method
		$queryArr = $query;

		//We need a menu item.  Either the one specified in the query, or the current active one if none specified
		if (empty($query['Itemid']))
		{
			$menuItem = $this->menu->getActive();
		}
		else
		{
			$menuItem = $this->menu->getItem($query['Itemid']);

			// If the given menu item doesn't belong to our component, unset the Itemid from query array
			if ($menuItem && $menuItem->component != 'com_eventbooking')
			{
				unset($query['Itemid']);
			}
		}

		if (empty($menuItem->query['view']))
		{
			$menuItem->query['view'] = '';
		}

		// Dealing with top level views in query string
		if ($menuItem
			&& isset($query['view'])
			&& $menuItem->query['view'] == $query['view']
			&& in_array($query['view'], ['calendar', 'fullcalendar', 'events', 'registrants', 'locations', 'massmail'])
		)
		{
			unset($query['view']);
		}

		//Are we dealing with the current view [category, event] which is attached to a menu item?
		if ($menuItem
			&& isset($query['view'], $query['id'], $menuItem->query['id'])
			&& $menuItem->query['view'] == $query['view']
			&& $menuItem->query['id'] == intval($query['id'])
		)
		{
			unset($query['view']);
			unset($query['id']);

			if (isset($query['catid']))
			{
				unset($query['catid']);
			}
		}

		//Dealing with the catid parameter in the link to event from category or upcoming event page
		if ($menuItem
			&& in_array($menuItem->query['view'], array('category', 'upcomingevents'))
			&& isset($query['catid'])
			&& $menuItem->query['id'] == intval($query['catid'])
		)
		{
			unset($query['catid']);
		}

		// Dealing with layout parameter in calendar view
		if ($menuItem
			&& $menuItem->query['view'] == 'calendar'
			&& isset($query['layout'])
			&& ((empty($menuItem->query['layout']) && $query['layout'] == 'default') || ($menuItem->query['layout'] == $query['layout']))
		)
		{
			unset($query['layout']);
		}

		$view    = isset($query['view']) ? $query['view'] : '';
		$id      = isset($query['id']) ? (int) $query['id'] : 0;
		$catId   = isset($query['catid']) ? (int) $query['catid'] : 0;
		$eventId = isset($query['event_id']) ? (int) $query['event_id'] : 0;
		$task    = isset($query['task']) ? $query['task'] : '';
		$layout  = isset($query['layout']) ? $query['layout'] : '';

		switch ($view)
		{
			case 'categories':
			case 'category':
				if ($id)
				{
					$segments = array_merge($segments, EventbookingHelperRoute::getCategoriesPath($id, 'alias'));
				}

				unset($query['view'], $query['id']);
				break;
			case 'event':
				if ($id)
				{
					$segments[] = EventbookingHelperRoute::getEventTitle($id);
				}

				if ($layout == 'form')
				{
					$segments[] = 'edit';
					unset($query['layout']);
				}
				else
				{
					$config = EventbookingHelper::getConfig();

					if ($catId && $config->insert_category != 2)
					{
						$segments = array_merge(EventbookingHelperRoute::getCategoriesPath($catId, 'alias'), $segments);
					}
				}

				unset($query['view'], $query['id']);
				break;
			case 'location':
				if ($layout == 'form' || $layout == 'popup')
				{
					if ($id)
					{
						$dbQuery->clear()
							->select($db->quoteName('alias' . $fieldSuffix))
							->from('#__eb_locations')
							->where('id = ' . (int) $id);
						$db->setQuery($dbQuery);
						$segments[] = $id . '-' . $db->loadResult();
						$segments[] = 'edit';
						unset($query['id']);
					}
					else
					{
						$segments[] = 'add location';
					}

					if ($layout == 'form')
					{
						unset($query['layout']);
					}
				}
				else
				{
					if (isset($query['location_id']))
					{
						$dbQuery->clear()
							->select($db->quoteName('alias' . $fieldSuffix))
							->from('#__eb_locations')
							->where('id = ' . (int) $query['location_id']);
						$db->setQuery($dbQuery);
						$segments[] = $db->loadResult();
						unset($query['location_id']);
					}
				}
				unset($query['view']);
				break;
			case 'map':
				if (isset($query['location_id']))
				{
					$dbQuery->clear()
						->select($db->quoteName('alias' . $fieldSuffix))
						->from('#__eb_locations')
						->where('id = ' . (int) $query['location_id']);
					$db->setQuery($dbQuery);
					$segments[] = $db->loadResult();
					unset($query['location_id']);
				}

				$segments[] = 'view map';
				unset($query['view']);
				break;
			case 'cart':
				$segments[] = 'view cart';
				unset($query['view']);
				break;
			case 'invite':
				if ($id)
				{
					$segments[] = EventbookingHelperRoute::getEventTitle($id);
				}

				$segments[] = 'invite friend';
				unset($query['view'], $query['id']);
				break;
			case 'password':
				if ($eventId)
				{
					$segments[] = EventbookingHelperRoute::getEventTitle($eventId);
				}

				$segments[] = 'password validation';
				unset($query['view'], $query['id']);
				break;
			case 'registrantlist':
				if ($id)
				{
					$segments[] = EventbookingHelperRoute::getEventTitle($id);
				}
				$segments[] = 'registrants list';
				unset($query['view'], $query['id']);
				break;
			case 'waitinglist':
				$segments[] = 'join waitinglist successfull';
				unset($query['view']);
				break;
			case 'failure':
				$segments[] = 'registration failure';
				unset($query['view']);
				break;
			case 'cancel':
				$segments[] = 'registration cancel';
				unset($query['view']);
				break;
			case 'complete':
				$segments[] = 'Registration Complete';
				unset($query['view']);
				break;
			case 'registrationcancel':
				$segments[] = 'registration cancelled';
				unset($query['view']);
				break;
			case 'search':
				$segments[] = 'search result';
				unset($query['view']);
				break;
			case 'payment':
				if ($layout == 'registration')
				{
					$segments[] = 'registration payment';
				}
				elseif ($layout == 'complete')
				{
					$segments[] = 'payment-complete';
				}
				else
				{
					$segments[] = 'remainder payment';
				}

				if (isset($query['registrant_id']))
				{
					$segments[] = $query['registrant_id'];
					unset($query['registrant_id']);
				}

				if (isset($query['registration_code']))
				{
					$segments[] = $query['registration_code'];
					unset($query['registration_code']);
				}

				if (isset($query['layout']))
				{
					unset($query['layout']);
				}
				unset($query['view']);
				break;
		}

		switch ($task)
		{
			case 'register.individual_registration':
				if ($eventId)
				{
					$segments[] = EventbookingHelperRoute::getEventTitle($eventId);
				}
				$segments[] = 'individual registration';
				unset($query['task']);
				break;
			case 'register.group_registration':
				if ($eventId)
				{
					$segments[] = EventbookingHelperRoute::getEventTitle($eventId);
				}
				$segments[] = 'group registration';
				unset($query['task']);
				break;
			case 'group_billing':
				$segments[] = 'group billing';
				unset($query['task']);
				break;
			case 'event.download_ical':
				if ($eventId)
				{
					$segments[] = EventbookingHelperRoute::getEventTitle($eventId);
				}
				$segments[] = 'download_ical';
				unset($query['task']);
				break;
			case 'event.unpublish':
				if ($id)
				{
					$segments[] = EventbookingHelperRoute::getEventTitle($id);
				}
				$segments[] = 'Unpublish';
				unset($query['task']);
				unset($query['id']);
				break;

			case 'event.publish':
				if ($id)
				{
					$segments[] = EventbookingHelperRoute::getEventTitle($id);
				}
				$segments[] = 'Publish';
				unset($query['task']);
				unset($query['id']);
				break;
			case 'registrant.export':
				if ($eventId)
				{
					$segments[] = EventbookingHelperRoute::getEventTitle($eventId);
				}
				$segments[] = 'Export Registrants';
				unset($query['task']);
				break;
			case 'checkout':
			case 'view_checkout':
				$segments[] = 'Checkout';
				unset($query['task']);
				break;
		}

		if (isset($query['event_id']))
		{
			unset($query['event_id']);
		}

		if (isset($query['catid']))
		{
			unset($query['catid']);
		}

		if (count($segments))
		{
			$unProcessedVariables = array(
				'option',
				'Itemid',
				'category_id',
				'search',
				'filter_city',
				'filter_state',
				'start',
				'limitstart',
				'limit',
				'print',
			);

			if ($view != 'location' && $view != 'map')
			{
				$unProcessedVariables[] = 'location_id';
			}

			foreach ($unProcessedVariables as $variable)
			{
				if (isset($queryArr[$variable]))
				{
					unset($queryArr[$variable]);
				}
			}

			$queryString = $db->quote(http_build_query($queryArr));
			$segments    = array_map('JApplication::stringURLSafe', $segments);
			$key         = $db->quote(md5(implode('/', $segments)));
			$dbQuery->clear()
				->select('COUNT(*)')
				->from('#__eb_urls')
				->where('md5_key = ' . $key);
			$db->setQuery($dbQuery);
			$total = $db->loadResult();

			if (!$total)
			{
				$dbQuery->clear()
					->insert('#__eb_urls')
					->columns($db->quoteName(['md5_key', 'query', 'view', 'record_id']))
					->values(implode(',', [$key, $queryString, $db->quote($view), (int) $id]));
				$db->setQuery($dbQuery);
				$db->execute();
			}
		}

		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array &$segments The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 * @throws  Exception
	 *
	 * @since   2.8.1
	 */
	public function parse(&$segments)
	{
		$vars = array();

		if (count($segments))
		{
			$db    = JFactory::getDbo();
			$key   = md5(str_replace(':', '-', implode('/', $segments)));
			$query = $db->getQuery(true);
			$query->select('`query`')
				->from('#__eb_urls')
				->where('md5_key = ' . $db->quote($key));
			$db->setQuery($query);
			$queryString = $db->loadResult();

			if ($queryString)
			{
				parse_str(html_entity_decode($queryString), $vars);
			}
			else
			{
				$method = strtoupper(JFactory::getApplication()->input->getMethod());

				if ($method == 'GET')
				{
					throw new Exception('Page not found', 404);
				}
			}
		}

		$item = JFactory::getApplication()->getMenu()->getActive();

		if ($item)
		{
			if (!empty($vars['view']) && !empty($item->query['view']) && $vars['view'] == $item->query['view'])
			{
				foreach ($item->query as $key => $value)
				{
					if ($key != 'option' && $key != 'Itemid' && !isset($vars[$key]))
					{
						$vars[$key] = $value;
					}
				}
			}
		}

		if (isset($vars['tmpl']) && !isset($_GET['tmpl']))
		{
			unset($vars['tmpl']);
		}

		return $vars;
	}
}

/**
 * Events Booking router functions
 *
 * These functions are proxies for the new router interface
 * for old SEF extensions.
 *
 * @param   array &$query An array of URL arguments
 *
 * @return  array  The URL arguments to use to assemble the subsequent URL.
 */
function EventbookingBuildRoute(&$query)
{
	$router = new EventbookingRouter();

	return $router->build($query);
}

function EventbookingParseRoute($segments)
{
	$router = new EventbookingRouter();

	return $router->parse($segments);
}
