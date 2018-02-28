<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

class EventbookingControllerRegistrant extends RADControllerAdmin
{
	public function display($cachable = false, array $urlparams = array())
	{
		/*  @var JDocumentHtml $document */
		$document = JFactory::getDocument();
		$config   = EventbookingHelper::getConfig();

		// Always load jquery
		JHtml::_('jquery.framework');

		$rootUrl = JUri::root(true);

		if ($config->load_bootstrap_css_in_frontend !== '0')
		{
			$document->addStyleSheet($rootUrl . '/media/com_eventbooking/assets/bootstrap/css/bootstrap.css');
		}

		$document->addStyleSheet($rootUrl . '/media/com_eventbooking/assets/css/style.css');

		JHtml::_('script', EventbookingHelper::getURL() . 'media/com_eventbooking/assets/js/eventbookingjq.js', false, false);

		if (file_exists(JPATH_ROOT . '/media/com_eventbooking/assets/css/custom.css') && filesize(JPATH_ROOT . '/media/com_eventbooking/assets/css/custom.css') > 0)
		{
			$document->addStyleSheet($rootUrl . '/media/com_eventbooking/assets/css/custom.css');
		}

		parent::display($cachable, $urlparams);
	}

	/**
	 * Save the registration record and back to registration record list
	 */
	public function save()
	{
		parent::save();

		if ($return = $this->input->getBase64('return', ''))
		{
			$this->setRedirect(base64_decode($return));
		}
		else
		{
			$this->setRedirect(JRoute::_(EventbookingHelperRoute::getViewRoute('registrants', $this->input->getInt('Itemid')), false));
		}
	}

	/**
	 * Delete the selected registration record
	 */
	public function delete()
	{
		parent::delete();

		$this->setRedirect(JRoute::_(EventbookingHelperRoute::getViewRoute('registrants', $this->input->getInt('Itemid')), false));
	}

	/**
	 * Cancel registration for the event
	 */
	public function cancel()
	{
		$app              = JFactory::getApplication();
		$db               = JFactory::getDbo();
		$query            = $db->getQuery(true);
		$user             = JFactory::getUser();
		$Itemid           = $this->input->getInt('Itemid', 0);
		$id               = $this->input->getInt('id', 0);
		$registrationCode = $this->input->getString('cancel_code', '');
		$fieldSuffix      = EventbookingHelper::getFieldSuffix();

		if ($id)
		{
			$query->select('a.id, b.user_id, cancel_before_date, DATEDIFF(cancel_before_date, NOW()) AS number_days')
				->from('#__eb_events AS a')
				->innerJoin('#__eb_registrants AS b ON a.id = b.event_id')
				->where('b.id = ' . $id);
		}
		else
		{
			$query->select('a.id, b.id AS registrant_id, b.user_id, cancel_before_date, DATEDIFF(cancel_before_date, NOW()) AS number_days')
				->from('#__eb_events AS a')
				->innerJoin('#__eb_registrants AS b ON a.id = b.event_id')
				->where('b.registration_code = ' . $db->quote($registrationCode));
		}

		if ($fieldSuffix)
		{
			EventbookingHelperDatabase::getMultilingualFields($query, ['a.title'], $fieldSuffix);
		}
		else
		{
			$query->select('a.title');
		}

		$db->setQuery($query);
		$rowEvent = $db->loadObject();

		if (!$rowEvent)
		{
			$app->redirect(JRoute::_('index.php?option=com_eventbooking&Itemid=' . $Itemid), JText::_('EB_INVALID_ACTION'));
		}

		if (($user->get('id') == 0 && !$registrationCode) || ($user->get('id') != $rowEvent->user_id))
		{
			$app->redirect(JRoute::_('index.php?option=com_eventbooking&Itemid=' . $Itemid), JText::_('EB_INVALID_ACTION'));
		}

		if ($rowEvent->number_days < 0)
		{
			$msg = JText::sprintf('EB_CANCEL_DATE_PASSED', JHtml::_('date', $rowEvent->cancel_before_date, EventbookingHelper::getConfigValue('date_format'), null));
			$app->redirect(JRoute::_('index.php?option=com_eventbooking&Itemid=' . $Itemid), $msg);
		}

		if ($registrationCode)
		{
			$id = $rowEvent->registrant_id;
		}

		/* @var EventbookingModelRegister $model */
		$model = $this->getModel('register');
		$model->cancelRegistration($id);

		$this->setRedirect(JRoute::_('index.php?option=com_eventbooking&view=registrationcancel&id=' . $id . '&Itemid=' . $Itemid, false));
	}

	/**
	 * Cancel editing a registration record
	 */
	public function cancel_edit()
	{
		if ($return = $this->input->getBase64('return', ''))
		{
			$this->setRedirect(base64_decode($return));
		}
		else
		{
			$this->setRedirect(JRoute::_(EventbookingHelperRoute::getViewRoute('registrants', $this->input->getInt('Itemid')), false));
		}
	}

	/**
	 * Download invoice associated to the registration record
	 *
	 * @throws Exception
	 */
	public function download_invoice()
	{
		$user = JFactory::getUser();

		if (!$user->id)
		{
			JFactory::getApplication()->redirect('index.php', JText::_('You do not have permission to download the invoice'));
		}

		$id = $this->input->getInt('id', 0);
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_eventbooking/table');
		$row = JTable::getInstance('eventbooking', 'Registrant');
		$row->load($id);
		$canDownload = false;

		if ($row->user_id == $user->id)
		{
			$canDownload = true;
		}

		if (!$canDownload)
		{
			if ($user->authorise('eventbooking.registrantsmanagement', 'com_eventbooking'))
			{
				$config = EventbookingHelper::getConfig();

				if ($config->only_show_registrants_of_event_owner)
				{
					$db    = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->select('created_by')
						->from('#__eb_events')
						->where('id = ' . $row->event_id);
					$db->setQuery($query);
					$createdBy = $db->loadResult();

					if ($createdBy == $user->id)
					{
						$canDownload = true;
					}
				}
				else
				{
					$canDownload = true;
				}
			}
		}

		if (!$canDownload)
		{
			JFactory::getApplication()->redirect('index.php', JText::_('You do not have permission to download the invoice'));
		}

		EventbookingHelper::downloadInvoice($id);
	}

	/**
	 * Download certificate associated to the registration record
	 *
	 * @throws Exception
	 */
	public function download_certificate()
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/table/registrant.php';

		$row    = JTable::getInstance('registrant', 'EventbookingTable');
		$user   = JFactory::getUser();
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		$config = EventbookingHelper::getConfig();

		$downloadCode = $this->input->getString('download_code');

		if (!$user->id && empty($downloadCode))
		{
			throw new Exception(JText::_('You do not have permission to download the certificate'), 403);
		}

		if (!empty($downloadCode))
		{
			$query->select('id')
				->from('#__eb_registrants')
				->where('registration_code = ' . $db->quote($downloadCode));
			$db->setQuery($query);

			$id = (int) $db->loadResult();
		}
		else
		{
			$id = $this->input->getInt('id', 0);
		}

		if (!$row->load($id))
		{
			throw new Exception(JText::_('Invalid Registration Record'), 404);
		}

		if (empty($downloadCode) && $row->user_id != $user->id && $row->email != $user->get('email'))
		{
			throw new Exception(JText::_('You do not have permission to download the certificate'), 403);
		}

		if ($row->published == 0)
		{
			throw new Exception(JText::_('EB_CERTIFICATE_PAID_REGISTRANTS_ONLY'), 403);
		}

		if ($config->download_certificate_if_checked_in && !$row->checked_in)
		{
			throw new Exception(JText::_('EB_CERTIFICATE_CHECKED_IN_REGISTRANTS_ONLY'), 403);
		}

		// Compare current date with event end date
		$currentDate = EventbookingHelper::getServerTimeFromGMTTime();
		$query->clear()
			->select('*')
			->select("TIMESTAMPDIFF(MINUTE, event_end_date, '$currentDate') AS event_end_date_minutes")
			->from('#__eb_events')
			->where('id = ' . $row->event_id);
		$db->setQuery($query);
		$rowEvent = $db->loadObject();

		if ($rowEvent->activate_certificate_feature == 0 || ($rowEvent->activate_certificate_feature == 2 && !$config->activate_certificate_feature))
		{
			throw new Exception(printf('Certificate is not enabled for event %s', $rowEvent->title), 403);
		}

		if ($rowEvent->event_end_date_minutes < 0)
		{
			throw new Exception(JText::_('EB_CERTIFICATE_AFTER_EVENT_END_DATE'), 403);
		}

		EventbookingHelper::downloadCertificates(array($row), $config);
	}

	/**
	 * Download tickets associated to the registration record
	 *
	 * @throws Exception
	 */
	public function download_ticket()
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/table/registrant.php';

		$row    = JTable::getInstance('registrant', 'EventbookingTable');
		$user   = JFactory::getUser();
		$config = EventbookingHelper::getConfig();

		$downloadCode = $this->input->getString('download_code');

		if (!$user->id && empty($downloadCode))
		{
			throw new Exception(JText::_('You do not have permission to download the ticket'), 403);
		}

		if (!empty($downloadCode))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id')
				->from('#__eb_registrants')
				->where('registration_code = ' . $db->quote($downloadCode));
			$db->setQuery($query);

			$id = (int) $db->loadResult();
		}
		else
		{
			$id = $this->input->getInt('id', 0);
		}

		if (!$row->load($id))
		{
			throw new Exception(JText::_('Invalid Registration Record'), 404);
		}

		if (empty($downloadCode) && $row->user_id != $user->id && $row->email != $user->get('email'))
		{
			throw new Exception(JText::_('You do not have permission to download the ticket'), 403);
		}

		if ($row->published == 0 || $row->payment_status != 1)
		{
			throw new Exception(JText::_('Ticket is only allowed for confirmed/paid registrants'), 403);
		}

		// The person is allowed to download ticket, let process it
		EventbookingHelperTicket::generateTicketsPDF($row, $config);

		$fileName = 'ticket_' . str_pad($row->id, 5, '0', STR_PAD_LEFT) . '.pdf';
		$filePath = JPATH_ROOT . '/media/com_eventbooking/tickets/' . $fileName;

		while (@ob_end_clean()) ;
		EventbookingHelper::processDownload($filePath, $fileName);
	}

	/**
	 * Export registrants data into a csv file
	 */
	public function export()
	{
		$eventId = $this->input->getInt('event_id', $this->input->getInt('filter_event_id'));

		if (!EventbookingHelperAcl::canExportRegistrants($eventId))
		{
			JFactory::getApplication()->redirect('index.php', JText::_('EB_NOT_ALLOWED_TO_EXPORT'));
		}

		set_time_limit(0);
		$config = EventbookingHelper::getConfig();
		$model  = $this->getModel('registrants');

		/* @var EventbookingModelRegistrants $model */
		$model->setState('filter_event_id', $eventId)
			->setState('limitstart', 0)
			->setState('limit', 0)
			->setState('filter_order', 'tbl.id')
			->setState('filter_order_Dir', 'ASC');

		$rows = $model->getData();

		if (count($rows) == 0)
		{
			echo JText::_('There are no registrants to export');

			return;
		}

		$rowFields = EventbookingHelperRegistration::getAllEventFields($eventId);
		$fieldIds  = array();

		foreach ($rowFields as $rowField)
		{
			$fieldIds[] = $rowField->id;
		}

		$fieldValues = $model->getFieldsData($fieldIds);

		if (is_callable('EventbookingHelperOverrideData::prepareRegistrantsExportData'))
		{
			list($fields, $headers) = EventbookingHelperOverrideData::prepareRegistrantsExportData($rows, $config, $rowFields, $fieldValues, $eventId);
		}
		else
		{
			list($fields, $headers) = EventbookingHelperData::prepareRegistrantsExportData($rows, $config, $rowFields, $fieldValues, $eventId);
		}

		if (is_callable('EventbookingHelperOverrideData::excelExport'))
		{
			EventbookingHelperOverrideData::excelExport($fields, $rows, 'registrants_list', $headers);
		}
		else
		{
			EventbookingHelperData::excelExport($fields, $rows, 'registrants_list', $headers);
		}
	}

	/**
	 * Resend confirmation email to registrants in case they didn't receive it
	 */
	public function resend_email()
	{
		$this->csrfProtection();

		$cid = $this->input->get('cid', array(), 'array');
		$cid = ArrayHelper::toInteger($cid);

		/* @var EventbookingModelRegistrant $model */
		$model = $this->getModel();
		$ret   = true;

		foreach ($cid as $id)
		{
			$ret = $model->resendEmail($id);
		}

		if ($ret)
		{
			$this->setMessage(JText::_('EB_EMAIL_SUCCESSFULLY_RESENT'));
		}
		else
		{
			$this->setMessage(JText::_('EB_COULD_NOT_RESEND_EMAIL_TO_GROUP_MEMBER'), 'notice');
		}

		$this->setRedirect($this->getViewListUrl());
	}

	/**
	 * Checkin registrant from given ID
	 */
	public function checkin()
	{
		$user = JFactory::getUser();

		if ($user->authorise('eventbooking.registrantsmanagement', 'com_eventbooking'))
		{
			/* @var EventbookingModelRegistrant $model */
			$model  = $this->getModel();
			$id     = $this->input->getInt('id');
			$result = $model->checkin($id);

			switch ($result)
			{
				case 0:
					$message = JText::_('EB_INVALID_REGISTRATION_RECORD');
					break;
				case 1:
					$message = JText::_('EB_REGISTRANT_ALREADY_CHECKED_IN');
					break;
				case 2:
					$message = JText::_('EB_CHECKED_IN_SUCCESSFULLY');
					break;
			}

			$this->setRedirect(JRoute::_(EventbookingHelperRoute::getViewRoute('registrants', null)), $message);
		}
		else
		{
			throw new Exception('You do not have permission to checkin registrant', 403);
		}
	}

	/*
	 * Check in a registrant
	 */
	public function check_in_webapp()
	{
		JSession::checkToken('get');

		if (JFactory::getUser()->authorise('eventbooking.registrantsmanagement', 'com_eventbooking'))
		{
			$id = $this->input->getInt('id');

			/* @var EventbookingModelRegistrant $model */
			$model = $this->getModel();

			try
			{
				$model->checkin($id, true);
				$this->setMessage(JText::_('EB_CHECKIN_SUCCESSFULLY'));
			}
			catch (Exception $e)
			{
				$this->setMessage($e->getMessage(), 'error');
			}

			$this->setRedirect(JRoute::_(EventbookingHelperRoute::getViewRoute('registrants', null)));
		}
		else
		{
			throw new Exception('You do not have permission to checkin registrant', 403);
		}
	}

	/**
	 * Reset check in for a registrant
	 */
	public function reset_check_in()
	{
		JSession::checkToken('get');

		if (JFactory::getUser()->authorise('eventbooking.registrantsmanagement', 'com_eventbooking'))
		{
			$id = $this->input->getInt('id');

			/* @var EventbookingModelRegistrant $model */
			$model = $this->getModel();

			try
			{
				$model->resetCheckin($id);
				$this->setMessage(JText::_('EB_RESET_CHECKIN_SUCCESSFULLY'));
			}
			catch (Exception $e)
			{
				$this->setMessage($e->getMessage(), 'error');
			}

			$this->setRedirect(JRoute::_(EventbookingHelperRoute::getViewRoute('registrants', null)));
		}
		else
		{
			throw new Exception('You do not have permission to checkin registrant', 403);
		}
	}

	/**
	 * Get url of the page which display list of records
	 *
	 * @return string
	 */
	protected function getViewListUrl()
	{
		$url = 'index.php?option=com_eventbooking&view=registrants&Itemid=' . $this->input->getInt('Itemid', EventbookingHelperRoute::findView('registrants', 0));

		return JRoute::_($url);
	}

	/**
	 * Get url of the page which allow adding/editing a record
	 *
	 * @param int $recordId
	 *
	 * @return string
	 */
	protected function getViewItemUrl($recordId = null)
	{
		$url = 'index.php?option=' . $this->option . '&view=' . $this->viewItem;

		if ($recordId)
		{
			$url .= '&id=' . $recordId;
		}

		$url .= '&Itemid=' . $this->input->getInt('Itemid', EventbookingHelperRoute::findView('registrants', 0));

		return JRoute::_($url);
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array $data An array of input data.
	 *
	 * @return  boolean
	 */
	protected function allowAdd($data = array())
	{
		if (!JFactory::getUser()->authorise('eventbooking.registrantsmanagement', 'com_eventbooking'))
		{
			return false;
		}

		return parent::allowAdd($data);
	}

	/**
	 * Method to check if you can edit a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array  $data An array of input data.
	 * @param   string $key  The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		$user = JFactory::getUser();

		if ($user->authorise('eventbooking.registrantsmanagement', 'com_eventbooking'))
		{
			return true;
		}

		if (!empty($data['id']))
		{
			$user  = JFactory::getUser();
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select('COUNT(*)')
				->from('#__eb_registrants')
				->where('id = ' . (int) $data['id'])
				->where('(user_id = ' . $user->get('id') . ' OR email = ' . $db->quote($user->get('email')) . ')');
			$db->setQuery($query);

			$total = $db->loadResult();

			if ($total)
			{
				return true;
			}
		}

		return parent::allowEdit($data, $key);
	}

	/**
	 * Method to check whether the current user is allowed to delete a record
	 *
	 * @param   int $id Record ID
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission for the component.
	 */
	protected function allowDelete($id)
	{
		return EventbookingHelperAcl::canDeleteRegistrant($id);
	}

	/**
	 * Method to check whether the current user can change status (publish, unpublish of a record)
	 *
	 * @param   int $id Id of the record
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
	 */
	protected function allowEditState($id)
	{
		if (!JFactory::getUser()->authorise('eventbooking.registrantsmanagement', 'com_eventbooking'))
		{
			return false;
		}

		return parent::allowEditState($id);
	}
}
