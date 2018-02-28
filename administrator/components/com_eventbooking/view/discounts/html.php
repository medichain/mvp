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

class EventbookingViewDiscountsHtml extends RADViewList
{
	protected function prepareView()
	{
		parent::prepareView();

		$this->nullDate = JFactory::getDbo()->getNullDate();
		$this->config   = EventbookingHelper::getConfig();
	}
}
