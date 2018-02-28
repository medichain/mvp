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

class EventBookingModelPayment extends RADModel
{
	/**
	 * Process reminder payment for registration
	 *
	 * @param array $data
	 *
	 * @return void
	 * @throws Exception
	 */
	public function processPayment($data)
	{
		jimport('joomla.user.helper');
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		$config = EventbookingHelper::getConfig();
		$row    = JTable::getInstance('EventBooking', 'Registrant');
		$row->load((int) $data['registrant_id']);

		// Calculate the payment amount
		$paymentMethod = isset($data['payment_method']) ? $data['payment_method'] : '';

		$row->deposit_payment_method = $paymentMethod;

		// Mark the the registration record as "deposit payment processing"
		$row->process_deposit_payment = 1;

		$event = EventbookingHelperDatabase::getEvent($row->event_id);

		$data['event_title'] = $event->title;

		// Store registration_code into session, use for registration complete code
		JFactory::getSession()->set('payment_id', $row->id);

		if ($row->deposit_amount > 0)
		{
			require_once JPATH_COMPONENT . '/payments/' . $paymentMethod . '.php';

			if (is_callable('EventbookingHelperOverrideRegistration::calculateRemainderFees'))
			{
				$fees = EventbookingHelperOverrideRegistration::calculateRemainderFees($row, $paymentMethod);
			}
			else
			{
				$fees = EventbookingHelperRegistration::calculateRemainderFees($row, $paymentMethod);
			}

			$row->deposit_payment_processing_fee = $fees['payment_processing_fee'];

			$row->store();

			$data['amount'] = $fees['gross_amount'];

			$itemName          = JText::_('EB_PROCESS_DEPOSIT_PAYMENT');
			$itemName          = str_replace('[EVENT_TITLE]', $data['event_title'], $itemName);
			$itemName          = str_replace('[REGISTRATION_ID]', $row->id, $itemName);
			$data['item_name'] = $itemName;

			// Guess card type based on card number
			if (!empty($data['x_card_num']) && empty($data['card_type']))
			{
				$data['card_type'] = EventbookingHelperCreditcard::getCardType($data['x_card_num']);
			}

			$query->clear()
				->select('params')
				->from('#__eb_payment_plugins')
				->where('name=' . $db->quote($paymentMethod));
			$db->setQuery($query);
			$params       = new Registry($db->loadResult());
			$paymentClass = new $paymentMethod($params);

			// Convert payment amount to USD if the currency is not supported by payment gateway
			$currency = $event->currency_code ? $event->currency_code : $config->currency_code;

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

			$paymentClass->processPayment($row, $data);
		}
		else
		{
			echo JText::_('EB_INVALID_DEPOSIT_PAYMENT');
		}
	}

	/**
	 * Process individual registration
	 *
	 * @param array $data
	 *
	 * @return void
	 * @throws Exception
	 */
	public function processRegistrationPayment($data)
	{
		jimport('joomla.user.helper');
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		$config = EventbookingHelper::getConfig();
		$row    = JTable::getInstance('EventBooking', 'Registrant');
		$row->load((int) $data['registrant_id']);
		$paymentMethod = isset($data['payment_method']) ? $data['payment_method'] : '';
		$event         = EventbookingHelperDatabase::getEvent($row->event_id);

		// Store registration_code into session, use for registration complete code
		JFactory::getSession()->set('eb_registration_code', $row->registration_code);

		if ($row->amount > 0)
		{
			require_once JPATH_COMPONENT . '/payments/' . $paymentMethod . '.php';

			if (is_callable('EventbookingHelperOverrideRegistration::calculateRegistrationFees'))
			{
				$fees = EventbookingHelperOverrideRegistration::calculateRegistrationFees($row, $paymentMethod);
			}
			else
			{
				$fees = EventbookingHelperRegistration::calculateRegistrationFees($row, $paymentMethod);
			}

			$row->payment_processing_fee = $fees['payment_processing_fee'];
			$row->amount                 = $fees['gross_amount'];
			$row->payment_method         = $paymentMethod;
			$row->store();

			$data['amount'] = $fees['gross_amount'];

			$itemName          = JText::_('EB_PROCESS_REGISTRATION_PAYMENT');
			$itemName          = str_replace('[EVENT_TITLE]', $event->title, $itemName);
			$itemName          = str_replace('[REGISTRATION_ID]', $row->id, $itemName);
			$data['item_name'] = $itemName;

			// Guess card type based on card number
			if (!empty($data['x_card_num']) && empty($data['card_type']))
			{
				$data['card_type'] = EventbookingHelperCreditcard::getCardType($data['x_card_num']);
			}

			$query->clear()
				->select('params')
				->from('#__eb_payment_plugins')
				->where('name = ' . $db->quote($paymentMethod));
			$db->setQuery($query);
			$params       = new Registry($db->loadResult());
			$paymentClass = new $paymentMethod($params);

			// Convert payment amount to USD if the currency is not supported by payment gateway
			$currency = $event->currency_code ? $event->currency_code : $config->currency_code;

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

			$paymentClass->processPayment($row, $data);
		}
		else
		{
			echo JText::_('EB_INVALID_DEPOSIT_PAYMENT');
		}
	}
}