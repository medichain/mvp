<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

/**
 * Event Booking Registrant Model
 *
 * @package        Joomla
 * @subpackage     Event Booking
 */
class EventbookingModelCommonRegistrant extends RADModelAdmin
{
	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->state->insert('filter_event_id', 'int', 0);
	}

	/**
	 * Initial registrant data
	 *
	 * @see RADModelAdmin::initData()
	 */
	public function initData()
	{
		parent::initData();

		$this->data->event_id = $this->state->filter_event_id;
	}

	/**
	 * Method to store a registrant
	 *
	 * @access    public
	 *
	 * @param    RADInput $input
	 *
	 * @return    boolean    True on success
	 */
	public function store($input, $ignore = array())
	{
		$app    = JFactory::getApplication();
		$user   = JFactory::getUser();
		$config = EventbookingHelper::getConfig();
		$db     = $this->getDbo();
		$query  = $db->getQuery(true);
		/* @var EventbookingTableRegistrant $row */
		$row  = $this->getTable();
		$data = $input->getData();

		$recalculateFee = false;

		if ($data['id'])
		{
			//We will need to calculate total amount here now
			$row->load($data['id']);
			$published = $row->published;
			if ($row->is_group_billing)
			{
				$rowFields = EventbookingHelperRegistration::getFormFields($data['event_id'], 1);
			}
			else
			{
				$rowFields = EventbookingHelperRegistration::getFormFields($data['event_id'], 0);
			}

			if ($user->authorise('eventbooking.registrantsmanagement', 'com_eventbooking') || empty($row->published))
			{
				$excludeFeeFields = false;
			}
			else
			{
				$excludeFeeFields = true;
			}

			// Reset number checked in counter if admin change checked in status
			if ($row->checked_in && isset($data['checked_in']) && $data['checked_in'] == 0)
			{
				$row->checked_in_count = 0;
			}

			$row->bind($data);

			/*
				Re - calculate registration fee in the following cases:
			*/
			if (!empty($data['re_calculate_fee']) || ($row->published == 0 && $app->isSite() && $user->id == $row->user_id))
			{
				$recalculateFee = true;

				if ($row->is_group_billing)
				{
					$event = EventbookingHelperDatabase::getEvent($row->event_id, $row->register_date);
					$form  = new RADForm($rowFields);
					$form->bind($data);

					if ($row->coupon_id)
					{
						$query->clear()
							->select($db->quoteName('code'))
							->from('#__eb_coupons')
							->where('id = ' . $row->coupon_id);
						$db->setQuery($query);
						$data['coupon_code'] = $db->loadResult();
					}

					$data['number_registrants'] = $row->number_registrants;
					$data['re_calculate_fee']   = true;

					if (is_callable('EventbookingHelperOverrideRegistration::calculateGroupRegistrationFees'))
					{
						$fees = EventbookingHelperOverrideRegistration::calculateGroupRegistrationFees($event, $form, $data, $config, $row->payment_method);
					}
					elseif (is_callable('EventbookingHelperOverrideHelper::calculateGroupRegistrationFees'))
					{
						$fees = EventbookingHelperOverrideHelper::calculateGroupRegistrationFees($event, $form, $data, $config, $row->payment_method);
					}
					else
					{
						$fees = EventbookingHelperRegistration::calculateGroupRegistrationFees($event, $form, $data, $config, $row->payment_method);
					}

					$row->total_amount    = round($fees['total_amount'], 2);
					$row->discount_amount = round($fees['discount_amount'], 2);
					$row->tax_amount      = round($fees['tax_amount'], 2);
					$row->amount          = round($fees['amount'], 2);

					$membersTotalAmount    = $fees['members_total_amount'];
					$membersDiscountAmount = $fees['members_discount_amount'];
					$membersTaxAmount      = $fees['members_tax_amount'];
					$membersLateFee        = $fees['members_late_fee'];
				}
				else
				{
					// Individual registration
					$event = EventbookingHelperDatabase::getEvent($row->event_id, $row->register_date);
					$form  = new RADForm($rowFields);
					$form->bind($data);

					if ($row->coupon_id)
					{
						$query->clear()
							->select($db->quoteName('code'))
							->from('#__eb_coupons')
							->where('id = ' . $row->coupon_id);
						$db->setQuery($query);
						$data['coupon_code'] = $db->loadResult();
					}

					if (is_callable('EventbookingHelperOverrideRegistration::calculateIndividualRegistrationFees'))
					{
						$fees = EventbookingHelperOverrideRegistration::calculateIndividualRegistrationFees($event, $form, $data, $config, $row->payment_method);
					}
					elseif (is_callable('EventbookingHelperOverrideHelper::calculateIndividualRegistrationFees'))
					{
						$fees = EventbookingHelperOverrideHelper::calculateIndividualRegistrationFees($event, $form, $data, $config, $row->payment_method);
					}
					else
					{
						$fees = EventbookingHelperRegistration::calculateIndividualRegistrationFees($event, $form, $data, $config, $row->payment_method);
					}


					$row->total_amount    = round($fees['total_amount'], 2);
					$row->discount_amount = round($fees['discount_amount'], 2);
					$row->tax_amount      = round($fees['tax_amount'], 2);
					$row->amount          = round($fees['amount'], 2);
				}
			}

			if (!$row->registration_code)
			{
				$row->registration_code = EventbookingHelperRegistration::getRegistrationCode();
			}

			$row->store();
			$form = new RADForm($rowFields);
			$form->storeData($row->id, $data, $excludeFeeFields);

			//Update group members records according to grop billing record
			if ($row->is_group_billing)
			{
				if (strpos($row->payment_method, 'os_offline') !== false)
				{
					$query->update('#__eb_registrants')
						->set('published=' . (int) $row->published)
						->where('group_id=' . $row->id);
					$db->setQuery($query);
					$db->execute();
					$query->clear();
				}

				// Update checked_in status
				$query->update('#__eb_registrants')
					->set('checked_in=' . (int) $row->checked_in)
					->set('event_id=' . (int) $row->event_id)
					->where('group_id=' . $row->id);
				$db->setQuery($query);
				$db->execute();
				$query->clear();
			}

			$event = EventbookingHelperDatabase::getEvent($row->event_id);

			if ($event->collect_member_information !== '')
			{
				$config->collect_member_information = $event->collect_member_information;
			}

			//Store group members data
			if ($row->number_registrants > 1 && $config->collect_member_information)
			{
				$ids              = (array) $data['ids'];
				$memberFormFields = EventbookingHelperRegistration::getFormFields($row->event_id, 2);
				for ($i = 0; $i < $row->number_registrants; $i++)
				{
					$memberId = $ids[$i];

					/* @var $rowMember EventbookingTableRegistrant */
					$rowMember = $this->getTable();
					$rowMember->load($memberId);
					$rowMember->event_id       = $row->event_id;
					$rowMember->published      = $row->published;
					$rowMember->payment_method = $row->payment_method;
					$rowMember->transaction_id = $row->transaction_id;
					if (!$memberId)
					{
						$rowMember->group_id = $row->id;
						$rowMember->user_id  = $row->user_id;
						$rowMember->number_registrants = 1;
					}

					if (!$rowMember->registration_code)
					{
						$rowMember->registration_code = EventbookingHelperRegistration::getRegistrationCode();
					}

					$memberForm = new RADForm($memberFormFields);
					$memberForm->setFieldSuffix($i + 1);
					$memberForm->bind($data);
					$memberForm->removeFieldSuffix();
					$memberData = $memberForm->getFormData();
					$rowMember->bind($memberData);

					if ($recalculateFee)
					{
						$rowMember->total_amount    = $membersTotalAmount[$i];
						$rowMember->discount_amount = $membersDiscountAmount[$i];
						$rowMember->late_fee        = $membersLateFee[$i];
						$rowMember->tax_amount      = $membersTaxAmount[$i];
						$rowMember->amount          = $rowMember->total_amount - $rowMember->discount_amount + $rowMember->tax_amount + $rowMember->late_fee;
					}
					$rowMember->store();
					$memberForm->storeData($rowMember->id, $memberData);
				}
			}

			$this->storeRegistrantTickets($row, $data);

			if ($row->published == 1 && ($published == 0 || $published == 3))
			{
				if (empty($row->payment_date) || ($row->payment_date == $db->getNullDate()))
				{
					$row->payment_date = JFactory::getDate()->toSql();
					$row->store();
				}

				//Change from pending to paid, trigger event, send emails
				JPluginHelper::importPlugin('eventbooking');
				JFactory::getApplication()->triggerEvent('onAfterPaymentSuccess', array($row));
				EventbookingHelperMail::sendRegistrationApprovedEmail($row, $config);
			}
			elseif ($row->published == 2 && $published != 2)
			{
				// Update status of group members record to cancelled as well
				if ($row->is_group_billing)
				{
					// We will need to set group members records to be cancelled
					$query->clear()
						->update('#__eb_registrants')
						->set('published = 2')
						->where('group_id = ' . (int) $row->id);
					$db->setQuery($query);
					$db->execute();
				}

				// Send registration cancelled email to registrant
				EventbookingHelperMail::sendRegistrationCancelledEmail($row, $config);

				//Registration is cancelled, send notification emails to waiting list
				if ($config->activate_waitinglist_feature)
				{
					EventbookingHelper::notifyWaitingList($row, $config);
				}
			}
			$input->set('id', $row->id);
		}
		else
		{
			jimport('joomla.user.helper');

			// In case number registrants is empty, we set it default to 1
			$data['number_registrants'] = (int) $data['number_registrants'];

			if (empty($data['number_registrants']))
			{
				$data['number_registrants'] = 1;
			}

			$data['transaction_id'] = strtoupper(JUserHelper::genRandomPassword());

			$row->bind($data);
			$row->registration_code = EventbookingHelperRegistration::getRegistrationCode();

			$rowFields = EventbookingHelperRegistration::getFormFields($data['event_id'], 0);
			$form      = new RADForm($rowFields);
			$form->bind($data);

			if (!$row->payment_method || $row->published == 0)
			{
				$row->payment_method = 'os_offline';
			}

			$row->register_date = JFactory::getDate()->toSql();

			// In case total amount is not entered, calculate it automatically
			if (empty($row->total_amount))
			{
				$rate              = EventbookingHelperRegistration::getRegistrationRate($data['event_id'], $data['number_registrants']);
				$row->total_amount = $row->amount = $rate * $data['number_registrants'] + $form->calculateFee();
			}

			if (empty($row->amount))
			{
				$row->amount = $row->total_amount - $row->discount_amount + $row->tax_amount + $row->late_fee + $row->payment_processing_fee;
			}

			if ($row->number_registrants > 1)
			{
				$row->is_group_billing = 1;
			}
			else
			{
				$row->is_group_billing = 0;
			}
			$row->store();
			$form->storeData($row->id, $data);

			$this->storeRegistrantTickets($row, $data);

			$app = JFactory::getApplication();
			JPluginHelper::importPlugin('eventbooking');

			$app->triggerEvent('onAfterStoreRegistrant', array($row));

			if ($row->published == 1)
			{
				// Trigger event and send emails
				$app->triggerEvent('onAfterPaymentSuccess', array($row));
			}

			// In case individual registration, we will send notification email to registrant
			if ($row->number_registrants == 1)
			{
				EventbookingHelper::loadLanguage();
				EventbookingHelper::sendEmails($row, $config);
			}

			$input->set('id', $row->id);
		}

		return true;
	}

	/**
	 * Resend confirmation email to registrant
	 *
	 * @param $id
	 *
	 * @return bool True if email is successfully delivered
	 */
	public function resendEmail($id)
	{
		$row = $this->getTable();
		$row->load($id);

		if ($row->group_id > 0)
		{
			// We don't send email to group members, return false
			return false;
		}

		// Load the default frontend language
		$lang = JFactory::getLanguage();
		$tag  = $row->language;

		if (!$tag || $tag == '*')
		{
			$tag = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
		}

		$lang->load('com_eventbooking', JPATH_ROOT, $tag);

		$config = EventbookingHelper::getConfig();

		if ($row->published == 3)
		{
			EventbookingHelperMail::sendWaitinglistEmail($row, $config);
		}
		else
		{
			EventbookingHelperMail::sendEmails($row, $config);
		}

		return true;
	}

	/**
	 * Resend confirmation email to registrant
	 *
	 * @param $id
	 *
	 * @return bool True if email is successfully delivered
	 * @throws Exception
	 */
	public function sendPaymentRequestEmail($id)
	{
		/* @var EventbookingTableRegistrant $row */
		$row = $this->getTable();
		$row->load($id);

		if ($row->group_id > 0)
		{
			// We don't send email to group members, return false
			throw new Exception('Request payment email could not be ent to group members');
		}

		if ($row->published == 1)
		{
			// We don't send request payment email to paid registration
			throw new Exception('Request payment can only be sent to waiting list or pending registration');
		}

		$config = EventbookingHelper::getConfig();

		EventbookingHelperMail::sendRequestPaymentEmail($row, $config);
	}

	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param array $cid   A list of the primary keys to change.
	 * @param int   $state The value of the published state.
	 *
	 * @throws Exception
	 */
	public function publish($cid, $state = 1)
	{
		$db = $this->getDbo();

		if ($state == 1 && count($cid))
		{
			$app = JFactory::getApplication();
			JPluginHelper::importPlugin('eventbooking');
			$config = EventbookingHelper::getConfig();
			$row    = new RADTable('#__eb_registrants', 'id', $db);

			foreach ($cid as $registrantId)
			{
				$row->load($registrantId);

				if (!$row->published)
				{
					if (empty($row->payment_date) || ($row->payment_date == $db->getNullDate()))
					{
						$row->payment_date = JFactory::getDate()->toSql();
						$row->store();
					}

					$row->published = 1;

					// Trigger event
					$app->triggerEvent('onAfterPaymentSuccess', array($row));

					// Re-generate invoice with Paid status
					if ($config->activate_invoice_feature && $row->invoice_number)
					{
						EventbookingHelper::generateInvoicePDF($row);
					}

					EventbookingHelperMail::sendRegistrationApprovedEmail($row, $config);
				}
			}
		}

		$cids  = implode(',', $cid);
		$query = $db->getQuery(true);
		$query->update('#__eb_registrants')
			->set('published = ' . (int) $state)
			->where("(id IN ($cids) OR group_id IN ($cids))");

		if ($state == 0)
		{
			$query->where("payment_method LIKE 'os_offline%'");
		}

		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Method to remove registrants
	 *
	 * @access    public
	 * @return    boolean    True on success
	 */
	public function delete($cid = array())
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		/* @var EventbookingTableRegistrant $row */
		$row = $this->getTable();

		if (count($cid))
		{
			foreach ($cid as $registrantId)
			{
				$row->load($registrantId);
				if ($row->group_id > 0)
				{
					$row->total_amount    = (float) $row->total_amount;
					$row->discount_amount = (float) $row->discount_amount;
					$row->tax_amount      = (float) $row->tax_amount;
					$row->amount          = (float) $row->amount;
					$query->update('#__eb_registrants')
						->set('number_registrants = number_registrants -1')
						->set('total_amount = total_amount - ' . $row->total_amount)
						->set('discount_amount = discount_amount - ' . $row->discount_amount)
						->set('tax_amount = tax_amount - ' . $row->tax_amount)
						->set('amount = amount - ' . $row->amount)
						->where('id=' . $row->group_id);
					$db->setQuery($query);
					$db->execute();
					$query->clear();

					$query->select('number_registrants')
						->from('#__eb_registrants')
						->where('id=' . $row->group_id);
					$db->setQuery($query);
					$numberRegistrants = (int) $db->loadResult();
					$query->clear();
					if ($numberRegistrants == 0)
					{
						$query->delete('#__eb_field_values')->where('registrant_id=' . $row->group_id);
						$db->setQuery($query);
						$db->execute();

						$query->clear()
							->delete('#__eb_registrants')
							->where('id = ' . $row->group_id);
						$db->setQuery($query)
							->execute();
						$query->clear();
					}
				}
			}

			$cids = implode(',', $cid);
			$query->select('id')
				->from('#__eb_registrants')
				->where('group_id IN (' . $cids . ')');
			$db->setQuery($query);

			$cid           = array_merge($cid, $db->loadColumn());
			$registrantIds = implode(',', $cid);

			$query->clear()
				->delete('#__eb_field_values')
				->where('registrant_id IN (' . $registrantIds . ')');
			$db->setQuery($query)
				->execute();

			$query->clear()
				->delete('#__eb_registrants')
				->where('id IN (' . $registrantIds . ')');
			$db->setQuery($query)
				->execute();

			$query->clear()
				->delete('#__eb_registrant_tickets')
				->where('registrant_id IN (' . $registrantIds . ')');
			$db->setQuery($query)
				->execute();
		}

		return true;
	}

	/**
	 * Check-in a registration record
	 *
	 * @param $id
	 * @pram  $group
	 *
	 * @return int
	 */
	public function checkin($id, $group = false)
	{
		/* @var EventbookingTableRegistrant $row */
		$row = $this->getTable();
		$row->load($id);

		if (empty($row))
		{
			return 0;
		}

		if ($row->checked_in)
		{
			return 1;
		}

		if ($group)
		{
			$row->checked_in_count = $row->number_registrants;
		}
		else
		{
			$row->checked_in_count = $row->checked_in_count + 1;
		}

		if ($row->checked_in_count == $row->number_registrants)
		{
			$row->checked_in = 1;
		}
		$row->store();

		return 2;
	}

	/**
	 * Reset check-in status for the registration record
	 *
	 * @param $id
	 *
	 * @throws Exception
	 */
	public function resetCheckin($id)
	{
		/* @var EventbookingTableRegistrant $row */
		$row = $this->getTable();
		$row->load($id);

		if (empty($row))
		{
			throw new Exception(JText::sprintf('Error checkin registration record %s', $id));
		}

		$row->checked_in_count = 0;
		$row->checked_in       = 0;

		$row->store();
	}

	/**
	 * Store registrant tickets data when the record is created/updated in the backend
	 *
	 * @param JTable $row
	 * @param array  $data
	 */
	private function storeRegistrantTickets($row, $data)
	{
		$user  = JFactory::getUser();
		$event = EventbookingHelperDatabase::getEvent($row->event_id);
		if ($event->has_multiple_ticket_types && $user->authorise('eventbooking.registrantsmanagement', 'com_eventbooking'))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->delete('#__eb_registrant_tickets')
				->where('registrant_id = ' . $row->id);
			$db->setQuery($query)
				->execute();

			$ticketTypes       = EventbookingHelperData::getTicketTypes($row->event_id);
			$numberRegistrants = 0;
			foreach ($ticketTypes as $ticketType)
			{
				if (!empty($data['ticket_type_' . $ticketType->id]))
				{
					$quantity = (int) $data['ticket_type_' . $ticketType->id];
					$query->clear()
						->insert('#__eb_registrant_tickets')
						->columns('registrant_id, ticket_type_id, quantity')
						->values("$row->id, $ticketType->id, $quantity");
					$db->setQuery($query)
						->execute();

					$numberRegistrants += $quantity;
				}
			}

			$config = EventbookingHelper::getConfig();
			if ($config->calculate_number_registrants_base_on_tickets_quantity)
			{
				$query->clear('')
					->update('#__eb_registrants')
					->set('number_registrants = ' . $numberRegistrants)
					->where('id = ' . $row->id);
				$db->setQuery($query)
					->execute();
			}
		}
	}
}
