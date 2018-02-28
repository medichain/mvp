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

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

class EventbookingModelCart extends RADModel
{
	/**
	 * Add one or multiple events to cart
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function processAddToCart($data)
	{
		if (is_array($data['id']))
		{
			$eventIds = $data['id'];
		}
		else
		{
			$eventIds = array($data['id']);
		}

		$eventIds = ArrayHelper::toInteger($eventIds);

		$cart = new EventbookingHelperCart();
		$cart->addEvents($eventIds);

		return true;
	}

	/**
	 * Update cart with new quantities
	 *
	 * @param array $eventIds
	 * @param array $quantities
	 *
	 * @return bool
	 */
	public function processUpdateCart($eventIds, $quantities)
	{
		$eventIds   = ArrayHelper::toInteger($eventIds);
		$quantities = ArrayHelper::toInteger($quantities);

		$cart = new EventbookingHelperCart();
		$cart->updateCart($eventIds, $quantities);

		return true;
	}

	/**
	 * Remove an event from cart
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public function removeEvent($id)
	{
		$cart = new EventbookingHelperCart();
		$cart->remove($id);

		return true;
	}

	/**
	 * Process checkout in case customer using shopping cart feature
	 *
	 * @param $data
	 *
	 * @return int
	 * @throws Exception
	 */
	public function processCheckout(&$data)
	{
		jimport('joomla.user.helper');
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		$user   = JFactory::getUser();
		$config = EventbookingHelper::getConfig();

		/* @var EventbookingTableRegistrant $row */
		$row                    = JTable::getInstance('EventBooking', 'Registrant');
		$data['transaction_id'] = strtoupper(JUserHelper::genRandomPassword());
		$cart                   = new EventbookingHelperCart();
		$items                  = $cart->getItems();
		$quantities             = $cart->getQuantities();
		$paymentMethod          = isset($data['payment_method']) ? $data['payment_method'] : '';
		$fieldSuffix            = EventbookingHelper::getFieldSuffix();
		if (!$user->id && $config->user_registration)
		{
			$userId          = EventbookingHelperRegistration::saveRegistration($data);
			$data['user_id'] = $userId;
		}
		$rowFields = EventbookingHelperRegistration::getFormFields(0, 4);
		$form      = new RADForm($rowFields);
		$form->bind($data);
		$data['collect_records_data'] = true;

		if (is_callable('EventbookingHelperOverrideRegistration::calculateCartRegistrationFee'))
		{
			$fees = EventbookingHelperOverrideRegistration::calculateCartRegistrationFee($cart, $form, $data, $config, $paymentMethod);
		}
		elseif (is_callable('EventbookingHelperOverrideHelper::calculateCartRegistrationFee'))
		{
			$fees = EventbookingHelperOverrideHelper::calculateCartRegistrationFee($cart, $form, $data, $config, $paymentMethod);
		}
		else
		{
			$fees = EventbookingHelperRegistration::calculateCartRegistrationFee($cart, $form, $data, $config, $paymentMethod);
		}

		// Save the active language
		if (JFactory::getApplication()->getLanguageFilter())
		{
			$language = JFactory::getLanguage()->getTag();
		}
		else
		{
			$language = '*';
		}

		$recordsData = $fees['records_data'];
		$cartId      = 0;

		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('eventbooking');

		// Store list of registrants
		if ($config->collect_member_information_in_cart)
		{
			$membersForm           = $fees['members_form'];
			$membersTotalAmount    = $fees['members_total_amount'];
			$membersDiscountAmount = $fees['members_discount_amount'];
			$membersTaxAmount      = $fees['members_tax_amount'];
			$membersLateFee        = $fees['members_late_fee'];
			$membersAmount         = $fees['members_amount'];
		}

		$count  = 0;
		$userIp = EventbookingHelper::getUserIp();

		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$eventId    = $items[$i];
			$recordData = $recordsData[$eventId];
			$row->bind($data);
			$row->event_id               = $eventId;
			$row->coupon_id              = isset($recordData['coupon_id']) ? $recordData['coupon_id'] : 0;
			$row->user_ip                = $userIp;
			$row->total_amount           = $recordData['total_amount'];
			$row->discount_amount        = $recordData['discount_amount'];
			$row->late_fee               = $recordData['late_fee'];
			$row->tax_amount             = $recordData['tax_amount'];
			$row->payment_processing_fee = $recordData['payment_processing_fee'];
			$row->amount                 = $recordData['amount'];
			$row->deposit_amount         = $recordData['deposit_amount'];

			if ($row->deposit_amount > 0)
			{
				$row->payment_status = 0;
			}
			else
			{
				$row->payment_status = 1;
			}

			if ($config->collect_member_information_in_cart)
			{
				$row->is_group_billing = 1;
			}

			$row->group_id      = 0;
			$row->published     = 0;
			$row->register_date = gmdate('Y-m-d H:i:s');
			if (isset($data['user_id']))
			{
				$row->user_id = $data['user_id'];
			}
			else
			{
				$row->user_id = $user->get('id');
			}
			$row->number_registrants = $quantities[$i];
			$row->event_id           = $eventId;
			if ($i == 0)
			{
				$row->cart_id                = 0;
				$row->coupon_discount_amount = $fees['coupon_discount_amount'];

				//Store registration code
				while (true)
				{
					$registrationCode = JUserHelper::genRandomPassword(10);
					$query->clear();
					$query->select('COUNT(*)')
						->from('#__eb_registrants')
						->where('registration_code=' . $db->quote($registrationCode));
					$db->setQuery($query);
					$total = $db->loadResult();
					if (!$total)
					{
						$row->registration_code = $registrationCode;
						break;
					}
				}
			}
			else
			{
				$row->cart_id                = $cartId;
				$row->coupon_discount_amount = 0;
			}
			$row->id       = 0;
			$row->language = $language;
			$row->store();
			$form->storeData($row->id, $data);

			if ($i == 0)
			{
				$cartId = $row->id;
			}

			if ($config->collect_member_information_in_cart)
			{
				for ($j = 0; $j < $row->number_registrants; $j++)
				{
					$count++;

					/* @var EventbookingTableRegistrant $rowMember */
					$rowMember                 = JTable::getInstance('EventBooking', 'Registrant');
					$rowMember->group_id       = $row->id;
					$rowMember->transaction_id = $row->transaction_id;
					$rowMember->event_id       = $row->event_id;
					$rowMember->payment_method = $row->payment_method;
					$rowMember->user_id        = $row->user_id;
					$rowMember->register_date  = $row->register_date;
					$rowMember->user_ip        = $row->user_ip;

					$rowMember->total_amount       = $membersTotalAmount[$eventId][$j];
					$rowMember->discount_amount    = $membersDiscountAmount[$eventId][$j];
					$rowMember->late_fee           = $membersLateFee[$eventId][$j];
					$rowMember->tax_amount         = $membersTaxAmount[$eventId][$j];
					$rowMember->amount             = $membersAmount[$eventId][$j];
					$rowMember->number_registrants = 1;

					/* @var RADForm $memberForm */
					$memberForm = $membersForm[$eventId][$j];
					$memberForm->removeFieldSuffix();

					$memberData = $memberForm->getFormData();
					$rowMember->bind($memberData);
					$rowMember->store();

					//Store members data custom field
					$memberForm->storeData($rowMember->id, $memberData);
				}
			}
			$dispatcher->trigger('onAfterStoreRegistrant', array($row));
		}

		$query->clear()
			->select($db->quoteName('title' . $fieldSuffix, 'title'))
			->from('#__eb_events')
			->where('id IN (' . implode(',', $items) . ')')
			->order('FIND_IN_SET(id, "' . implode(',', $items) . '")');

		$db->setQuery($query);
		$eventTitles         = $db->loadColumn();
		$data['event_title'] = implode(', ', $eventTitles);

		$itemName          = JText::_('EB_EVENT_REGISTRATION');
		$itemName          = str_replace('[EVENT_TITLE]', $data['event_title'], $itemName);
		$itemName          = str_replace('[FIRST_NAME]', $row->first_name, $itemName);
		$itemName          = str_replace('[LAST_NAME]', $row->last_name, $itemName);
		$itemName          = str_replace('[REGISTRANT_ID]', $row->id, $itemName);
		$data['item_name'] = $itemName;

		// Validate credit card
		if (!empty($data['x_card_num']) && empty($data['card_type']))
		{
			$data['card_type'] = EventbookingHelperCreditcard::getCardType($data['x_card_num']);
		}

		if (!empty($fees['bundle_discount_ids']))
		{
			$query->clear()
				->update('#__eb_discounts')
				->set('used = used + 1')
				->where('id IN (' . implode(',', $fees['bundle_discount_ids']) . ')');
			$db->setQuery($query);
			$db->execute();
		}

		$session = JFactory::getSession();
		$session->set('eb_registration_code', $row->registration_code);
		if ($fees['amount'] > 0)
		{
			require_once JPATH_COMPONENT . '/payments/' . $paymentMethod . '.php';

			if ($fees['deposit_amount'] > 0)
			{
				$data['amount'] = $fees['deposit_amount'];
			}
			else
			{
				$data['amount'] = $fees['amount'];
			}

			$row->load($cartId);

			$query->clear()
				->select('params')
				->from('#__eb_payment_plugins')
				->where('name = ' . $db->quote($paymentMethod));
			$db->setQuery($query);
			$params       = new Registry($db->loadResult());
			$paymentClass = new $paymentMethod($params);

			// Convert payment amount to USD if the currency is not supported by payment gateway
			$currency = $config->currency_code;

			if (method_exists($paymentClass, 'getSupportedCurrencies'))
			{
				$currencies = $paymentClass->getSupportedCurrencies();

				if (!in_array($currency, $currencies))
				{
					$data['amount'] = EventbookingHelper::convertAmountToUSD($data['amount'], $currency);
					$currency       = 'USD';
				}
			}

			$data['currency'] = $currency;

			$country         = empty($data['country']) ? $config->default_country : $data['country'];
			$data['country'] = EventbookingHelper::getCountryCode($country);

			// Store payment amount and payment currency for future validation
			$row->payment_currency = $currency;
			$row->payment_amount   = $data['amount'];
			$row->store();

			$paymentClass->processPayment($row, $data);
		}
		else
		{
			$row->load($cartId);
			$row->payment_date = gmdate('Y-m-d H:i:s');
			$row->published    = 1;
			$row->store();

			// Update status of all registrants
			$query->clear();
			$query->update('#__eb_registrants')
				->set('published = 1')
				->set('payment_date=NOW()')
				->where('cart_id = ' . $row->id);
			$db->setQuery($query);
			$db->execute();

			$dispatcher->trigger('onAfterPaymentSuccess', array($row));
			EventbookingHelper::sendEmails($row, $config);

			return 1;
		}
	}

	/**
	 * Get information of events which user added to cart
	 *
	 * @return array|mixed
	 */
	public function getData()
	{
		$config = EventbookingHelper::getConfig();
		$cart   = new EventbookingHelperCart();
		$rows   = $cart->getEvents();

		if ($config->show_price_including_tax && !$config->get('setup_price'))
		{
			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$row       = $rows[$i];
				$taxRate   = $row->tax_rate;
				$row->rate = round($row->rate * (1 + $taxRate / 100), 2);

				if ($config->show_discounted_price)
				{
					$row->discounted_rate = round($row->discounted_rate * (1 + $taxRate / 100), 2);
				}
			}
		}

		return $rows;
	}
}
