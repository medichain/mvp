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

class EventbookingControllerEmail extends EventbookingController
{
	public function delete_all()
	{
		JFactory::getDbo()->truncateTable('#__eb_emails');

		$this->setRedirect('index.php?option=com_eventbooking&view=emails');
	}
}
