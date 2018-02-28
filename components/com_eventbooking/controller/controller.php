<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class EventbookingController extends RADController
{
	/**
	 * Display information
	 */
	public function display($cachable = false, array $urlparams = array())
	{
		$task     = $this->getTask();
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

		if ($config->multiple_booking)
		{
			$document->addStyleSheet($rootUrl . '/media/com_eventbooking/assets/css/font-awesome.css');
		}

		JHtml::_('script', 'media/com_eventbooking/assets/js/eventbookingjq.js', false, false);

		if ($config->calendar_theme)
		{
			$theme = $config->calendar_theme;
		}
		else
		{
			$theme = 'default';
		}

		$document->addStyleSheet($rootUrl . '/media/com_eventbooking/assets/css/themes/' . $theme . '.css');

		$customCssFile = JPATH_ROOT . '/media/com_eventbooking/assets/css/custom.css';

		if (file_exists($customCssFile) && filesize($customCssFile) > 0)
		{
			$document->addStyleSheet($rootUrl . '/media/com_eventbooking/assets/css/custom.css');
		}

		switch ($task)
		{
			case 'view_category':
				$this->input->set('view', 'category');
				break;
			case 'group_registration':
				$this->input->set('view', 'register');
				$this->input->set('layout', 'group');
				break;
			case 'cancel':
				$this->input->set('view', 'cancel');
				$this->input->set('layout', 'default');
				break;
			#Cart function
			case 'view_cart':
				$this->input->set('view', 'cart');
				$this->input->set('layout', 'default');
				break;
			case 'view_checkout':
			case 'checkout':
				$this->input->set('view', 'register');
				$this->input->set('layout', 'cart');
				break;
			default:
				$view = $this->input->getCmd('view');

				if (!$view)
				{
					$this->input->set('view', 'categories');
					$this->input->set('layout', 'default');
				}

				break;
		}

		parent::display($cachable, $urlparams);
	}


	/**
	 * Process download a file
	 */
	public function download_file()
	{
		$filePath = JPATH_ROOT . '/media/com_eventbooking/files';
		$fileName = basename($this->input->getString('file_name'));

		if (file_exists($filePath . '/' . $fileName))
		{
			while (@ob_end_clean()) ;
			EventbookingHelper::processDownload($filePath . '/' . $fileName, $fileName, true);

			$this->app->close();
		}
		else
		{
			$this->app->redirect('index.php?option=com_eventbooking&Itemid=' . $this->input->getInt('Itemid'), JText::_('File does not exist'));
		}
	}

	/***
	 * Get search parameters from search module and performing redirect
	 */
	public function search()
	{
		$categoryId = $this->input->getInt('category_id', 0);
		$locationId = $this->input->getInt('location_id', 0);
		$Itemid     = $this->input->getInt('Itemid', 0);
		$search     = $this->input->getString('search', '');
		$layout     = $this->input->getCmd('layout', '');

		$url = 'index.php?option=com_eventbooking&view=search';

		if ($categoryId)
		{
			$url .= '&category_id=' . $categoryId;
		}

		if ($locationId)
		{
			$url .= '&location_id=' . $locationId;
		}

		if ($search)
		{
			$url .= '&search=' . $search;
		}

		if ($layout && ($layout != 'default'))
		{
			$url .= '&layout=' . $layout;
		}

		$url .= '&Itemid=' . $Itemid;

		$this->app->redirect(JRoute::_($url, false, 0));
	}

	/**
	 * Validate the username, make sure it has not been registered by someone else
	 */
	public function validate_username()
	{
		$db         = JFactory::getDbo();
		$query      = $db->getQuery(true);
		$username   = $this->input->getUsername('fieldValue', '');
		$validateId = $this->input->get('fieldId', '', 'none');

		$query->select('COUNT(*)')
			->from('#__users')
			->where('username=' . $db->quote($username));
		$db->setQuery($query);
		$total        = $db->loadResult();
		$arrayToJs    = array();
		$arrayToJs[0] = $validateId;

		if ($total)
		{
			$arrayToJs[1] = false;
		}
		else
		{
			$arrayToJs[1] = true;
		}

		echo json_encode($arrayToJs);

		$this->app->close();
	}

	/**
	 * Validate the email
	 */
	public function validate_email()
	{
		$db           = JFactory::getDbo();
		$user         = JFactory::getUser();
		$config       = EventbookingHelper::getConfig();
		$query        = $db->getQuery(true);
		$email        = $this->input->get('fieldValue', '', 'string');
		$eventId      = $this->input->getInt('event_id', 0);
		$validateId   = $this->input->get('fieldId', '', 'none');
		$arrayToJs    = array();
		$arrayToJs[0] = $validateId;

		if (!$config->multiple_booking)
		{
			$event = EventbookingHelperDatabase::getEvent($eventId);

			if ($event->prevent_duplicate_registration === '')
			{
				$preventDuplicateRegistration = $config->prevent_duplicate_registration;
			}
			else
			{
				$preventDuplicateRegistration = $event->prevent_duplicate_registration;
			}

			if ($preventDuplicateRegistration)
			{
				$query->select('COUNT(id)')
					->from('#__eb_registrants')
					->where('event_id = ' . $eventId)
					->where('email = ' . $db->quote($email))
					->where('(published=1 OR (payment_method LIKE "os_offline%" AND published NOT IN (2,3)))');
				$db->setQuery($query);
				$total = $db->loadResult();

				if ($total)
				{
					$arrayToJs[1] = false;
					$arrayToJs[2] = JText::_('EB_EMAIL_REGISTER_FOR_EVENT_ALREADY');
				}
			}
		}

		if (!isset($arrayToJs[1]))
		{
			$query->clear()
				->select('COUNT(*)')
				->from('#__users')
				->where('email = ' . $db->quote($email));
			$db->setQuery($query);
			$total = $db->loadResult();

			if (!$total || $user->id || !$config->user_registration)
			{
				$arrayToJs[1] = true;
			}
			else
			{
				$arrayToJs[1] = false;
				$arrayToJs[2] = JText::_('EB_EMAIL_USED_BY_OTHER_CUSTOMER');
			}
		}

		echo json_encode($arrayToJs);

		$this->app->close();
	}

	/**
	 * Get list of states for the selected country, using in AJAX request
	 */
	public function get_states()
	{
		$countryName = $this->input->getString('country_name', '');
		$fieldName   = $this->input->getString('field_name', 'state');
		$stateName   = $this->input->getString('state_name', '');

		if (!$countryName)
		{
			$config      = EventbookingHelper::getConfig();
			$countryName = $config->default_country;
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('required')
			->from('#__eb_fields')
			->where('name=' . $db->quote('state'));
		$db->setQuery($query);
		$required = $db->loadResult();
		($required) ? $class = 'validate[required]' : $class = '';

		$query->clear()
			->select('country_id')
			->from('#__eb_countries')
			->where('name=' . $db->quote($countryName));
		$db->setQuery($query);
		$countryId = $db->loadResult();

		//get states
		$query->clear()
			->select('state_name AS value, state_name AS text')
			->from('#__eb_states')
			->where('country_id=' . (int) $countryId);
		$db->setQuery($query);
		$states  = $db->loadObjectList();
		$options = array();

		if (count($states))
		{
			$options[] = JHtml::_('select.option', '', JText::_('EB_SELECT_STATE'));
			$options   = array_merge($options, $states);
		}
		else
		{
			$options[] = JHtml::_('select.option', 'N/A', JText::_('EB_NA'));
		}

		echo JHtml::_('select.genericlist', $options, $fieldName, ' class="input-large ' . $class . '" id="' . $fieldName . '"', 'value', 'text',
			$stateName);

		$this->app->close();
	}

	/**
	 * Get depend fields status
	 */
	public function get_depend_fields_status()
	{
		$input          = $this->input;
		$db             = JFactory::getDbo();
		$query          = $db->getQuery(true);
		$fieldId        = $input->getInt('field_id', 0);
		$fieldSuffix    = $input->getString('field_suffix', '');
		$languageSuffix = EventbookingHelper::getFieldSuffix();

		//Get list of depend fields
		$allFieldIds = EventbookingHelper::getAllDependencyFields($fieldId);

		$query->select('*')
			->from('#__eb_fields')
			->where('published=1')
			->where('id IN (' . implode(',', $allFieldIds) . ')')
			->order('ordering');

		if ($languageSuffix)
		{
			EventbookingHelperDatabase::getMultilingualFields($query, ['title', 'depend_on_options'], $languageSuffix);
		}

		$db->setQuery($query);
		$rowFields    = $db->loadObjectList();
		$masterFields = array();
		$fieldsAssoc  = array();

		foreach ($rowFields as $rowField)
		{
			if ($rowField->depend_on_field_id)
			{
				$masterFields[] = $rowField->depend_on_field_id;
			}

			$fieldsAssoc[$rowField->id] = $rowField;
		}

		$masterFields = array_unique($masterFields);

		if (count($masterFields))
		{
			$hiddenFields = array();

			foreach ($rowFields as $rowField)
			{
				if ($rowField->depend_on_field_id && isset($fieldsAssoc[$rowField->depend_on_field_id]))
				{
					// If master field is hided, then children field should be hided, too
					if (in_array($rowField->depend_on_field_id, $hiddenFields))
					{
						$hiddenFields[] = $rowField->id;
					}
					else
					{
						if ($fieldSuffix)
						{
							$fieldName = $fieldsAssoc[$rowField->depend_on_field_id]->name . '_' . $fieldSuffix;
						}
						else
						{
							$fieldName = $fieldsAssoc[$rowField->depend_on_field_id]->name;
						}

						$masterFieldValues = $input->get($fieldName, '', 'none');

						if (is_array($masterFieldValues))
						{
							$selectedOptions = $masterFieldValues;
						}
						else
						{
							$selectedOptions = array($masterFieldValues);
						}

						$dependOnOptions = json_decode($rowField->depend_on_options);

						if (!count(array_intersect($selectedOptions, $dependOnOptions)))
						{
							$hiddenFields[] = $rowField->id;
						}
					}
				}
			}
		}

		$showFields = array();
		$hideFields = array();

		foreach ($rowFields as $rowField)
		{
			if (in_array($rowField->id, $hiddenFields))
			{
				$hideFields[] = 'field_' . $rowField->name . ($fieldSuffix ? '_' . $fieldSuffix : '');
			}
			else
			{
				$showFields[] = 'field_' . $rowField->name . ($fieldSuffix ? '_' . $fieldSuffix : '');
			}
		}

		echo json_encode(array('show_fields' => implode(',', $showFields), 'hide_fields' => implode(',', $hideFields)));

		$this->app->close();
	}

	/**
	 * Confirm the payment. Used for Paypal base payment gateway
	 */
	public function payment_confirm()
	{
		/* @var EventBookingModelRegister $model */
		$model         = $this->getModel('Register');
		$paymentMethod = $this->input->getString('payment_method');
		$model->paymentConfirm($paymentMethod);
	}

	/**
	 * Process upload file
	 */
	public function upload_file()
	{
		jimport('joomla.filesystem.folder');

		$config     = EventbookingHelper::getConfig();
		$json       = array();
		$pathUpload = JPATH_ROOT . '/media/com_eventbooking/files';

		if (!JFolder::exists($pathUpload))
		{
			JFolder::create($pathUpload);
		}

		$allowedExtensions = $config->attachment_file_types;

		if (!$allowedExtensions)
		{
			$allowedExtensions = 'doc|docx|ppt|pptx|pdf|zip|rar|bmp|gif|jpg|jepg|png|swf|zipx';
		}

		$allowedExtensions = explode('|', $allowedExtensions);
		$allowedExtensions = array_map('trim', $allowedExtensions);

		$file     = $this->input->files->get('file', array(), 'raw');
		$fileName = $file['name'];
		$fileExt  = JFile::getExt($fileName);

		if (in_array(strtolower($fileExt), $allowedExtensions))
		{
			$fileName = JFile::makeSafe($fileName);

			if (JFile::exists($pathUpload . '/' . $fileName))
			{
				$targetFileName = time() . '_' . $fileName;
			}
			else
			{
				$targetFileName = $fileName;
			}

			JFile::upload($file['tmp_name'], $pathUpload . '/' . $targetFileName, false, true);

			$json['success'] = JText::sprintf('EB_FILE_UPLOADED', $fileName);
			$json['file']    = $targetFileName;
		}
		else
		{
			$json['error'] = JText::sprintf('EB_FILE_NOT_ALLOWED', $fileExt, implode(', ', $allowedExtensions));
		}

		echo json_encode($json);

		$this->app->close();
	}

	/**
	 * Get profile data of the registrant, return JSON format using for ajax request
	 */
	public function get_profile_data()
	{
		$config  = EventbookingHelper::getConfig();
		$input   = JFactory::getApplication()->input;
		$userId  = $input->getInt('user_id', 0);
		$eventId = $input->getInt('event_id');
		$data    = array();

		if ($userId && $eventId)
		{
			$rowFields = EventbookingHelperRegistration::getFormFields($eventId, 0);
			$data      = EventbookingHelperRegistration::getFormData($rowFields, $eventId, $userId, $config);
		}

		if ($userId && !isset($data['first_name']))
		{
			//Load the name from Joomla default name
			$user = JFactory::getUser($userId);
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
			if (empty($user))
			{
				$user = JFactory::getUser($userId);
			}

			$data['email'] = $user->email;
		}

		echo json_encode($data);

		$this->app->close();
	}
}
