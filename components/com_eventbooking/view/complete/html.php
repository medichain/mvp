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

class EventbookingViewCompleteHtml extends RADViewHtml
{
	public $hasModel = false;

	/**
	 * Display the view
	 *
	 * @throws Exception
	 */
	public function display()
	{
		//Hardcoded the layout, it happens with some clients. Maybe it is a bug of Joomla core code, will find out it later
		$this->setLayout('default');

		$app              = JFactory::getApplication();
		$config           = EventbookingHelper::getConfig();
		$message          = EventbookingHelper::getMessages();
		$fieldSuffix      = EventbookingHelper::getFieldSuffix();
		$registrationCode = JFactory::getSession()->get('eb_registration_code', '');

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__eb_registrants')
			->where('registration_code = ' . $db->quote($registrationCode));
		$db->setQuery($query);
		$rowRegistrant = $db->loadObject();

		if (!$registrationCode || !$rowRegistrant)
		{
			$app->redirect('index.php', JText::_('EB_INVALID_REGISTRATION_CODE'));
		}

		if ($rowRegistrant->published == 0 && ($rowRegistrant->payment_method == 'os_ideal'))
		{
			// Use online payment method and the payment is not success for some reason, we need to redirec to failure page
			$failureUrl = JRoute::_('index.php?option=com_eventbooking&view=failure&id=' . $rowRegistrant->id . '&Itemid=' . $this->Itemid, false, false);
			$app->redirect($failureUrl, 'Something went wrong, you are NOT successfully registered');
		}

		$rowEvent = EventbookingHelperDatabase::getEvent($rowRegistrant->event_id);

		if ($rowRegistrant->published == 0 && strpos($rowRegistrant->payment_method, 'os_offline') !== false)
		{
			$offlineSuffix = str_replace('os_offline', '', $rowRegistrant->payment_method);

			if ($offlineSuffix && EventbookingHelper::isValidMessage($message->{'thanks_message_offline' . $offlineSuffix}))
			{
				$thankMessage = $message->{'thanks_message_offline' . $offlineSuffix};
			}
			elseif ($fieldSuffix && EventbookingHelper::isValidMessage($rowEvent->{'thanks_message_offline' . $fieldSuffix}))
			{
				$thankMessage = $rowEvent->{'thanks_message_offline' . $fieldSuffix};
			}
			elseif ($fieldSuffix && EventbookingHelper::isValidMessage($message->{'thanks_message_offline' . $fieldSuffix}))
			{
				$thankMessage = $message->{'thanks_message_offline' . $fieldSuffix};
			}
			elseif (EventbookingHelper::isValidMessage($rowEvent->thanks_message_offline))
			{
				$thankMessage = $rowEvent->thanks_message_offline;
			}
			else
			{
				$thankMessage = $message->thanks_message_offline;
			}
		}
		else
		{
			if ($fieldSuffix && EventbookingHelper::isValidMessage($rowEvent->{'thanks_message' . $fieldSuffix}))
			{
				$thankMessage = $rowEvent->{'thanks_message' . $fieldSuffix};
			}
			elseif ($fieldSuffix && EventbookingHelper::isValidMessage($message->{'thanks_message' . $fieldSuffix}))
			{
				$thankMessage = $message->{'thanks_message' . $fieldSuffix};
			}
			elseif (EventbookingHelper::isValidMessage($rowEvent->thanks_message))
			{
				$thankMessage = $rowEvent->thanks_message;
			}
			else
			{
				$thankMessage = $message->thanks_message;
			}
		}

		if ($config->multiple_booking)
		{
			$rowFields = EventbookingHelperRegistration::getFormFields($rowRegistrant->id, 4);
		}
		elseif ($rowRegistrant->is_group_billing)
		{
			$rowFields = EventbookingHelperRegistration::getFormFields($rowEvent->id, 1);
		}
		else
		{
			$rowFields = EventbookingHelperRegistration::getFormFields($rowEvent->id, 0);
		}

		$form = new RADForm($rowFields);
		$data = EventbookingHelperRegistration::getRegistrantData($rowRegistrant, $rowFields);
		$form->bind($data);
		$form->buildFieldsDependency();

		if (is_callable('EventbookingHelperOverrideRegistration::buildTags'))
		{
			$replaces = EventbookingHelperOverrideRegistration::buildTags($rowRegistrant, $form, $rowEvent, $config, false);
		}
		elseif (is_callable('EventbookingHelperOverrideHelper::buildTags'))
		{
			$replaces = EventbookingHelperOverrideHelper::buildTags($rowRegistrant, $form, $rowEvent, $config, false);
		}
		else
		{
			$replaces = EventbookingHelperRegistration::buildTags($rowRegistrant, $form, $rowEvent, $config, false);
		}

		foreach ($replaces as $key => $value)
		{
			$key          = strtoupper($key);
			$thankMessage = str_ireplace("[$key]", $value, $thankMessage);
		}

		if (strpos($thankMessage, '[QRCODE]') !== false)
		{
			EventbookingHelper::generateQrcode($rowRegistrant->id);
			$imgTag       = '<img src="media/com_eventbooking/qrcodes/' . $rowRegistrant->id . '.png" border="0" />';
			$thankMessage = str_ireplace("[QRCODE]", $imgTag, $thankMessage);
		}

		$trackingCode = $config->conversion_tracking_code;

		if (!empty($trackingCode))
		{
			$filterInput = JFilterInput::getInstance();

			$replaces['total_amount']           = $filterInput->clean($replaces['total_amount'], 'float');
			$replaces['discount_amount']        = $filterInput->clean($replaces['discount_amount'], 'float');
			$replaces['tax_amount']             = $filterInput->clean($replaces['tax_amount'], 'float');
			$replaces['amount']                 = $filterInput->clean($replaces['amount'], 'float');
			$replaces['payment_processing_fee'] = $filterInput->clean($replaces['payment_processing_fee'], 'float');

			foreach ($replaces as $key => $value)
			{
				$key          = strtoupper($key);
				$trackingCode = str_ireplace("[$key]", $value, $trackingCode);
			}
		}

		$this->message                = $thankMessage;
		$this->registrationCode       = $registrationCode;
		$this->print                  = $this->input->getInt('print', 0);
		$this->conversionTrackingCode = $trackingCode;
		$this->showPrintButton        = $config->get('show_print_button', '1');

		// Reset cart
		$cart = new EventbookingHelperCart();
		$cart->reset();

		parent::display();
	}
}
