<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class EventbookingControllerMassmail extends EventbookingController
{
	/**
	 * Send Massmail to registrants of an event
	 */
	public function send()
	{
		// Check and make sure this user has registrant management permission
		if (JFactory::getUser()->authorise('eventbooking.registrantsmanagement', 'com_eventbooking'))
		{
			$data = $this->input->getData();

			/* @var EventbookingModelMassmail $model */
			$model = $this->getModel();
			$model->send($data);
			$this->setRedirect(JRoute::_('index.php?option=com_eventbooking&view=massmail&Itemid=' . $this->input->getInt('Itemid'), false), JText::_('EB_EMAIL_SENT'));
		}
		else
		{
			throw new Exception('You do not have permission to send mass mail', 403);
		}
	}
}
