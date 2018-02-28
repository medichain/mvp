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

class EventbookingModelCoupon extends RADModelAdmin
{
	/**
	 * Post - process, Store coupon code mapping with events.
	 *
	 * @param EventbookingTableCoupon $row
	 * @param RADInput                $input
	 * @param bool                    $isNew
	 */
	protected function afterStore($row, $input, $isNew)
	{
		$assignment = $input->getInt('assignment', 0);
		$eventIds   = $input->get('event_id', array(), 'array');

		if ($assignment == 0 || count($eventIds) == 0)
		{
			$row->event_id = -1;
		}
		else
		{
			$row->event_id = 1;
		}

		$row->store();
		$couponId = $row->id;
		$db       = $this->getDbo();
		$query    = $db->getQuery(true);

		if (!$isNew)
		{
			$query->delete('#__eb_coupon_events')->where('coupon_id = ' . $couponId);
			$config = EventbookingHelper::getConfig();

			if ($config->hide_past_events_from_events_dropdown)
			{
				$currentDate = $db->quote(JHtml::_('date', 'Now', 'Y-m-d'));
				$query->where('event_id IN (SELECT id FROM #__eb_events AS a WHERE a.published = 1 AND (DATE(a.event_date) >= ' . $currentDate . ' OR DATE(a.event_end_date) >= ' . $currentDate . '))');
			}

			$db->setQuery($query);
			$db->execute();

			$query->clear();
		}

		if ($row->event_id != -1)
		{
			$query->insert('#__eb_coupon_events')->columns('coupon_id, event_id');

			for ($i = 0, $n = count($eventIds); $i < $n; $i++)
			{
				$eventId = (int) $eventIds[$i];

				if ($eventId > 0)
				{
					$eventId *= $assignment;
					$query->values("$couponId, $eventId");
				}
			}

			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Method to remove  fields
	 *
	 * @access    public
	 * @return    boolean    True on success
	 */
	public function delete($cid = array())
	{
		if (count($cid))
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			$cids  = implode(',', $cid);
			$query->delete('#__eb_coupon_events')->where('coupon_id IN (' . $cids . ')');
			$db->setQuery($query);
			$db->execute();
			//Do not allow deleting core fields
			$query->clear();
			$query->delete('#__eb_coupons')->where('id IN (' . $cids . ')');
			$db->setQuery($query);
			$db->execute();
		}

		return true;
	}

	/**
	 * @param $file
	 *
	 * @return int
	 * @throws Exception
	 */
	public function import($file)
	{
		$db      = JFactory::getDbo();
		$query   = $db->getQuery(true);
		$coupons = EventbookingHelperData::getDataFromFile($file);

		$imported = 0;
		if (count($coupons))
		{
			$imported = 0;
			foreach ($coupons as $coupon)
			{
				if (empty($coupon['code']) || empty($coupon['discount']))
				{
					continue;
				}

				/* @var EventbookingTableCoupon $row */
				$row = $this->getTable();

				$eventIds = $coupon['event'];
				if (!$eventIds)
				{
					$coupon['event_id'] = -1;
				}
				else
				{
					$coupon['event_id'] = 1;
				}

				if ($coupon['valid_from'])
				{
					$coupon ['valid_from'] = JHtml::date($coupon['valid_from'], 'Y-m-d', null);
				}
				else
				{
					$coupon ['valid_from'] = '';
				}

				if ($coupon['valid_to'])
				{
					$coupon ['valid_to'] = JHtml::date($coupon['valid_to'], 'Y-m-d', null);
				}
				else
				{
					$coupon ['valid_to'] = '';
				}
				$row->bind($coupon);
				$row->store();
				$couponId = $row->id;

				if ($eventIds)
				{
					$eventIds = explode(',', $eventIds);
					$query->clear();
					$query->insert('#__eb_coupon_events')->columns('coupon_id, event_id');
					for ($i = 0, $n = count($eventIds); $i < $n; $i++)
					{
						$eventId = (int) $eventIds[$i];
						if ($eventId > 0)
						{
							$query->values("$couponId, $eventId");
						}
					}
					$db->setQuery($query);
					$db->execute();
				}
				$imported++;
			}
		}

		return $imported;
	}

	/**
	 * Generate batch coupon
	 *
	 * @param RADInput $input
	 */
	public function batch($input)
	{
		$db                  = JFactory::getDbo();
		$query               = $db->getQuery(true);
		$numberCoupon        = $input->getInt('number_coupon', 50);
		$charactersSet       = $input->getString('characters_set');
		$prefix              = $input->getString('prefix');
		$length              = $input->getInt('length', 20);
		$data                = array();
		$data['discount']    = $input->getFloat('discount', 0);
		$data['coupon_type'] = $input->getInt('coupon_type', 0);
		$data['times']       = $input->getInt('times');
		$eventIds            = $input->get('event_id', array(), 'array');

		if (count($eventIds) == 0 || $eventIds[0] == -1)
		{
			$data['event_id'] = -1;
		}
		else
		{
			$data['event_id'] = 1;
		}

		if ($input->getString('valid_from'))
		{
			$data ['valid_from'] = JHtml::date($input->getString('valid_from'), 'Y-m-d', null);
		}
		else
		{
			$data ['valid_from'] = '';
		}

		if ($input->getString('valid_to'))
		{
			$data ['valid_to'] = JHtml::date($input->getString('valid_to'), 'Y-m-d', null);
		}
		else
		{
			$data ['valid_to'] = '';
		}
		$data['used']       = 0;
		$data ['published'] = $input->getInt('published');

		for ($i = 0; $i < $numberCoupon; $i++)
		{
			$salt       = $this->genRandomCoupon($length, $charactersSet);
			$couponCode = $prefix . $salt;

			/* @var EventbookingTableCoupon $row */
			$row          = $this->getTable();
			$data['code'] = $couponCode;

			$row->bind($data);
			$row->store();

			if ($row->event_id != -1)
			{
				$couponId = $row->id;
				$query->clear();
				$query->insert('#__eb_coupon_events')->columns('coupon_id, event_id');
				for ($j = 0, $n = count($eventIds); $j < $n; $j++)
				{
					$eventId = (int) $eventIds[$j];
					if ($eventId > 0)
					{
						$query->values("$couponId, $eventId");
					}
				}
				$db->setQuery($query);
				$db->execute();
			}
		}
	}

	/**
	 * Get list of registration records which use the current coupon code
	 *
	 * @return array
	 */
	public function getRegistrants()
	{
		if ($this->state->id)
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('id, first_name, last_name, email, register_date, total_amount')
				->from('#__eb_registrants')
				->where('coupon_id = ' . $this->state->id)
				->where('group_id = 0')
				->where('(published = 1 OR (payment_method LIKE "os_offline%" AND published NOT IN (2,3)))')
				->order('id');
			$db->setQuery($query);

			return $db->loadObjectList();
		}

		return array();
	}

	/**
	 * Generate random Coupon
	 *
	 * @param int    $length
	 * @param string $charactersSet
	 *
	 * @return string
	 */
	public static function genRandomCoupon($length = 8, $charactersSet)
	{
		$salt = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

		if ($charactersSet)
		{
			$salt = $charactersSet;
		}

		$base     = strlen($salt);
		$makePass = '';

		/*
		 * Start with a cryptographic strength random string, then convert it to
		 * a string with the numeric base of the salt.
		 * Shift the base conversion on each character so the character
		 * distribution is even, and randomize the start shift so it's not
		 * predictable.
		 */
		$random = JCrypt::genRandomBytes($length + 1);
		$shift  = ord($random[0]);

		for ($i = 1; $i <= $length; ++$i)
		{
			$makePass .= $salt[($shift + ord($random[$i])) % $base];
			$shift += ord($random[$i]);
		}

		return $makePass;
	}
}
