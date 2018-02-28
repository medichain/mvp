<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class EventbookingViewPaymentHtml extends RADViewHtml
{
	use EventbookingViewCaptcha;

	/**
	 * Display interface to user
	 */
	public function display()
	{
		$layout = $this->getLayout();

		if ($layout == 'complete')
		{
			$this->displayPaymentComplete();

			return;
		}

		// Load common js code
		$document = JFactory::getDocument();
		$rootUri  = JUri::root(true);
		$document->addScriptDeclaration(
			'var siteUrl = "' . EventbookingHelper::getSiteUrl() . '";'
		);
		$document->addScript($rootUri . '/media/com_eventbooking/assets/js/paymentmethods.js');

		$customJSFile = JPATH_ROOT . '/media/com_eventbooking/assets/js/custom.js';

		if (file_exists($customJSFile) && filesize($customJSFile) > 0)
		{
			$document->addScript($rootUri . '/media/com_eventbooking/assets/js/custom.js');
		}

		EventbookingHelper::addLangLinkForAjax();

		if ($layout == 'registration')
		{
			$this->displayRegistrationPayment();

			return;
		}

		$registrantId = $this->input->getInt('registrant_id');

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

		$event = EventbookingHelperDatabase::getEvent($rowRegistrant->event_id);
		$this->setBaseFormData($rowRegistrant, $event);

		if (count($this->methods) == 0)
		{
			echo JText::_('EB_ENABLE_PAYMENT_METHODS');

			return;
		}

		if (is_callable('EventbookingHelperOverrideRegistration::calculateRemainderFees'))
		{
			$fees = EventbookingHelperOverrideRegistration::calculateRemainderFees($rowRegistrant, $this->paymentMethod);
		}
		else
		{
			$fees = EventbookingHelperRegistration::calculateRemainderFees($rowRegistrant, $this->paymentMethod);
		}

		$this->loadCaptcha();

		// Assign these parameters
		$this->fees          = $fees;
		$this->onClickHandle = 'calculateRemainderFee();';

		parent::display();
	}

	/**
	 * Display form which allow users to click on to complete payment for a registration
	 *
	 * @return void
	 */
	protected function displayRegistrationPayment()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$registrationCode = $this->input->getString('order_number') ?: $this->input->getString('registration_code');

		$query->select('*')
			->from('#__eb_registrants')
			->where('registration_code = ' . $db->quote($registrationCode));
		$db->setQuery($query);
		$rowRegistrant = $db->loadObject();

		if (empty($rowRegistrant))
		{
			echo JText::_('EB_INVALID_REGISTRATION_RECORD');

			return;
		}

		if ($rowRegistrant->published == 1)
		{
			echo JText::_('EB_PAYMENT_WAS_COMPLETED');

			return;
		}

		$event = EventbookingHelperDatabase::getEvent($rowRegistrant->event_id);

		// Validate and make sure there is still space available to join
		if ($event->event_capacity > 0 && ($event->event_capacity - $event->total_registrants < $rowRegistrant->number_registrants))
		{
			echo JText::_('EB_EVENT_IS_FULL_COULD_NOT_JOIN');;

			return;
		}

		$this->setBaseFormData($rowRegistrant, $event);


		if (count($this->methods) == 0)
		{
			echo JText::_('EB_ENABLE_PAYMENT_METHODS');

			return;
		}

		if (is_callable('EventbookingHelperOverrideRegistration::calculateRegistrationFees'))
		{
			$fees = EventbookingHelperOverrideRegistration::calculateRegistrationFees($rowRegistrant, $this->paymentMethod);
		}
		else
		{
			$fees = EventbookingHelperRegistration::calculateRegistrationFees($rowRegistrant, $this->paymentMethod);
		}

		// Assign these parameters
		$this->fees          = $fees;
		$this->onClickHandle = 'calculateRegistrationFee();';

		parent::display();
	}

	/**
	 * Method to calculate and set base form data
	 *
	 * @param EventbookingTableRegistrant $rowRegistrant
	 * @param EventbookingTableEvent      $event
	 */
	protected function setBaseFormData($rowRegistrant, $event)
	{
		$config    = EventbookingHelper::getConfig();
		$user      = JFactory::getUser();
		$userId    = $user->get('id');
		$rowFields = EventbookingHelperRegistration::getDepositPaymentFormFields();

		$captchaInvalid = $this->input->getInt('captcha_invalid', 0);

		if ($captchaInvalid)
		{
			$data = $this->input->post->getData();
		}
		else
		{
			$data = EventbookingHelperRegistration::getRegistrantData($rowRegistrant, $rowFields);

			// IN case there is no data, get it from URL (get for example)
			if (empty($data))
			{
				$data = $this->input->getData();
			}
		}

		if ($userId && !isset($data['first_name']))
		{
			//Load the name from Joomla default name
			$name = $user->name;

			if ($name)
			{
				$pos = strpos($name, ' ');

				if ($pos !== false)
				{
					$data['first_name'] = substr($name, 0, $pos);
					$data['last_name']  = substr($name, $pos + 1);
				}
				else
				{
					$data['first_name'] = $name;
					$data['last_name']  = '';
				}
			}
		}

		if ($userId && !isset($data['email']))
		{
			$data['email'] = $user->email;
		}

		if (!isset($data['country']) || !$data['country'])
		{
			$data['country'] = $config->default_country;
		}

		//Get data
		$form = new RADForm($rowFields);

		if ($captchaInvalid)
		{
			$useDefault = false;
		}
		else
		{
			$useDefault = true;
		}

		$form->bind($data, $useDefault);

		$paymentMethod = $this->input->post->getString('payment_method', os_payments::getDefautPaymentMethod(trim($event->payment_methods)));

		$expMonth           = $this->input->post->getInt('exp_month', date('m'));
		$expYear            = $this->input->post->getInt('exp_year', date('Y'));
		$lists['exp_month'] = JHtml::_('select.integerlist', 1, 12, 1, 'exp_month', ' class="input-small" ', $expMonth, '%02d');
		$currentYear        = date('Y');
		$lists['exp_year']  = JHtml::_('select.integerlist', $currentYear, $currentYear + 10, 1, 'exp_year', 'class="input-small"', $expYear);

		$methods = os_payments::getPaymentMethods(trim($event->payment_methods), false);

		if (count($methods) == 0)
		{
			echo JText::_('EB_ENABLE_PAYMENT_METHODS');

			return;
		}

		// Check to see if there is payment processing fee or not
		$showPaymentFee = false;

		foreach ($methods as $method)
		{
			if ($method->paymentFee)
			{
				$showPaymentFee = true;
				break;
			}
		}

		if (empty($paymentMethod) && count($methods))
		{
			$paymentMethod = $methods[0]->getName();
		}

		// Assign these parameters
		$this->bootstrapHelper = new EventbookingHelperBootstrap($config->twitter_bootstrap_version);
		$this->paymentMethod   = $paymentMethod;
		$this->config          = $config;
		$this->event           = $event;
		$this->methods         = $methods;
		$this->lists           = $lists;
		$this->message         = EventbookingHelper::getMessages();
		$this->fieldSuffix     = EventbookingHelper::getFieldSuffix();
		$this->message         = EventbookingHelper::getMessages();
		$this->form            = $form;
		$this->rowRegistrant   = $rowRegistrant;
		$this->showPaymentFee  = $showPaymentFee;
		$this->currencySymol   = $event->currency_symbol ?: $config->currency_symbol;

		$this->loadCaptcha();
	}

	/**
	 * Display payment complete page
	 */
	protected function displayPaymentComplete()
	{
		$config      = EventbookingHelper::getConfig();
		$message     = EventbookingHelper::getMessages();
		$fieldSuffix = EventbookingHelper::getFieldSuffix();

		if (strlen(trim(strip_tags($message->{'deposit_payment_thanks_message' . $fieldSuffix}))))
		{
			$thankMessage = $message->{'deposit_payment_thanks_message' . $fieldSuffix};
		}
		else
		{
			$thankMessage = $message->deposit_payment_thanks_message;
		}

		$id = (int) JFactory::getSession()->get('payment_id', 0);

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__eb_registrants')
			->where('id = ' . $id);
		$db->setQuery($query);
		$row = $db->loadObject();

		if (empty($row->id))
		{
			echo JText::_('Invalid Registration Record');

			return;
		}

		$replaces = EventbookingHelperRegistration::buildDepositPaymentTags($row, $config);

		foreach ($replaces as $key => $value)
		{
			$key          = strtoupper($key);
			$thankMessage = str_ireplace("[$key]", $value, $thankMessage);
		}

		$this->message = $thankMessage;

		parent::display();
	}
}
