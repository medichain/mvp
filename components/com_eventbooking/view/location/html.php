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
 * Location Html View class
 *
 * @property EventbookingModelLocation $model
 */
class EventbookingViewLocationHtml extends RADViewHtml
{
	/**
	 * Location data
	 *
	 * @var \stdClass
	 */
	protected $location;

	/**
	 * List of events from the location
	 *
	 * @var array
	 */
	protected $items;

	/**
	 * Pagination object
	 *
	 * @var JPagination
	 */
	protected $pagination;

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
	 * Display events from a location
	 */
	public function display()
	{
		$layout = $this->getLayout();

		if (in_array($this->getLayout(), ['form', 'popup']))
		{
			$this->displayForm();

			return;
		}

		$location = EventbookingHelperDatabase::getLocation($this->input->getInt('location_id'));

		// Make sure this is a valid location
		if (empty($location))
		{
			throw new Exception(JText::_('EB_LOCATION_NOT_FOUND'), 404);
		}

		// Set the layout to display events from this location
		if (($layout == '' || $layout == 'default') && !empty($location->layout))
		{
			$this->setLayout($location->layout);
		}

		$user = JFactory::getUser();

		$this->location        = $location;
		$this->items           = $this->model->getData();
		$this->pagination      = $this->model->getPagination();
		$this->config          = EventbookingHelper::getConfig();
		$this->bootstrapHelper = new EventbookingHelperBootstrap($this->config->twitter_bootstrap_version);

		$this->nullDate   = JFactory::getDbo()->getNullDate();
		$this->viewLevels = $user->getAuthorisedViewLevels();
		$this->userId     = $user->get('id');

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

		// Page title
		if (!$this->params->get('page_title'))
		{
			$this->params->set('page_title', $this->location->name);
		}


		// Page heading
		$this->params->def('page_heading', $this->location->name);

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
		// Load requires javascript libraries
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

		EventbookingHelperJquery::colorbox('eb-modal');
	}

	/**
	 * Method to build document pathway
	 *
	 * @return void
	 */
	protected function buildPathway()
	{
		JFactory::getApplication()->getPathway()->addItem($this->location->name);
	}

	/**
	 * Display Form to allow adding location for event
	 *
	 * @throws \Exception
	 */
	protected function displayForm()
	{
		if (!JFactory::getUser()->authorise('eventbooking.addlocation', 'com_eventbooking'))
		{
			JFactory::getApplication()->redirect(JUri::root(), JText::_('EB_NO_PERMISSION'));

			return;
		}
		
		$document = JFactory::getDocument();
		$document->addScriptDeclaration(
			'var siteUrl = "' . EventbookingHelper::getSiteUrl() . '";'
		);

		$config = EventbookingHelper::getConfig();
		$item   = $this->model->getLocationData();

		$options   = array();
		$options[] = JHtml::_('select.option', '', JText::_('EB_SELECT_COUNTRY'), 'value', 'text');
		$countries = EventbookingHelperDatabase::getAllCountries();

		foreach ($countries as $country)
		{
			$options[] = JHtml::_('select.option', $country->name, $country->name);
		}

		$lists['country']   = JHtml::_('select.genericlist', $options, 'country', '', 'value', 'text', $item->country);
		$lists['published'] = JHtml::_('select.booleanlist', 'published', '', $item->published);

		$this->item   = $item;
		$this->lists  = $lists;
		$this->config = $config;

		$this->bootstrapHelper = new EventbookingHelperBootstrap($config->twitter_bootstrap_version);

		parent::display();
	}
}
