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

class EventbookingControllerEvent extends EventbookingController
{
	/**
	 * Import Events from a csv file
	 */
	public function import()
	{
		$inputFile = $this->input->files->get('input_file');
		$fileName  = $inputFile ['name'];
		$fileExt   = strtolower(JFile::getExt($fileName));

		if (!in_array($fileExt, array('csv', 'xls', 'xlsx')))
		{
			$this->setRedirect('index.php?option=com_eventbooking&view=event&layout=import', JText::_('Invalid File Type. Only CSV, XLS and XLS file types are supported'));

			return;
		}

		/* @var  EventbookingModelEvent $model */
		$model = $this->getModel('Event');
		try
		{
			$numberImportedEvents = $model->import($inputFile['tmp_name']);
			$this->setRedirect('index.php?option=com_eventbooking&view=events', JText::sprintf('EB_NUMBER_EVENTS_IMPORTED', $numberImportedEvents));
		}
		catch (Exception $e)
		{
			$this->setRedirect('index.php?option=com_eventbooking&view=event&layout=import');
			$this->setMessage($e->getMessage(), 'error');
		}
	}

	/**
	 * Export events into an Excel File
	 */
	public function export()
	{
		set_time_limit(0);
		$model = $this->getModel('events');

		/* @var EventbookingModelEvents $model */

		$model->setState('limitstart', 0)
			->setState('limit', 0);

		$cid = $this->input->get('cid', array(), 'array');
		$model->setEventIds($cid);

		$rowEvents = $model->getData();

		if (count($rowEvents) == 0)
		{
			$this->setMessage(JText::_('There are no events to export'));
			$this->setRedirect('index.php?option=com_eventbooking&view=events');

			return;
		}

		$config = EventbookingHelper::getConfig();

		$fields = array(
			'id',
			'title',
			'alias',
			'category',
			'additional_categories',
			'image',
			'location',
			'event_date',
			'event_end_date',
			'cut_off_date',
			'registration_start_date',
			'individual_price',
			'tax_rate',
			'event_capacity',
			'registration_type',
			'registration_handle_url',
			'attachment',
			'short_description',
			'description',
			'event_password',
			'access',
			'registration_access',
			'featured',
			'published',
			'created_by',
			'min_group_number',
			'max_group_number',
			'enable_coupon',
			'deposit_amount',
			'deposit_type',
			'enable_cancel_registration',
			'cancel_before_date',
			'enable_auto_reminder',
			'remind_before_x_days',
			'meta_keywords',
			'meta_description',
			'discount_groups',
			'discount',
			'discount_type',
			'early_bird_discount_amount',
			'early_bird_discount_type',
			'early_bird_discount_date',
			'tbl.enable_terms_and_conditions',
		);

		if ($config->event_custom_field)
		{
			EventbookingHelperData::prepareCustomFieldsData($rowEvents);
			$fields = array_merge($fields, array_keys($rowEvents[0]->paramData));
		}

		EventbookingHelperData::excelExport($fields, $rowEvents, 'events_list');
	}
}
