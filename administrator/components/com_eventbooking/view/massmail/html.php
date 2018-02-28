<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class EventbookingViewMassmailHtml extends RADViewHtml
{
	public function display()
	{
		$lists['event_id'] = EventbookingHelperHtml::getEventsDropdown(EventbookingHelperDatabase::getAllEvents(), 'event_id');
		$this->lists       = $lists;

		parent::display();
	}
}
