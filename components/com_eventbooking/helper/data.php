<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

class EventbookingHelperData
{
	/**
	 * Get day name from given day number
	 *
	 * @param $dayNumber
	 *
	 * @return mixed
	 */
	public static function getDayName($dayNumber)
	{
		static $days;

		if ($days == null)
		{
			$days = array(
				JText::_('EB_SUNDAY'),
				JText::_('EB_MONDAY'),
				JText::_('EB_TUESDAY'),
				JText::_('EB_WEDNESDAY'),
				JText::_('EB_THURSDAY'),
				JText::_('EB_FRIDAY'),
				JText::_('EB_SATURDAY'),
			);
		}

		$i = $dayNumber % 7;

		return $days[$i];
	}

	/**
	 * Get day name from day number in mini calendar
	 *
	 * @param $dayNumber
	 *
	 * @return mixed
	 */
	public static function getDayNameMini($dayNumber)
	{
		static $daysMini = null;

		if ($daysMini === null)
		{
			$daysMini    = array();
			$daysMini[0] = JText::_('EB_MINICAL_SUNDAY');
			$daysMini[1] = JText::_('EB_MINICAL_MONDAY');
			$daysMini[2] = JText::_('EB_MINICAL_TUESDAY');
			$daysMini[3] = JText::_('EB_MINICAL_WEDNESDAY');
			$daysMini[4] = JText::_('EB_MINICAL_THURSDAY');
			$daysMini[5] = JText::_('EB_MINICAL_FRIDAY');
			$daysMini[6] = JText::_('EB_MINICAL_SATURDAY');
		}

		$i = $dayNumber % 7; //

		return $daysMini[$i];
	}

	/**
	 * Get day name HTML code for a given day
	 *
	 * @param int  $dayNumber
	 * @param bool $colored
	 *
	 * @return string
	 */
	public static function getDayNameHtml($dayNumber, $colored = false)
	{
		$i = $dayNumber % 7; // modulo 7

		if ($i == '0' && $colored === true)
		{
			$dayName = '<span class="sunday">' . self::getDayName($i) . '</span>';
		}
		elseif ($i == '6' && $colored === true)
		{
			$dayName = '<span class="saturday">' . self::getDayName($i) . '</span>';
		}
		else
		{
			$dayName = self::getDayName($i);
		}

		return $dayName;
	}

	/**
	 * Get day name HTML code for a given day
	 *
	 * @param int  $dayNumber
	 * @param bool $colored
	 *
	 * @return string
	 */
	public static function getDayNameHtmlMini($dayNumber, $colored = false)
	{
		$i = $dayNumber % 7; // modulo 7

		if ($i == '0' && $colored === true)
		{
			$dayName = '<span class="sunday">' . self::getDayNameMini($i) . '</span>';
		}
		elseif ($i == '6' && $colored === true)
		{
			$dayName = '<span class="saturday">' . self::getDayNameMini($i) . '</span>';
		}
		else
		{
			$dayName = self::getDayNameMini($i);
		}

		return $dayName;
	}

	/**
	 * Build the data used for rendering calendar
	 *
	 * @param array $rows
	 * @param int   $year
	 * @param int   $month
	 * @param bool  $mini
	 *
	 * @return array
	 */
	public static function getCalendarData($rows, $year, $month, $mini = false)
	{
		$rowCount         = count($rows);
		$data             = array();
		$data['startday'] = $startDay = (int) EventbookingHelper::getConfigValue('calendar_start_date');
		$data['year']     = $year;
		$data['month']    = $month;
		$data["daynames"] = array();
		$data["dates"]    = array();
		$month            = intval($month);

		if ($month <= '9')
		{
			$month = '0' . $month;
		}

		// Get days in week
		for ($i = 0; $i < 7; $i++)
		{
			if ($mini)
			{
				$data["daynames"][$i] = self::getDayNameMini(($i + $startDay) % 7);
			}
			else
			{
				$data["daynames"][$i] = self::getDayName(($i + $startDay) % 7);
			}
		}

		// Today date data
		$date       = new DateTime('now', new DateTimeZone(JFactory::getConfig()->get('offset')));
		$todayDay   = $date->format('d');
		$todayMonth = $date->format('m');
		$todayYear  = $date->format('Y');

		// Start days in month
		$date->setDate($year, $month, 1);
		$start = ($date->format('w') - $startDay + 7) % 7;

		//Previous month
		$preMonth = clone $date;
		$preMonth->modify('-1 month');
		$priorMonth = $preMonth->format('m');
		$priorYear  = $preMonth->format('Y');

		$dayCount = 0;

		for ($a = $start; $a > 0; $a--)
		{
			$data["dates"][$dayCount]              = array();
			$data["dates"][$dayCount]["monthType"] = "prior";
			$data["dates"][$dayCount]["month"]     = $priorMonth;
			$data["dates"][$dayCount]["year"]      = $priorYear;
			$dayCount++;
		}

		sort($data["dates"]);

		// Current month
		$end = $date->format('t');

		for ($d = 1; $d <= $end; $d++)
		{
			$data["dates"][$dayCount]              = array();
			$data["dates"][$dayCount]["monthType"] = "current";
			$data["dates"][$dayCount]["month"]     = $month;
			$data["dates"][$dayCount]["year"]      = $year;

			if ($month == $todayMonth && $year == $todayYear && $d == $todayDay)
			{
				$data["dates"][$dayCount]["today"] = true;
			}
			else
			{
				$data["dates"][$dayCount]["today"] = false;
			}

			$data["dates"][$dayCount]['d']      = $d;
			$data["dates"][$dayCount]['events'] = array();

			if ($rowCount > 0)
			{
				foreach ($rows as $row)
				{
					$date_of_event = explode('-', $row->event_date);
					$date_of_event = (int) $date_of_event[2];

					if ($d == $date_of_event)
					{
						$i                                      = count($data["dates"][$dayCount]['events']);
						$data["dates"][$dayCount]['events'][$i] = $row;
					}
				}
			}

			$dayCount++;
		}

		// Following month
		$date->modify('+1 month');
		$days        = (7 - $date->format('w') + $startDay) % 7;
		$followMonth = $date->format('m');
		$followYear  = $date->format('Y');

		for ($d = 1; $d <= $days; $d++)
		{
			$data["dates"][$dayCount]              = array();
			$data["dates"][$dayCount]["monthType"] = "following";
			$data["dates"][$dayCount]["month"]     = $followMonth;
			$data["dates"][$dayCount]["year"]      = $followYear;
			$dayCount++;
		}

		return $data;
	}

	/**
	 * Calculate the discounted prices for events
	 *
	 * @param $rows
	 */
	public static function calculateDiscount($rows)
	{
		$db       = JFactory::getDbo();
		$query    = $db->getQuery(true);
		$user     = JFactory::getUser();
		$config   = EventbookingHelper::getConfig();
		$nullDate = $db->getNullDate();
		$userId   = $user->get('id');

		for ($i = 0, $n = count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];

			if ($userId > 0)
			{
				$query->select('COUNT(id)')
					->from('#__eb_registrants')
					->where('user_id = ' . $userId)
					->where('event_id = ' . $row->id)
					->where('(published=1 OR (payment_method LIKE "os_offline%" AND published NOT IN (2,3)))');
				$db->setQuery($query);
				$row->user_registered = $db->loadResult();
				$query->clear();
			}

			// Calculate discount price
			if ($config->show_discounted_price)
			{
				$discount = 0;

				if (($row->early_bird_discount_date != $nullDate) && ($row->date_diff >= 0))
				{
					if ($row->early_bird_discount_type == 1)
					{
						$discount += $row->individual_price * $row->early_bird_discount_amount / 100;
					}
					else
					{
						$discount += $row->early_bird_discount_amount;
					}
				}

				if ($userId > 0)
				{
					$discountRate = EventbookingHelperRegistration::calculateMemberDiscount($row->discount_amounts, $row->discount_groups);

					if ($discountRate > 0)
					{
						if ($row->discount_type == 1)
						{
							$discount += $row->individual_price * $discountRate / 100;
						}
						else
						{
							$discount += $discountRate;
						}
					}
				}

				$row->discounted_price = $row->individual_price - $discount;
			}

			$lateFee = 0;

			if (($row->late_fee_date != $nullDate) && $row->late_fee_date_diff >= 0 && $row->late_fee_amount > 0)
			{
				if ($row->late_fee_type == 1)
				{
					$lateFee = $row->individual_price * $row->late_fee_amount / 100;
				}
				else
				{

					$lateFee = $row->late_fee_amount;
				}
			}

			$row->late_fee = $lateFee;
		}
	}

	/**
	 * Get all children categories of a given category
	 *
	 * @param int $id
	 *
	 * @return array
	 */
	public static function getAllChildrenCategories($id)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$queue       = array($id);
		$categoryIds = array($id);

		while (count($queue))
		{
			$categoryId = array_pop($queue);

			//Get list of children categories of the current category
			$query->clear()
				->select('id')
				->from('#__eb_categories')
				->where('parent = ' . $categoryId)
				->where('published = 1');
			$db->setQuery($query);
			$db->setQuery($query);
			$children = $db->loadColumn();

			if (count($children))
			{
				$queue       = array_merge($queue, $children);
				$categoryIds = array_merge($categoryIds, $children);
			}
		}

		return $categoryIds;
	}

	/**
	 * Get parent categories of the given category
	 *
	 * @param $categoryId
	 *
	 * @return array
	 */
	public static function getParentCategories($categoryId)
	{
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$parents     = array();
		$fieldSuffix = EventbookingHelper::getFieldSuffix();

		while (true)
		{
			$query->clear()
				->select('id, parent')
				->select($db->quoteName('name' . $fieldSuffix, 'name'))
				->where('id = ' . $categoryId)
				->where('published = 1');
			$db->setQuery($query);
			$row = $db->loadObject();

			if ($row)
			{
				$parents[]  = $row;
				$categoryId = $row->parent;
			}
			else
			{
				break;
			}
		}

		return $parents;
	}

	/**
	 * Get all ticket types of this event
	 *
	 * @param $eventId
	 *
	 * @return array
	 */
	public static function getTicketTypes($eventId)
	{
		static $ticketTypes;

		if (!isset($ticketTypes[$eventId]))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*, 0 AS registered')
				->from('#__eb_ticket_types')
				->where('event_id = ' . $eventId)
				->order('id');
			$db->setQuery($query);
			$rows = $db->loadObjectList();

			$query->clear()
				->select('a.ticket_type_id')
				->select('IFNULL(SUM(a.quantity), 0) AS registered')
				->from('#__eb_registrant_tickets AS a')
				->innerJoin('#__eb_registrants AS b ON a.registrant_id = b.id')
				->where('b.event_id = ' . $eventId)
				->where('b.group_id = 0')
				->where('(b.published = 1 OR (b.payment_method LIKE "os_offline%" AND b.published NOT IN (2,3)))')
				->group('a.ticket_type_id');
			$db->setQuery($query);
			$rowTickets = $db->loadObjectList('ticket_type_id');

			if (count($rowTickets))
			{
				foreach ($rows as $row)
				{
					if (isset($rowTickets[$row->id]))
					{
						$row->registered = $rowTickets[$row->id]->registered;
					}
				}
			}

			$ticketTypes[$eventId] = $rows;
		}

		return $ticketTypes[$eventId];

	}

	/***
	 * Get categories used to generate breadcrump
	 *
	 * @param $id
	 * @param $parentId
	 *
	 * @return array
	 */
	public static function getCategoriesBreadcrumb($id, $parentId)
	{
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$query->select('id, parent')
			->select($db->quoteName('name' . $fieldSuffix, 'name'))
			->from('#__eb_categories')
			->where('published = 1');
		$db->setQuery($query);
		$categories = $db->loadObjectList('id');
		$paths      = array();

		while ($id != $parentId)
		{
			if (isset($categories[$id]))
			{
				$paths[] = $categories[$id];
				$id      = $categories[$id]->parent;
			}
			else
			{
				break;
			}
		}

		return $paths;
	}

	/**
	 * Pre-process event's data before passing to the view for displaying
	 *
	 * @param array  $rows
	 * @param string $context
	 */
	public static function preProcessEventData($rows, $context = 'list')
	{
		// Calculate discounted price
		self::calculateDiscount($rows);

		// Get categories data for each events
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$fieldSuffix = EventbookingHelper::getFieldSuffix();

		$query->select('*')
			->from('#__eb_locations');

		if ($fieldSuffix)
		{
			EventbookingHelperDatabase::getMultilingualFields($query, ['name', 'alias', 'description'], $fieldSuffix);
		}

		$db->setQuery($query);
		$locations = $db->loadObjectList('id');

		$query->clear()
			->select('a.id, a.name, a.alias')
			->from('#__eb_categories AS a')
			->innerJoin('#__eb_event_categories AS b ON a.id = b.category_id')
			->order('b.id');

		if ($fieldSuffix)
		{
			EventbookingHelperDatabase::getMultilingualFields($query, array('a.name', 'a.alias'), $fieldSuffix);
		}

		foreach ($rows as $row)
		{
			$query->where('event_id=' . $row->id);
			$db->setQuery($query);
			$row->categories     = $db->loadObjectList();
			$row->category_id    = $row->categories[0]->id;
			$row->category_name  = $row->categories[0]->name;
			$row->category_alias = $row->categories[0]->alias;

			if ($row->currency_code && !$row->currency_symbol)
			{
				$row->currency_symbol = $row->currency_code;
			}

			// Location data
			if ($row->location_id)
			{
				$row->location = $locations[$row->location_id];
			}
			else
			{
				$row->location = null;
			}

			$query->clear('where');
		}

		// Process content plugin
		foreach ($rows as $row)
		{
			if ($context == 'list')
			{
				$row->short_description = JHtml::_('content.prepare', $row->short_description);
			}
			else
			{
				$row->description = JHtml::_('content.prepare', $row->description);
			}
		}

		$config = EventbookingHelper::getConfig();

		// Process event custom fields data
		if ($config->event_custom_field && ($config->show_event_custom_field_in_category_layout || $context == 'item'))
		{
			EventbookingHelperData::prepareCustomFieldsData($rows);
		}

		// Calculate price including tax
		if ($config->show_price_including_tax && !$config->get('setup_price'))
		{
			foreach ($rows as $row)
			{
				$taxRate                = $row->tax_rate;
				$row->individual_price  = round($row->individual_price * (1 + $taxRate / 100), 2);
				$row->fixed_group_price = round($row->fixed_group_price * (1 + $taxRate / 100), 2);

				if ($config->show_discounted_price)
				{
					$row->discounted_price = round($row->discounted_price * (1 + $taxRate / 100), 2);
				}
			}
		}

		// Get ticket types for events
		if ($config->display_ticket_types)
		{
			foreach ($rows as $row)
			{
				if ($row->has_multiple_ticket_types)
				{
					$row->ticketTypes = self::getTicketTypes($row->id);
				}
			}
		}
	}

	/**
	 * Decode custom fields data and store it for each event record
	 *
	 * @param $items
	 */
	public static function prepareCustomFieldsData($items)
	{
		$xml          = JFactory::getXML(JPATH_ROOT . '/components/com_eventbooking/fields.xml');
		$fields       = $xml->fields->fieldset->children();
		$customFields = array();

		foreach ($fields as $field)
		{
			$name                  = $field->attributes()->name;
			$label                 = JText::_($field->attributes()->label);
			$customFields["$name"] = $label;
		}

		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$item   = $items[$i];
			$params = new Registry();
			$params->loadString($item->custom_fields, 'JSON');
			$paramData = array();

			foreach ($customFields as $name => $label)
			{
				$paramData[$name]['title'] = $label;
				$fieldValue                = $params->get($name);

				if (is_array($fieldValue))
				{
					$fieldValue = implode(', ', $fieldValue);
				}

				$paramData[$name]['value'] = $fieldValue;

				if (!property_exists($item, $name))
				{
					$item->{$name} = $fieldValue;
				}
			}

			$item->paramData = $paramData;
		}
	}

	/**
	 * Get data from excel file using PHPExcel library
	 *
	 * @param $file
	 *
	 * @return array
	 */
	public static function getDataFromFile($file)
	{
		require_once JPATH_ROOT . '/libraries/osphpexcel/PHPExcel.php';

		$data = array();

		$reader = PHPExcel_IOFactory::load($file);

		if ($reader instanceof PHPExcel_Reader_CSV)
		{
			$config = EventbookingHelper::getConfig();
			$reader->setDelimiter($config->get('csv_delimiter', ','));
		}

		$rows = $reader->getActiveSheet()->toArray(null, true, true, true);

		if (count($rows) > 1)
		{
			for ($i = 2, $n = count($rows); $i <= $n; $i++)
			{
				$row = array();

				foreach ($rows[1] as $key => $fieldName)
				{
					$row[$fieldName] = $rows[$i][$key];
				}

				$data[] = $row;
			}
		}

		return $data;
	}

	/**
	 * Prepare registrants data before exporting to excel
	 *
	 * @param array     $rows
	 * @param RADConfig $config
	 * @param array     $rowFields
	 * @param array     $fieldValues
	 * @param int       $eventId
	 * @param bool      $forImport
	 *
	 * @return array
	 */
	public static function prepareRegistrantsExportData($rows, $config, $rowFields, $fieldValues, $eventId = 0, $forImport = false)
	{
		$showGroup = false;

		foreach ($rows as $row)
		{
			if ($row->is_group_billing || $row->group_id > 0)
			{
				$showGroup = true;
				break;
			}
		}

		// Determine whether we need to show payment method column
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('name, title')
			->from('#__eb_payment_plugins')
			->where('published=1');
		$db->setQuery($query);
		$plugins = $db->loadObjectList('name');

		$showPaymentMethodColumn = false;

		if (count($plugins) > 1)
		{
			$showPaymentMethodColumn = true;
		}

		if ($eventId)
		{
			$event = EventbookingHelperDatabase::getEvent($eventId);

			if ($event->has_multiple_ticket_types)
			{
				$ticketTypes = EventbookingHelperData::getTicketTypes($eventId);

				$ticketTypeIds = array();

				foreach ($ticketTypes as $ticketType)
				{
					$ticketTypeIds[] = $ticketType->id;
				}

				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('registrant_id, ticket_type_id, quantity')
					->from('#__eb_registrant_tickets')
					->where('ticket_type_id IN (' . implode(',', $ticketTypeIds) . ')');
				$db->setQuery($query);

				$registrantTickets = $db->loadObjectList();

				$tickets = array();

				foreach ($registrantTickets as $registrantTicket)
				{
					$tickets[$registrantTicket->registrant_id][$registrantTicket->ticket_type_id] = $registrantTicket->quantity;
				}
			}
		}

		$headers = [JText::_('EB_ID'), JText::_('EB_EVENT')];
		$fields  = ['id'];

		if ($forImport)
		{
			$fields[] = 'event_id';
		}
		else
		{
			$fields[] = 'title';
		}

		if ($config->show_event_date)
		{
			$headers[] = JText::_('EB_EVENT_DATE');
			$fields[]  = 'event_date';
		}

		$headers[] = JText::_('EB_USER_ID');
		$fields[]  = 'user_id';

		if ($showGroup)
		{
			$headers[] = JText::_('EB_GROUP');
			$fields[]  = 'registration_group_name';
		}

		if (count($rowFields))
		{
			foreach ($rowFields as $rowField)
			{
				if (!$rowField->hide_on_export)
				{
					$headers[] = $rowField->title;
					$fields[]  = $rowField->name;
				}
			}
		}

		if (!empty($ticketTypes))
		{
			foreach ($ticketTypes as $ticketType)
			{
				$headers[] = $ticketType->title;
				$fields[]  = 'event_ticket_type_' . $ticketType->id;
			}
		}

		$headers[] = JText::_('EB_NUMBER_REGISTRANTS');
		$headers[] = JText::_('EB_AMOUNT');
		$headers[] = JText::_('EB_DISCOUNT_AMOUNT');
		$headers[] = JText::_('EB_LATE_FEE');
		$headers[] = JText::_('EB_TAX');
		$headers[] = JText::_('EB_GROSS_AMOUNT');

		$fields[] = 'number_registrants';
		$fields[] = 'total_amount';
		$fields[] = 'discount_amount';
		$fields[] = 'late_fee';
		$fields[] = 'tax_amount';
		$fields[] = 'amount';

		if ($config->activate_deposit_feature)
		{
			$headers[] = JText::_('EB_DEPOSIT_AMOUNT');
			$headers[] = JText::_('EB_DUE_AMOUNT');

			$fields[] = 'deposit_amount';
			$fields[] = 'due_amount';
		}

		if ($config->show_coupon_code_in_registrant_list)
		{
			$headers[] = JText::_('EB_COUPON');
			$fields[]  = 'coupon_code';
		}

		$headers[] = JText::_('EB_REGISTRATION_DATE');
		$fields[]  = 'register_date';

		if ($showPaymentMethodColumn || $forImport)
		{
			$headers[] = JText::_('EB_PAYMENT_METHOD');
			$fields[]  = 'payment_method';
		}

		if ($config->activate_tickets_pdf)
		{
			$headers[] = JText::_('EB_TICKET_NUMBER');
			$headers[] = JText::_('EB_TICKET_CODE');
			$fields[]  = 'ticket_number';
			$fields[]  = 'ticket_code';
		}

		$headers[] = JText::_('EB_TRANSACTION_ID');
		$fields[]  = 'transaction_id';

		if ($config->activate_deposit_feature)
		{
			$headers[] = JText::_('EB_DEPOSIT_PAYMENT_TRANSACTION_ID');
			$fields[]  = 'deposit_payment_transaction_id';
		}

		$headers[] = JText::_('EB_PAYMENT_STATUS');

		if ($forImport)
		{
			$fields[] = 'published';
		}
		else
		{
			$fields[] = 'payment_status';
		}

		if ($config->activate_checkin_registrants)
		{
			$headers[] = JText::_('EB_CHECKED_IN');
			$fields[]  = 'checked_in';
		}

		if ($config->activate_invoice_feature)
		{
			$headers[] = JText::_('EB_INVOICE_NUMBER');
			$fields[]  = 'invoice_number';
		}

		foreach ($rows as $row)
		{
			if ($config->show_event_date)
			{
				$row->event_date = JHtml::_('date', $row->event_date, $config->date_format, null);
			}

			if ($showGroup)
			{
				if ($row->is_group_billing)
				{
					$row->registration_group_name = $row->first_name . ' ' . $row->last_name;
				}
				elseif ($row->group_id > 0)
				{
					$row->registration_group_name = $row->group_name;
				}
				else
				{
					$row->registration_group_name = '';
				}
			}

			foreach ($rowFields as $rowField)
			{
				if (!$rowField->is_core)
				{
					$fieldValue = @$fieldValues[$row->id][$rowField->id];

					if (is_string($fieldValue) && is_array(json_decode($fieldValue)))
					{
						$fieldValue = implode(', ', json_decode($fieldValue));
					}

					$row->{$rowField->name} = $fieldValue;
				}
			}


			if (!empty($ticketTypes))
			{
				foreach ($ticketTypes as $ticketType)
				{
					if (!empty($tickets[$row->id][$ticketType->id]))
					{
						$row->{'event_ticket_type_' . $ticketType->id} = $tickets[$row->id][$ticketType->id];
					}
					else
					{
						$row->{'event_ticket_type_' . $ticketType->id} = 0;
					}
				}
			}

			$row->total_amount    = EventbookingHelper::formatAmount($row->total_amount, $config);
			$row->discount_amount = EventbookingHelper::formatAmount($row->discount_amount, $config);
			$row->late_fee        = EventbookingHelper::formatAmount($row->late_fee, $config);
			$row->tax_amount      = EventbookingHelper::formatAmount($row->tax_amount, $config);
			$row->amount          = EventbookingHelper::formatAmount($row->amount, $config);

			if ($config->activate_deposit_feature)
			{
				if ($row->deposit_amount > 0)
				{
					$row->deposit_amount = EventbookingHelper::formatAmount($row->deposit_amount, $config);
					$row->due_amount     = EventbookingHelper::formatAmount($row->amount - $row->deposit_amount, $config);
				}
				else
				{
					$row->deposit_amount = '';
					$row->due_amount     = '';
				}
			}

			$row->register_date = JHtml::_('date', $row->register_date, $config->date_format);

			if ($config->activate_tickets_pdf)
			{
				if ($row->ticket_number)
				{
					$row->ticket_number = EventbookingHelperTicket::formatTicketNumber($row->ticket_prefix, $row->ticket_number, $config);
				}
				else
				{
					$row->ticket_number = '';
				}
			}

			if (!$forImport)
			{
				switch ($row->published)
				{
					case 0:
						$row->payment_status = JText::_('EB_PENDING');
						break;
					case 1:
						$row->payment_status = JText::_('EB_PAID');
						break;
					case 2:
						$row->payment_status = JText::_('EB_CANCELLED');
						break;
					case 3:
						$row->payment_status = JText::_('EB_WAITING_LIST');
						break;
					default:
						break;
				}

				if ($row->checked_in)
				{
					$row->checked_in = JText::_('JYES');
				}
				else
				{
					$row->checked_in = JText::_('JNO');
				}

				if ($config->activate_invoice_feature)
				{

					if ($row->invoice_number)
					{
						$row->invoice_number = EventbookingHelper::formatInvoiceNumber($row->invoice_number, $config);
					}
					else
					{
						$row->invoice_number = '';
					}
				}

				if ($row->payment_method && isset($plugins[$row->payment_method]))
				{
					$row->payment_method = JText::_($plugins[$row->payment_method]->title);
				}
				else
				{
					$row->payment_method = '';
				}
			}
		}

		return array($fields, $headers);
	}

	/**
	 * Export the given data to Excel
	 *
	 * @param $fields
	 * @param $rows
	 * @param $filename
	 * @param $headers
	 */
	public static function excelExport($fields, $rows, $filename, $headers = array())
	{
		$config   = EventbookingHelper::getConfig();
		$fileType = $config->get('export_data_format', 'xlsx');

		if ($fileType == 'csv')
		{
			static::csvExport($fields, $rows, $filename, $headers);

			return;
		}

		if ($fileType == 'xlsx')
		{
			static::xlsxExport($fields, $rows, $filename, $headers);

			return;
		}

		require_once JPATH_ROOT . '/libraries/osphpexcel/PHPExcel.php';

		$exporter    = new PHPExcel();
		$user        = JFactory::getUser();
		$createdDate = JFactory::getDate('now', JFactory::getConfig()->get('offset'))->format('Y-m-d');

		//Set properties Excel
		$exporter->getProperties()
			->setCreator($user->name)
			->setLastModifiedBy($user->name);

		//Set some styles and layout for Excel file
		$borderedCenter = new PHPExcel_Style();
		$borderedCenter->applyFromArray(
			array(
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				),
				'font'      => array(
					'name' => 'Times New Roman', 'bold' => false, 'italic' => false, 'size' => 11
				),
				'borders'   => array(
					'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
					'right'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
					'top'    => array('style' => PHPExcel_Style_Border::BORDER_THIN),
					'left'   => array('style' => PHPExcel_Style_Border::BORDER_THIN),
				)
			)
		);

		$borderedLeft = new PHPExcel_Style();
		$borderedLeft->applyFromArray(
			array(
				'alignment' => array(
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				),
				'font'      => array(
					'name' => 'Times New Roman', 'bold' => false, 'italic' => false, 'size' => 11
				),
				'borders'   => array(
					'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
					'right'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
					'top'    => array('style' => PHPExcel_Style_Border::BORDER_THIN),
					'left'   => array('style' => PHPExcel_Style_Border::BORDER_THIN),
				)
			)
		);

		$sheet    = $exporter->setActiveSheetIndex(0);
		$column   = 'A';
		$rowIndex = '1';

		if (empty($headers))
		{
			$headers = $fields;
		}

		foreach ($headers as $header)
		{
			$sheet->setCellValue($column . $rowIndex, $header);
			$sheet->getColumnDimension($column)->setAutoSize(true);
			$column++;
		}

		$rowIndex++;

		foreach ($rows as $row)
		{
			$column = 'A';
			foreach ($fields as $field)
			{
				$cellData = isset($row->{$field}) ? $row->{$field} : '';
				$sheet->setCellValue($column . $rowIndex, $cellData);
				$sheet->getColumnDimension($column)->setAutoSize(true);
				$column++;
			}
			$rowIndex++;
		}

		switch ($fileType)
		{
			case 'csv' :
				$writer = 'CSV';
				break;
			case 'xls' :
				$writer = 'Excel5';
				break;
			case 'xlsx' :
				$writer = 'Excel2007';
				break;
			default :
				$writer = 'Excel2007';
				break;
		}

		header('Content-Type: application/vnd.ms-exporter');
		header('Content-Disposition: attachment;filename=' . $filename . '_on_' . $createdDate . '.' . $fileType);
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($exporter, $writer);

		if ($fileType == 'csv')
		{
			/* @var PHPExcel_Writer_CSV $objWriter */
			$objWriter->setDelimiter($config->get('csv_delimiter', ','));
		}

		$objWriter->save('php://output');

		JFactory::getApplication()->close();
	}

	/**
	 * Export the given data to CSV, this method will be used as a backup in case we have performance issue with PHPExcel on large dataset
	 *
	 * @param $fields
	 * @param $rows
	 * @param $filename
	 * @param $headers
	 */
	public static function csvExport($fields, $rows, $filename, $headers = array())
	{
		$browser   = JFactory::getApplication()->client->browser;
		$mime_type = ($browser == JApplicationWebClient::IE || $browser == JApplicationWebClient::OPERA) ? 'application/octetstream' : 'application/octet-stream';

		$createdDate = JFactory::getDate('now', JFactory::getConfig()->get('offset'))->format('Y-m-d');
		$filename    = $filename . '_on_' . $createdDate;

		header('Content-Encoding: UTF-8');
		header('Content-Type: ' . $mime_type . ' ;charset=UTF-8');
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');

		if ($browser == JApplicationWebClient::IE)
		{
			header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		}
		else
		{
			header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
			header('Pragma: no-cache');
		}

		$fp = fopen('php://output', 'w');
		fwrite($fp, "\xEF\xBB\xBF");


		$config    = EventbookingHelper::getConfig();
		$delimiter = $config->get('csv_delimiter', ',');

		fputcsv($fp, $headers, $delimiter);

		foreach ($rows as $row)
		{
			$values = array();
			foreach ($fields as $field)
			{
				$values[] = isset($row->{$field}) ? $row->{$field} : '';
			}
			fputcsv($fp, $values, $delimiter);
		}

		fclose($fp);

		JFactory::getApplication()->close();
	}

	/**
	 * Export registrants data into XLSX format
	 *
	 * @param       $fields
	 * @param       $rows
	 * @param       $filename
	 * @param array $headers
	 */
	public static function xlsxExport($fields, $rows, $filename, $headers = array())
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/vendor/xlsxwriter/xlsxwriter.class.php';

		$user        = JFactory::getUser();
		$createdDate = JFactory::getDate('now', JFactory::getConfig()->get('offset'))->format('Y-m-d');

		header('Content-disposition: attachment; filename="' . ($filename . '_on_' . $createdDate . '.xlsx') . '"');
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');

		$writer = new XLSXWriter();
		$writer->setAuthor($user->name);

		$data = array();

		if (empty($headers))
		{
			$data[] = $fields;
		}
		else
		{
			$data[] = $headers;
		}

		foreach ($rows as $row)
		{
			$values = array();

			foreach ($fields as $field)
			{
				$values[] = isset($row->{$field}) ? $row->{$field} : '';
			}

			$data[] = $values;
		}

		$writer->writeSheet($data, 'Sheet1');

		$writer->writeToStdOut();

		JFactory::getApplication()->close();
	}
}