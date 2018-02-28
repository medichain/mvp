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

class EventbookingModelDiscount extends RADModelAdmin
{
	/**
	 * Pre-process data before custom field is being saved to database
	 *
	 * @param JTable   $row
	 * @param RADInput $input
	 * @param bool     $isNew
	 */
	protected function beforeStore($row, $input, $isNew)
	{
		$row->event_ids = implode(',', $input->get('event_id', array(), 'array'));
	}

	/**
	 * Post - process, Store discount rule mapping with events.
	 *
	 * @param EventbookingTableDiscount $row
	 * @param RADInput                  $input
	 * @param bool                      $isNew
	 */
	protected function afterStore($row, $input, $isNew)
	{
		$eventIds   = $input->get('event_id', array(), 'array');
		$discountId = $row->id;
		$db         = $this->getDbo();
		$query      = $db->getQuery(true);

		if (!$isNew)
		{
			$query->delete('#__eb_discount_events')->where('discount_id = ' . $discountId);
			$db->setQuery($query);
			$db->execute();
		}

		foreach ($eventIds as $eventId)
		{
			$query->clear()
				->insert('#__eb_discount_events')
				->columns('discount_id, event_id');
			for ($i = 0, $n = count($eventIds); $i < $n; $i++)
			{
				$eventId = (int) $eventId;
				$query->values("$discountId, $eventId");
			}
			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Delete the mapping between discount and events before the actual discounts are being deleted
	 *
	 * @param array $cid Ids of deleted record
	 */
	protected function beforeDelete($cid)
	{
		if (count($cid))
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			$cids  = implode(',', $cid);
			$query->delete('#__eb_discount_events')
				->where('discount_id IN (' . $cids . ')');
			$db->setQuery($query);
			$db->execute();
		}
	}
}
