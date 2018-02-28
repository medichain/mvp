<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class EventbookingControllerPayment extends EventbookingController
{
	use EventbookingControllerCaptcha;

	/**
	 * Process individual registration
	 */
	public function process()
	{
		$app          = JFactory::getApplication();
		$input        = $this->input;
		$registrantId = $input->getInt('registrant_id', 0);

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__eb_registrants')
			->where('id = ' . $registrantId);
		$db->setQuery($query);
		$rowRegistrant = $db->loadObject();

		if (empty($rowRegistrant))
		{
			echo JText::_('EB_INVALID_REGISTRATION_RECORD');

			return;
		}

		if ($rowRegistrant->payment_status == 1)
		{
			echo JText::_('EB_DEPOSIT_PAYMENT_COMPLETED');

			return;
		}

		$errors = array();

		// Validate captcha
		if (!$this->validateCaptcha($this->input))
		{
			$errors[] = JText::_('EB_INVALID_CAPTCHA_ENTERED');
		}

		$data = $input->post->getData();

		if (count($errors))
		{
			foreach ($errors as $error)
			{
				$app->enqueueMessage($error, 'error');
			}

			$input->set('captcha_invalid', 1);
			$input->set('view', 'payment');
			$input->set('layout', 'default');
			$this->display();

			return;
		}

		/* @var EventBookingModelPayment $model */
		$model = $this->getModel('payment');

		$model->processPayment($data);
	}

	/**
	 * Process individual registration
	 */
	public function process_registration_payment()
	{
		$app          = JFactory::getApplication();
		$input        = $this->input;
		$registrantId = $input->getInt('registrant_id', 0);

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__eb_registrants')
			->where('id = ' . $registrantId);
		$db->setQuery($query);
		$rowRegistrant = $db->loadObject();

		if (empty($rowRegistrant))
		{
			echo JText::_('EB_INVALID_REGISTRATION_RECORD');

			return;
		}

		$event = EventbookingHelperDatabase::getEvent($rowRegistrant->event_id);

		if ($event->event_capacity > 0 && ($event->event_capacity - $event->total_registrants < $rowRegistrant->number_registrants))
		{
			echo JText::_('EB_EVENT_IS_FULL_COULD_NOT_JOIN');;

			return;
		}

		if ($rowRegistrant->published == 1)
		{
			echo JText::_('EB_PAYMENT_WAS_COMPLETED');

			return;
		}

		$errors = array();

		// Validate captcha
		if (!$this->validateCaptcha($this->input))
		{
			$errors[] = JText::_('EB_INVALID_CAPTCHA_ENTERED');
		}

		$data = $input->post->getData();

		if (count($errors))
		{
			foreach ($errors as $error)
			{
				$app->enqueueMessage($error, 'error');
			}

			$input->set('captcha_invalid', 1);
			$input->set('view', 'payment');
			$input->set('layout', 'registration');
			$this->display();

			return;
		}

		/* @var EventBookingModelPayment $model */
		$model = $this->getModel('payment');

		$model->processRegistrationPayment($data);
	}
}
