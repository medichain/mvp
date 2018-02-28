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
 * Category view class
 *
 * @property EventbookingModelCategory $model
 */
class EventbookingViewCategoryHtml extends RADViewList
{
	/**
	 * Id of the active category
	 *
	 * @var int
	 */
	protected $categoryId;

	/**
	 * The active category
	 *
	 * @var stdClass
	 */
	protected $category = null;

	/**
	 * List of children categories
	 *
	 * @var array
	 */
	protected $categories = array();

	/**
	 * Component config
	 *
	 * @var RADConfig
	 */
	protected $config;

	/**
	 * Twitter bootstrap helper
	 *
	 * @var EventbookingHelperBootstrap
	 */
	protected $bootstrapHelper;

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
	 * Prepare the view data before it is rendered
	 *
	 * @return  void
	 * @throws  \Exception
	 */
	protected function prepareView()
	{
		parent::prepareView();

		$user   = JFactory::getUser();
		$config = EventbookingHelper::getConfig();

		// If category id is passed, make sure it is valid and the user is allowed to access
		if ($categoryId = (int) $this->state->get('id'))
		{
			$this->category = $this->model->getCategory();

			if (empty($this->category))
			{
				throw new Exception(JText::_('JGLOBAL_CATEGORY_NOT_FOUND'), 404);
			}

			if (!in_array($this->category->access, $user->getAuthorisedViewLevels()))
			{
				throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
			}

			// Load sub-categories of the current category
			$model = new EventbookingModelCategories(
				[
					'table_prefix'    => '#__eb_',
					'remember_states' => false,
					'ignore_request'  => true,
				]
			);

			$this->categories = $model->setState('limitstart', 0)
				->setState('limit', 0)
				->setState('filter_order', 'tbl.ordering')
				->setState('id', $categoryId)
				->getData();
		}

		// Set layout for this category from category setup
		$layout = $this->getLayout();

		if (($layout == '' || $layout == 'default') && !empty($this->category->layout))
		{
			$this->setLayout($this->category->layout);
		}

		// Calculate page intro text
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$message     = EventbookingHelper::getMessages();

		if ($fieldSuffix && EventbookingHelper::isValidMessage($message->{'intro_text' . $fieldSuffix}))
		{
			$introText = $message->{'intro_text' . $fieldSuffix};
		}
		elseif (EventbookingHelper::isValidMessage($message->intro_text))
		{
			$introText = $message->intro_text;
		}
		else
		{
			$introText = '';
		}

		if ($config->multiple_booking)
		{
			// Store last access category for routing back from shopping cart
			JFactory::getSession()->set('last_category_id', $categoryId);
		}

		$this->config          = $config;
		$this->categoryId      = $categoryId;
		$this->bootstrapHelper = new EventbookingHelperBootstrap($config->twitter_bootstrap_version);
		$this->viewLevels      = $user->getAuthorisedViewLevels();
		$this->userId          = $user->id;
		$this->nullDate        = JFactory::getDbo()->getNullDate();
		$this->introText       = $introText;

		// Prepare document meta data before it is rendered
		$this->prepareDocument();
	}

	/**
	 * Method to prepare document before it is rendered
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		if ($this->category)
		{
			$query = array('id' => $this->category->id);
		}
		else
		{
			$query =  array();
		}

		$this->params = $this->getParams(['categories', 'category'], $query);

		// Page title
		if (!$this->params->get('page_title') && $this->category)
		{
			// Page title
			if ($this->category->page_title)
			{
				$pageTitle = $this->category->page_title;
			}
			else
			{
				$pageTitle = JText::_('EB_SUB_CATEGORIES_PAGE_TITLE');
				$pageTitle = str_replace('[CATEGORY_NAME]', $this->category->name, $pageTitle);
			}

			$this->params->set('page_title', $pageTitle);
		}

		// Page heading
		if (!$this->params->get('page_heading'))
		{
			if ($this->category)
			{
				$pageHeading = $this->category->page_heading ?: $this->category->name;
			}
			else
			{
				$pageHeading = JText::_('EB_EVENT_LIST');
			}

			$this->params->set('page_heading', $pageHeading);
		}

		// Meta keywords and description
		if (!$this->params->get('menu-meta_keywords') && !empty($this->category->meta_keywords))
		{
			$this->params->set('menu-meta_keywords', $this->category->meta_keywords);
		}

		if (!$this->params->get('menu-meta_description') && !empty($this->category->meta_description))
		{
			$this->params->set('menu-meta_description', $this->category->meta_description);
		}

		// Load required assets for the view
		$this->loadAssets();

		// Build pathway
		$this->buildPathway();

		// Set page meta data
		$this->setDocumentMetadata();

		// Add Feed links to document
		if ($this->config->get('show_feed_link', 1))
		{
			$this->addFeedLinks();
		}

		// Use override menu item
		if ($this->params->get('menu_item_id') > 0)
		{
			$this->Itemid = $this->params->get('menu_item_id');
		}
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

		if ($this->config->show_location_in_category_view || ($this->getLayout() == 'timeline'))
		{
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
		$app    = JFactory::getApplication();
		$active = $app->getMenu()->getActive();

		if (isset($active->query['view']) && in_array($active->query['view'], ['categories', 'category']))
		{
			$parentId = (int) $active->query['id'];

			if ($categoryId = $this->state->get('id'))
			{
				$pathway = $app->getPathway();
				$paths   = EventbookingHelperData::getCategoriesBreadcrumb($categoryId, $parentId);

				for ($i = count($paths) - 1; $i >= 0; $i--)
				{
					$path    = $paths[$i];
					$pathUrl = EventbookingHelperRoute::getCategoryRoute($path->id, $this->Itemid);
					$pathway->addItem($path->name, $pathUrl);
				}
			}
		}
	}
}
