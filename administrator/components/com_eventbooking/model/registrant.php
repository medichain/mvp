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

class EventbookingModelRegistrant extends EventbookingModelCommonRegistrant
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
	 * Send batch emails to selected registrants
	 *
	 * @param RADInput $input
	 *
	 * @throws Exception
	 */
	public function batchMail($input)
	{
		$cid          = $input->get('cid', array(), 'array');
		$emailSubject = $input->getString('subject');
		$emailMessage = $input->get('message', '', 'raw');


		if (empty($cid))
		{
			throw new Exception('Please select registrants to send mass mail');
		}

		if (empty($emailSubject))
		{
			throw new Exception('Please enter subject of the email');
		}

		if (empty($emailMessage))
		{
			throw new Exception('Please enter message ofthe email');
		}

		// OK, data is valid, process sending email
		$mailer  = JFactory::getMailer();
		$config  = EventbookingHelper::getConfig();
		$siteUrl = EventbookingHelper::getSiteUrl();
		$db      = JFactory::getDbo();
		$query   = $db->getQuery(true);

		// Upload file
		$attachment = $input->files->get('attachment', null, 'raw');

		if ($attachment['name'])
		{
			$allowedExtensions = $config->attachment_file_types;

			if (!$allowedExtensions)
			{
				$allowedExtensions = 'doc|docx|ppt|pptx|pdf|zip|rar|bmp|gif|jpg|jepg|png|swf|zipx';
			}

			$allowedExtensions = explode('|', $allowedExtensions);
			$allowedExtensions = array_map('trim', $allowedExtensions);
			$allowedExtensions = array_map('strtolower', $allowedExtensions);
			$fileName          = $attachment['name'];
			$fileExt           = JFile::getExt($fileName);

			if (in_array(strtolower($fileExt), $allowedExtensions))
			{
				$fileName = JFile::makeSafe($fileName);
				$mailer->addAttachment($attachment['tmp_name'], $fileName);
			}
			else
			{
				throw new Exception(JText::sprintf('Attachment file type %s is not allowed', $fileExt));
			}
		}

		if ($config->from_name)
		{
			$fromName = $config->from_name;
		}
		else
		{
			$fromName = JFactory::getConfig()->get('fromname');
		}

		if ($config->from_email)
		{
			$fromEmail = $config->from_email;
		}
		else
		{
			$fromEmail = JFactory::getConfig()->get('mailfrom');
		}

		// Get list of registration records
		$query->select('a.*, b.title, b.event_date, b.event_end_date, b.short_description, b.description')
			->from('#__eb_registrants AS a')
			->innerJoin('#__eb_events AS b ON a.event_id = b.id')
			->where('a.id IN (' . implode(',', $cid) . ')');
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		// Get list of core fields		
		$query->clear()
			->select('name')
			->from('#__eb_fields')
			->where('is_core = 1');
		$db->setQuery($query);
		$fields = $db->loadObjectList();

		$emails = array();

		foreach ($rows as $row)
		{
			$subject = $emailSubject;
			$message = $emailMessage;
			$email   = $row->email;

			if (!in_array($email, $emails))
			{
				$downloadCertificateLink = $siteUrl . 'index.php?option=com_eventbooking&task=registrant.download_certificate&download_code=' . $row->registration_code;

				$replaces = array();

				$replaces['event_title']               = $row->title;
				$replaces['event_date']                = JHtml::_('date', $row->event_date, $config->event_date_format, null);
				$replaces['event_end_date']            = JHtml::_('date', $row->event_end_date, $config->event_date_format, null);
				$replaces['short_description']         = $row->short_description;
				$replaces['description']               = $row->description;
				$replaces['first_name']                = $row->first_name;
				$replaces['DOWNLOAD_CERTIFICATE_LINK'] = $downloadCertificateLink;

				foreach ($replaces as $key => $value)
				{
					$key     = strtoupper($key);
					$subject = str_ireplace("[$key]", $value, $subject);
					$message = str_ireplace("[$key]", $value, $message);
				}

				foreach ($fields as $field)
				{
					$key     = $field->name;
					$value   = $row->{$field->name};
					$subject = str_ireplace("[$key]", $value, $subject);
					$message = str_ireplace("[$key]", $value, $message);
				}

				// Process [REGISTRATION_DETAIL] tag if it is used in the message
				if (strpos($message, '[REGISTRATION_DETAIL]') !== false)
				{
					// Build this tag
					if ($config->multiple_booking)
					{
						$rowFields = EventbookingHelperRegistration::getFormFields($row->id, 4);
					}
					elseif ($row->is_group_billing)
					{
						$rowFields = EventbookingHelperRegistration::getFormFields($row->event_id, 1);
					}
					else
					{
						$rowFields = EventbookingHelperRegistration::getFormFields($row->event_id, 0);
					}

					$form = new RADForm($rowFields);
					$data = EventbookingHelperRegistration::getRegistrantData($row, $rowFields);
					$form->bind($data);
					$form->buildFieldsDependency();
					$registrationDetail = EventbookingHelperRegistration::getEmailContent($config, $row, true, $form);
					$message            = str_replace("[REGISTRATION_DETAIL]", $registrationDetail, $message);
				}

				if (strpos($message, '[QRCODE]') !== false)
				{
					EventbookingHelper::generateQrcode($row->id);
					$imgTag  = '<img src="' . $siteUrl . 'media/com_eventbooking/qrcodes/' . $row->id . '.png" border="0" />';
					$message = str_ireplace("[QRCODE]", $imgTag, $message);
				}

				if (JMailHelper::isEmailAddress($email))
				{
					$emails[] = $email;
					$mailer->sendMail($fromEmail, $fromName, $email, $subject, $message, 1);
					$mailer->clearAllRecipients();
				}
			}
		}
	}

	/**
	 * @param $file
	 *
	 * @return int
	 * @throws Exception
	 */
	public function import($file)
	{
		$config      = EventbookingHelper::getConfig();
		$registrants = EventbookingHelperData::getDataFromFile($file);

		$imported = 0;
		$todayDate = JFactory::getDate()->toSql();

		if (count($registrants))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('name, title')
				->from('#__eb_payment_plugins');
			$db->setQuery($query);
			$plugins = $db->loadObjectList('title');

			foreach ($registrants as $registrant)
			{
				if (empty($registrant['event_id']))
				{
					continue;
				}

				/* @var EventbookingTableRegistrant $row */
				$row = $this->getTable();

				if (!empty($registrant['id']))
				{
					$row->load($registrant['id']);
				}

				if ($registrant['register_date'])
				{
					try
					{
						$registerDate                = DateTime::createFromFormat($config->date_format, $registrant['register_date']);
						$registrant['register_date'] = $registerDate->format('Y=m-d');
					}
					catch (Exception $e)
					{
						$registrant['register_date'] = $todayDate;
					}
				}
				else
				{
					$registrant ['register_date'] = $todayDate;
				}

				if ($registrant['payment_method'] && isset($plugins[$registrant['payment_method']]))
				{
					$registrant['payment_method'] = $plugins[$registrant['payment_method']]->name;
				}

				$row->bind($registrant);

				if ($row->number_registrants > 1)
				{
					$row->is_group_billing = 1;
				}

				$row->store();

				$registrantId = $row->id;

				$fields = self::getEventFields($row->event_id, $config);

				if (count($fields))
				{
					$query->clear()
						->delete('#__eb_field_values')
						->where('registrant_id = ' . $registrantId);
					$db->setQuery($query);
					$db->execute();

					foreach ($fields as $fieldName => $field)
					{
						$fieldValue = isset($registrant[$fieldName]) ? $registrant[$fieldName] : '';
						$fieldId    = $field->id;

						if ($field->fieldtype == 'Checkboxes' || $field->multiple)
						{
							$fieldValue = json_encode(explode(', ', $fieldValue));
						}

						$query->clear()
							->insert('#__eb_field_values')
							->columns('registrant_id, field_id, field_value')
							->values("$registrantId, $fieldId, " . $db->quote($fieldValue));
						$db->setQuery($query);
						$db->execute();
					}
				}

				$imported++;
			}
		}

		return $imported;
	}

	/**
	 * Get all custom fields of the given event
	 *
	 * @param int $eventId
	 *
	 * @pram RADConfig $config
	 *
	 * @return array
	 */
	public static function getEventFields($eventId, $config)
	{
		static $fields;

		if (!isset($fields[$eventId]))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id, name, fieldtype')
				->from('#__eb_fields')
				->where('is_core = 0')
				->where('published = 1');

			if ($config->custom_field_by_category)
			{
				//Get main category of the event
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
				$query->where(' (event_id = -1 OR id IN (SELECT field_id FROM #__eb_field_events WHERE event_id=' . $eventId . '))');
			}

			$db->setQuery($query);
			$fields[$eventId] = $db->loadObjectList('name');
		}

		return $fields[$eventId];
	}
}
