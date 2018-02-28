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

use Joomla\Utilities\ArrayHelper;

class EventbookingControllerField extends EventbookingController
{
	public function __construct(RADInput $input = null, array $config = array())
	{
		parent::__construct($input, $config);

		$this->registerTask('un_required', 'required');
	}

	/**
	 * Change status of the required fields to make them required/not required
	 */
	public function required()
	{
		$cid  = $this->input->get('cid', array(), 'array');
		$cid  = ArrayHelper::toInteger($cid);
		$task = $this->getTask();

		if ($task == 'required')
		{
			$state = 1;
		}
		else
		{
			$state = 0;
		}

		/* @var EventbookingModelField $model */
		$model = $this->getModel();
		$model->required($cid, $state);

		$this->setRedirect(JRoute::_('index.php?option=com_eventbooking&view=fields', false), JText::_('EB_FIELD_REQUIRED_STATE_UPDATED'));
	}
}
