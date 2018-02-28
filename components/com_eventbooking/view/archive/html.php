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

class EventbookingViewArchiveHtml extends RADViewHtml
{
	public function display()
	{
		$app    = JFactory::getApplication();
		$active = $app->getMenu()->getActive();
		$model  = $this->getModel();
		$state  = $model->getState();
		$items  = $model->getData();
		$config = EventbookingHelper::getConfig();

		$category = null;
		if ($state->id)
		{
			$category = EventbookingHelperDatabase::getCategory($state->id);
		}

		if ($config->show_list_of_registrants)
		{
			EventbookingHelperJquery::colorbox('eb-colorbox-register-lists');
		}

		if ($config->show_location_in_category_view)
		{
			$width  = (int) $config->get('map_width', 800);
			$height = (int) $config->get('map_height', 600);
			if ($this->deviceType == 'mobile')
			{
				EventbookingHelperJquery::colorbox('eb-colorbox-map', '100%', $height . 'px', 'true', 'false');
			}
			else
			{
				EventbookingHelperJquery::colorbox('eb-colorbox-map', $width . 'px', $height . 'px', 'true', 'false');
			}
		}

		// Process page meta data
		$params = EventbookingHelper::getViewParams($active, array('archive'));

		if (!$params->get('page_title'))
		{
			$params->set('page_title', JText::_('EB_EVENTS_ARCHIVE'));
		}

		EventbookingHelperHtml::prepareDocument($params, $category);

		$this->findAndSetActiveMenuItem();

		$this->items           = $items;
		$this->pagination      = $model->getPagination();
		$this->config          = $config;
		$this->categoryId      = $state->id;
		$this->category        = $category;
		$this->nullDate        = JFactory::getDbo()->getNullDate();
		$this->bootstrapHelper = new EventbookingHelperBootstrap($config->twitter_bootstrap_version);

		parent::display();
	}
}
