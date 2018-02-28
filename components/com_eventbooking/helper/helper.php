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

class EventbookingHelper
{
	/**
	 * Return the current installed version
	 */
	public static function getInstalledVersion()
	{
		return '3.1.2';
	}

	/**
	 * Method to get user time from GMT time
	 *
	 * @param string $time
	 * @param string $format
	 *
	 * @return string
	 */
	public static function getUserTimeFromGMTTime($time = 'now', $format = 'Y-m-d H:i:s')
	{
		$gmtTz  = new DateTimeZone('GMT');
		$userTz = new DateTimeZone(JFactory::getUser()->getParam('timezone', \JFactory::getApplication()->get('offset', 'GMT')));
		$date   = new DateTime($time, $gmtTz);
		$date->setTimezone($userTz);

		return $date->format($format);
	}

	/**
	 * Method to get server time from GMT time
	 *
	 * @param string $time
	 * @param string $format
	 *
	 * @return string
	 */
	public static function getServerTimeFromGMTTime($time = 'now', $format = 'Y-m-d H:i:s')
	{
		$gmtTz  = new DateTimeZone('GMT');
		$userTz = new DateTimeZone(JFactory::getApplication()->get('offset', 'GMT'));
		$date   = new DateTime($time, $gmtTz);
		$date->setTimezone($userTz);

		return $date->format($format);
	}

	/**
	 * Check if a method is overrided in a child class
	 *
	 * @param $class
	 * @param $method
	 *
	 * @return bool
	 */
	public static function isMethodOverridden($class, $method)
	{
		if (class_exists($class) && method_exists($class, $method))
		{
			$reflectionMethod = new ReflectionMethod($class, $method);

			if ($reflectionMethod->getDeclaringClass()->getName() == $class)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Get configuration data and store in config object
	 *
	 * @return RADConfig
	 */
	public static function getConfig()
	{
		static $config;

		if (!$config)
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/config/config.php';
			$config = new RADConfig('#__eb_configs');

			if ($config->show_event_date)
			{
				$config->set('sort_events_dropdown', 'event_date, title');
			}
			else
			{
				$config->set('sort_events_dropdown', 'title');
			}

			// Make sure some important config data has value
			if (!$config->thumb_width)
			{
				$config->set('thumb_width', 200);
			}

			if (!$config->thumb_height)
			{
				$config->set('thumb_height', 200);
			}
		}


		return $config;
	}

	/**
	 * Get specify config value
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	public static function getConfigValue($key, $default = null)
	{
		$config = self::getConfig();

		return $config->get($key, $default);
	}

	/**
	 * Get component settings from json config file
	 *
	 * @param $appName
	 *
	 * @return array
	 */
	public static function getComponentSettings($appName)
	{
		$settings = json_decode(file_get_contents(JPATH_ADMINISTRATOR . '/components/com_eventbooking/config.json'), true);

		return $settings[strtolower($appName)];
	}

	/**
	 * Check to see whether the return value is a valid date format
	 *
	 * @param $value
	 *
	 * @return bool
	 */
	public static function isValidDate($value)
	{
		// basic date format yyyy-mm-dd
		$expr = '/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/D';

		return preg_match($expr, $value, $match) && checkdate($match[2], $match[3], $match[1]);
	}

	/**
	 * Get the device type (desktop, tablet, mobile) accessing the extension
	 *
	 * @return string
	 */
	public static function getDeviceType()
	{
		$session    = JFactory::getSession();
		$deviceType = $session->get('eb_device_type');

		// If no data found from session, using mobile detect class to detect the device type
		if (!$deviceType)
		{
			if (!class_exists('EB_Mobile_Detect'))
			{
				require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/vendor/serbanghita/Mobile_Detect.php';
			}

			$mobileDetect = new EB_Mobile_Detect();
			$deviceType   = 'desktop';

			if ($mobileDetect->isMobile())
			{
				$deviceType = 'mobile';
			}

			if ($mobileDetect->isTablet())
			{
				$deviceType = 'tablet';
			}

			// Store the device type into session so that we don't have to find it for next request
			$session->set('eb_device_type', $deviceType);
		}

		return $deviceType;
	}

	/**
	 * Get page params of the given view
	 *
	 * @param $active
	 * @param $views
	 *
	 * @return Registry
	 */
	public static function getViewParams($active, $views)
	{
		if ($active && isset($active->query['view']) && in_array($active->query['view'], $views))
		{
			return $active->params;
		}

		return new Registry();
	}

	/**
	 * Apply some fixes for request data
	 *
	 * @return void
	 */
	public static function prepareRequestData()
	{
		//Remove cookie vars from request data
		$cookieVars = array_keys($_COOKIE);

		if (count($cookieVars))
		{
			foreach ($cookieVars as $key)
			{
				if (!isset($_POST[$key]) && !isset($_GET[$key]))
				{
					unset($_REQUEST[$key]);
				}
			}
		}

		if (isset($_REQUEST['start']) && !isset($_REQUEST['limitstart']))
		{
			$_REQUEST['limitstart'] = $_REQUEST['start'];
		}

		if (!isset($_REQUEST['limitstart']))
		{
			$_REQUEST['limitstart'] = 0;
		}

		// Fix PayPal IPN sending to wrong URL
		if (!empty($_POST['txn_type']) && empty($_REQUEST['task']) && empty($_REQUEST['view']))
		{
			$_REQUEST['task']           = 'payment_confirm';
			$_REQUEST['payment_method'] = 'os_paypal';
		}
	}

	/**
	 * Get the email messages used for sending emails or displaying in the form
	 *
	 * @return RADConfig
	 */
	public static function getMessages()
	{
		static $message;

		if (!$message)
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/config/config.php';

			$message = new RADConfig('#__eb_messages', 'message_key', 'message');
		}

		return $message;
	}

	/**
	 * Get field suffix used in sql query
	 *
	 * @param null $activeLanguage
	 *
	 * @return string
	 */
	public static function getFieldSuffix($activeLanguage = null)
	{
		$prefix = '';

		if (JLanguageMultilang::isEnabled())
		{
			if (!$activeLanguage)
			{
				$activeLanguage = JFactory::getLanguage()->getTag();
			}

			if ($activeLanguage != self::getDefaultLanguage())
			{
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('`sef`')
					->from('#__languages')
					->where('lang_code = ' . $db->quote($activeLanguage))
					->where('published = 1');
				$db->setQuery($query);
				$sef = $db->loadResult();

				if ($sef)
				{
					$prefix = '_' . $sef;
				}
			}
		}

		return $prefix;
	}

	/**
	 * Get list of language uses on the site
	 *
	 * @return array
	 */
	public static function getLanguages()
	{
		$db      = JFactory::getDbo();
		$query   = $db->getQuery(true);
		$default = self::getDefaultLanguage();
		$query->select('lang_id, lang_code, title, `sef`')
			->from('#__languages')
			->where('published = 1')
			->where('lang_code != ' . $db->quote($default))
			->order('ordering');
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Get front-end default language
	 *
	 * @return string
	 */
	public static function getDefaultLanguage()
	{
		$params = JComponentHelper::getParams('com_languages');

		return $params->get('site', 'en-GB');
	}

	/**
	 * Get sef of current language
	 *
	 * @return mixed
	 */
	public static function addLangLinkForAjax()
	{
		$langLink = '';

		if (JLanguageMultilang::isEnabled())
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$tag   = JFactory::getLanguage()->getTag();
			$query->select('`sef`')
				->from('#__languages')
				->where('published = 1')
				->where('lang_code=' . $db->quote($tag));
			$db->setQuery($query, 0, 1);
			$langLink = '&lang=' . $db->loadResult();
		}

		JFactory::getDocument()->addScriptDeclaration(
			'var langLinkForAjax="' . $langLink . '";'
		);
	}

	/**
	 * This function is used to check to see whether we need to update the database to support multilingual or not
	 *
	 * @return boolean
	 */
	public static function isSynchronized()
	{
		$db             = JFactory::getDbo();
		$fields         = array_keys($db->getTableColumns('#__eb_categories'));
		$extraLanguages = self::getLanguages();

		if (count($extraLanguages))
		{
			foreach ($extraLanguages as $extraLanguage)
			{
				$prefix = $extraLanguage->sef;

				if (!in_array('name_' . $prefix, $fields))
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Convert payment amount to USD currency in case the currency is not supported by the payment gateway
	 *
	 * @param $amount
	 * @param $currency
	 *
	 * @return float
	 */
	public static function convertAmountToUSD($amount, $currency)
	{
		$http     = JHttpFactory::getHttp();
		$url      = 'http://download.finance.yahoo.com/d/quotes.csv?e=.csv&f=sl1d1t1&s=USD' . $currency . '=X';
		$response = $http->get($url);

		if ($response->code == 200)
		{
			$currencyData = explode(',', $response->body);
			$rate         = floatval($currencyData[1]);

			if ($rate > 0)
			{
				$amount = $amount / $rate;
			}
		}

		return round($amount, 2);
	}

	/**
	 * Synchronize Events Booking database to support multilingual
	 */
	public static function setupMultilingual()
	{
		$db        = JFactory::getDbo();
		$languages = self::getLanguages();

		if (count($languages))
		{
			$categoryTableFields = array_keys($db->getTableColumns('#__eb_categories'));
			$eventTableFields    = array_keys($db->getTableColumns('#__eb_events'));
			$fieldTableFields    = array_keys($db->getTableColumns('#__eb_fields'));
			$locationTableFields = array_keys($db->getTableColumns('#__eb_locations'));

			foreach ($languages as $language)
			{
				$prefix = $language->sef;

				$varcharFields = array(
					'name',
					'alias',
					'page_title',
					'page_heading',
					'meta_keywords',
					'meta_description'
				);

				foreach ($varcharFields as $varcharField)
				{
					$fieldName = $varcharField . '_' . $prefix;

					if (!in_array($fieldName, $categoryTableFields))
					{
						$sql = "ALTER TABLE  `#__eb_categories` ADD  `$fieldName` VARCHAR( 255 );";
						$db->setQuery($sql);
						$db->execute();
					}
				}

				$fieldName = 'description_' . $prefix;

				if (!in_array($fieldName, $categoryTableFields))
				{
					$sql = "ALTER TABLE  `#__eb_categories` ADD  `$fieldName` TEXT NULL;";
					$db->setQuery($sql);
					$db->execute();
				}

				$varcharFields = array(
					'title',
					'alias',
					'page_title',
					'page_heading',
					'meta_keywords',
					'meta_description',
					'price_text',
					'registration_handle_url',
				);

				foreach ($varcharFields as $varcharField)
				{
					$fieldName = $varcharField . '_' . $prefix;

					if (!in_array($fieldName, $eventTableFields))
					{
						$sql = "ALTER TABLE  `#__eb_events` ADD  `$fieldName` VARCHAR( 255 );";
						$db->setQuery($sql);
						$db->execute();
					}
				}

				$textFields = array(
					'short_description',
					'description',
					'registration_form_message',
					'registration_form_message_group',
					'user_email_body',
					'user_email_body_offline',
					'thanks_message',
					'thanks_message_offline',
					'registration_approved_email_body',
				);

				foreach ($textFields as $textField)
				{
					$fieldName = $textField . '_' . $prefix;

					if (!in_array($fieldName, $eventTableFields))
					{
						$sql = "ALTER TABLE  `#__eb_events` ADD  `$fieldName` TEXT NULL;";
						$db->setQuery($sql);
						$db->execute();
					}
				}


				$fieldName = 'title_' . $prefix;

				if (!in_array($fieldName, $fieldTableFields))
				{
					$sql = "ALTER TABLE  `#__eb_fields` ADD  `$fieldName` VARCHAR( 255 );";
					$db->setQuery($sql);
					$db->execute();
				}

				$textFields = array(
					'description',
					'values',
					'default_values',
					'depend_on_options',
				);

				foreach ($textFields as $textField)
				{
					$fieldName = $textField . '_' . $prefix;

					if (!in_array($fieldName, $fieldTableFields))
					{
						$sql = "ALTER TABLE  `#__eb_fields` ADD  `$fieldName` TEXT NULL;";
						$db->setQuery($sql);
						$db->execute();
					}
				}


				$varcharFields = array(
					'name',
					'alias',
				);

				foreach ($varcharFields as $varcharField)
				{
					$fieldName = $varcharField . '_' . $prefix;

					if (!in_array($fieldName, $locationTableFields))
					{
						$sql = "ALTER TABLE  `#__eb_locations` ADD  `$fieldName` VARCHAR( 255 );";
						$db->setQuery($sql);
						$db->execute();
					}
				}

				$fieldName = 'description_' . $prefix;

				if (!in_array($fieldName, $locationTableFields))
				{
					$sql = "ALTER TABLE  `#__eb_locations` ADD  `$fieldName` TEXT NULL;";
					$db->setQuery($sql);
					$db->execute();
				}
			}
		}
	}

	/**
	 * Get language use for re-captcha
	 *
	 * @return string
	 */
	public static function getRecaptchaLanguage()
	{
		$language  = JFactory::getLanguage();
		$tag       = explode('-', $language->getTag());
		$tag       = $tag[0];
		$available = array('en', 'pt', 'fr', 'de', 'nl', 'ru', 'es', 'tr');

		if (in_array($tag, $available))
		{
			return "lang : '" . $tag . "',";
		}
	}

	/**
	 * Count total none-offline payment methods.
	 *
	 * @return int
	 */
	public static function getNumberNoneOfflinePaymentMethods()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from('#__eb_payment_plugins')
			->where('published = 1')
			->where('NAME NOT LIKE "os_offline%"');
		$db->setQuery($query);

		return (int) $db->loadResult();
	}

	/**
	 * Get URL of the site, using for Ajax request
	 *
	 * @return string
	 *
	 * @throws Exception
	 */
	public static function getSiteUrl()
	{
		$uri  = JUri::getInstance();
		$base = $uri->toString(array('scheme', 'host', 'port'));

		if (strpos(php_sapi_name(), 'cgi') !== false && !ini_get('cgi.fix_pathinfo') && !empty($_SERVER['REQUEST_URI']))
		{
			$script_name = $_SERVER['PHP_SELF'];
		}
		else
		{
			$script_name = $_SERVER['SCRIPT_NAME'];
		}

		$path = rtrim(dirname($script_name), '/\\');

		if ($path)
		{
			$siteUrl = $base . $path . '/';
		}
		else
		{
			$siteUrl = $base . '/';
		}

		if (JFactory::getApplication()->isAdmin())
		{
			$adminPos = strrpos($siteUrl, 'administrator/');
			$siteUrl  = substr_replace($siteUrl, '', $adminPos, 14);
		}

		return $siteUrl;
	}

	/**
	 * @return string
	 */
	public static function validateEngine()
	{
		$config     = self::getConfig();
		$dateFormat = $config->date_field_format ? $config->date_field_format : '%Y-%m-%d';
		$dateFormat = str_replace('%', '', $dateFormat);
		$dateNow    = JHtml::_('date', JFactory::getDate(), $dateFormat);

		//validate[required,custom[integer],min[-5]] text-input
		$validClass = array(
			"",
			"validate[custom[integer]]",
			"validate[custom[number]]",
			"validate[custom[email]]",
			"validate[custom[url]]",
			"validate[custom[phone]]",
			"validate[custom[date],past[$dateNow]]",
			"validate[custom[ipv4]]",
			"validate[minSize[6]]",
			"validate[maxSize[12]]",
			"validate[custom[integer],min[-5]]",
			"validate[custom[integer],max[50]]",);

		return json_encode($validClass);
	}

	public static function getURL()
	{
		static $url;

		if (!$url)
		{
			$ssl = self::getConfigValue('use_https');
			$url = self::getSiteUrl();

			if ($ssl)
			{
				$url = str_replace('http://', 'https://', $url);
			}
		}

		return $url;
	}

	/**
	 * Get Itemid of Event Booking extension
	 *
	 * @return int
	 */
	public static function getItemid()
	{
		JLoader::register('EventbookingHelperRoute', JPATH_ROOT . '/components/com_eventbooking/helper/route.php');

		return EventbookingHelperRoute::getDefaultMenuItem();
	}

	/**
	 * Format the currency according to the settings in Configuration
	 *
	 * @param  float     $amount the input amount
	 * @param  RADConfig $config the config object
	 *
	 * @return string   the formatted string
	 */
	public static function formatAmount($amount, $config)
	{
		$decimals      = isset($config->decimals) ? $config->decimals : 2;
		$dec_point     = isset($config->dec_point) ? $config->dec_point : '.';
		$thousands_sep = isset($config->thousands_sep) ? $config->thousands_sep : ',';

		return number_format($amount, $decimals, $dec_point, $thousands_sep);
	}

	/**
	 * Format the currency according to the settings in Configuration
	 *
	 * @param  float     $amount         the input amount
	 * @param  RADConfig $config         the config object
	 * @param  string    $currencySymbol the currency symbol. If null, the one in configuration will be used
	 *
	 * @return string   the formatted string
	 */
	public static function formatCurrency($amount, $config, $currencySymbol = null)
	{
		if (EventbookingHelper::isMethodOverridden('EventbookingHelperOverrideHelper', 'formatCurrency'))
		{
			return EventbookingHelperOverrideHelper::formatCurrency($amount, $config, $currencySymbol);
		}

		$decimals      = isset($config->decimals) ? $config->decimals : 2;
		$dec_point     = isset($config->dec_point) ? $config->dec_point : '.';
		$thousands_sep = isset($config->thousands_sep) ? $config->thousands_sep : ',';
		$symbol        = $currencySymbol ? $currencySymbol : $config->currency_symbol;

		return $config->currency_position ? (number_format($amount, $decimals, $dec_point, $thousands_sep) . $symbol) : ($symbol .
			number_format($amount, $decimals, $dec_point, $thousands_sep));
	}

	/**
	 * Load Event Booking language file
	 */
	public static function loadLanguage()
	{
		static $loaded;

		if (!$loaded)
		{
			$lang = JFactory::getLanguage();
			$tag  = $lang->getTag();

			if (!$tag)
			{
				$tag = 'en-GB';
			}

			$lang->load('com_eventbooking', JPATH_ROOT, $tag);

			$loaded = true;
		}
	}

	/**
	 * Method to load component frontend component language
	 *
	 * @param $tag
	 * @param $force
	 */
	public static function loadComponentLanguage($tag, $force = false)
	{
		$language = JFactory::getLanguage();

		if ($force && (!$tag || $tag == '*'))
		{
			$tag = self::getDefaultLanguage();
		}

		if ($tag && $tag != '*' && ($tag != $language->getTag() || $force))
		{
			$language->load('com_eventbooking', JPATH_ROOT, $tag, true);
		}
	}

	/**
	 * Parent category select list
	 *
	 * @param object $row
	 *
	 * @return string
	 */
	public static function parentCategories($row)
	{
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$fieldSuffix = EventbookingHelper::getFieldSuffix();

		$query->select('id, parent AS parent_id')
			->select('name' . $fieldSuffix . ' AS title')
			->from('#__eb_categories');

		if ($row->id)
		{
			$query->where('id != ' . $row->id);
		}

		if (!$row->parent)
		{
			$row->parent = 0;
		}

		$db->setQuery($query);
		$rows     = $db->loadObjectList();
		$children = array();

		if ($rows)
		{
			// first pass - collect children
			foreach ($rows as $v)
			{
				$pt   = $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}

		$list = JHtml::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

		$options   = array();
		$options[] = JHtml::_('select.option', '0', JText::_('Top'));

		foreach ($list as $item)
		{
			$options[] = JHtml::_('select.option', $item->id, '&nbsp;&nbsp;&nbsp;' . $item->treename);
		}

		return JHtml::_('select.genericlist', $options, 'parent',
			array(
				'option.text.toHtml' => false,
				'option.text'        => 'text',
				'option.value'       => 'value',
				'list.attr'          => ' class="inputbox" ',
				'list.select'        => $row->parent,));
	}

	/**
	 * Display list of files which users can choose for event attachment
	 *
	 * @param $attachment
	 * @param $config
	 *
	 * @return mixed
	 */
	public static function attachmentList($attachment, $config)
	{
		jimport('joomla.filesystem.folder');

		$path      = JPATH_ROOT . '/media/com_eventbooking';
		$files     = JFolder::files($path,
			strlen(trim($config->attachment_file_types)) ? $config->attachment_file_types : 'bmp|gif|jpg|png|swf|zip|doc|pdf|xls|zip');
		$options   = array();
		$options[] = JHtml::_('select.option', '', JText::_('EB_SELECT_ATTACHMENT'));

		for ($i = 0, $n = count($files); $i < $n; $i++)
		{
			$file      = $files[$i];
			$options[] = JHtml::_('select.option', $file, $file);
		}

		return JHtml::_('select.genericlist', $options, 'available_attachment[]', 'class="advancedSelect input-xlarge" multiple="multiple" size="6" ', 'value', 'text', $attachment);
	}

	/**
	 * Get total events of a category
	 *
	 * @param int  $categoryId
	 * @param bool $includeChildren
	 *
	 * @return int
	 * @throws Exception
	 */
	public static function getTotalEvent($categoryId, $includeChildren = true)
	{
		$user   = JFactory::getUser();
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		$config = self::getConfig();

		$arrCats   = array();
		$cats      = array();
		$arrCats[] = $categoryId;
		$cats[]    = $categoryId;

		if ($includeChildren)
		{
			while (count($arrCats))
			{
				$catId = array_pop($arrCats);

				//Get list of children category
				$query->clear()
					->select('id')
					->from('#__eb_categories')
					->where('parent = ' . $catId)
					->where('published = 1');
				$db->setQuery($query);
				$childrenCategories = $db->loadColumn();
				$arrCats            = array_merge($arrCats, $childrenCategories);
				$cats               = array_merge($cats, $childrenCategories);
			}
		}

		$query->clear()
			->select('COUNT(a.id)')
			->from('#__eb_events AS a')
			->innerJoin('#__eb_event_categories AS b ON a.id = b.event_id')
			->where('b.category_id IN (' . implode(',', $cats) . ')')
			->where('published = 1')
			->where('`access` IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');

		if ($config->hide_past_events)
		{
			$currentDate = $db->quote(JHtml::_('date', 'Now', 'Y-m-d'));

			if ($config->show_children_events_under_parent_event)
			{
				$query->where('(DATE(a.event_date) >= ' . $currentDate . ' OR DATE(a.cut_off_date) >= ' . $currentDate . ' OR DATE(a.max_end_date) >= ' . $currentDate . ')');
			}
			else
			{
				$query->where('(DATE(a.event_date) >= ' . $currentDate . ' OR DATE(a.cut_off_date) >= ' . $currentDate . ')');
			}
		}

		$db->setQuery($query);

		return (int) $db->loadResult();
	}

	/**
	 * Get all dependencies custom fields
	 *
	 * @param $id
	 *
	 * @return array
	 */
	public static function getAllDependencyFields($id)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$queue  = array($id);
		$fields = array($id);

		while (count($queue))
		{
			$masterFieldId = array_pop($queue);

			//Get list of dependency fields of this master field
			$query->clear()
				->select('id')
				->from('#__eb_fields')
				->where('depend_on_field_id=' . $masterFieldId);
			$db->setQuery($query);
			$rows = $db->loadObjectList();

			if (count($rows))
			{
				foreach ($rows as $row)
				{
					$queue[]  = $row->id;
					$fields[] = $row->id;
				}
			}
		}

		return $fields;
	}

	/**
	 * Get total registrants of the given event
	 *
	 * @param int $eventId
	 *
	 * @return int
	 */
	public static function getTotalRegistrants($eventId)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('SUM(number_registrants) AS total_registrants')
			->from('#__eb_registrants')
			->where('event_id = ' . $eventId)
			->where('group_id = 0')
			->where('(published=1 OR (payment_method LIKE "os_offline%" AND published NOT IN (2,3)))');
		$db->setQuery($query);

		return (int) $db->loadResult();
	}

	/**
	 * Get max number of registrants allowed for an event
	 *
	 * @param $event
	 *
	 * @return int
	 */
	public static function getMaxNumberRegistrants($event)
	{
		$eventCapacity  = (int) $event->event_capacity;
		$maxGroupNumber = (int) $event->max_group_number;

		if ($eventCapacity)
		{
			$maxRegistrants = $eventCapacity - $event->total_registrants;
		}
		else
		{
			$maxRegistrants = -1;
		}

		if ($maxGroupNumber)
		{
			if ($maxRegistrants == -1)
			{
				$maxRegistrants = $maxGroupNumber;
			}
			else
			{
				$maxRegistrants = $maxRegistrants > $maxGroupNumber ? $maxGroupNumber : $maxRegistrants;
			}
		}

		if ($maxRegistrants == -1)
		{
			//Default max registrants, we should only allow smaller than 10 registrants to make the form not too long
			$maxRegistrants = 10;
		}

		return $maxRegistrants;
	}

	/**
	 * Send notification emails to waiting list users when someone cancel registration
	 *
	 * @param $row
	 * @param $config
	 */
	public static function notifyWaitingList($row, $config)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__eb_registrants')
			->where('event_id=' . (int) $row->event_id)
			->where('group_id = 0')
			->where('published = 3')
			->order('id');
		$db->setQuery($query);
		$registrants = $db->loadObjectList();

		if (count($registrants))
		{
			$mailer = JFactory::getMailer();

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

			$message                           = EventbookingHelper::getMessages();
			$fieldSuffix                       = EventbookingHelper::getFieldSuffix();
			$replaces                          = array();
			$replaces['registrant_first_name'] = $row->first_name;
			$replaces['registrant_last_name']  = $row->last_name;

			if (JFactory::getApplication()->isSite())
			{
				$replaces['event_link'] = JUri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')) . JRoute::_(EventbookingHelperRoute::getEventRoute($row->event_id, 0, EventbookingHelper::getItemid()));
			}
			else
			{
				$replaces['event_link'] = JUri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')) . '/' . EventbookingHelperRoute::getEventRoute($row->event_id, 0, EventbookingHelper::getItemid());
			}

			$query->clear()
				->select('*, title' . $fieldSuffix . ' AS title')
				->from('#__eb_events')
				->where('id = ' . (int) $row->event_id);
			$db->setQuery($query);
			$rowEvent                   = $db->loadObject();
			$replaces['event_title']    = $rowEvent->title;
			$replaces['event_date']     = JHtml::_('date', $rowEvent->event_date, $config->event_date_format, null);
			$replaces['event_end_date'] = JHtml::_('date', $rowEvent->event_end_date, $config->event_date_format, null);

			if (strlen(trim($message->{'registrant_waitinglist_notification_subject' . $fieldSuffix})))
			{
				$subject = $message->{'registrant_waitinglist_notification_subject' . $fieldSuffix};
			}
			else
			{
				$subject = $message->registrant_waitinglist_notification_subject;
			}

			if (empty($subject))
			{
				//Admin has not entered email subject and email message for notification yet, simply return
				return false;
			}

			if (self::isValidMessage($message->{'registrant_waitinglist_notification_body' . $fieldSuffix}))
			{
				$body = $message->{'registrant_waitinglist_notification_body' . $fieldSuffix};
			}
			else
			{
				$body = $message->registrant_waitinglist_notification_body;
			}

			foreach ($registrants as $registrant)
			{
				if (!JMailHelper::isEmailAddress($registrant->email))
				{
					continue;
				}

				$message                = $body;
				$replaces['first_name'] = $registrant->first_name;
				$replaces['last_name']  = $registrant->last_name;

				foreach ($replaces as $key => $value)
				{
					$key     = strtoupper($key);
					$subject = str_replace("[$key]", $value, $subject);
					$message = str_replace("[$key]", $value, $message);
				}

				//Send email to waiting list users
				$mailer->sendMail($fromEmail, $fromName, $registrant->email, $subject, $message, 1);
				$mailer->clearAllRecipients();
			}
		}
	}

	/**
	 * Get country code
	 *
	 * @param string $countryName
	 *
	 * @return string
	 */
	public static function getCountryCode($countryName)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		if (empty($countryName))
		{
			$countryName = self::getConfigValue('default_country');
		}

		$query->select('country_2_code')
			->from('#__eb_countries')
			->where('LOWER(name) = ' . $db->quote(\Joomla\String\StringHelper::strtolower($countryName)));
		$db->setQuery($query);
		$countryCode = $db->loadResult();

		if (!$countryCode)
		{
			$countryCode = 'US';
		}

		return $countryCode;
	}

	/**
	 * Get state_2_code of a state, use to pass to payment gateway
	 *
	 * @param string $country
	 * @param string $state
	 *
	 * @return string
	 */
	public static function getStateCode($country, $state)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('state_2_code')
			->from('#__eb_states AS a')
			->innerJoin('#__eb_countries AS b ON a.country_id = b.id')
			->where('a.state_name = ' . $db->quote($state))
			->where('b.name = ' . $db->quote($country));
		$db->setQuery($query);

		return $db->loadResult() ?: $state;
	}

	/**
	 * Get color code of an event based on in category
	 *
	 * @param int $eventId
	 *
	 * @return array
	 */
	public static function getColorCodeOfEvent($eventId)
	{
		static $colors;

		if (!isset($colors[$eventId]))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('color_code')
				->from('#__eb_categories AS a')
				->innerJoin('#__eb_event_categories AS b ON (a.id = b.category_id AND b.main_category = 1)')
				->where('b.event_id = ' . $eventId);
			$db->setQuery($query);
			$colors[$eventId] = $db->loadResult();
		}

		return $colors[$eventId];
	}

	/**
	 * Method to get main category of an event
	 *
	 * @param $eventId
	 *
	 * @return mixed
	 */
	public static function getEventMainCategory($eventId)
	{
		static $categories;

		if (!isset($categories[$eventId]))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->quoteName(['text_color', 'color_code']))
				->from('#__eb_categories AS a')
				->innerJoin('#__eb_event_categories AS b ON (a.id = b.category_id AND b.main_category = 1)')
				->where('b.event_id = ' . $eventId);
			$db->setQuery($query);
			$categories[$eventId] = $db->loadObject();
		}

		return $categories[$eventId];
	}

	/**
	 * Get categories of the given events
	 *
	 * @param array $eventIds
	 *
	 * @return array
	 */
	public static function getCategories($eventIds = array())
	{
		if (count($eventIds))
		{
			$db          = JFactory::getDbo();
			$query       = $db->getQuery(true);
			$fieldSuffix = EventbookingHelper::getFieldSuffix();
			$query->select('a.id, a.name' . $fieldSuffix . ' AS name, a.color_code')
				->from('#__eb_categories AS a')
				->where('published = 1')
				->where('id IN (SELECT category_id FROM #__eb_event_categories WHERE event_id IN (' . implode(',', $eventIds) . ') AND main_category = 1)')
				->order('a.ordering');
			$db->setQuery($query);

			return $db->loadObjectList();
		}

		return array();
	}

	/**
	 * Get title of the given payment method
	 *
	 * @param string $methodName
	 *
	 * @return string
	 */
	public static function getPaymentMethodTitle($methodName)
	{
		static $titles;

		if (!isset($titles[$methodName]))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('title')
				->from('#__eb_payment_plugins')
				->where('name = ' . $db->quote($methodName));
			$db->setQuery($query);
			$methodTitle = $db->loadResult();

			if ($methodTitle)
			{
				$titles[$methodName] = $methodTitle;
			}
			else
			{
				$titles[$methodName] = $methodName;
			}
		}

		return $titles[$methodName];
	}

	/**
	 * Display copy right information
	 */
	public static function displayCopyRight()
	{
		echo '<div class="copyright" style="text-align:center;margin-top: 5px;"><a href="http://joomdonation.com/joomla-extensions/events-booking-joomla-events-registration.html" target="_blank"><strong>Event Booking</strong></a> version ' .
			self::getInstalledVersion() . ', Copyright (C) 2010 - ' . date('Y') .
			' <a href="http://joomdonation.com" target="_blank"><strong>Ossolution Team</strong></a></div>';
	}

	/**
	 * Check if the given message entered via HTML editor has actual data
	 *
	 * @param $string
	 *
	 * @return bool
	 */
	public static function isValidMessage($string)
	{
		$string = strip_tags($string, '<img>');

		// Remove all scpecial characters
		$string = preg_replace("/[^a-zA-Z]+/", "", $string);

		$string = trim($string);

		if (strlen($string) > 10)
		{
			return true;
		}

		return false;
	}

	/**
	 * Generate user selection box
	 *
	 * @param int    $userId
	 * @param string $fieldName
	 * @param int    $registrantId
	 *
	 * @return string
	 */
	public static function getUserInput($userId, $fieldName = 'user_id', $registrantId = 0)
	{
		if (JFactory::getApplication()->isSite())
		{
			// Initialize variables.
			$html = array();
			$link = 'index.php?option=com_eventbooking&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=user_id';
			// Initialize some field attributes.
			$attr = ' class="inputbox"';
			// Load the modal behavior script.
			JHtml::_('behavior.modal', 'a.modal_user_id');
			// Build the script.
			$script   = array();
			$script[] = '	function jSelectUser_user_id(id, title) {';
			$script[] = '			document.getElementById("jform_user_id").value = title; ';
			$script[] = '			document.getElementById("user_id").value = id; ';

			if (!$registrantId)
			{
				$script[] = 'populateRegistrantData()';
			}

			$script[] = '		SqueezeBox.close();';
			$script[] = '	}';

			// Add the script to the document head.
			JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
			// Load the current username if available.
			$table = JTable::getInstance('user');

			if ($userId)
			{
				$table->load($userId);
			}
			else
			{
				$table->name = '';
			}

			// Create a dummy text field with the user name.
			$html[] = '<div class="input-append">';
			$html[] = '	<input type="text" readonly="" name="jform[user_id]" id="jform_user_id"' . ' value="' . $table->name . '"' . $attr . ' />';
			$html[] = '	<input type="hidden" name="user_id" id="user_id"' . ' value="' . $userId . '"' . $attr . ' />';
			// Create the user select button.
			$html[] = '<a class="btn btn-primary button-select modal_user_id" title="' . JText::_('JLIB_FORM_CHANGE_USER') . '"' . ' href="' . $link . '"' .
				' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
			$html[] = ' <span class="icon-user"></span></a>';
			$html[] = '</div>';

			return implode("\n", $html);
		}
		else
		{
			JHtml::_('jquery.framework');
			$field = JFormHelper::loadFieldType('User');

			$element = new SimpleXMLElement('<field />');
			$element->addAttribute('name', $fieldName);
			$element->addAttribute('class', 'readonly input-medium');

			if (!$registrantId)
			{
				$element->addAttribute('onchange', 'populateRegistrantData();');
			}

			$field->setup($element, $userId);

			return $field->input;
		}
	}

	/**
	 * Generate article selection box
	 *
	 * @param int    $fieldValue
	 * @param string $fieldName
	 *
	 * @return string
	 */
	public static function getArticleInput($fieldValue, $fieldName = 'article_id')
	{
		// Initialize variables.
		JHtml::_('behavior.modal');
		$link = 'index.php?option=com_content&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1&function=js_select_article';
		$html = array();
		?>
		<script type="text/javascript">
			function js_select_article(id, title, catid, object, link, lang) {
				var old_id = document.getElementById('<?php echo $fieldName; ?>').value;
				if (old_id != id) {
					document.id('article_name').value = title;
					document.getElementById('<?php echo $fieldName; ?>').value = id;
					jModalClose();
				}
			}
		</script>
		<?php
		$table = JTable::getInstance('content');

		if ($fieldValue)
		{
			$table->load($fieldValue);
		}
		else
		{
			$table->title = '';
		}

		$html[] = '<div class="input-prepend input-append">';
		$html[] = '<div class="media-preview add-on"><span title="" class="hasTipPreview"><span class="icon-eye"></span></span></div>';
		$html[] = '	<input type="text" disabled="disabled" class="input-small hasTipImgpath" id="article_name"' . ' value="' . htmlspecialchars($table->title, ENT_COMPAT, 'UTF-8') . '"' .
			' disabled="disabled"/>';
		// Create the user select button.
		$html[] = '<a title="" rel="{handler: \'iframe\', size: {x: 800, y: 500}}" data-toggle="modal" role="button" class="btn hasTooltip modal" href="' . $link . '" data-original-title="Select or Change article"><span class="icon-file"></span> Select</a>';
		$html[] = '</div>';
		// Create the real field, hidden, that stored the user id.
		$html[] = '<input type="hidden" id="' . $fieldName . '" name="' . $fieldName . '" value="' . $fieldValue . '" />';

		return implode("\n", $html);
	}

	/**
	 * Format invoice number
	 *
	 * @param string    $invoiceNumber
	 * @param RADConfig $config
	 *
	 * @return string formatted invoice number
	 */
	public static function formatInvoiceNumber($invoiceNumber, $config)
	{
		return $config->invoice_prefix .
		str_pad($invoiceNumber, $config->invoice_number_length ? $config->invoice_number_length : 4, '0', STR_PAD_LEFT);
	}

	/**
	 * Format certificate number
	 *
	 * @param int       $id
	 * @param RADConfig $config
	 *
	 * @return string formatted certificate number
	 */
	public static function formatCertificateNumber($id, $config)
	{
		return $config->certificate_prefix .
		str_pad($id, $config->certificate_number_length ? $config->certificate_number_length : 5, '0', STR_PAD_LEFT);
	}

	/**
	 * Update max child date of a recurring event
	 *
	 * @param $parentId
	 */
	public static function updateParentMaxEventDate($parentId)
	{
		$db       = JFactory::getDbo();
		$query    = $db->getQuery(true);
		$nullDate = $db->getNullDate();
		$query->select('MAX(event_date) AS max_event_date, MAX(cut_off_date) AS max_cut_off_date')
			->from('#__eb_events')
			->where('published = 1')
			->where('parent_id = ' . $parentId);
		$db->setQuery($query);
		$maxDateInfo  = $db->loadObject();
		$maxEventDate = $maxDateInfo->max_event_date;

		if ($maxDateInfo->max_cut_off_date != $nullDate)
		{
			$oMaxEventDate  = new DateTime($maxDateInfo->max_event_date);
			$oMaxCutOffDate = new DateTime($maxDateInfo->max_cut_off_date);

			if ($oMaxCutOffDate > $oMaxEventDate)
			{
				$maxEventDate = $maxDateInfo->max_cut_off_date;
			}
		}

		$query->clear()
			->update('#__eb_events')
			->set('max_end_date = ' . $db->quote($maxEventDate))
			->where('id = ' . $parentId);
		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Generate invoice PDF
	 *
	 * @param object $row
	 */
	public static function generateInvoicePDF($row)
	{
		require_once JPATH_ROOT . "/components/com_eventbooking/tcpdf/tcpdf.php";
		require_once JPATH_ROOT . "/components/com_eventbooking/tcpdf/config/lang/eng.php";

		self::loadLanguage();

		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$config      = self::getConfig();
		$fieldSuffix = EventbookingHelper::getFieldSuffix($row->language);
		$sitename    = JFactory::getConfig()->get("sitename");

		$query->select('*')
			->from('#__eb_events')
			->where('id = ' . (int) $row->event_id);

		if ($fieldSuffix)
		{
			EventbookingHelperDatabase::getMultilingualFields($query, array('title'), $fieldSuffix);
		}

		$db->setQuery($query);
		$rowEvent = $db->loadObject();

		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor($sitename);
		$pdf->SetTitle('Invoice');
		$pdf->SetSubject('Invoice');
		$pdf->SetKeywords('Invoice');
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT);
		$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->setFooterMargin(PDF_MARGIN_FOOTER);
		//set auto page breaks
		$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		$font = empty($config->pdf_font) ? 'times' : $config->pdf_font;
		$pdf->SetFont($font, '', 8);
		$pdf->AddPage();

		if ($config->multiple_booking)
		{
			if (self::isValidMessage($config->{'invoice_format_cart' . $fieldSuffix}))
			{
				$invoiceOutput = $config->{'invoice_format_cart' . $fieldSuffix};
			}
			else
			{
				$invoiceOutput = $config->invoice_format_cart;
			}
		}
		else
		{
			if (self::isValidMessage($rowEvent->invoice_format))
			{
				$invoiceOutput = $rowEvent->invoice_format;
			}
			elseif (self::isValidMessage($config->{'invoice_format' . $fieldSuffix}))
			{
				$invoiceOutput = $config->{'invoice_format' . $fieldSuffix};
			}
			else
			{
				$invoiceOutput = $config->invoice_format;
			}
		}

		if (strpos($invoiceOutput, '[QRCODE]') !== false)
		{
			EventbookingHelper::generateQrcode($row->id);
			$imgTag        = '<img src="media/com_eventbooking/qrcodes/' . $row->id . '.png" border="0" />';
			$invoiceOutput = str_ireplace("[QRCODE]", $imgTag, $invoiceOutput);
		}

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
		$data = self::getRegistrantData($row, $rowFields);
		$form->bind($data);
		$form->buildFieldsDependency();

		if (is_callable('EventbookingHelperOverrideRegistration::buildTags'))
		{
			$replaces = EventbookingHelperOverrideRegistration::buildTags($row, $form, $rowEvent, $config);
		}
		elseif (is_callable('EventbookingHelperOverrideHelper::buildTags'))
		{
			$replaces = EventbookingHelperOverrideHelper::buildTags($row, $form, $rowEvent, $config);
		}
		else
		{
			$replaces = EventbookingHelperRegistration::buildTags($row, $form, $rowEvent, $config);
		}

		$replaces['invoice_number'] = self::formatInvoiceNumber($row->invoice_number, $config);

		if (empty($row->payment_date) || ($row->payment_date == $db->getNullDate()))
		{
			$replaces['invoice_date'] = JHtml::_('date', $row->register_date, $config->date_format);
		}
		else
		{
			$replaces['invoice_date'] = JHtml::_('date', $row->payment_date, $config->date_format);
		}

		if ($row->published == 0)
		{
			$invoiceStatus = JText::_('EB_INVOICE_STATUS_PENDING');
		}
		elseif ($row->published == 1)
		{
			$invoiceStatus = JText::_('EB_INVOICE_STATUS_PAID');
		}
		else
		{
			$invoiceStatus = JText::_('EB_INVOICE_STATUS_UNKNOWN');
		}

		$replaces['INVOICE_STATUS'] = $invoiceStatus;
		unset($replaces['total_amount']);
		unset($replaces['discount_amount']);
		unset($replaces['tax_amount']);

		if ($config->multiple_booking)
		{
			$sql = 'SELECT a.title' . $fieldSuffix . ' AS title, a.event_date, b.* FROM #__eb_events AS a INNER JOIN #__eb_registrants AS b ' . ' ON a.id = b.event_id ' .
				' WHERE b.id=' . $row->id . ' OR b.cart_id=' . $row->id;
			$db->setQuery($sql);
			$rowEvents                          = $db->loadObjectList();
			$subTotal                           = $replaces['amt_total_amount'];
			$taxAmount                          = $replaces['amt_tax_amount'];
			$discountAmount                     = $replaces['amt_discount_amount'];
			$total                              = $replaces['amt_amount'];
			$paymentProcessingFee               = $replaces['amt_payment_processing_fee'];
			$replaces['EVENTS_LIST']            = EventbookingHelperHtml::loadCommonLayout(
				'emailtemplates/tmpl/invoice_items.php',
				array(
					'rowEvents'            => $rowEvents,
					'subTotal'             => $subTotal,
					'taxAmount'            => $taxAmount,
					'discountAmount'       => $discountAmount,
					'paymentProcessingFee' => $paymentProcessingFee,
					'total'                => $total,
					'config'               => $config,));
			$replaces['SUB_TOTAL']              = EventbookingHelper::formatCurrency($subTotal, $config);
			$replaces['DISCOUNT_AMOUNT']        = EventbookingHelper::formatCurrency($discountAmount, $config);
			$replaces['TAX_AMOUNT']             = EventbookingHelper::formatCurrency($taxAmount, $config);
			$replaces['TOTAL_AMOUNT']           = EventbookingHelper::formatCurrency($total, $config);
			$replaces['PAYMENT_PROCESSING_FEE'] = EventbookingHelper::formatCurrency($paymentProcessingFee, $config);
			$replaces['DEPOSIT_AMOUNT']         = EventbookingHelper::formatCurrency($replaces['amt_deposit_amount'], $config);
			$replaces['DUE_AMOUNT']             = EventbookingHelper::formatCurrency($replaces['amt_due_amount'], $config);
		}
		else
		{
			$replaces['ITEM_QUANTITY']          = 1;
			$replaces['ITEM_AMOUNT']            = $replaces['ITEM_SUB_TOTAL'] = self::formatCurrency($row->total_amount, $config, $rowEvent->currency_symbol);
			$replaces['DISCOUNT_AMOUNT']        = self::formatCurrency($row->discount_amount, $config);
			$replaces['SUB_TOTAL']              = self::formatCurrency($row->total_amount - $row->discount_amount, $config, $rowEvent->currency_symbol);
			$replaces['TAX_AMOUNT']             = self::formatCurrency($row->tax_amount, $config, $rowEvent->currency_symbol);
			$replaces['PAYMENT_PROCESSING_FEE'] = self::formatCurrency($row->payment_processing_fee, $config, $rowEvent->currency_symbol);
			$replaces['TOTAL_AMOUNT']           = self::formatCurrency($row->total_amount - $row->discount_amount + $row->payment_processing_fee + $row->tax_amount, $config, $rowEvent->currency_symbol);
			$itemName                           = JText::_('EB_EVENT_REGISTRATION');
			$itemName                           = str_ireplace('[EVENT_TITLE]', $rowEvent->title, $itemName);
			$replaces['ITEM_NAME']              = $itemName;
		}
		foreach ($replaces as $key => $value)
		{
			$key           = strtoupper($key);
			$invoiceOutput = str_ireplace("[$key]", $value, $invoiceOutput);
		}

		$pdf->writeHTML($invoiceOutput, true, false, false, false, '');

		//Filename
		$filePath = JPATH_ROOT . '/media/com_eventbooking/invoices/' . $replaces['invoice_number'] . '.pdf';
		$pdf->Output($filePath, 'F');
	}

	/**
	 * Download PDF Certificates
	 *
	 * @param array     $rows
	 * @param RADConfig $config
	 */
	public static function downloadCertificates($rows, $config)
	{
		if (EventbookingHelper::isMethodOverridden('EventbookingHelperOverrideHelper', 'downloadCertificates'))
		{
			EventbookingHelperOverrideHelper::downloadCertificates($rows, $config);

			return;
		}

		require_once JPATH_ROOT . "/components/com_eventbooking/tcpdf/tcpdf.php";
		require_once JPATH_ROOT . "/components/com_eventbooking/tcpdf/config/lang/eng.php";

		self::loadLanguage();

		$sitename = JFactory::getConfig()->get("sitename");

		$events = array();

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$pdf = new TCPDF($config->get('certificate_page_orientation', PDF_PAGE_ORIENTATION), PDF_UNIT, $config->get('certificate_page_format', PDF_PAGE_FORMAT), true, 'UTF-8', false);
		$pdf->SetCreator('Events Booking');
		$pdf->SetAuthor($sitename);
		$pdf->SetTitle('Certificate');
		$pdf->SetSubject('Certificate');
		$pdf->SetKeywords('Certificate');
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		//set auto page breaks
		$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		$font = empty($config->pdf_font) ? 'times' : $config->pdf_font;
		$pdf->SetFont($font, '', 8);

		foreach ($rows as $row)
		{
			if (!isset($events[$row->event_id]))
			{
				$fieldSuffix = EventbookingHelper::getFieldSuffix($row->language);

				$query->clear()
					->select('*')
					->from('#__eb_events')
					->where('id = ' . (int) $row->event_id);

				if ($fieldSuffix)
				{
					EventbookingHelperDatabase::getMultilingualFields($query, array('title'), $fieldSuffix);
				}

				$db->setQuery($query);
				$events[$row->event_id] = $db->loadObject();
			}

			$rowEvent = $events[$row->event_id];

			if ($rowEvent->certificate_bg_image)
			{
				$backgroundImage = $rowEvent->certificate_bg_image;
			}
			else
			{
				$backgroundImage = $config->get('default_certificate_bg_image');
			}

			if ($backgroundImage && file_exists(JPATH_ROOT . '/' . $backgroundImage))
			{
				$backgroundImagePath = JPATH_ROOT . '/' . $backgroundImage;
			}
			else
			{
				$backgroundImagePath = '';
			}

			if (self::isValidMessage($rowEvent->certificate_layout))
			{
				$invoiceOutput = $rowEvent->certificate_layout;
			}
			else
			{
				$invoiceOutput = $config->certificate_layout;
			}

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
				$query->clear()
					->select('*')
					->from('#__eb_registrants')
					->where('group_id = ' . $row->id);
				$db->setQuery($query);
				$rowMembers = $db->loadObjectList();

				foreach ($rowMembers as $rowMember)
				{
					$pdf->AddPage();

					if ($backgroundImagePath)
					{
						$pdf->Image($backgroundImagePath, $rowEvent->ticket_bg_left, $rowEvent->ticket_bg_top);
					}

					$rowFields = EventbookingHelperRegistration::getFormFields($row->event_id, 0);

					$form = new RADForm($rowFields);
					$data = self::getRegistrantData($rowMember, $rowFields);
					$form->bind($data);
					$form->buildFieldsDependency();

					if (is_callable('EventbookingHelperOverrideRegistration::buildTags'))
					{
						$replaces = EventbookingHelperOverrideRegistration::buildTags($rowMember, $form, $rowEvent, $config);
					}
					elseif (is_callable('EventbookingHelperOverrideHelper::buildTags'))
					{
						$replaces = EventbookingHelperOverrideHelper::buildTags($rowMember, $form, $rowEvent, $config);
					}
					else
					{
						$replaces = EventbookingHelperRegistration::buildTags($rowMember, $form, $rowEvent, $config);
					}

					$replaces['certificate_number'] = self::formatCertificateNumber($rowMember->id, $config);
					$replaces['registration_date']  = JHtml::_('date', $row->register_date, $config->date_format);

					$output = $invoiceOutput;

					foreach ($replaces as $key => $value)
					{
						$key    = strtoupper($key);
						$output = str_ireplace("[$key]", $value, $output);
					}

					$pdf->writeHTML($output, true, false, false, false, '');
				}
			}
			else
			{
				$pdf->AddPage();

				if ($backgroundImagePath)
				{
					$pdf->Image($backgroundImagePath, $rowEvent->ticket_bg_left, $rowEvent->ticket_bg_top);
				}

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
				$data = self::getRegistrantData($row, $rowFields);
				$form->bind($data);
				$form->buildFieldsDependency();

				if (is_callable('EventbookingHelperOverrideRegistration::buildTags'))
				{
					$replaces = EventbookingHelperOverrideRegistration::buildTags($row, $form, $rowEvent, $config);
				}
				elseif (is_callable('EventbookingHelperOverrideHelper::buildTags'))
				{
					$replaces = EventbookingHelperOverrideHelper::buildTags($row, $form, $rowEvent, $config);
				}
				else
				{
					$replaces = EventbookingHelperRegistration::buildTags($row, $form, $rowEvent, $config);
				}

				$replaces['certificate_number'] = self::formatCertificateNumber($row->id, $config);
				$replaces['registration_date']  = JHtml::_('date', $row->register_date, $config->date_format);

				foreach ($replaces as $key => $value)
				{
					$key           = strtoupper($key);
					$invoiceOutput = str_ireplace("[$key]", $value, $invoiceOutput);
				}

				$pdf->writeHTML($invoiceOutput, true, false, false, false, '');
			}
		}

		if (count($rows) > 1)
		{
			//Filename
			$filePath = JPATH_ROOT . '/media/com_eventbooking/certificates/certificates_' . date('Y-m-d') . '.pdf';
			$fileName = 'certificates_' . date('Y-m-d') . '.pdf';
		}
		else
		{
			$row = $rows[0];

			//Filename
			$fileName = self::formatCertificateNumber($row->id, $config) . '.pdf';
			$filePath = JPATH_ROOT . '/media/com_eventbooking/certificates/' . $fileName;
		}

		$pdf->Output($filePath, 'F');

		// Process download
		while (@ob_end_clean()) ;
		self::processDownload($filePath, $fileName);
	}

	/**
	 * Generate QRcode for a transaction
	 *
	 * @param $registrantId
	 */
	public static function generateQrcode($registrantId)
	{
		$filename = $registrantId . '.png';

		if (!file_exists(JPATH_ROOT . '/media/com_eventbooking/qrcodes/' . $filename))
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/vendor/phpqrcode/qrlib.php';
			$Itemid     = EventbookingHelperRoute::findView('registrants', EventbookingHelper::getItemid());
			$checkinUrl = self::getSiteUrl() . 'index.php?option=com_eventbooking&task=registrant.checkin&id=' . $registrantId . '&Itemid=' . $Itemid;
			QRcode::png($checkinUrl, JPATH_ROOT . '/media/com_eventbooking/qrcodes/' . $filename);
		}
	}

	/**
	 * Generate and download invoice of given registration record
	 *
	 * @param int $id
	 */
	public static function downloadInvoice($id)
	{
		JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_eventbooking/table');
		$config = self::getConfig();
		$row    = JTable::getInstance('EventBooking', 'Registrant');
		$row->load($id);
		$invoiceStorePath = JPATH_ROOT . '/media/com_eventbooking/invoices/';

		if ($row)
		{
			if (!$row->invoice_number)
			{
				$row->invoice_number = self::getInvoiceNumber();
				$row->store();
			}

			$invoiceNumber = self::formatInvoiceNumber($row->invoice_number, $config);

			if (is_callable('EventbookingHelperOverrideHelper::generateInvoicePDF'))
			{
				EventbookingHelperOverrideHelper::generateInvoicePDF($row);
			}
			else
			{
				self::generateInvoicePDF($row);
			}

			$invoicePath = $invoiceStorePath . $invoiceNumber . '.pdf';
			$fileName    = $invoiceNumber . '.pdf';
			while (@ob_end_clean()) ;
			self::processDownload($invoicePath, $fileName);
		}
	}

	/**
	 * Convert all img tags to use absolute URL
	 *
	 * @param $html_content
	 *
	 * @return string
	 */
	public static function convertImgTags($html_content)
	{
		$patterns     = array();
		$replacements = array();
		$i            = 0;
		$src_exp      = "/src=\"(.*?)\"/";
		$link_exp     = "[^http:\/\/www\.|^www\.|^https:\/\/|^http:\/\/]";
		$siteURL      = JUri::root();
		preg_match_all($src_exp, $html_content, $out, PREG_SET_ORDER);

		foreach ($out as $val)
		{
			$links = preg_match($link_exp, $val[1], $match, PREG_OFFSET_CAPTURE);

			if ($links == '0')
			{
				$patterns[$i]     = $val[1];
				$patterns[$i]     = "\"$val[1]";
				$replacements[$i] = $siteURL . $val[1];
				$replacements[$i] = "\"$replacements[$i]";
			}

			$i++;
		}

		$mod_html_content = str_replace($patterns, $replacements, $html_content);

		return $mod_html_content;
	}

	/**
	 * Process download a file
	 *
	 * @param string $file : Full path to the file which will be downloaded
	 */
	public static function processDownload($filePath, $filename, $detectFilename = false)
	{
		$fsize    = @filesize($filePath);
		$mod_date = date('r', filemtime($filePath));
		$cont_dis = 'attachment';

		if ($detectFilename)
		{
			$pos      = strpos($filename, '_');
			$filename = substr($filename, $pos + 1);
		}

		$ext  = JFile::getExt($filename);
		$mime = self::getMimeType($ext);

		// required for IE, otherwise Content-disposition is ignored
		if (ini_get('zlib.output_compression'))
		{
			ini_set('zlib.output_compression', 'Off');
		}

		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Expires: 0");
		header("Content-Transfer-Encoding: binary");
		header(
			'Content-Disposition:' . $cont_dis . ';' . ' filename="' . $filename . '";' . ' modification-date="' . $mod_date . '";' . ' size=' . $fsize .
			';'); //RFC2183
		header("Content-Type: " . $mime); // MIME type
		header("Content-Length: " . $fsize);

		if (!ini_get('safe_mode'))
		{ // set_time_limit doesn't work in safe mode
			@set_time_limit(0);
		}

		self::readfile_chunked($filePath);
	}

	/**
	 * Get mimetype of a file
	 *
	 * @return string
	 */
	public static function getMimeType($ext)
	{
		require_once JPATH_ROOT . "/components/com_eventbooking/helper/mime.mapping.php";

		foreach ($mime_extension_map as $key => $value)
		{
			if ($key == $ext)
			{
				return $value;
			}
		}

		return "";
	}

	/**
	 * Read file
	 *
	 * @param string $filename
	 * @param        $retbytes
	 *
	 * @return unknown
	 */
	public static function readfile_chunked($filename, $retbytes = true)
	{
		$chunksize = 1 * (1024 * 1024); // how many bytes per chunk
		$cnt       = 0;
		$handle    = fopen($filename, 'rb');

		if ($handle === false)
		{
			return false;
		}

		while (!feof($handle))
		{
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			@ob_flush();
			flush();
			if ($retbytes)
			{
				$cnt += strlen($buffer);
			}
		}

		$status = fclose($handle);

		if ($retbytes && $status)
		{
			return $cnt; // return num. bytes delivered like readfile() does.
		}

		return $status;
	}

	/**
	 * Check to see whether the current user can
	 *
	 * @param int $eventId
	 */
	public static function checkEventAccess($eventId)
	{
		$user  = JFactory::getUser();
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('`access`')
			->from('#__eb_events')
			->where('id=' . $eventId);
		$db->setQuery($query);
		$access = (int) $db->loadResult();

		if (!in_array($access, $user->getAuthorisedViewLevels()))
		{
			JFactory::getApplication()->redirect('index.php', JText::_('NOT_AUTHORIZED'));
		}
	}

	/**
	 * Check to see whether a users to access to registration history
	 * Enter description here
	 */
	public static function checkAccessHistory()
	{
		$user = JFactory::getUser();

		if (!$user->get('id'))
		{
			JFactory::getApplication()->redirect('index.php?option=com_eventbooking', JText::_('NOT_AUTHORIZED'));
		}
	}

	/**
	 * Check to see whether the current users can add events from front-end
	 */
	public static function checkAddEvent()
	{
		return JFactory::getUser()->authorise('eventbooking.addevent', 'com_eventbooking');
	}

	/**
	 * Get list of recurring event dates
	 *
	 * @param DateTime $startDate
	 * @param DateTime $endDate
	 * @param int      $dailyFrequency
	 * @param int      $numberOccurencies
	 *
	 * @return array
	 */
	public static function getDailyRecurringEventDates($startDate, $endDate, $dailyFrequency, $numberOccurencies)
	{
		$eventDates   = array($startDate);
		$timeZone     = new DateTimeZone(JFactory::getConfig()->get('offset'));
		$date         = new DateTime($startDate, $timeZone);
		$dateInterval = new DateInterval('P' . $dailyFrequency . 'D');

		if ($numberOccurencies)
		{
			for ($i = 1; $i < $numberOccurencies; $i++)
			{
				$date->add($dateInterval);
				$eventDates[] = $date->format('Y-m-d H:i:s');
			}
		}
		else
		{
			$recurringEndDate = new DateTime($endDate . ' 23:59:59', $timeZone);

			while (true)
			{
				$date->add($dateInterval);

				if ($date <= $recurringEndDate)
				{
					$eventDates[] = $date->format('Y-m-d H:i:s');
				}
				else
				{
					break;
				}
			}
		}

		return $eventDates;
	}

	/**
	 * Get weekly recurring event dates
	 *
	 * @param DateTime $startDate
	 * @param DateTime $endDate
	 * @param Int      $weeklyFrequency
	 * @param int      $numberOccurrences
	 * @param array    $weekDays
	 *
	 * @return array
	 */
	public static function getWeeklyRecurringEventDates($startDate, $endDate, $weeklyFrequency, $numberOccurrences, $weekDays)
	{
		$eventDates = array();

		$timeZone           = new DateTimeZone(JFactory::getConfig()->get('offset'));
		$recurringStartDate = new Datetime($startDate, $timeZone);
		$hour               = $recurringStartDate->format('H');
		$minutes            = $recurringStartDate->format('i');
		$dayOfWeek          = $recurringStartDate->format('w');
		$startWeek          = clone $recurringStartDate;

		if ($dayOfWeek > 0)
		{
			$startWeek->modify('- ' . $dayOfWeek . ' day');
		}

		$startWeek->setTime($hour, $minutes, 0);
		$dateInterval = new DateInterval('P' . $weeklyFrequency . 'W');

		if ($numberOccurrences)
		{
			$count = 0;

			while ($count < $numberOccurrences)
			{
				foreach ($weekDays as $weekDay)
				{
					$date = clone $startWeek;

					if ($weekDay > 0)
					{
						$date->add(new DateInterval('P' . $weekDay . 'D'));
					}

					if (($date >= $recurringStartDate) && ($count < $numberOccurrences))
					{
						$eventDates[] = $date->format('Y-m-d H:i:s');
						$count++;
					}
				}

				$startWeek->add($dateInterval);
			}
		}
		else
		{
			$recurringEndDate = new DateTime($endDate . ' 23:59:59', $timeZone);

			while (true)
			{
				foreach ($weekDays as $weekDay)
				{
					$date = clone $startWeek;

					if ($weekDay > 0)
					{
						$date->add(new DateInterval('P' . $weekDay . 'D'));
					}

					if (($date >= $recurringStartDate) && ($date <= $recurringEndDate))
					{
						$eventDates[] = $date->format('Y-m-d H:i:s');
					}
				}

				if ($date > $recurringEndDate)
				{
					break;
				}

				$startWeek->add($dateInterval);
			}
		}

		return $eventDates;
	}

	/**
	 * Get list of monthly recurring
	 *
	 * @param DateTime $startDate
	 * @param DateTime $endDate
	 * @param int      $monthlyFrequency
	 * @param int      $numberOccurrences
	 * @param string   $monthDays
	 *
	 * @return array
	 */
	public static function getMonthlyRecurringEventDates($startDate, $endDate, $monthlyFrequency, $numberOccurrences, $monthDays)
	{
		$eventDates         = array();
		$timeZone           = new DateTimeZone(JFactory::getConfig()->get('offset'));
		$recurringStartDate = new Datetime($startDate, $timeZone);
		$date               = clone $recurringStartDate;
		$dateInterval       = new DateInterval('P' . $monthlyFrequency . 'M');
		$monthDays          = explode(',', $monthDays);

		if ($numberOccurrences)
		{
			$count = 0;

			while ($count < $numberOccurrences)
			{
				$currentMonth = $date->format('m');
				$currentYear  = $date->format('Y');

				foreach ($monthDays as $day)
				{
					$date->setDate($currentYear, $currentMonth, $day);

					if (($date >= $recurringStartDate) && ($count < $numberOccurrences))
					{
						$eventDates[] = $date->format('Y-m-d H:i:s');
						$count++;
					}
				}

				$date->add($dateInterval);
			}
		}
		else
		{
			$recurringEndDate = new DateTime($endDate . ' 23:59:59', $timeZone);

			while (true)
			{
				$currentMonth = $date->format('m');
				$currentYear  = $date->format('Y');

				foreach ($monthDays as $day)
				{
					$date->setDate($currentYear, $currentMonth, $day);

					if (($date >= $recurringStartDate) && ($date <= $recurringEndDate))
					{
						$eventDates[] = $date->format('Y-m-d H:i:s');
					}
				}

				if ($date > $recurringEndDate)
				{
					break;
				}

				$date->add(new DateInterval('P' . $monthlyFrequency . 'M'));
			}
		}

		return $eventDates;
	}

	/**
	 * Get list of event dates for recurring events happen on specific date in a month
	 *
	 * @param $startDate
	 * @param $endDate
	 * @param $monthlyFrequency
	 * @param $numberOccurrences
	 * @param $n
	 * @param $day
	 *
	 * @return array
	 */
	public static function getMonthlyRecurringAtDayInWeekEventDates($startDate, $endDate, $monthlyFrequency, $numberOccurrences, $n, $day)
	{
		$eventDates         = array();
		$timeZone           = new DateTimeZone(JFactory::getConfig()->get('offset'));
		$recurringStartDate = new Datetime($startDate, $timeZone);
		$date               = clone $recurringStartDate;
		$dateInterval       = new DateInterval('P' . $monthlyFrequency . 'M');

		if ($numberOccurrences)
		{
			$count = 0;

			while ($count < $numberOccurrences)
			{
				$currentMonth = $date->format('M');
				$currentYear  = $date->format('Y');
				$timeString   = "$n $day";
				$timeString .= " of $currentMonth $currentYear";
				$date->modify($timeString);
				$date->setTime($recurringStartDate->format('H'), $recurringStartDate->format('i'), 0);

				if (($date >= $recurringStartDate) && ($count < $numberOccurrences))
				{
					$eventDates[] = $date->format('Y-m-d H:i:s');
					$count++;
				}

				$date->add($dateInterval);
			}
		}
		else
		{
			$recurringEndDate = new DateTime($endDate . ' 23:59:59', $timeZone);

			while (true)
			{
				$currentMonth = $date->format('M');
				$currentYear  = $date->format('Y');
				$timeString   = "$n $day";
				$timeString .= " of $currentMonth $currentYear";
				$date->modify($timeString);
				$date->setTime($recurringStartDate->format('H'), $recurringStartDate->format('i'), 0);

				if (($date >= $recurringStartDate) && ($date <= $recurringEndDate))
				{
					$eventDates[] = $date->format('Y-m-d H:i:s');
				}

				if ($date > $recurringEndDate)
				{
					break;
				}

				$date->add(new DateInterval('P' . $monthlyFrequency . 'M'));
			}
		}

		return $eventDates;
	}

	public static function getDeliciousButton($title, $link)
	{
		$img_url = JUri::root(true) . "/media/com_eventbooking/assets/images/socials/delicious.png";
		$alt     = JText::sprintf('EB_SUBMIT_ITEM_IN_SOCIAL_NETWORK', $title, 'Delicious');

		return '<a href="http://del.icio.us/post?url=' . rawurlencode($link) . '&amp;title=' . rawurlencode($title) . '" title="' . $alt . '" target="blank" >
		<img src="' . $img_url . '" alt="' . $alt . '" />
		</a>';
	}

	public static function getDiggButton($title, $link)
	{
		$img_url = JUri::root(true) . "/media/com_eventbooking/assets/images/socials/digg.png";
		$alt     = JText::sprintf('EB_SUBMIT_ITEM_IN_SOCIAL_NETWORK', $title, 'Digg');

		return '<a href="http://digg.com/submit?url=' . rawurlencode($link) . '&amp;title=' . rawurlencode($title) . '" title="' . $alt . '" target="blank" >
        <img src="' . $img_url . '" alt="' . $alt . '" />
        </a>';
	}

	public static function getFacebookButton($title, $link)
	{
		$img_url = JUri::root(true) . "/media/com_eventbooking/assets/images/socials/facebook.png";
		$alt     = JText::sprintf('EB_SUBMIT_ITEM_IN_SOCIAL_NETWORK', $title, 'FaceBook');

		return '<a href="http://www.facebook.com/sharer.php?u=' . rawurlencode($link) . '&amp;t=' . rawurlencode($title) . '" title="' . $alt . '" target="blank" >
        <img src="' . $img_url . '" alt="' . $alt . '" />
        </a>';
	}

	public static function getGoogleButton($title, $link)
	{
		$img_url = JUri::root(true) . "/media/com_eventbooking/assets/images/socials/google.png";
		$alt     = JText::sprintf('EB_SUBMIT_ITEM_IN_SOCIAL_NETWORK', $title, 'Google Bookmarks');

		return '<a href="http://www.google.com/bookmarks/mark?op=edit&bkmk=' . rawurlencode($link) . '" title="' . $alt . '" target="blank" >
        <img src="' . $img_url . '" alt="' . $alt . '" />
        </a>';
	}

	public static function getStumbleuponButton($title, $link)
	{
		$img_url = JUri::root(true) . "/media/com_eventbooking/assets/images/socials/stumbleupon.png";
		$alt     = JText::sprintf('EB_SUBMIT_ITEM_IN_SOCIAL_NETWORK', $title, 'Stumbleupon');

		return '<a href="http://www.stumbleupon.com/submit?url=' . rawurlencode($link) . '&amp;title=' . rawurlencode($title) . '" title="' . $alt . '" target="blank" >
        <img src="' . $img_url . '" alt="' . $alt . '" />
        </a>';
	}

	public static function getTechnoratiButton($title, $link)
	{
		$img_url = JUri::root(true) . "/media/com_eventbooking/assets/images/socials/technorati.png";
		$alt     = JText::sprintf('EB_SUBMIT_ITEM_IN_SOCIAL_NETWORK', $title, 'Technorati');

		return '<a href="http://technorati.com/faves?add=' . rawurlencode($link) . '" title="' . $alt . '" target="blank" >
        <img src="' . $img_url . '" alt="' . $alt . '" />
        </a>';
	}

	public static function getTwitterButton($title, $link)
	{
		$img_url = JUri::root(true) . "/media/com_eventbooking/assets/images/socials/twitter.png";
		$alt     = JText::sprintf('EB_SUBMIT_ITEM_IN_SOCIAL_NETWORK', $title, 'Twitter');

		return '<a href="http://twitter.com/?status=' . rawurlencode($title . " " . $link) . '" title="' . $alt . '" target="blank" >
        <img src="' . $img_url . '" alt="' . $alt . '" />
        </a>';
	}

	public static function getLinkedInButton($title, $link)
	{
		$img_url = JUri::root(true) . "/media/com_eventbooking/assets/images/socials/linkedin.png";
		$alt     = JText::sprintf('EB_SUBMIT_ITEM_IN_SOCIAL_NETWORK', $title, 'LinkedIn');

		return '<a href="http://www.linkedin.com/shareArticle?mini=true&amp;url=' . $link . '&amp;title=' . $title . '" title="' . $alt . '" target="_blank" ><img src="' . $img_url . '" alt="' . $alt . '" /></a>';
	}

	/**
	 * Calculate level for categories, used when upgrade from old version to new version
	 *
	 * @param     $id
	 * @param     $list
	 * @param     $children
	 * @param int $maxlevel
	 * @param int $level
	 *
	 * @return mixed
	 */
	public static function calculateCategoriesLevel($id, $list, &$children, $maxlevel = 9999, $level = 1)
	{
		if (@$children[$id] && $level <= $maxlevel)
		{
			foreach ($children[$id] as $v)
			{
				$id        = $v->id;
				$v->level  = $level;
				$list[$id] = $v;
				$list      = self::calculateCategoriesLevel($id, $list, $children, $maxlevel, $level + 1);
			}
		}

		return $list;
	}

	/**
	 * Get User IP address
	 *
	 * @return mixed
	 */
	public static function getUserIp()
	{
		return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
	}

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
		return EventbookingHelperRegistration::calculateMemberDiscount($discount, $groupIds);
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
		return EventbookingHelperRegistration::acceptRegistration($event);
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
		return EventbookingHelperRegistration::getAllEventFields($eventId);
	}

	/**
	 * Get name of published core fields in the system
	 *
	 * @return array
	 */
	public static function getPublishedCoreFields()
	{
		return EventbookingHelperRegistration::getPublishedCoreFields();
	}

	/**
	 * Get the form fields to display in deposit payment form
	 *
	 * @return array
	 */
	public static function getDepositPaymentFormFields()
	{
		return EventbookingHelperRegistration::getDepositPaymentFormFields();
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
		return EventbookingHelperRegistration::getFormFields($eventId, $registrationType, $activeLanguage);
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
		return EventbookingHelperRegistration::getRegistrationRate($eventId, $numberRegistrants);
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
		return EventbookingHelperRegistration::calculateIndividualRegistrationFees($event, $form, $data, $config, $paymentMethod);
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
		return EventbookingHelperRegistration::calculateGroupRegistrationFees($event, $form, $data, $config, $paymentMethod);
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
		return EventbookingHelperRegistration::calculateCartRegistrationFee($cart, $form, $data, $config, $paymentMethod);
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
		return EventbookingHelperRegistration::showBillingStep($eventId);
	}

	/**
	 * Get the form data used to bind to the RADForm object
	 *
	 * @param array  $rowFields
	 * @param int    $eventId
	 * @param int    $userId
	 * @param object $config
	 *
	 * @return array
	 */
	public static function getFormData($rowFields, $eventId, $userId, $config)
	{
		return EventbookingHelperRegistration::getFormData($rowFields, $eventId, $userId);
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
		return EventbookingHelperRegistration::getRegistrantData($rowRegistrant, $rowFields);
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
		return EventbookingHelperRegistration::saveRegistration($data);
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
		return EventbookingHelperRegistration::needInvoice($row);
	}

	/**
	 * Get the invoice number for this registration record
	 *
	 * @return int
	 */
	public static function getInvoiceNumber()
	{
		return EventbookingHelperRegistration::getInvoiceNumber();
	}

	/**
	 * Update Group Members record to have same information with billing record
	 *
	 * @param int $groupId
	 */
	public static function updateGroupRegistrationRecord($groupId)
	{
		EventbookingHelperRegistration::updateGroupRegistrationRecord($groupId);
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
		return EventbookingHelperRegistration::buildDepositPaymentTags($row, $config);
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
		return EventbookingHelperRegistration::buildEventTags($event, $config);
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
		return EventbookingHelperRegistration::buildTags($row, $form, $event, $config, $loadCss);
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
		return EventbookingHelperRegistration::getEmailContent($config, $row, $loadCss, $form, $toAdmin);
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
	public static function getMemberDetails($config, $rowMember, $rowEvent, $rowLocation, $loadCss = true, $memberForm)
	{
		return EventbookingHelperRegistration::getMemberDetails($config, $rowMember, $rowEvent, $rowLocation, $loadCss, $memberForm);
	}

	/**
	 * Check to see whether the current users can access View List function
	 *
	 * @return bool
	 */
	public static function canViewRegistrantList()
	{
		return EventbookingHelperAcl::canViewRegistrantList();
	}

	/**
	 * Check to see whether this users has permission to edit registrant
	 */
	public static function checkEditRegistrant($rowRegistrant)
	{
		if (!EventbookingHelperAcl::canEditRegistrant($rowRegistrant))
		{
			JFactory::getApplication()->redirect(JUri::root(), JText::_('NOT_AUTHORIZED'));
		}
	}

	/**
	 * Check to see whether this event can be cancelled
	 *
	 * @param int $eventId
	 *
	 * @return bool
	 */
	public static function canCancel($eventId)
	{
		return EventbookingHelperAcl::canCancel($eventId);
	}

	public static function canExportRegistrants($eventId = 0)
	{
		return EventbookingHelperAcl::canExportRegistrants($eventId);
	}

	/**
	 * Check to see whether the current user can change status (publish/unpublish) of the given event
	 *
	 * @param $eventId
	 *
	 * @return bool
	 */
	public static function canChangeEventStatus($eventId)
	{
		return EventbookingHelperAcl::canChangeEventStatus($eventId);
	}

	/**
	 * Check to see whether the user can cancel registration for the given event
	 *
	 * @param $eventId
	 *
	 * @return bool|int
	 */
	public static function canCancelRegistration($eventId)
	{
		return EventbookingHelperAcl::canCancelRegistration($eventId);
	}

	/**
	 * Check to see whether the current user can edit registrant
	 *
	 * @param int $eventId
	 *
	 * @return boolean
	 */
	public static function checkEditEvent($eventId)
	{
		return EventbookingHelperAcl::checkEditEvent($eventId);
	}

	/**
	 * Check to see whether the current user can delete the given registrant
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public static function canDeleteRegistrant($id = 0)
	{
		return EventbookingHelperAcl::canDeleteRegistrant($id);
	}

	/**
	 * Helper function for sending emails to registrants and administrator
	 *
	 * @param RegistrantEventBooking $row
	 * @param object                 $config
	 */
	public static function sendEmails($row, $config)
	{
		EventbookingHelperMail::sendEmails($row, $config);
	}
}
