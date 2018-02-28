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
use Joomla\Utilities\ArrayHelper;

class EventbookingHelperRegistration
{
	/**
	 * Calculate discount rate which the current user will receive
	 *
	 * @param $discount
	 * @param $groupIds
	 *
	 * @return float
	 */
	public static function calculateMemberDiscount($discount, $groupIds)
	{
		$user = JFactory::getUser();

		if (!$discount)
		{
			return 0;
		}

		if (!$groupIds)
		{
			return $discount;
		}

		$userGroupIds = explode(',', $groupIds);
		$userGroupIds = ArrayHelper::toInteger($userGroupIds);
		$groups       = $user->get('groups');

		if (count(array_intersect($groups, $userGroupIds)))
		{
			//Calculate discount amount
			if (strpos($discount, ',') !== false)
			{
				$discountRates = explode(',', $discount);
				$maxDiscount   = 0;

				foreach ($groups as $group)
				{
					$index = array_search($group, $userGroupIds);

					if ($index !== false && isset($discountRates[$index]))
					{
						$maxDiscount = max($maxDiscount, $discountRates[$index]);
					}
				}

				return $maxDiscount;
			}
			else
			{
				return $discount;
			}
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Check to see whether this event still accept registration
	 *
	 * @param EventbookingTableEvent $event
	 *
	 * @return bool
	 */
	public static function acceptRegistration($event)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$user  = JFactory::getUser();

		$accessLevels = $user->getAuthorisedViewLevels();
		if (empty($event)
			|| !$event->published
			|| !in_array($event->access, $accessLevels)
			|| !in_array($event->registration_access, $accessLevels)
		)
		{
			return false;
		}

		if ($event->registration_type == 3)
		{
			return false;
		}

		if (!in_array($event->registration_access, $user->getAuthorisedViewLevels()))
		{
			return false;
		}

		if ($event->registration_start_minutes < 0)
		{
			return false;
		}

		// If cut off date is entered, we will check registration based on cut of date, not event date
		if ($event->cut_off_date != $db->getNullDate())
		{
			if ($event->cut_off_minutes > 0)
			{
				return false;
			}
		}
		else
		{
			if ($event->number_event_dates < 0)
			{
				return false;
			}
		}

		if ($event->event_capacity && ($event->total_registrants >= $event->event_capacity))
		{
			return false;
		}

		$config = EventbookingHelper::getConfig();

		//Check to see whether the current user has registered for the event
		if ($event->prevent_duplicate_registration === '')
		{
			$preventDuplicateRegistration = $config->prevent_duplicate_registration;
		}
		else
		{
			$preventDuplicateRegistration = $event->prevent_duplicate_registration;
		}

		if ($preventDuplicateRegistration && $user->id)
		{
			$query->clear()
				->select('COUNT(id)')
				->from('#__eb_registrants')
				->where('event_id = ' . $event->id)
				->where('user_id = ' . $user->id)
				->where('(published=1 OR (payment_method LIKE "os_offline%" AND published NOT IN (2,3)))');
			$db->setQuery($query);
			$total = $db->loadResult();

			if ($total)
			{
				return false;
			}
		}

		if (!$config->multiple_booking)
		{
			// Check for quantity fields
			$query->clear()
				->select('*')
				->from('#__eb_fields')
				->where('published=1')
				->where('quantity_field = 1')
				->where('quantity_values != ""')
				->where(' `access` IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');

			if ($config->custom_field_by_category)
			{
				//Get main category of the event
				$categoryQuery = $db->getQuery(true);
				$categoryQuery->select('category_id')
					->from('#__eb_event_categories')
					->where('event_id = ' . $event->id)
					->where('main_category = 1');

				$db->setQuery($categoryQuery);
				$categoryId = (int) $db->loadResult();
				$query->where('(category_id = -1 OR id IN (SELECT field_id FROM #__eb_field_categories WHERE category_id=' . $categoryId . '))');
			}
			else
			{
				$negEventId = -1 * $event->id;
				$subQuery   = $db->getQuery(true);
				$subQuery->select('field_id')
					->from('#__eb_field_events')
					->where("(event_id = $event->id OR (event_id < 0 AND event_id != $negEventId))");

				$query->where(' (event_id = -1 OR id IN (' . (string) $subQuery . '))');
			}

			$db->setQuery($query);
			$quantityFields = $db->loadObjectList();

			if (count($quantityFields))
			{
				foreach ($quantityFields as $field)
				{
					$values         = explode("\r\n", $field->values);
					$quantityValues = explode("\r\n", $field->quantity_values);

					if (count($values) && count($quantityValues))
					{
						$multilingualValues = array();

						if (JLanguageMultilang::isEnabled())
						{
							$multilingualValues = RADFormField::getMultilingualOptions($field->id);
						}

						$values = EventbookingHelperHtml::getAvailableQuantityOptions($values, $quantityValues, $event->id, $field->id, ($field->fieldtype == 'Checkboxes') ? true : false, $multilingualValues);

						if (!count($values))
						{
							return false;
						}
					}
				}
			}
		}

		if ($event->has_multiple_ticket_types)
		{
			$ticketTypes = EventbookingHelperData::getTicketTypes($event->id);

			foreach ($ticketTypes as $ticketType)
			{
				if (!$ticketType->capacity || ($ticketType->capacity > $ticketType->registered))
				{
					return true;
				}
			}

			return false;
		}

		return true;
	}

	/**
	 * Get all custom fields for an event
	 *
	 * @param int $eventId
	 *
	 * @return array
	 */
	public static function getAllEventFields($eventId)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, name, title, is_core, hide_on_export, show_on_registrants')
			->from('#__eb_fields')
			->where('published = 1')
			->order('ordering');

		if ($eventId)
		{
			$config = EventbookingHelper::getConfig();

			if ($config->custom_field_by_category)
			{
				$subQuery = $db->getQuery(true);
				$subQuery->select('category_id')
					->from('#__eb_event_categories')
					->where('event_id = ' . $eventId)
					->where('main_category = 1');
				$db->setQuery($subQuery);
				$categoryId = (int) $db->loadResult();
				$query->where('(category_id = -1 OR id IN (SELECT field_id FROM #__eb_field_categories WHERE category_id=' . $categoryId . '))');
			}
			else
			{
				$negEventId = -1 * $eventId;
				$subQuery   = $db->getQuery(true);
				$subQuery->select('field_id')
					->from('#__eb_field_events')
					->where("(event_id = $eventId OR (event_id < 0 AND event_id != $negEventId))");
				$query->where('(event_id = -1 OR id IN (' . (string) $subQuery . '))');
			}
		}

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Get name of published core fields in the system
	 *
	 * @return array
	 */
	public static function getPublishedCoreFields()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('name')
			->from('#__eb_fields')
			->where('published = 1')
			->where('is_core = 1');
		$db->setQuery($query);

		return $db->loadColumn();
	}

	/**
	 * Get the form fields to display in deposit payment form
	 *
	 * @return array
	 */
	public static function getDepositPaymentFormFields()
	{
		$user        = JFactory::getUser();
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$query->select('*')
			->from('#__eb_fields')
			->where('published=1')
			->where('id < 13')
			->where(' `access` IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')')
			->order('ordering');

		if ($fieldSuffix)
		{
			EventbookingHelperDatabase::getMultilingualFields($query, array('title', 'description', 'values', 'default_values', 'depend_on_options'), $fieldSuffix);
		}

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Get the form fields to display in registration form
	 *
	 * @param int    $eventId (ID of the event or ID of the registration record in case the system use shopping cart)
	 * @param int    $registrationType
	 * @param string $activeLanguage
	 *
	 * @return array
	 */
	public static function getFormFields($eventId = 0, $registrationType = 0, $activeLanguage = null)
	{
		if (EventbookingHelper::isMethodOverridden('EventbookingHelperOverrideRegistration', 'getFormFields'))
		{
			return EventbookingHelperOverrideRegistration::getFormFields($eventId, $registrationType, $activeLanguage);
		}

		static $cache;

		$cacheKey = md5(serialize(func_get_args()));

		if (empty($cache[$cacheKey]))
		{
			$app         = JFactory::getApplication();
			$user        = JFactory::getUser();
			$db          = JFactory::getDbo();
			$query       = $db->getQuery(true);
			$config      = EventbookingHelper::getConfig();
			$fieldSuffix = EventbookingHelper::getFieldSuffix($activeLanguage);
			$query->select('*')
				->from('#__eb_fields')
				->where('published=1');

			if (!$user->authorise('core.admin', 'com_eventbooking') || $app->isSite())
			{
				$query->where(' `access` IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');
			}

			if ($fieldSuffix)
			{
				EventbookingHelperDatabase::getMultilingualFields($query, array('title', 'description', 'values', 'default_values', 'depend_on_options'), $fieldSuffix);
			}

			switch ($registrationType)
			{
				case 0:
					$query->where('display_in IN (0, 1, 3, 5)');
					break;
				case 1:
					$query->where('display_in IN (0, 2, 3)');
					break;
				case 2:
					$query->where('display_in IN (0, 4, 5)');
					break;
			}

			$subQuery = $db->getQuery(true);

			if ($registrationType == 4)
			{
				$cart  = new EventbookingHelperCart();
				$items = $cart->getItems();

				if ($config->custom_field_by_category)
				{
					if (!count($items))
					{
						//In this case, we have ID of registration record, so, get list of events from that registration
						$subQuery->select('event_id')
							->from('#__eb_registrants')
							->where('id = ' . $eventId);
						$db->setQuery($subQuery);
						$cartEventId = (int) $db->loadResult();
						$subQuery->clear();
					}
					else
					{
						$cartEventId = (int) $items[0];
					}

					$subQuery->select('category_id')
						->from('#__eb_event_categories')
						->where('event_id = ' . $cartEventId)
						->where('main_category = 1');
					$db->setQuery($subQuery);
					$categoryId = (int) $db->loadResult();
					$query->where('(category_id = -1 OR id IN (SELECT field_id FROM #__eb_field_categories WHERE category_id=' . $categoryId . '))');
				}
				else
				{
					if (!count($items))
					{
						//In this case, we have ID of registration record, so, get list of events from that registration
						$subQuery->select('event_id')
							->from('#__eb_registrants')
							->where('id = ' . $eventId);
						$db->setQuery($subQuery);
						$items = $db->loadColumn();
					}

					$query->where('(event_id = -1 OR id IN (SELECT field_id FROM #__eb_field_events WHERE event_id IN (' . implode(',', $items) . ')))');
				}

				$query->where('display_in IN (0, 1, 2, 3)');
			}
			else
			{
				if ($config->custom_field_by_category)
				{
					//Get main category of the event
					$subQuery->select('category_id')
						->from('#__eb_event_categories')
						->where('event_id = ' . $eventId)
						->where('main_category = 1');
					$db->setQuery($subQuery);
					$categoryId = (int) $db->loadResult();
					$query->where('(category_id = -1 OR id IN (SELECT field_id FROM #__eb_field_categories WHERE category_id=' . $categoryId . '))');
				}
				else
				{
					$negEventId = -1 * $eventId;
					$query->where('(event_id = -1 OR id IN (SELECT field_id FROM #__eb_field_events WHERE event_id = ' . $eventId . ' OR event_id < 0))')
						->where('id NOT IN (SELECT field_id FROM #__eb_field_events WHERE event_id = ' . $negEventId . ')');
				}
			}

			$query->order('ordering');
			$db->setQuery($query);

			$cache[$cacheKey] = $db->loadObjectList();
		}

		return $cache[$cacheKey];
	}

	/**
	 * Get registration rate for group registration
	 *
	 * @param int $eventId
	 * @param int $numberRegistrants
	 *
	 * @return mixed
	 */
	public static function getRegistrationRate($eventId, $numberRegistrants)
	{
		if (EventbookingHelper::isMethodOverridden('EventbookingHelperOverrideRegistration', 'getRegistrationRate'))
		{
			return EventbookingHelperOverrideRegistration::getRegistrationRate($eventId, $numberRegistrants);
		}

		// We need to keep it here for backward compatible purpose
		if (EventbookingHelper::isMethodOverridden('EventbookingHelperOverrideHelper', 'getRegistrationRate'))
		{
			return EventbookingHelperOverrideHelper::getRegistrationRate($eventId, $numberRegistrants);
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('price')
			->from('#__eb_event_group_prices')
			->where('event_id = ' . $eventId)
			->where('registrant_number <= ' . $numberRegistrants)
			->order('registrant_number DESC');
		$db->setQuery($query, 0, 1);
		$rate = $db->loadResult();

		if (!$rate)
		{
			$query->clear()
				->select('individual_price')
				->from('#__eb_events')
				->where('id = ' . $eventId);
			$db->setQuery($query);
			$rate = $db->loadResult();
		}

		return $rate;
	}

	/**
	 * Calculate registration fee
	 *
	 * @param EventbookingTableRegistrant $row
	 * @param string                      $paymentMethod
	 *
	 * @return array
	 */
	public static function calculateRegistrationFees($row, $paymentMethod)
	{
		$fees['amount']                 = $row->amount - $row->payment_processing_fee;
		$fees['payment_processing_fee'] = 0;

		if ($paymentMethod)
		{
			$method            = os_payments::loadPaymentMethod($paymentMethod);
			$params            = new Registry($method->params);
			$paymentFeeAmount  = (float) $params->get('payment_fee_amount');
			$paymentFeePercent = (float) $params->get('payment_fee_percent');

			if ($paymentFeeAmount != 0 || $paymentFeePercent != 0)
			{
				$fees['payment_processing_fee'] = round($paymentFeeAmount + $fees['amount'] * $paymentFeePercent / 100, 2);
			}
		}

		$fees['gross_amount'] = $fees['amount'] + $fees['payment_processing_fee'];

		return $fees;
	}

	/**
	 * Calculate remainder fee
	 *
	 * @param EventbookingTableRegistrant $row
	 * @param string                      $paymentMethod
	 *
	 * @return array
	 */
	public static function calculateRemainderFees($row, $paymentMethod)
	{
		$fees['amount']                 = $amount = $row->amount - $row->deposit_amount;
		$fees['payment_processing_fee'] = 0;

		if ($paymentMethod)
		{
			$method            = os_payments::loadPaymentMethod($paymentMethod);
			$params            = new Registry($method->params);
			$paymentFeeAmount  = (float) $params->get('payment_fee_amount');
			$paymentFeePercent = (float) $params->get('payment_fee_percent');

			if ($paymentFeeAmount != 0 || $paymentFeePercent != 0)
			{
				$fees['payment_processing_fee'] = round($paymentFeeAmount + $fees['amount'] * $paymentFeePercent / 100, 2);
			}
		}

		$fees['gross_amount'] = $fees['amount'] + $fees['payment_processing_fee'];

		return $fees;
	}

	/**
	 * Calculate fees use for individual registration
	 *
	 * @param object    $event
	 * @param RADForm   $form
	 * @param array     $data
	 * @param RADConfig $config
	 * @param string    $paymentMethod
	 *
	 * @return array
	 */
	public static function calculateIndividualRegistrationFees($event, $form, $data, $config, $paymentMethod = null)
	{
		$fees       = array();
		$user       = JFactory::getUser();
		$db         = JFactory::getDbo();
		$query      = $db->getQuery(true);
		$couponCode = isset($data['coupon_code']) ? $data['coupon_code'] : '';

		$feeCalculationTags = array(
			'NUMBER_REGISTRANTS' => 1,
			'INDIVIDUAL_PRICE'   => $event->individual_price
		);

		if ($config->event_custom_field && file_exists(JPATH_ROOT . '/components/com_eventbooking/fields.xml'))
		{
			EventbookingHelperData::prepareCustomFieldsData(array($event));

			$filterInput = JFilterInput::getInstance();

			foreach ($event->paramData as $customFieldName => $param)
			{
				$feeCalculationTags[strtoupper($customFieldName)] = $filterInput->clean($param['value'], 'float');
			}
		}

		$totalAmount = $event->individual_price + $form->calculateFee($feeCalculationTags);

		if ($event->has_multiple_ticket_types)
		{
			$ticketTypes = EventbookingHelperData::getTicketTypes($event->id);

			foreach ($ticketTypes as $ticketType)
			{
				if (!empty($data['ticket_type_' . $ticketType->id]))
				{
					$totalAmount += (int) $data['ticket_type_' . $ticketType->id] * $ticketType->price;
				}
			}
		}

		if ($config->get('setup_price'))
		{
			$totalAmount = $totalAmount / (1 + $event->tax_rate / 100);
		}

		$discountAmount        = 0;
		$fees['discount_rate'] = 0;
		$nullDate              = $db->getNullDate();

		if ($user->id)
		{
			$discountRate = self::calculateMemberDiscount($event->discount_amounts, $event->discount_groups);

			if ($discountRate > 0)
			{
				$fees['discount_rate'] = $discountRate;

				if ($event->discount_type == 1)
				{
					$discountAmount = $totalAmount * $discountRate / 100;
				}
				else
				{
					$discountAmount = $discountRate;
				}
			}
		}

		if (($event->early_bird_discount_date != $nullDate) && ($event->date_diff >= 0))
		{
			if ($event->early_bird_discount_amount > 0)
			{
				if ($event->early_bird_discount_type == 1)
				{
					$discountAmount = $discountAmount + $totalAmount * $event->early_bird_discount_amount / 100;
				}
				else
				{
					$discountAmount = $discountAmount + $event->early_bird_discount_amount;
				}
			}
		}

		if ($couponCode)
		{
			$negEventId     = -1 * $event->id;
			$nullDateQuoted = $db->quote($db->getNullDate());

			//Validate the coupon
			$query->clear()
				->select('*')
				->from('#__eb_coupons')
				->where('published = 1')
				->where('`access` IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')')
				->where('code = ' . $db->quote($couponCode))
				->where('(valid_from = ' . $nullDateQuoted . ' OR valid_from <= NOW())')
				->where('(valid_to = ' . $nullDateQuoted . ' OR valid_to >= NOW())')
				->where('(times = 0 OR times > used)')
				->where('discount > used_amount')
				->where('enable_for IN (0, 1)')
				->where('user_id IN (0, ' . $user->id . ')')
				->where('(event_id = -1 OR id IN (SELECT coupon_id FROM #__eb_coupon_events WHERE event_id = ' . $event->id . ' OR event_id < 0))')
				->where('id NOT IN (SELECT coupon_id FROM #__eb_coupon_events WHERE event_id = ' . $negEventId . ')')
				->order('id DESC');
			$db->setQuery($query);
			$coupon = $db->loadObject();

			if ($coupon)
			{
				$fees['coupon_valid'] = 1;
				$fees['coupon']       = $coupon;

				if ($coupon->coupon_type == 0)
				{
					$discountAmount = $discountAmount + $totalAmount * $coupon->discount / 100;
				}
				elseif ($coupon->coupon_type == 1)
				{
					$discountAmount = $discountAmount + $coupon->discount;
				}
			}
			else
			{
				$fees['coupon_valid'] = 0;
			}
		}
		else
		{
			$fees['coupon_valid'] = 1;
		}

		$fees['bundle_discount_amount'] = 0;
		$fees['bundle_discount_ids']    = array();

		// Calculate bundle discount if setup
		if ($user->id > 0)
		{
			$nullDate    = $db->quote($db->getNullDate());
			$currentDate = $db->quote(JHtml::_('date', 'Now', 'Y-m-d'));
			$query->clear()
				->select('id, event_ids, discount_amount')
				->from('#__eb_discounts')
				->where('(from_date = ' . $nullDate . ' OR DATE(from_date) <=' . $currentDate . ')')
				->where('(to_date = ' . $nullDate . ' OR DATE(to_date) >= ' . $currentDate . ')')
				->where('(times = 0 OR times > used)')
				->where('id IN (SELECT discount_id FROM #__eb_discount_events WHERE event_id = ' . $event->id . ')');
			$db->setQuery($query);
			$discountRules = $db->loadObjectList();

			if (!empty($discountRules))
			{
				$query->clear()
					->select('DISTINCT event_id')
					->from('#__eb_registrants')
					->where('user_id = ' . $user->id)
					->where('(published = 1 OR (payment_method LIKE "os_offline%" AND published IN (0, 1)))');
				$registeredEventIds = $db->loadColumn();

				if (count($registeredEventIds))
				{
					$registeredEventIds[] = $event->id;

					foreach ($discountRules as $rule)
					{
						$eventIds = explode(',', $rule->event_ids);

						if (!array_diff($eventIds, $registeredEventIds))
						{
							$fees['bundle_discount_amount'] += $rule->discount_amount;
							$discountAmount += $rule->discount_amount;
							$fees['bundle_discount_ids'][] = $rule->id;
						}
					}
				}
			}
		}

		if ($discountAmount > $totalAmount)
		{
			$discountAmount = $totalAmount;
		}

		// Late Fee
		$lateFee = 0;

		if (($event->late_fee_date != $nullDate) && $event->late_fee_date_diff >= 0 && $event->late_fee_amount > 0)
		{
			if ($event->late_fee_type == 1)
			{
				$lateFee = $event->individual_price * $event->late_fee_amount / 100;
			}
			else
			{

				$lateFee = $event->late_fee_amount;
			}
		}

		if ($event->tax_rate > 0 && ($totalAmount - $discountAmount + $lateFee > 0))
		{

			$taxAmount = round(($totalAmount - $discountAmount + $lateFee) * $event->tax_rate / 100, 2);
			$amount    = $totalAmount - $discountAmount + $taxAmount + $lateFee;
		}
		else
		{
			$taxAmount = 0;
			$amount    = $totalAmount - $discountAmount + $taxAmount + $lateFee;
		}

		// Init payment processing fee amount
		$fees['payment_processing_fee'] = 0;

		// Payment processing fee
		$hasPaymentProcessingFee = false;
		$paymentFeeAmount        = 0;
		$paymentFeePercent       = 0;

		if ($paymentMethod)
		{
			$method            = os_payments::loadPaymentMethod($paymentMethod);
			$params            = new Registry($method->params);
			$paymentFeeAmount  = (float) $params->get('payment_fee_amount');
			$paymentFeePercent = (float) $params->get('payment_fee_percent');

			if ($paymentFeeAmount != 0 || $paymentFeePercent != 0)
			{
				$hasPaymentProcessingFee = true;
			}
		}

		$paymentType = isset($data['payment_type']) ? (int) $data['payment_type'] : 0;

		if ($paymentType == 0 && $amount > 0 && $hasPaymentProcessingFee)
		{
			$fees['payment_processing_fee'] = round($paymentFeeAmount + $amount * $paymentFeePercent / 100, 2);
			$amount += $fees['payment_processing_fee'];
		}

		$couponDiscountAmount = 0;

		if (!empty($coupon) && $coupon->coupon_type == 2)
		{
			$couponAvailableAmount = $coupon->discount - $coupon->used_amount;

			if ($couponAvailableAmount >= $amount)
			{
				$couponDiscountAmount = $amount;
				$amount               = 0;
			}
			else
			{
				$amount               = $amount - $couponAvailableAmount;
				$couponDiscountAmount = $couponAvailableAmount;
			}
		}

		$discountAmount += $couponDiscountAmount;

		// Calculate the deposit amount as well
		if ($config->activate_deposit_feature && $event->deposit_amount > 0)
		{
			if ($event->deposit_type == 2)
			{
				$depositAmount = $event->deposit_amount;
			}
			else
			{
				$depositAmount = $event->deposit_amount * $amount / 100;
			}
		}
		else
		{
			$depositAmount = 0;
		}

		if ($paymentType == 1 && $depositAmount > 0 && $hasPaymentProcessingFee)
		{
			$fees['payment_processing_fee'] = round($paymentFeeAmount + $depositAmount * $paymentFeePercent / 100, 2);
			$amount += $fees['payment_processing_fee'];
			$depositAmount += $fees['payment_processing_fee'];
		}

		$fees['total_amount']           = round($totalAmount, 2);
		$fees['discount_amount']        = round($discountAmount, 2);
		$fees['tax_amount']             = round($taxAmount, 2);
		$fees['amount']                 = round($amount, 2);
		$fees['deposit_amount']         = round($depositAmount, 2);
		$fees['late_fee']               = round($lateFee, 2);
		$fees['coupon_discount_amount'] = round($couponDiscountAmount, 2);

		return $fees;
	}

	/**
	 * Calculate fees use for group registration
	 *
	 * @param object    $event
	 * @param RADForm   $form
	 * @param array     $data
	 * @param RADConfig $config
	 * @param string    $paymentMethod
	 *
	 * @return array
	 */
	public static function calculateGroupRegistrationFees($event, $form, $data, $config, $paymentMethod = null)
	{
		$fees              = array();
		$session           = JFactory::getSession();
		$user              = JFactory::getUser();
		$db                = JFactory::getDbo();
		$query             = $db->getQuery(true);
		$couponCode        = isset($data['coupon_code']) ? $data['coupon_code'] : '';
		$eventId           = $event->id;
		$numberRegistrants = (int) $session->get('eb_number_registrants', '');

		if (!$numberRegistrants && isset($data['number_registrants']))
		{
			$numberRegistrants = (int) $data['number_registrants'];
		}

		$memberFormFields = EventbookingHelperRegistration::getFormFields($eventId, 2);
		$rate             = static::getRegistrationRate($eventId, $numberRegistrants);

		$feeCalculationTags = array(
			'NUMBER_REGISTRANTS' => $numberRegistrants,
			'INDIVIDUAL_PRICE'   => $rate,
		);

		if ($config->event_custom_field && file_exists(JPATH_ROOT . '/components/com_eventbooking/fields.xml'))
		{
			EventbookingHelperData::prepareCustomFieldsData(array($event));

			$filterInput = JFilterInput::getInstance();

			foreach ($event->paramData as $customFieldName => $param)
			{
				$feeCalculationTags[strtoupper($customFieldName)] = $filterInput->clean($param['value'], 'float');
			}
		}

		$extraFee = $form->calculateFee($feeCalculationTags);

		$nullDate              = $db->getNullDate();
		$membersForm           = array();
		$membersTotalAmount    = array();
		$membersDiscountAmount = array();
		$membersLateFee        = array();
		$membersTaxAmount      = array();
		$membersAmount         = array();

		if ($event->collect_member_information === '')
		{
			$collectMemberInformation = $config->collect_member_information;
		}
		else
		{
			$collectMemberInformation = $event->collect_member_information;
		}

		// Members data
		if ($collectMemberInformation)
		{
			$membersData = $session->get('eb_group_members_data', null);

			if ($membersData)
			{
				$membersData = unserialize($membersData);
			}
			elseif (!empty($data['re_calculate_fee']))
			{
				$membersData = $data;
			}
			else
			{
				$membersData = array();
			}

			for ($i = 0; $i < $numberRegistrants; $i++)
			{
				$memberForm = new RADForm($memberFormFields);
				$memberForm->setFieldSuffix($i + 1);
				$memberForm->bind($membersData);
				$memberExtraFee = $memberForm->calculateFee($feeCalculationTags);
				$extraFee += $memberExtraFee;
				$membersTotalAmount[$i] = $rate + $memberExtraFee;

				if ($config->get('setup_price'))
				{
					$membersTotalAmount[$i] = $membersTotalAmount[$i] / (1 + $event->tax_rate / 100);
				}

				$membersDiscountAmount[$i] = 0;
				$membersLateFee[$i]        = 0;
				$membersForm[$i]           = $memberForm;
			}
		}

		if ($event->fixed_group_price > 0)
		{
			$totalAmount = $event->fixed_group_price + $extraFee;
		}
		else
		{
			$totalAmount = $rate * $numberRegistrants + $extraFee;
		}

		if ($config->get('setup_price'))
		{
			$totalAmount = $totalAmount / (1 + $event->tax_rate / 100);
		}

		// Calculate discount amount
		$discountAmount = 0;

		if ($user->id)
		{
			$discountRate = static::calculateMemberDiscount($event->discount_amounts, $event->discount_groups);

			if ($discountRate > 0)
			{
				if ($event->discount_type == 1)
				{
					$discountAmount = $totalAmount * $discountRate / 100;

					if ($collectMemberInformation)
					{
						for ($i = 0; $i < $numberRegistrants; $i++)
						{
							$membersDiscountAmount[$i] += $membersTotalAmount[$i] * $discountRate / 100;
						}
					}
				}
				else
				{
					$discountAmount = $numberRegistrants * $discountRate;

					if ($collectMemberInformation)
					{
						for ($i = 0; $i < $numberRegistrants; $i++)
						{
							$membersDiscountAmount[$i] += $discountRate;
						}
					}
				}
			}
		}

		if ($couponCode)
		{
			$negEventId     = -1 * $event->id;
			$nullDateQuoted = $db->quote($db->getNullDate());
			$query->clear()
				->select('*')
				->from('#__eb_coupons')
				->where('published = 1')
				->where('`access` IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')')
				->where('code = ' . $db->quote($couponCode))
				->where('(valid_from = ' . $nullDateQuoted . ' OR valid_from <= NOW())')
				->where('(valid_to = ' . $nullDateQuoted . ' OR valid_to >= NOW())')
				->where('(times = 0 OR times > used)')
				->where('discount > used_amount')
				->where('enable_for IN (0, 2)')
				->where('user_id IN (0, ' . $user->id . ')')
				->where('(event_id = -1 OR id IN (SELECT coupon_id FROM #__eb_coupon_events WHERE event_id = ' . $event->id . ' OR event_id < 0))')
				->where('id NOT IN (SELECT coupon_id FROM #__eb_coupon_events WHERE event_id = ' . $negEventId . ')')
				->order('id DESC');
			$db->setQuery($query);
			$coupon = $db->loadObject();

			if ($coupon)
			{
				$fees['coupon_valid'] = 1;
				$fees['coupon']       = $coupon;

				if ($coupon->coupon_type == 0)
				{
					$discountAmount = $discountAmount + $totalAmount * $coupon->discount / 100;

					if ($collectMemberInformation)
					{
						for ($i = 0; $i < $numberRegistrants; $i++)
						{
							$membersDiscountAmount[$i] += $membersTotalAmount[$i] * $coupon->discount / 100;
						}
					}
				}
				elseif ($coupon->coupon_type == 1)
				{
					if ($coupon->apply_to == 0)
					{
						$discountAmount = $discountAmount + $numberRegistrants * $coupon->discount;

						if ($collectMemberInformation)
						{
							for ($i = 0; $i < $numberRegistrants; $i++)
							{
								$membersDiscountAmount[$i] += $coupon->discount;
							}
						}
					}
					else
					{
						$discountAmount = $discountAmount + $coupon->discount;
						$membersDiscountAmount[0] += $coupon->discount;
					}
				}
			}
			else
			{
				$fees['coupon_valid'] = 0;
			}
		}
		else
		{
			$fees['coupon_valid'] = 1;
		}

		if (($event->early_bird_discount_date != $nullDate) && ($event->date_diff >= 0))
		{
			if ($event->early_bird_discount_amount > 0)
			{
				if ($event->early_bird_discount_type == 1)
				{
					$discountAmount = $discountAmount + $totalAmount * $event->early_bird_discount_amount / 100;

					if ($collectMemberInformation)
					{
						for ($i = 0; $i < $numberRegistrants; $i++)
						{
							$membersDiscountAmount[$i] += $membersTotalAmount[$i] * $event->early_bird_discount_amount / 100;
						}
					}
				}
				else
				{
					$discountAmount = $discountAmount + $numberRegistrants * $event->early_bird_discount_amount;

					if ($collectMemberInformation)
					{
						for ($i = 0; $i < $numberRegistrants; $i++)
						{
							$membersDiscountAmount[$i] += $event->early_bird_discount_amount;
						}
					}
				}
			}
		}

		$fees['bundle_discount_amount'] = 0;
		$fees['bundle_discount_ids']    = array();

		// Calculate bundle discount if setup
		if ($user->id > 0)
		{
			$nullDate    = $db->quote($db->getNullDate());
			$currentDate = $db->quote(JHtml::_('date', 'Now', 'Y-m-d'));
			$query->clear()
				->select('id, event_ids, discount_amount')
				->from('#__eb_discounts')
				->where('(from_date = ' . $nullDate . ' OR DATE(from_date) <=' . $currentDate . ')')
				->where('(to_date = ' . $nullDate . ' OR DATE(to_date) >= ' . $currentDate . ')')
				->where('(times = 0 OR times > used)')
				->where('id IN (SELECT discount_id FROM #__eb_discount_events WHERE event_id = ' . $event->id . ')');
			$db->setQuery($query);
			$discountRules = $db->loadObjectList();

			if (!empty($discountRules))
			{
				$query->clear()
					->select('DISTINCT event_id')
					->from('#__eb_registrants')
					->where('user_id = ' . $user->id)
					->where('(published = 1 OR (payment_method LIKE "os_offline%" AND published IN (0, 1)))');
				$registeredEventIds = $db->loadColumn();

				if (count($registeredEventIds))
				{
					$registeredEventIds[] = $event->id;

					foreach ($discountRules as $rule)
					{
						$eventIds = explode(',', $rule->event_ids);

						if (!array_diff($eventIds, $registeredEventIds))
						{
							$fees['bundle_discount_amount'] += $rule->discount_amount;
							$discountAmount += $rule->discount_amount;
							$fees['bundle_discount_ids'][] = $rule->id;
						}
					}
				}
			}
		}

		// Late Fee
		$lateFee = 0;

		if (($event->late_fee_date != $nullDate) && $event->late_fee_date_diff >= 0 && $event->late_fee_amount > 0)
		{
			if ($event->late_fee_type == 1)
			{
				$lateFee = $totalAmount * $event->late_fee_amount / 100;

				if ($collectMemberInformation)
				{
					for ($i = 0; $i < $numberRegistrants; $i++)
					{
						$membersLateFee[$i] = $membersTotalAmount[$i] * $event->late_fee_amount / 100;
					}
				}
			}
			else
			{

				$lateFee = $numberRegistrants * $event->late_fee_amount;

				if ($collectMemberInformation)
				{
					for ($i = 0; $i < $numberRegistrants; $i++)
					{
						$membersLateFee[$i] = $event->late_fee_amount;
					}
				}
			}
		}

		// In case discount amount greater than total amount, reset it to total amount
		if ($discountAmount > $totalAmount)
		{
			$discountAmount = $totalAmount;
		}

		if ($collectMemberInformation)
		{
			for ($i = 0; $i < $numberRegistrants; $i++)
			{
				if ($membersDiscountAmount[$i] > $membersTotalAmount[$i])
				{
					$membersDiscountAmount[$i] = $membersTotalAmount[$i];
				}
			}
		}

		// Calculate tax amount
		if ($event->tax_rate > 0 && ($totalAmount - $discountAmount + $lateFee > 0))
		{
			$taxAmount = round(($totalAmount - $discountAmount + $lateFee) * $event->tax_rate / 100, 2);
			// Gross amount
			$amount = $totalAmount - $discountAmount + $taxAmount + $lateFee;

			if ($collectMemberInformation)
			{
				for ($i = 0; $i < $numberRegistrants; $i++)
				{
					$membersTaxAmount[$i] = round(($membersTotalAmount[$i] - $membersDiscountAmount[$i] + $membersLateFee[$i]) * $event->tax_rate / 100, 2);
					$membersAmount[$i]    = $membersTotalAmount[$i] - $membersDiscountAmount[$i] + $membersLateFee[$i] + $membersTaxAmount[$i];
				}
			}
		}
		else
		{
			$taxAmount = 0;
			// Gross amount
			$amount = $totalAmount - $discountAmount + $taxAmount + $lateFee;

			if ($collectMemberInformation)
			{
				for ($i = 0; $i < $numberRegistrants; $i++)
				{
					$membersTaxAmount[$i] = 0;
					$membersAmount[$i]    = $membersTotalAmount[$i] - $membersDiscountAmount[$i] + $membersLateFee[$i] + $membersTaxAmount[$i];
				}
			}
		}

		// Init payment processing fee amount
		$fees['payment_processing_fee'] = 0;

		// Payment processing fee
		$hasPaymentProcessingFee = false;
		$paymentFeeAmount        = 0;
		$paymentFeePercent       = 0;

		if ($paymentMethod)
		{
			$method            = os_payments::loadPaymentMethod($paymentMethod);
			$params            = new Registry($method->params);
			$paymentFeeAmount  = (float) $params->get('payment_fee_amount');
			$paymentFeePercent = (float) $params->get('payment_fee_percent');

			if ($paymentFeeAmount != 0 || $paymentFeePercent != 0)
			{
				$hasPaymentProcessingFee = true;
			}
		}

		$paymentType = isset($data['payment_type']) ? (int) $data['payment_type'] : 0;

		if ($paymentType == 0 && $amount > 0 && $hasPaymentProcessingFee)
		{
			$fees['payment_processing_fee'] = round($paymentFeeAmount + $amount * $paymentFeePercent / 100, 2);
			$amount += $fees['payment_processing_fee'];
		}

		$couponDiscountAmount = 0;

		if (!empty($coupon) && $coupon->coupon_type == 2)
		{
			$couponAvailableAmount = $coupon->discount - $coupon->used_amount;

			if ($couponAvailableAmount >= $amount)
			{
				$couponDiscountAmount = $amount;
			}
			else
			{
				$couponDiscountAmount = $couponAvailableAmount;
			}

			$amount -= $couponDiscountAmount;

			if ($collectMemberInformation)
			{
				for ($i = 0; $i < $numberRegistrants; $i++)
				{
					if ($couponAvailableAmount >= $membersAmount[$i])
					{
						$memberCouponDiscountAmount = $membersAmount[$i];
					}
					else
					{
						$memberCouponDiscountAmount = $couponAvailableAmount;
					}

					$membersAmount[$i] = $membersAmount[$i] - $memberCouponDiscountAmount;
					$membersDiscountAmount[$i] += $memberCouponDiscountAmount;

					$couponAvailableAmount -= $memberCouponDiscountAmount;

					if ($couponAvailableAmount <= 0)
					{
						break;
					}
				}
			}
		}

		$discountAmount += $couponDiscountAmount;

		// Deposit amount
		if ($config->activate_deposit_feature && $event->deposit_amount > 0)
		{
			if ($event->deposit_type == 2)
			{
				$depositAmount = $numberRegistrants * $event->deposit_amount;
			}
			else
			{
				$depositAmount = $event->deposit_amount * $amount / 100;
			}
		}
		else
		{
			$depositAmount = 0;
		}

		if ($paymentType == 1 && $depositAmount > 0 && $hasPaymentProcessingFee)
		{
			$fees['payment_processing_fee'] = round($paymentFeeAmount + $depositAmount * $paymentFeePercent / 100, 2);
			$amount += $fees['payment_processing_fee'];
			$depositAmount += $fees['payment_processing_fee'];
		}

		$fees['total_amount']            = round($totalAmount, 2);
		$fees['discount_amount']         = round($discountAmount, 2);
		$fees['late_fee']                = round($lateFee, 2);
		$fees['tax_amount']              = round($taxAmount, 2);
		$fees['amount']                  = round($amount, 2);
		$fees['deposit_amount']          = round($depositAmount, 2);
		$fees['members_form']            = $membersForm;
		$fees['members_total_amount']    = $membersTotalAmount;
		$fees['members_discount_amount'] = $membersDiscountAmount;
		$fees['members_tax_amount']      = $membersTaxAmount;
		$fees['members_amount']          = $membersAmount;
		$fees['members_late_fee']        = $membersLateFee;
		$fees['coupon_discount_amount']  = $couponDiscountAmount;

		return $fees;
	}

	/**
	 * Calculate registration fee for cart registration
	 *
	 * @param EventbookingHelperCart $cart
	 * @param RADForm                $form
	 * @param array                  $data
	 * @param RADConfig              $config
	 * @param string                 $paymentMethod
	 *
	 * @return array
	 */
	public static function calculateCartRegistrationFee($cart, $form, $data, $config, $paymentMethod = null)
	{
		$user                 = JFactory::getUser();
		$db                   = JFactory::getDbo();
		$query                = $db->getQuery(true);
		$fees                 = array();
		$recordsData          = array();
		$totalAmount          = 0;
		$discountAmount       = 0;
		$lateFee              = 0;
		$taxAmount            = 0;
		$amount               = 0;
		$couponDiscountAmount = 0;
		$depositAmount        = 0;
		$paymentProcessingFee = 0;
		$feeAmount            = $form->calculateFee();
		$items                = $cart->getItems();
		$quantities           = $cart->getQuantities();
		$paymentType          = isset($data['payment_type']) ? $data['payment_type'] : 1;
		$couponCode           = isset($data['coupon_code']) ? $data['coupon_code'] : '';
		$collectRecordsData   = isset($data['collect_records_data']) ? $data['collect_records_data'] : false;
		$paymentFeeAmount     = 0;
		$paymentFeePercent    = 0;

		if ($paymentMethod)
		{
			$method            = os_payments::loadPaymentMethod($paymentMethod);
			$params            = new Registry($method->params);
			$paymentFeeAmount  = (float) $params->get('payment_fee_amount');
			$paymentFeePercent = (float) $params->get('payment_fee_percent');
		}

		$couponDiscountedEventIds = array();
		$couponAvailableAmount    = 0;

		if ($couponCode)
		{
			$nullDateQuoted = $db->quote($db->getNullDate());

			$query->clear()
				->select('*')
				->from('#__eb_coupons')
				->where('published = 1')
				->where('`access` IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')')
				->where('code = ' . $db->quote($couponCode))
				->where('(valid_from = ' . $nullDateQuoted . ' OR valid_from <= NOW())')
				->where('(valid_to = ' . $nullDateQuoted . ' OR valid_to >= NOW())')
				->where('user_id IN (0, ' . $user->id . ')')
				->where('(times = 0 OR times > used)')
				->where('discount > used_amount')
				->where('(event_id = -1 OR id IN (SELECT coupon_id FROM #__eb_coupon_events WHERE event_id IN (' . implode(',', $items) . ')))')
				->order('id DESC');
			$db->setQuery($query);
			$coupon = $db->loadObject();

			if ($coupon)
			{
				$fees['coupon_valid'] = 1;

				if ($coupon->event_id != -1)
				{
					// Get list of events which will receive discount
					$query->clear()
						->select('event_id')
						->from('#__eb_coupon_events')
						->where('coupon_id = ' . $coupon->id);
					$db->setQuery($query);
					$couponDiscountedEventIds = $db->loadColumn();
				}

				if ($coupon->coupon_type == 2)
				{
					$couponAvailableAmount = $coupon->discount - $coupon->used_amount;
				}
			}
			else
			{
				$fees['coupon_valid'] = 0;
			}
		}
		else
		{
			$fees['coupon_valid'] = 1;
		}

		if ($config->collect_member_information_in_cart)
		{
			$membersForm           = array();
			$membersTotalAmount    = array();
			$membersDiscountAmount = array();
			$membersLateFee        = array();
			$membersTaxAmount      = array();
			$membersAmount         = array();
		}

		// Calculate bundle discount if setup
		$fees['bundle_discount_amount'] = 0;
		$fees['bundle_discount_ids']    = array();

		$nullDate    = $db->quote($db->getNullDate());
		$currentDate = $db->quote(JHtml::_('date', 'Now', 'Y-m-d'));
		$query->clear()
			->select('id, event_ids, discount_amount')
			->from('#__eb_discounts')
			->where('(from_date = ' . $nullDate . ' OR DATE(from_date) <=' . $currentDate . ')')
			->where('(to_date = ' . $nullDate . ' OR DATE(to_date) >= ' . $currentDate . ')')
			->where('(times = 0 OR times > used)')
			->where('id IN (SELECT discount_id FROM #__eb_discount_events WHERE event_id IN (' . implode(',', $items) . '))');
		$db->setQuery($query);
		$discountRules = $db->loadObjectList();

		if (!empty($discountRules))
		{
			$registeredEventIds = $items;

			if ($user->id)
			{
				$query->clear()
					->select('DISTINCT event_id')
					->from('#__eb_registrants')
					->where('user_id = ' . $user->id)
					->where('(published = 1 OR (payment_method LIKE "os_offline%" AND published IN (0, 1)))');
				$registeredEventIds = array_merge($registeredEventIds, $db->loadColumn());
			}

			foreach ($discountRules as $rule)
			{
				$eventIds = explode(',', $rule->event_ids);
				if (!array_diff($eventIds, $registeredEventIds))
				{
					$fees['bundle_discount_amount'] += $rule->discount_amount;
					$fees['bundle_discount_ids'][] = $rule->id;
				}
			}
		}

		$count                 = 0;
		$paymentFeeAmountAdded = false;

		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$eventId               = (int) $items[$i];
			$quantity              = (int) $quantities[$i];
			$recordsData[$eventId] = array();
			$event                 = EventbookingHelperDatabase::getEvent($eventId);
			$rate                  = static::getRegistrationRate($eventId, $quantity);

			if ($i == 0)
			{
				$registrantTotalAmount = $rate * $quantity + $feeAmount;
			}
			else
			{
				$registrantTotalAmount = $rate * $quantity;
			}

			if ($config->get('setup_price'))
			{
				$registrantTotalAmount = $registrantTotalAmount / (1 + $event->tax_rate / 100);
			}

			// Members data
			if ($config->collect_member_information_in_cart)
			{
				$memberFormFields = EventbookingHelperRegistration::getFormFields($eventId, 2);

				for ($j = 0; $j < $quantity; $j++)
				{
					$count++;
					$memberForm = new RADForm($memberFormFields);
					$memberForm->setFieldSuffix($count);
					$memberForm->bind($data);
					$memberExtraFee = $memberForm->calculateFee();
					$registrantTotalAmount += $memberExtraFee;
					$membersTotalAmount[$eventId][$j] = $rate + $memberExtraFee;

					if ($config->get('setup_price'))
					{
						$membersTotalAmount[$eventId][$j] = $membersTotalAmount[$eventId][$j] / (1 + $event->tax_rate / 100);
					}

					$membersDiscountAmount[$eventId][$j] = 0;
					$membersLateFee[$eventId][$j]        = 0;
					$membersForm[$eventId][$j]           = $memberForm;
				}
			}

			if ($i == 0)
			{
				$registrantDiscount = $fees['bundle_discount_amount'];
			}
			else
			{
				$registrantDiscount = 0;
			}

			// Member discount
			if ($user->id)
			{
				$discountRate = static::calculateMemberDiscount($event->discount_amounts, $event->discount_groups);

				if ($discountRate > 0)
				{
					if ($event->discount_type == 1)
					{
						$registrantDiscount = $registrantTotalAmount * $discountRate / 100;

						if ($config->collect_member_information_in_cart)
						{
							for ($j = 0; $j < $quantity; $j++)
							{
								$membersDiscountAmount[$eventId][$j] += $membersTotalAmount[$eventId][$j] * $discountRate / 100;
							}
						}
					}
					else
					{
						$registrantDiscount = $quantity * $discountRate;

						if ($config->collect_member_information_in_cart)
						{
							for ($j = 0; $j < $quantity; $j++)
							{
								$membersDiscountAmount[$eventId][$j] += $discountRate;
							}
						}
					}
				}
			}

			if (($event->early_bird_discount_date != $nullDate) && $event->date_diff >= 0 && $event->early_bird_discount_amount > 0)
			{
				if ($event->early_bird_discount_type == 1)
				{
					$registrantDiscount += $registrantTotalAmount * $event->early_bird_discount_amount / 100;

					if ($config->collect_member_information_in_cart)
					{
						for ($j = 0; $j < $quantity; $j++)
						{
							$membersDiscountAmount[$eventId][$j] += $membersTotalAmount[$eventId][$j] * $event->early_bird_discount_amount / 100;
						}
					}
				}
				else
				{
					$registrantDiscount += $quantity * $event->early_bird_discount_amount;

					if ($config->collect_member_information_in_cart)
					{
						for ($j = 0; $j < $quantity; $j++)
						{
							$membersDiscountAmount[$eventId][$j] += $event->early_bird_discount_amount;
						}
					}
				}
			}

			// Coupon discount
			if (!empty($coupon) && ($coupon->event_id == -1 || in_array($eventId, $couponDiscountedEventIds)))
			{
				if ($coupon->coupon_type == 0)
				{
					$registrantDiscount = $registrantDiscount + $registrantTotalAmount * $coupon->discount / 100;

					if ($config->collect_member_information_in_cart)
					{
						for ($j = 0; $j < $quantity; $j++)
						{
							$membersDiscountAmount[$eventId][$j] += $membersTotalAmount[$eventId][$j] * $coupon->discount / 100;
						}
					}
				}
				elseif ($coupon->coupon_type == 1)
				{
					$registrantDiscount = $registrantDiscount + $coupon->discount;

					if ($config->collect_member_information_in_cart)
					{
						$membersDiscountAmount[$eventId][0] += $coupon->discount;
					}
				}

				if ($collectRecordsData)
				{
					$recordsData[$eventId]['coupon_id'] = $coupon->id;
				}
			}

			if ($registrantDiscount > $registrantTotalAmount)
			{
				$registrantDiscount = $registrantTotalAmount;
			}

			// Late Fee
			$registrantLateFee = 0;
			if (($event->late_fee_date != $nullDate) && $event->late_fee_date_diff >= 0 && $event->late_fee_amount > 0)
			{
				if ($event->late_fee_type == 1)
				{
					$registrantLateFee = $registrantTotalAmount * $event->late_fee_amount / 100;

					if ($config->collect_member_information_in_cart)
					{
						for ($j = 0; $j < $quantity; $j++)
						{
							$membersLateFee[$eventId][$j] = $membersTotalAmount[$eventId][$j] * $event->late_fee_amount / 100;
						}
					}
				}
				else
				{

					$registrantLateFee = $quantity * $event->late_fee_amount;

					if ($config->collect_member_information_in_cart)
					{
						for ($j = 0; $j < $quantity; $j++)
						{
							$membersLateFee[$eventId][$j] = $event->late_fee_amount;
						}
					}
				}
			}

			if ($event->tax_rate > 0)
			{
				$registrantTaxAmount = $event->tax_rate * ($registrantTotalAmount - $registrantDiscount + $registrantLateFee) / 100;
				$registrantAmount    = $registrantTotalAmount - $registrantDiscount + $registrantTaxAmount + $registrantLateFee;

				if ($config->collect_member_information_in_cart)
				{
					for ($j = 0; $j < $quantity; $j++)
					{
						$membersTaxAmount[$eventId][$j] = round($event->tax_rate * ($membersTotalAmount[$eventId][$j] - $membersDiscountAmount[$eventId][$j] + $membersLateFee[$eventId][$j]) / 100, 2);
						$membersAmount[$eventId][$j]    = $membersTotalAmount[$eventId][$j] - $membersDiscountAmount[$eventId][$j] + $membersLateFee[$eventId][$j] + $membersTaxAmount[$eventId][$j];
					}
				}
			}
			else
			{
				$registrantTaxAmount = 0;
				$registrantAmount    = $registrantTotalAmount - $registrantDiscount + $registrantTaxAmount + $registrantLateFee;

				if ($config->collect_member_information_in_cart)
				{
					for ($j = 0; $j < $quantity; $j++)
					{
						$membersTaxAmount[$eventId][$j] = 0;
						$membersAmount[$eventId][$j]    = $membersTotalAmount[$eventId][$j] - $membersDiscountAmount[$eventId][$j] + $membersLateFee[$eventId][$j] + $membersTaxAmount[$eventId][$j];
					}
				}
			}

			if (($paymentFeeAmount > 0 || $paymentFeePercent > 0) && $registrantAmount > 0)
			{
				if ($paymentFeeAmountAdded)
				{
					$registrantPaymentProcessingFee = $registrantAmount * $paymentFeePercent / 100;
				}
				else
				{
					$paymentFeeAmountAdded          = true;
					$registrantPaymentProcessingFee = $paymentFeeAmount + $registrantAmount * $paymentFeePercent / 100;
				}

				$registrantAmount += $registrantPaymentProcessingFee;
			}
			else
			{

				$registrantPaymentProcessingFee = 0;
			}

			if (!empty($coupon) && $coupon->coupon_type == 2 && ($coupon->event_id == -1 || in_array($eventId, $couponDiscountedEventIds)))
			{
				if ($couponAvailableAmount > $registrantAmount)
				{
					$registrantCouponDiscountAmount = $registrantAmount;
				}
				else
				{
					$registrantCouponDiscountAmount = $couponAvailableAmount;
				}

				$registrantAmount -= $registrantCouponDiscountAmount;
				$registrantDiscount += $registrantCouponDiscountAmount;
				$couponAvailableAmount -= $registrantCouponDiscountAmount;

				$couponDiscountAmount += $registrantCouponDiscountAmount;

				if ($config->collect_member_information_in_cart)
				{
					$totalMemberDiscountAmount = $registrantCouponDiscountAmount;

					for ($j = 0; $j < $quantity; $j++)
					{
						if ($totalMemberDiscountAmount > $membersAmount[$eventId][$j])
						{
							$memberCouponDiscountAmount = $membersAmount[$eventId][$j];
						}
						else
						{
							$memberCouponDiscountAmount = $totalMemberDiscountAmount;
						}

						$totalMemberDiscountAmount -= $memberCouponDiscountAmount;

						$membersAmount[$eventId][$j] -= $memberCouponDiscountAmount;

						$membersDiscountAmount[$eventId][$j] += $memberCouponDiscountAmount;

						if ($totalMemberDiscountAmount <= 0)
						{
							break;
						}
					}
				}
			}

			if ($config->activate_deposit_feature && $event->deposit_amount > 0 && $paymentType == 1)
			{
				if ($event->deposit_type == 2)
				{
					$registrantDepositAmount = $event->deposit_amount * $quantity;
				}
				else
				{
					$registrantDepositAmount = round($registrantAmount * $event->deposit_amount / 100, 2);
				}
			}
			else
			{
				$registrantDepositAmount = 0;
			}

			$totalAmount += $registrantTotalAmount;
			$discountAmount += $registrantDiscount;
			$lateFee += $registrantLateFee;
			$depositAmount += $registrantDepositAmount;
			$taxAmount += $registrantTaxAmount;
			$amount += $registrantAmount;
			$paymentProcessingFee += $registrantPaymentProcessingFee;

			if ($collectRecordsData)
			{
				$recordsData[$eventId]['total_amount']           = round($registrantTotalAmount, 2);
				$recordsData[$eventId]['discount_amount']        = round($registrantDiscount, 2);
				$recordsData[$eventId]['late_fee']               = round($registrantLateFee, 2);
				$recordsData[$eventId]['tax_amount']             = round($registrantTaxAmount, 2);
				$recordsData[$eventId]['payment_processing_fee'] = round($registrantPaymentProcessingFee, 2);
				$recordsData[$eventId]['amount']                 = round($registrantAmount, 2);
				$recordsData[$eventId]['deposit_amount']         = round($registrantDepositAmount, 2);
			}
		}

		$fees['total_amount']           = round($totalAmount, 2);
		$fees['discount_amount']        = round($discountAmount, 2);
		$fees['late_fee']               = round($lateFee, 2);
		$fees['tax_amount']             = round($taxAmount, 2);
		$fees['amount']                 = round($amount, 2);
		$fees['deposit_amount']         = round($depositAmount, 2);
		$fees['payment_processing_fee'] = round($paymentProcessingFee, 2);
		$fees['coupon_discount_amount'] = round($couponDiscountAmount, 2);

		if ($collectRecordsData)
		{
			$fees['records_data'] = $recordsData;
		}

		if ($config->collect_member_information_in_cart)
		{
			$fees['members_form']            = $membersForm;
			$fees['members_total_amount']    = $membersTotalAmount;
			$fees['members_discount_amount'] = $membersDiscountAmount;
			$fees['members_tax_amount']      = $membersTaxAmount;
			$fees['members_late_fee']        = $membersLateFee;
			$fees['members_amount']          = $membersAmount;
		}

		return $fees;
	}

	/**
	 * Check to see whether we will show billing form on group registration
	 *
	 * @param int $eventId
	 *
	 * @return boolean
	 */
	public static function showBillingStep($eventId)
	{
		$config = EventbookingHelper::getConfig();
		$event  = EventbookingHelperDatabase::getEvent($eventId);

		if ($event->collect_member_information === '')
		{
			$collectMemberInformation = $config->collect_member_information;
		}
		else
		{
			$collectMemberInformation = $event->collect_member_information;
		}

		if (!$collectMemberInformation || $config->show_billing_step_for_free_events)
		{
			return true;
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('individual_price, fixed_group_price')
			->from('#__eb_events')
			->where('id=' . $eventId);
		$db->setQuery($query);
		$event = $db->loadObject();

		if ($event->individual_price == 0 && $event->fixed_group_price == 0)
		{
			$query->clear()
				->select('COUNT(*)')
				->from('#__eb_fields')
				->where('fee_field = 1')
				->where('published = 1');

			if ($config->custom_field_by_category)
			{
				$categoryQuery = $db->getQuery(true);
				$categoryQuery->select('category_id')
					->from('#__eb_event_categories')
					->where('event_id = ' . $eventId)
					->where('main_category = 1');
				$db->setQuery($categoryQuery);
				$categoryId = (int) $db->loadResult();
				$query->where('(category_id = -1 OR id IN (SELECT field_id FROM #__eb_field_categories WHERE category_id=' . $categoryId . '))');
			}
			else
			{
				$negEventId = -1 * $eventId;
				$subQuery   = $db->getQuery(true);
				$subQuery->select('field_id')
					->from('#__eb_field_events')
					->where("(event_id = $eventId OR (event_id < 0 AND event_id != $negEventId))");

				$query->where('(event_id = -1 OR id IN (' . (string) $subQuery . '))');
			}

			$db->setQuery($query);

			$numberFeeFields = (int) $db->loadResult();

			if ($numberFeeFields == 0)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Get the form data used to bind to the RADForm object
	 *
	 * @param array $rowFields
	 * @param int   $eventId
	 * @param int   $userId
	 *
	 * @return array
	 */
	public static function getFormData($rowFields, $eventId, $userId)
	{
		$data = array();

		if ($userId)
		{
			$mappings = array();

			foreach ($rowFields as $rowField)
			{
				if ($rowField->field_mapping)
				{
					$mappings[$rowField->name] = $rowField->field_mapping;
				}
			}

			JPluginHelper::importPlugin('eventbooking');
			$results = JFactory::getApplication()->triggerEvent('onGetProfileData', array($userId, $mappings));

			if (count($results))
			{
				foreach ($results as $res)
				{
					if (is_array($res) && count($res))
					{
						$data = $res;
						break;
					}
				}
			}

			if (!count($data))
			{
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('*')
					->from('#__eb_registrants')
					->where('user_id=' . $userId . ' AND event_id=' . $eventId . ' AND first_name != "" AND group_id=0')
					->order('id DESC');
				$db->setQuery($query, 0, 1);
				$rowRegistrant = $db->loadObject();

				if (!$rowRegistrant)
				{
					//Try to get registration record from other events if available
					$query->clear('where')->where('user_id=' . $userId . ' AND first_name != "" AND group_id=0');
					$db->setQuery($query, 0, 1);
					$rowRegistrant = $db->loadObject();
				}

				if ($rowRegistrant)
				{
					$data = self::getRegistrantData($rowRegistrant, $rowFields);
				}
			}
		}

		return $data;
	}

	/**
	 * Get data of registrant using to auto populate registration form
	 *
	 * @param EventbookingTableRegistrant $rowRegistrant
	 * @param array                       $rowFields
	 *
	 * @return array
	 */
	public static function getRegistrantData($rowRegistrant, $rowFields)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$data  = array();
		$query->select('a.name, b.field_value')
			->from('#__eb_fields AS a')
			->innerJoin('#__eb_field_values AS b ON a.id = b.field_id')
			->where('b.registrant_id=' . $rowRegistrant->id);
		$db->setQuery($query);
		$fieldValues = $db->loadObjectList('name');

		for ($i = 0, $n = count($rowFields); $i < $n; $i++)
		{
			$rowField = $rowFields[$i];

			if ($rowField->is_core)
			{
				$data[$rowField->name] = $rowRegistrant->{$rowField->name};
			}
			else
			{
				if (isset($fieldValues[$rowField->name]))
				{
					$data[$rowField->name] = $fieldValues[$rowField->name]->field_value;
				}
			}
		}

		return $data;
	}

	/**
	 * Create a user account
	 *
	 * @param array $data
	 *
	 * @return int Id of created user
	 */
	public static function saveRegistration($data)
	{
		$config = EventbookingHelper::getConfig();

		if ($config->use_cb_api)
		{
			if (is_callable('EventbookingHelperOverrideRegistration::userRegistrationCB'))
			{
				return EventbookingHelperOverrideRegistration::userRegistrationCB($data['first_name'], $data['last_name'], $data['email'], $data['username'], $data['password1']);
			}
			else
			{
				return self::userRegistrationCB($data['first_name'], $data['last_name'], $data['email'], $data['username'], $data['password1']);
			}
		}
		else
		{
			// Add path to load xml form definition
			if (JLanguageMultilang::isEnabled())
			{
				JForm::addFormPath(JPATH_ROOT . '/components/com_users/models/forms');
				JForm::addFieldPath(JPATH_ROOT . '/components/com_users/models/fields');
			}

			//Need to load com_users language file
			$lang = JFactory::getLanguage();
			$tag  = $lang->getTag();

			if (!$tag)
			{
				$tag = 'en-GB';
			}

			$lang->load('com_users', JPATH_ROOT, $tag);
			$data['name']     = rtrim($data['first_name'] . ' ' . $data['last_name']);
			$data['password'] = $data['password2'] = $data['password1'];
			$data['email1']   = $data['email2'] = $data['email'];

			require_once JPATH_ROOT . '/components/com_users/models/registration.php';

			$model = new UsersModelRegistration();

			$model->register($data);

			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id')
				->from('#__users')
				->where('username = ' . $db->quote($data['username']));
			$db->setQuery($query);

			return (int) $db->loadResult();
		}
	}

	/**
	 * Use CB API for saving user account
	 *
	 * @param       $firstName
	 * @param       $lastName
	 * @param       $email
	 * @param       $username
	 * @param       $password
	 *
	 * @return int
	 */
	public static function userRegistrationCB($firstName, $lastName, $email, $username, $password)
	{
		if ((!file_exists(JPATH_SITE . '/libraries/CBLib/CBLib/Core/CBLib.php')) || (!file_exists(JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php')))
		{
			echo 'CB not installed';

			return;
		}

		include_once(JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php');

		cbimport('cb.html');

		global $_CB_framework, $_PLUGINS, $ueConfig;

		$approval     = $ueConfig['reg_admin_approval'];
		$confirmation = ($ueConfig['reg_confirmation']);
		$user         = new \CB\Database\Table\UserTable();

		$user->set('username', $username);
		$user->set('email', $email);
		$user->set('name', trim($firstName . ' ' . $lastName));
		$user->set('gids', array((int) $_CB_framework->getCfg('new_usertype')));
		$user->set('sendEmail', 0);
		$user->set('registerDate', $_CB_framework->getUTCDate());
		$user->set('password', $user->hashAndSaltPassword($password));
		$user->set('registeripaddr', cbGetIPlist());

		if ($approval == 0)
		{
			$user->set('approved', 1);
		}
		else
		{
			$user->set('approved', 0);
		}

		if ($confirmation == 0)
		{
			$user->set('confirmed', 1);
		}
		else
		{
			$user->set('confirmed', 0);
		}

		if (($user->get('confirmed') == 1) && ($user->get('approved') == 1))
		{
			$user->set('block', 0);
		}
		else
		{
			$user->set('block', 1);
		}

		$_PLUGINS->trigger('onBeforeUserRegistration', array(&$user, &$user));

		if ($user->store())
		{
			if ($user->get('confirmed') == 0)
			{
				$user->store();
			}

			$messagesToUser = activateUser($user, 1, 'UserRegistration');

			$_PLUGINS->trigger('onAfterUserRegistration', array(&$user, &$user, true));

			return $user->get('id');
		}

		return 0;
	}

	/**
	 * We only need to generate invoice for paid events only
	 *
	 * @param $row
	 *
	 * @return bool
	 */
	public static function needInvoice($row)
	{
		// Don't generate invoice for waiting list records
		if ($row->published === 3 || $row->cart_id > 0 || $row->group_id > 0)
		{
			return false;
		}

		$config = EventbookingHelper::getConfig();

		if ($config->always_generate_invoice)
		{
			return true;
		}

		if ($row->amount > 0)
		{
			return true;
		}

		if ($config->multiple_booking)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('SUM(amount)')
				->from('#__eb_registrants')
				->where('id=' . $row->id . ' OR cart_id=' . $row->id);
			$db->setQuery($query);
			$totalAmount = $db->loadResult();

			if ($totalAmount > 0)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the invoice number for this registration record
	 *
	 * return int
	 */
	public static function getInvoiceNumber()
	{
		$config = EventbookingHelper::getConfig();
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		$query->select('MAX(invoice_number)')
			->from('#__eb_registrants');
		$db->setQuery($query);
		$invoiceNumber = (int) $db->loadResult();
		$invoiceNumber++;

		return max($invoiceNumber, (int) $config->invoice_start_number);
	}

	/**
	 * Update Group Members record to have same information with billing record
	 *
	 * @param int $groupId
	 */
	public static function updateGroupRegistrationRecord($groupId)
	{
		$db     = JFactory::getDbo();
		$config = EventbookingHelper::getConfig();

		$row = JTable::getInstance('EventBooking', 'Registrant');

		if (!$row->load($groupId))
		{
			return;
		}

		$event = EventbookingHelperDatabase::getEvent($row->event_id);

		if ($event->collect_member_information === '')
		{
			$collectMemberInformation = $config->collect_member_information;
		}
		else
		{
			$collectMemberInformation = $event->collect_member_information;
		}

		if ($collectMemberInformation)
		{
			$query = $db->getQuery(true);
			$query->update('#__eb_registrants')
				->set('published = ' . $row->published)
				->set('payment_status = ' . $row->payment_status)
				->set('transaction_id = ' . $db->quote($row->transaction_id))
				->set('payment_method = ' . $db->quote($row->payment_method))
				->where('group_id = ' . $row->id);

			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Method to build common tags use for email messages
	 *
	 * @param EventbookingTableRegistrant $row
	 * @param RADConfig                   $config
	 *
	 * @return array
	 */
	public static function buildDepositPaymentTags($row, $config)
	{
		$event  = EventbookingHelperDatabase::getEvent($row->event_id);
		$method = os_payments::loadPaymentMethod($row->deposit_payment_method);

		$rowFields = static::getDepositPaymentFormFields();
		$replaces  = array();

		foreach ($rowFields as $rowField)
		{
			$replaces[$rowField->name] = $row->{$rowField->name};
		}

		if ($method)
		{
			$replaces['payment_method'] = JText::_($method->title);
		}
		else
		{
			$replaces['payment_method'] = '';
		}

		$replaces['AMOUNT']          = EventbookingHelper::formatCurrency($row->amount - $row->deposit_amount, $config, $event->currency_symbol);
		$replaces['PAYMENT_AMOUNT']  = EventbookingHelper::formatCurrency($row->amount - $row->deposit_amount + $row->deposit_payment_processing_fee, $config, $event->currency_symbol);
		$replaces['REGISTRATION_ID'] = $row->id;
		$replaces['TRANSACTION_ID']  = $row->deposit_payment_transaction_id;

		$replaces = array_merge($replaces, static::buildEventTags($event, $config));

		return $replaces;
	}

	/**
	 * Build tags related to event
	 *
	 * @param EventbookingTableEvent $event
	 * @param RADConfig              $config
	 *
	 * @return array
	 */
	public static function buildEventTags($event, $config)
	{
		$replaces                      = array();
		$replaces['event_title']       = $event->title;
		$replaces['event_date']        = JHtml::_('date', $event->event_date, $config->event_date_format, null);
		$replaces['event_end_date']    = JHtml::_('date', $event->event_end_date, $config->event_date_format, null);
		$replaces['short_description'] = $event->short_description;
		$replaces['description']       = $event->description;

		if ($event->location_id > 0)
		{
			$rowLocation         = EventbookingHelperDatabase::getLocation($event->location_id);
			$locationInformation = array();

			if ($rowLocation->address)
			{
				$locationInformation[] = $rowLocation->address;
			}

			$replaces['location']      = $rowLocation->name . ' (' . implode(', ', $locationInformation) . ')';
			$replaces['location_name'] = $rowLocation->name;
		}
		else
		{
			$replaces['location']      = '';
			$replaces['location_name'] = '';
		}

		if ($config->event_custom_field && file_exists(JPATH_ROOT . '/components/com_eventbooking/fields.xml'))
		{
			EventbookingHelperData::prepareCustomFieldsData(array($event));

			foreach ($event->paramData as $customFieldName => $param)
			{
				$replaces[strtoupper($customFieldName)] = $param['value'];
			}
		}

		return $replaces;
	}

	/**
	 * Build tags array to use to replace the tags use in email & messages
	 *
	 * @param EventbookingTableRegistrant $row
	 * @param RADForm                     $form
	 * @param EventbookingTableEvent      $event
	 * @param RADConfig                   $config
	 * @param bool                        $loadCss
	 *
	 * @return array
	 */
	public static function buildTags($row, $form, $event, $config, $loadCss = true)
	{
		$db       = JFactory::getDbo();
		$query    = $db->getQuery(true);
		$nullDate = $db->getNullDate();
		$siteUrl  = EventbookingHelper::getSiteUrl();
		$replaces = array();

		$Itemid = JFactory::getApplication()->input->getInt('Itemid', 0);

		if (!$Itemid)
		{
			$Itemid = EventbookingHelper::getItemid();
		}

		$fieldSuffix = EventbookingHelper::getFieldSuffix($row->language);

		// Event information
		if ($config->multiple_booking)
		{
			$query->select('event_id')
				->from('#__eb_registrants')
				->where("(id = $row->id OR cart_id = $row->id)")
				->order('id');
			$db->setQuery($query);
			$eventIds = $db->loadColumn();


			$query->clear()
				->select($db->quoteName('title' . $fieldSuffix, 'title'))
				->from('#__eb_events')
				->where('id IN (' . implode(',', $eventIds) . ')')
				->order('FIND_IN_SET(id, "' . implode(',', $eventIds) . '")');

			$db->setQuery($query);
			$eventTitles = $db->loadColumn();
			$eventTitle  = implode(', ', $eventTitles);
		}
		else
		{
			$eventTitle = $event->title;
		}

		$timeFormat              = $config->event_time_format ? $config->event_time_format : 'g:i a';
		$replaces['date']        = date($config->date_format);
		$replaces['event_title'] = $eventTitle;

		if ($event->event_date == EB_TBC_DATE)
		{
			$replaces['event_date']      = JText::_('EB_TBC');
			$replaces['event_date_date'] = JText::_('EB_TBC');
			$replaces['event_date_time'] = JText::_('EB_TBC');
		}
		else
		{
			$replaces['event_date']      = JHtml::_('date', $event->event_date, $config->event_date_format, null);
			$replaces['event_date_date'] = JHtml::_('date', $event->event_date, $config->date_format, null);
			$replaces['event_date_time'] = JHtml::_('date', $event->event_date, $timeFormat, null);
		}

		if ($event->event_end_date != $nullDate)
		{
			$replaces['event_end_date']      = JHtml::_('date', $event->event_end_date, $config->event_date_format, null);
			$replaces['event_end_date_date'] = JHtml::_('date', $event->event_end_date, $config->date_format, null);
			$replaces['event_end_date_time'] = JHtml::_('date', $event->event_end_date, $timeFormat, null);
		}
		else
		{
			$replaces['event_end_date']      = '';
			$replaces['event_end_date_date'] = '';
			$replaces['event_end_date_time'] = '';
		}

		$replaces['short_description'] = $event->short_description;
		$replaces['description']       = $event->description;
		$replaces['alias']             = $event->alias;
		$replaces['price_text']        = $event->price_text;
		$replaces['event_link']        = $siteUrl . 'index.php?option=com_eventbooking&view=event&id=' . $event->id . '&Itemid=' . $Itemid;

		// Add support for group members name tags
		if ($row->is_group_billing)
		{
			$groupMembersNames = array();

			$query->clear()
				->select('first_name, last_name')
				->from('#__eb_registrants')
				->where('group_id = ' . $row->id)
				->order('id');
			$db->setQuery($query);
			$rowMembers = $db->loadObjectList();

			foreach ($rowMembers as $rowMember)
			{
				$groupMembersNames[] = trim($rowMember->first_name . ' ' . $rowMember->last_name);
			}
		}
		else
		{
			$groupMembersNames = array(trim($row->first_name . ' ' . $row->last_name));
		}

		$replaces['group_members_names'] = implode(', ', $groupMembersNames);

		// Event custom fields
		if ($config->event_custom_field && file_exists(JPATH_ROOT . '/components/com_eventbooking/fields.xml'))
		{
			EventbookingHelperData::prepareCustomFieldsData(array($event));

			foreach ($event->paramData as $customFieldName => $param)
			{
				$replaces[$customFieldName] = $param['value'];
			}
		}

		// Form fields
		$fields = $form->getFields();

		foreach ($fields as $field)
		{
			if ($field->hideOnDisplay)
			{
				$fieldValue = '';
			}
			else
			{
				if (is_string($field->value) && is_array(json_decode($field->value)))
				{
					$fieldValue = implode(', ', json_decode($field->value));
				}
				else
				{
					$fieldValue = $field->value;
				}
			}

			if ($fieldValue && $field->type == 'Date')
			{
				$date = JFactory::getDate($fieldValue);
				if ($date)
				{
					$dateFormat = $config->date_field_format ? $config->date_field_format : '%Y-%m-%d';
					$dateFormat = str_replace('%', '', $dateFormat);
					$fieldValue = $date->format($dateFormat);
				}
			}

			$replaces[$field->name] = $fieldValue;
		}

		if (isset($replaces['last_name']))
		{
			$replaces['name'] = $replaces['first_name'] . ' ' . $replaces['last_name'];
		}
		else
		{
			$replaces['name'] = $replaces['first_name'];
		}

		if ($row->coupon_id)
		{
			$query->clear()
				->select('a.code')
				->from('#__eb_coupons AS a')
				->innerJoin('#__eb_registrants AS b ON a.id = b.coupon_id')
				->where('b.id=' . $row->id);
			$db->setQuery($query);
			$replaces['couponCode'] = $db->loadResult();
		}
		else
		{
			$replaces['couponCode'] = '';
		}

		$replaces['user_id'] = $row->user_id;

		if ($row->user_id)
		{
			$query->clear()
				->select('username')
				->from('#__users')
				->where('id = ' . $row->user_id);
			$db->setQuery($query);
			$replaces['username'] = $db->loadResult();
		}
		else
		{
			$replaces['username'] = '';
		}

		$replaces['user_id'] = $row->user_id;

		$replaces['TICKET_TYPES'] = '';

		if ($config->multiple_booking)
		{
			//Amount calculation
			$query->clear()
				->select('SUM(total_amount)')
				->from('#__eb_registrants')
				->where("(id = $row->id OR cart_id = $row->id)");
			$db->setQuery($query);
			$totalAmount = $db->loadResult();

			$query->clear('select')
				->select('SUM(tax_amount)');
			$db->setQuery($query);
			$taxAmount = $db->loadResult();

			$query->clear('select')
				->select('SUM(payment_processing_fee)');
			$db->setQuery($query);
			$paymentProcessingFee = $db->loadResult();

			$query->clear('select')
				->select('SUM(discount_amount)');
			$db->setQuery($query);
			$discountAmount = $db->loadResult();

			$query->clear('select')
				->select('SUM(late_fee)');
			$db->setQuery($query);
			$lateFee = $db->loadResult();

			$amount = $totalAmount - $discountAmount + $paymentProcessingFee + $taxAmount + $lateFee;

			if ($row->payment_status == 1)
			{
				$depositAmount = 0;
				$dueAmount     = 0;
			}
			else
			{
				$query->clear('select')
					->select('SUM(deposit_amount)');
				$db->setQuery($query);
				$depositAmount = $db->loadResult();

				$dueAmount = $amount - $depositAmount;
			}

			$replaces['total_amount']           = EventbookingHelper::formatCurrency($totalAmount, $config, $event->currency_symbol);
			$replaces['tax_amount']             = EventbookingHelper::formatCurrency($taxAmount, $config, $event->currency_symbol);
			$replaces['discount_amount']        = EventbookingHelper::formatCurrency($discountAmount, $config, $event->currency_symbol);
			$replaces['late_fee']               = EventbookingHelper::formatCurrency($lateFee, $config, $event->currency_symbol);
			$replaces['payment_processing_fee'] = EventbookingHelper::formatCurrency($paymentProcessingFee, $config, $event->currency_symbol);
			$replaces['amount']                 = EventbookingHelper::formatCurrency($amount, $config, $event->currency_symbol);
			$replaces['deposit_amount']         = EventbookingHelper::formatCurrency($depositAmount, $config, $event->currency_symbol);
			$replaces['due_amount']             = EventbookingHelper::formatCurrency($dueAmount, $config, $event->currency_symbol);

			$replaces['amt_total_amount']           = $totalAmount;
			$replaces['amt_tax_amount']             = $taxAmount;
			$replaces['amt_discount_amount']        = $discountAmount;
			$replaces['amt_late_fee']               = $lateFee;
			$replaces['amt_amount']                 = $amount;
			$replaces['amt_payment_processing_fee'] = $paymentProcessingFee;
			$replaces['amt_deposit_amount']         = $depositAmount;
			$replaces['amt_due_amount']             = $dueAmount;
		}
		else
		{
			$replaces['total_amount']           = EventbookingHelper::formatCurrency($row->total_amount, $config, $event->currency_symbol);
			$replaces['tax_amount']             = EventbookingHelper::formatCurrency($row->tax_amount, $config, $event->currency_symbol);
			$replaces['discount_amount']        = EventbookingHelper::formatCurrency($row->discount_amount, $config, $event->currency_symbol);
			$replaces['late_fee']               = EventbookingHelper::formatCurrency($row->late_fee, $config, $event->currency_symbol);
			$replaces['payment_processing_fee'] = EventbookingHelper::formatCurrency($row->payment_processing_fee, $config, $event->currency_symbol);
			$replaces['amount']                 = EventbookingHelper::formatCurrency($row->amount, $config, $event->currency_symbol);

			if ($row->payment_status)
			{
				$depositAmount = 0;
				$dueAmount     = 0;
			}
			else
			{
				$depositAmount = $row->deposit_amount;
				$dueAmount     = $row->amount - $row->deposit_amount;
			}

			$replaces['deposit_amount'] = EventbookingHelper::formatCurrency($depositAmount, $config, $event->currency_symbol);
			$replaces['due_amount']     = EventbookingHelper::formatCurrency($dueAmount, $config, $event->currency_symbol);

			// Ticket Types
			if ($event->has_multiple_ticket_types)
			{
				$query->clear()
					->select('id, title')
					->from('#__eb_ticket_types')
					->where('event_id = ' . $event->id);
				$db->setQuery($query);
				$ticketTypes = $db->loadObjectList('id');

				$query->clear()
					->select('ticket_type_id, quantity')
					->from('#__eb_registrant_tickets')
					->where('registrant_id = ' . $row->id);
				$db->setQuery($query);
				$registrantTickets = $db->loadObjectList();

				$ticketsOutput = array();

				foreach ($registrantTickets as $registrantTicket)
				{
					$ticketsOutput[] = $ticketTypes[$registrantTicket->ticket_type_id]->title . ': ' . $registrantTicket->quantity;
				}

				$replaces['TICKET_TYPES'] = implode(', ', $ticketsOutput);
			}
		}

		$replaces['individual_price'] = EventbookingHelper::formatCurrency($event->individual_price, $config, $event->currency_symbol);

		// Add support for location tag
		$query->clear()
			->select('a.*')
			->from('#__eb_locations AS a')
			->innerJoin('#__eb_events AS b ON a.id=b.location_id')
			->where('b.id =' . $row->event_id);

		if ($fieldSuffix)
		{
			EventbookingHelperDatabase::getMultilingualFields($query, ['a.name', 'a.alias', 'a.description'], $fieldSuffix);
		}

		$db->setQuery($query);
		$rowLocation = $db->loadObject();

		if ($rowLocation)
		{
			$locationInformation = array();

			if ($rowLocation->address)
			{
				$locationInformation[] = $rowLocation->address;
			}

			$replaces['location'] = $rowLocation->name . ' (' . implode(', ', $locationInformation) . ')';
		}
		else
		{
			$replaces['location'] = '';
		}

		// Registration record related tags
		$replaces['number_registrants'] = $row->number_registrants;
		$replaces['invoice_number']     = $row->invoice_number;
		$replaces['invoice_number']     = EventbookingHelper::formatInvoiceNumber($row->invoice_number, $config);
		$replaces['transaction_id']     = $row->transaction_id;
		$replaces['id']                 = $row->id;
		$replaces['date']               = JHtml::_('date', 'Now', $config->date_format);

		if ($row->payment_date != $db->getNullDate())
		{
			$replaces['payment_date'] = JHtml::_('date', $row->payment_date, $config->date_format);;
		}
		else
		{
			$replaces['payment_date'] = '';
		}

		$method = os_payments::loadPaymentMethod($row->payment_method);

		if ($method)
		{
			$replaces['payment_method'] = JText::_($method->title);
		}
		else
		{
			$replaces['payment_method'] = '';
		}

		// Registration detail tags
		$replaces['registration_detail'] = static::getEmailContent($config, $row, $loadCss, $form);

		// Cancel link
		$query->clear()
			->select('enable_cancel_registration')
			->from('#__eb_events')
			->where('id = ' . $row->event_id);
		$db->setQuery($query);
		$enableCancel = $db->loadResult();

		if ($enableCancel)
		{
			$replaces['cancel_registration_link'] = $siteUrl . 'index.php?option=com_eventbooking&task=registrant.cancel&cancel_code=' . $row->registration_code . '&Itemid=' . $Itemid;
		}
		else
		{
			$replaces['cancel_registration_link'] = '';
		}

		if ($config->activate_deposit_feature)
		{
			$replaces['deposit_payment_link'] = $siteUrl . 'index.php?option=com_eventbooking&view=payment&registrant_id=' . $row->id . '&Itemid=' . $Itemid;
		}
		else
		{
			$replaces['deposit_payment_link'] = '';
		}

		$replaces['download_certificate_link'] = $siteUrl . 'index.php?option=com_eventbooking&task=registrant.download_certificate&download_code=' . $row->registration_code . '&Itemid=' . $Itemid;
		$replaces['download_ticket_link']      = $siteUrl . 'index.php?option=com_eventbooking&task=registrant.download_ticket&download_code=' . $row->registration_code . '&Itemid=' . $Itemid;

		// Make sure if a custom field is not available, the used tag would be empty
		$query->clear()
			->select('name')
			->from('#__eb_fields')
			->where('published = 1');
		$db->setQuery($query);
		$allFields = $db->loadColumn();

		foreach ($allFields as $field)
		{
			if (!isset($replaces[$field]))
			{
				$replaces[$field] = '';
			}
		}

		// Registration status tag
		switch ($row->published)
		{
			case 0 :
				$replaces['REGISTRATION_STATUS'] = JText::_('EB_PENDING');
				break;
			case 1 :
				$replaces['REGISTRATION_STATUS'] = JText::_('EB_PAID');
				break;
			case 2 :
				$replaces['REGISTRATION_STATUS'] = JText::_('EB_CANCELLED');
				break;
			case 3:
				$replaces['REGISTRATION_STATUS'] = JText::_('EB_WAITING_LIST');
				break;
			default:
				$replaces['REGISTRATION_STATUS'] = '';
				break;
		}

		return $replaces;
	}

	/**
	 * Get email content, used for [REGISTRATION_DETAIL] tag
	 *
	 * @param RADConfig                   $config
	 * @param EventbookingTableRegistrant $row
	 * @param bool                        $loadCss
	 * @param RADForm                     $form
	 * @param bool                        $toAdmin
	 *
	 * @return string
	 */
	public static function getEmailContent($config, $row, $loadCss = true, $form = null, $toAdmin = false)
	{
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		$data   = array();
		$Itemid = JFactory::getApplication()->input->getInt('Itemid', 0);

		if ($config->multiple_booking)
		{
			if ($loadCss)
			{
				$layout = 'email_cart.php';
			}
			else
			{
				$layout = 'cart.php';
			}
		}
		else
		{
			if ($row->is_group_billing)
			{
				if ($loadCss)
				{
					$layout = 'email_group_detail.php';
				}
				else
				{
					$layout = 'group_detail.php';
				}
			}
			else
			{
				if ($loadCss)
				{
					$layout = 'email_individual_detail.php';
				}
				else
				{
					$layout = 'individual_detail.php';
				}
			}
		}

		if (!$loadCss)
		{
			// Need to pass bootstrap helper
			$data['bootstrapHelper'] = new EventbookingHelperBootstrap($config->twitter_bootstrap_version);
		}

		$fieldSuffix = EventbookingHelper::getFieldSuffix();

		if ($config->multiple_booking)
		{
			$data['row']    = $row;
			$data['config'] = $config;
			$data['Itemid'] = $Itemid;

			$query->select('a.*, b.event_date, b.event_end_date')
				->select($db->quoteName('b.title' . $fieldSuffix, 'title'))
				->from('#__eb_registrants AS a')
				->innerJoin('#__eb_events AS b ON a.event_id = b.id')
				->where("(a.id = $row->id OR a.cart_id = $row->id)")
				->order('a.id');
			$db->setQuery($query);
			$rows = $db->loadObjectList();

			$query->clear()
				->select('SUM(total_amount)')
				->from('#__eb_registrants')
				->where("(id = $row->id OR cart_id = $row->id)");
			$db->setQuery($query);
			$totalAmount = $db->loadResult();

			$query->clear('select')
				->select('SUM(tax_amount)');
			$db->setQuery($query);
			$taxAmount = $db->loadResult();

			$query->clear('select')
				->select('SUM(discount_amount)');
			$db->setQuery($query);
			$discountAmount = $db->loadResult();

			$query->clear('select')
				->select('SUM(late_fee)');
			$db->setQuery($query);
			$lateFee = $db->loadResult();

			$query->clear('select')
				->select('SUM(payment_processing_fee)');
			$db->setQuery($query);
			$paymentProcessingFee = $db->loadResult();

			$amount = $totalAmount + $paymentProcessingFee - $discountAmount + $taxAmount + $lateFee;

			$query->clear('select')
				->select('SUM(deposit_amount)');
			$db->setQuery($query);
			$depositAmount = $db->loadResult();

			//Added support for custom field feature
			$data['discountAmount']       = $discountAmount;
			$data['lateFee']              = $lateFee;
			$data['totalAmount']          = $totalAmount;
			$data['items']                = $rows;
			$data['amount']               = $amount;
			$data['taxAmount']            = $taxAmount;
			$data['paymentProcessingFee'] = $paymentProcessingFee;
			$data['depositAmount']        = $depositAmount;
			$data['form']                 = $form;
		}
		else
		{
			$query->select('*')
				->from('#__eb_events')
				->where('id = ' . $row->event_id);

			if ($fieldSuffix)
			{
				$query->select($db->quoteName('title' . $fieldSuffix, 'title'));
			}

			$db->setQuery($query);
			$rowEvent = $db->loadObject();

			$query->clear()
				->select('a.*')
				->from('#__eb_locations AS a')
				->innerJoin('#__eb_events AS b ON a.id = b.location_id')
				->where('b.id = ' . $row->event_id);

			if ($fieldSuffix)
			{
				EventbookingHelperDatabase::getMultilingualFields($query, ['a.name', 'a.alias', 'a.description'], $fieldSuffix);
			}

			$db->setQuery($query);
			$rowLocation = $db->loadObject();
			//Override config
			$data['row']         = $row;
			$data['rowEvent']    = $rowEvent;
			$data['config']      = $config;
			$data['rowLocation'] = $rowLocation;
			$data['form']        = $form;

			if ($rowEvent->collect_member_information === '')
			{
				$collectMemberInformation = $config->collect_member_information;
			}
			else
			{
				$collectMemberInformation = $rowEvent->collect_member_information;
			}

			if ($row->is_group_billing && $collectMemberInformation)
			{
				$query->clear();
				$query->select('*')
					->from('#__eb_registrants')
					->where('group_id = ' . $row->id)
					->order('id');
				$db->setQuery($query);
				$rowMembers         = $db->loadObjectList();
				$data['rowMembers'] = $rowMembers;
			}

			if ($rowEvent->has_multiple_ticket_types)
			{
				$query->clear()
					->select('a.*, b.quantity')
					->from('#__eb_ticket_types AS a')
					->innerJoin('#__eb_registrant_tickets AS b ON a.id = ticket_type_id')
					->where('b.registrant_id = ' . $row->id);
				$db->setQuery($query);
				$data['ticketTypes'] = $db->loadObjectList();
			}
		}

		if ($toAdmin && $row->payment_method == 'os_offline_creditcard')
		{
			$cardNumber = JFactory::getApplication()->input->getString('x_card_num', '');

			if ($cardNumber)
			{
				$last4Digits         = substr($cardNumber, strlen($cardNumber) - 4);
				$data['last4Digits'] = $last4Digits;
			}
		}

		return EventbookingHelperHtml::loadCommonLayout('emailtemplates/tmpl/' . $layout, $data);
	}

	/**
	 * Get group member detail, using for [MEMBER_DETAIL] tag in the email message
	 *
	 * @param RADConfig                   $config
	 * @param EventbookingTableRegistrant $rowMember
	 * @param EventbookingTableEvent      $rowEvent
	 * @param EventbookingTableLocation   $rowLocation
	 * @param bool                        $loadCss
	 * @param RADForm                     $memberForm
	 *
	 * @return string
	 */
	public static function getMemberDetails($config, $rowMember, $rowEvent, $rowLocation, $loadCss = true, $memberForm = null)
	{
		$data                = array();
		$data['rowMember']   = $rowMember;
		$data['rowEvent']    = $rowEvent;
		$data['config']      = $config;
		$data['rowLocation'] = $rowLocation;
		$data['memberForm']  = $memberForm;

		return EventbookingHelperHtml::loadCommonLayout('emailtemplates/tmpl/email_group_member_detail.php', $data);
	}


	/**
	 * Load payment method object
	 *
	 * @param string $name
	 *
	 * @return RADPayment
	 * @throws Exception
	 */
	public static function loadPaymentMethod($name)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__eb_payment_plugins')
			->where('published = 1')
			->where('name = ' . $db->quote($name));
		$db->setQuery($query);
		$row = $db->loadObject();

		if ($row && file_exists(JPATH_ROOT . '/components/com_eventbooking/payments/' . $row->name . '.php'))
		{
			require_once JPATH_ROOT . '/components/com_eventbooking/payments/' . $row->name . '.php';

			$params = new Registry($row->params);

			/* @var RADPayment $method */
			$method = new $row->name($params);
			$method->setTitle($row->title);

			return $method;
		}

		throw new Exception(sprintf('Payment method %s not found', $name));
	}

	/**
	 * Get unique registration code for a registration record
	 *
	 * @return string
	 */
	public static function getRegistrationCode()
	{
		jimport('joomla.user.helper');

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		while (true)
		{
			$registrationCode = JUserHelper::genRandomPassword(10);
			$query->clear()
				->select('COUNT(*)')
				->from('#__eb_registrants')
				->where('registration_code = ' . $db->quote($registrationCode));
			$db->setQuery($query);
			$total = $db->loadResult();

			if (!$total)
			{
				return $registrationCode;
			}
		}
	}
}
