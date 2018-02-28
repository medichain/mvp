<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

use Joomla\Registry\Registry;

defined('_JEXEC') or die;

class EventbookingViewEventHtml extends RADViewHtml
{
	use EventbookingViewCaptcha;

	/**
	 * Event Data
	 *
	 * @var \stdClass
	 */
	protected $item;

	/**
	 * Model state
	 *
	 * @var RADModelState
	 */
	protected $state;

	/**
	 * Children events of the current event
	 *
	 * @var array
	 */
	protected $items;

	/**
	 * Component config
	 *
	 * @var RADConfig
	 */
	protected $config;

	/**
	 * ID of current user
	 *
	 * @var int
	 */
	protected $userId;

	/**
	 * The access levels of the current user
	 *
	 * @var array
	 */
	protected $viewLevels;

	/**
	 * The value represent database null date
	 *
	 * @var string
	 */
	protected $nullDate;

	/**
	 * Render event view
	 *
	 * @return void
	 * @throws Exception
	 */
	public function display()
	{
		if ($this->getLayout() == 'form')
		{
			$this->displayForm();

			return;
		}

		$user   = JFactory::getUser();
		$config = EventbookingHelper::getConfig();

		/* @var EventbookingModelEvent $model */
		$model = $this->getModel();
		$item  = $model->getEventData();

		// Check to make sure the event is valid and user is allowed to access to it
		if (empty($item))
		{
			throw new \Exception(JText::_('EB_EVENT_NOT_FOUND'), 404);
		}

		if (!$item->published && !$user->authorise('core.admin', 'com_eventbooking') && $item->created_by != $user->id)
		{
			throw new \Exception(JText::_('EB_EVENT_NOT_FOUND'), 404);
		}

		if (!in_array($item->access, $user->getAuthorisedViewLevels()))
		{
			throw new \Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		// Update Hits
		$model->updateHits($item->id);

		//Use short description in case user don't enter long description
		if (!EventbookingHelper::isValidMessage($item->description))
		{
			$item->description = $item->short_description;
		}

		if ($item->location_id)
		{
			$this->location = $item->location;
		}

		if ($item->event_type == 1 && $config->show_children_events_under_parent_event)
		{
			$this->items = EventbookingModelEvent::getAllChildrenEvents($item->id);
		}

		if (isset($item->paramData))
		{
			$this->paramData = $item->paramData;
		}

		if ($this->input->get('tmpl', '') == 'component')
		{
			$this->showTaskBar = false;
		}
		else
		{
			$this->showTaskBar = true;
		}

		JPluginHelper::importPlugin('eventbooking');
		$dispatcher = JEventDispatcher::getInstance();
		$plugins    = $dispatcher->trigger('onEventDisplay', array($item));

		$this->viewLevels      = $user->getAuthorisedViewLevels();
		$this->item            = $item;
		$this->state           = $model->getState();
		$this->config          = $config;
		$this->userId          = $user->id;
		$this->nullDate        = JFactory::getDbo()->getNullDate();
		$this->plugins         = $plugins;
		$this->rowGroupRates   = EventbookingHelperDatabase::getGroupRegistrationRates($item->id);
		$this->bootstrapHelper = new EventbookingHelperBootstrap($config->twitter_bootstrap_version);
		$this->print           = $this->input->getInt('print', 0);

		// Prepare document meta data
		$this->prepareDocument();

		parent::display();
	}

	/**
	 * Method to prepare document before it is rendered
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$this->params = $this->getParams();

		// Process page meta data
		if (!$this->params->get('page_title'))
		{
			if ($this->item->page_title)
			{
				$pageTitle = $this->item->page_title;
			}
			else
			{
				$pageTitle = JText::_('EB_EVENT_PAGE_TITLE');
				$pageTitle = str_replace('[EVENT_TITLE]', $this->item->title, $pageTitle);
				$pageTitle = str_replace('[CATEGORY_NAME]', $this->item->category_name, $pageTitle);
			}

			$this->params->set('page_title', $pageTitle);
		}

		$this->params->def('page_heading', $this->item->title);

		$this->params->def('menu-meta_keywords', $this->item->meta_keywords);

		$this->params->def('menu-meta_description', $this->item->meta_description);

		// Load document assets
		$this->loadAssets();

		// Build document pathway
		$this->buildPathway();

		// Set page meta data
		$this->setDocumentMetadata();
	}

	/**
	 * Load assets (javascript/css) for this specific view
	 *
	 * @return void
	 */
	protected function loadAssets()
	{
		if ($this->config->multiple_booking)
		{
			if ($this->deviceType == 'mobile')
			{
				EventbookingHelperJquery::colorbox('eb-colorbox-addcart', '100%', '450px', 'false', 'false');
			}
			else
			{
				EventbookingHelperJquery::colorbox('eb-colorbox-addcart', '800px', 'false', 'false', 'false', 'false');
			}
		}

		if ($this->config->show_list_of_registrants)
		{
			EventbookingHelperJquery::colorbox('eb-colorbox-register-lists');
		}

		$width  = (int) $this->config->get('map_width', 800);
		$height = (int) $this->config->get('map_height', 600);

		if ($this->deviceType == 'mobile')
		{
			EventbookingHelperJquery::colorbox('eb-colorbox-map', '100%', $height . 'px', 'true', 'false');
		}
		else
		{
			EventbookingHelperJquery::colorbox('eb-colorbox-map', $width . 'px', $height . 'px', 'true', 'false');
		}

		if ($this->config->show_invite_friend)
		{
			EventbookingHelperJquery::colorbox('eb-colorbox-invite');
		}

		EventbookingHelperJquery::colorbox('a.eb-modal');
	}

	/**
	 * Method to build document pathway
	 *
	 * @return void
	 */
	protected function buildPathway()
	{
		$app     = JFactory::getApplication();
		$active  = $app->getMenu()->getActive();
		$pathway = $app->getPathway();

		if (isset($active->query['view']) && ($active->query['view'] == 'categories' || $active->query['view'] == 'category'))
		{
			$categoryId = (int) $this->state->get('catid');

			if ($categoryId)
			{
				$parentId = (int) $active->query['id'];
				$paths    = EventbookingHelperData::getCategoriesBreadcrumb($categoryId, $parentId);

				for ($i = count($paths) - 1; $i >= 0; $i--)
				{
					$category = $paths[$i];
					$pathUrl  = EventbookingHelperRoute::getCategoryRoute($category->id, $this->Itemid);
					$pathway->addItem($category->name, $pathUrl);
				}

				$pathway->addItem($this->item->title);
			}
		}
		elseif (isset($active->query['view']) && in_array($active->query['view'], ['calendar', 'upcomingevents']))
		{
			$pathway->addItem($this->item->title);
		}
	}

	/**
	 * Display form which allows add/edit event
	 *
	 * @throws \Exception
	 */
	protected function displayForm()
	{
		EventbookingHelperJquery::colorbox('eb-colorbox-addlocation');

		$user        = JFactory::getUser();
		$config      = EventbookingHelper::getConfig();
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$item        = $this->model->getData();
		$fieldSuffix = EventbookingHelper::getFieldSuffix();

		if ($config->submit_event_form_layout == 'simple')
		{
			$this->setLayout('simple');
		}

		if ($item->id)
		{
			$ret = EventbookingHelperAcl::checkEditEvent($item->id);
		}
		else
		{
			$ret = EventbookingHelperAcl::checkAddEvent();
		}

		if (!$ret)
		{

			$app = JFactory::getApplication();

			if (!$user->id)
			{
				$active = $app->getMenu()->getActive();

				$option = isset($active->query['option']) ? $active->query['option'] : '';
				$view   = isset($active->query['view']) ? $active->query['view'] : '';
				$layout = isset($active->query['layout']) ? $active->query['layout'] : '';

				if ($option == 'com_eventbooking' && $view == 'events' && $layout == 'form')
				{
					$returnUrl = 'index.php?Itemid=' . $active->id;
				}
				else
				{
					$returnUrl = JUri::getInstance()->toString();
				}

				$app->redirect('index.php?option=com_users&view=login&return=' . base64_encode($returnUrl));
			}
			else
			{
				$app->redirect(JUri::root(), JText::_('EB_NO_ADDING_EVENT_PERMISSION'));
			}
		}

		$prices = EventbookingHelperDatabase::getGroupRegistrationRates($item->id);

		//Get list of location
		$options = array();

		$query->select('id, name')
			->from('#__eb_locations')
			->where('published = 1')
			->order('name');

		if (!$user->authorise('core.admin') && !$config->show_all_locations_in_event_submission_form)
		{
			$query->where('user_id = ' . (int) $user->id);
		}

		$db->setQuery($query);

		$options[]            = JHtml::_('select.option', '', JText::_('EB_SELECT_LOCATION'), 'id', 'name');
		$options              = array_merge($options, $db->loadObjectList());
		$lists['location_id'] = JHtml::_('select.genericlist', $options, 'location_id', '', 'id', 'name', $item->location_id);

		// Categories dropdown
		$query->clear()
			->select('id, parent AS parent_id')
			->select($db->quoteName('name' . $fieldSuffix, 'title'))
			->from('#__eb_categories')
			->where('published = 1')
			->where('submit_event_access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')')
			->order($db->quoteName('name' . $fieldSuffix));

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

		$list    = JHtml::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
		$options = array();

		foreach ($list as $listItem)
		{
			$options[] = JHtml::_('select.option', $listItem->id, '&nbsp;&nbsp;&nbsp;' . $listItem->treename, 'value', 'text');
		}

		if ($item->id)
		{
			$query->clear()
				->select('category_id')
				->from('#__eb_event_categories')
				->where('event_id=' . $item->id)
				->where('main_category=1');
			$db->setQuery($query);
			$mainCategoryId = $db->loadResult();

			$query->clear()
				->select('category_id')
				->from('#__eb_event_categories')
				->where('event_id=' . $item->id)
				->where('main_category=0');
			$db->setQuery($query);
			$additionalCategories = $db->loadColumn();
		}
		else
		{
			$mainCategoryId       = 0;
			$additionalCategories = array();
		}

		$lists['main_category_id'] = JHtml::_('select.genericlist', $options, 'main_category_id',
			array(
				'option.text.toHtml' => false,
				'option.text'        => 'text',
				'option.value'       => 'value',
				'list.attr'          => '',
				'list.select'        => $mainCategoryId,));

		$lists['category_id'] = JHtml::_('select.genericlist', $options, 'category_id[]',
			array(
				'option.text.toHtml' => false,
				'option.text'        => 'text',
				'option.value'       => 'value',
				'list.attr'          => 'class="inputbox"  size="5" multiple="multiple"',
				'list.select'        => $additionalCategories,));


		$options   = array();
		$options[] = JHtml::_('select.option', 1, JText::_('%'));
		$options[] = JHtml::_('select.option', 2, $config->currency_symbol);

		$lists['discount_type']            = JHtml::_('select.genericlist', $options, 'discount_type', ' class="input-mini" ', 'value', 'text',
			$item->discount_type);
		$lists['early_bird_discount_type'] = JHtml::_('select.genericlist', $options, 'early_bird_discount_type', ' class="input-mini" ', 'value',
			'text', $item->early_bird_discount_type);

		if ($config->activate_deposit_feature)
		{
			$lists['deposit_type'] = JHtml::_('select.genericlist', $options, 'deposit_type', ' class="input-small" ', 'value', 'text', $item->deposit_type);
		}

		$options   = array();
		$options[] = JHtml::_('select.option', 0, JText::_('EB_INDIVIDUAL_GROUP'));
		$options[] = JHtml::_('select.option', 1, JText::_('EB_INDIVIDUAL_ONLY'));
		$options[] = JHtml::_('select.option', 2, JText::_('EB_GROUP_ONLY'));
		$options[] = JHtml::_('select.option', 3, JText::_('EB_DISABLE_REGISTRATION'));

		$lists['registration_type'] = JHtml::_('select.genericlist', $options, 'registration_type', ' class="inputbox" ', 'value', 'text',
			$item->registration_type);

		$lists['access']                     = JHtml::_('access.level', 'access', $item->access, 'class="inputbox"', false);
		$lists['registration_access']        = JHtml::_('access.level', 'registration_access', $item->registration_access, 'class="inputbox"', false);
		$lists['enable_cancel_registration'] = JHtml::_('select.booleanlist', 'enable_cancel_registration', ' class="inputbox" ',
			$item->enable_cancel_registration);
		$lists['enable_auto_reminder']       = JHtml::_('select.booleanlist', 'enable_auto_reminder', ' class="inputbox" ', $item->enable_auto_reminder);
		$lists['published']                  = JHtml::_('select.booleanlist', 'published', ' class="inputbox" ', $item->published);

		if ($item->event_date != $db->getNullDate())
		{
			$selectedHour   = date('G', strtotime($item->event_date));
			$selectedMinute = date('i', strtotime($item->event_date));
		}
		else
		{
			$selectedHour   = 0;
			$selectedMinute = 0;
		}

		$lists['event_date_hour']   = JHtml::_('select.integerlist', 0, 23, 1, 'event_date_hour', ' class="input-mini" ', $selectedHour);
		$lists['event_date_minute'] = JHtml::_('select.integerlist', 0, 55, 5, 'event_date_minute', ' class="input-mini" ', $selectedMinute, '%02d');

		if ($item->event_end_date != $db->getNullDate())
		{
			$selectedHour   = date('G', strtotime($item->event_end_date));
			$selectedMinute = date('i', strtotime($item->event_end_date));
		}
		else
		{
			$selectedHour   = 0;
			$selectedMinute = 0;
		}

		$lists['event_end_date_hour']   = JHtml::_('select.integerlist', 0, 23, 1, 'event_end_date_hour', ' class="input-mini" ', $selectedHour);
		$lists['event_end_date_minute'] = JHtml::_('select.integerlist', 0, 55, 5, 'event_end_date_minute', ' class="input-mini" ', $selectedMinute,
			'%02d');

		// Cut off time
		if ($item->cut_off_date != $db->getNullDate())
		{
			$selectedHour   = date('G', strtotime($item->cut_off_date));
			$selectedMinute = date('i', strtotime($item->cut_off_date));
		}
		else
		{
			$selectedHour   = 0;
			$selectedMinute = 0;
		}

		$lists['cut_off_hour']   = JHtml::_('select.integerlist', 0, 23, 1, 'cut_off_hour', ' class="inputbox input-mini" ', $selectedHour);
		$lists['cut_off_minute'] = JHtml::_('select.integerlist', 0, 55, 5, 'cut_off_minute', ' class="inputbox input-mini" ', $selectedMinute, '%02d');

		// Registration start time
		if ($item->registration_start_date != $db->getNullDate())
		{
			$selectedHour   = date('G', strtotime($item->registration_start_date));
			$selectedMinute = date('i', strtotime($item->registration_start_date));
		}
		else
		{
			$selectedHour   = 0;
			$selectedMinute = 0;
		}

		$lists['registration_start_hour']   = JHtml::_('select.integerlist', 0, 23, 1, 'registration_start_hour', ' class="inputbox input-mini" ', $selectedHour);
		$lists['registration_start_minute'] = JHtml::_('select.integerlist', 0, 55, 5, 'registration_start_minute', ' class="inputbox input-mini" ', $selectedMinute, '%02d');

		$query->clear()
			->select('id, title')
			->from('#__content')
			->where('`state` = 1')
			->order('title');
		$db->setQuery($query);
		$options             = array();
		$options[]           = JHtml::_('select.option', 0, JText::_('EB_SELECT_ARTICLE'), 'id', 'title');
		$options             = array_merge($options, $db->loadObjectList());
		$lists['article_id'] = JHtml::_('select.genericlist', $options, 'article_id', 'class="inputbox"', 'id', 'title', $item->article_id);

		$options   = array();
		$options[] = JHtml::_('select.option', 0, JText::_('JNO'));
		$options[] = JHtml::_('select.option', 1, JText::_('JYES'));

		$lists['published']                  = JHtml::_('select.genericlist', $options, 'published', ' class="input-medium" ', 'value', 'text', $item->published);
		$lists['enable_cancel_registration'] = JHtml::_('select.genericlist', $options, 'enable_cancel_registration', ' class="input-medium" ', 'value', 'text', $item->enable_cancel_registration);
		$lists['enable_auto_reminder']       = JHtml::_('select.genericlist', $options, 'enable_auto_reminder', ' class="input-medium" ', 'value', 'text', $item->enable_auto_reminder);

		//Custom field handles
		if ($config->event_custom_field)
		{
			$registry = new Registry;
			$registry->loadString($item->custom_fields);
			$data         = new stdClass;
			$data->params = $registry->toArray();
			$form         = JForm::getInstance('pmform', JPATH_ROOT . '/components/com_eventbooking/fields.xml', array(), false, '//config');
			$form->bind($data);
			$this->form = $form;
		}

		// Load captcha
		$this->loadCaptcha();

		$this->item     = $item;
		$this->prices   = $prices;
		$this->lists    = $lists;
		$this->nullDate = $db->getNullDate();
		$this->config   = $config;
		$this->return   = $this->input->getBase64('return');

		parent::display();
	}
}