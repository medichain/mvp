<?php
/**
 * @package        Joomla
 * @subpackage     Events Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 - 2017 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

use Joomla\String\StringHelper;

class plgContentEBRestriction extends JPlugin
{
	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{
		if (file_exists(JPATH_ROOT . '/components/com_eventbooking/eventbooking.php') && is_object($row))
		{
			// Check whether the plugin should process or not
			if (StringHelper::strpos($row->text, 'ebrestriction') === false)
			{
				return true;
			}

			// Search for this tag in the content
			$regex     = '#{ebrestriction ids="(.*?)"}(.*?){/ebrestriction}#s';
			$row->text = preg_replace_callback($regex, array(&$this, 'processRestriction'), $row->text);
		}

		return true;
	}

	/**
	 * Process content restriction
	 *
	 * @param array $matches
	 *
	 * @return string
	 */
	private function processRestriction($matches)
	{
		$requiredEventIds = $matches[1];
		$protectedText    = $matches[2];
		$registeredEvents = $this->getRegisteredEvents();
		if (count($registeredEvents) == 0)
		{
			return '';
		}
		elseif ($requiredEventIds == '*')
		{
			return $protectedText;
		}
		else
		{
			$requiredEventIds = explode(',', $requiredEventIds);
			if (count(array_intersect($requiredEventIds, $registeredEvents)))
			{
				return $protectedText;
			}
			else
			{
				return '';
			}
		}
	}

	/**
	 *  Get list of events which the current user has registered
	 *
	 * @return array
	 */
	private function getRegisteredEvents()
	{
		$user = JFactory::getUser();
		if ($user->id)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->clear()->select('event_id')->from('#__eb_registrants')->where('published=1')->where('user_id=' . $user->id);
			$db->setQuery($query);

			return $db->loadColumn();
		}

		return array();
	}
}
