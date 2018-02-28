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

/**
 * Membership Pro controller
 *
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class EventbookingControllerCoupon extends EventbookingController
{
	/**
	 * Method to import coupon codes from a csv file
	 */
	public function import()
	{
		$inputFile = $this->input->files->get('input_file');
		$fileName  = $inputFile ['name'];
		$fileExt   = strtolower(JFile::getExt($fileName));

		if (!in_array($fileExt, array('csv', 'xls', 'xlsx')))
		{
			$this->setRedirect('index.php?option=com_eventbooking&view=coupon&layout=import', JText::_('Invalid File Type. Only CSV, XLS and XLS file types are supported'));

			return;
		}

		/* @var  EventbookingModelCoupon $model */
		$model = $this->getModel('Coupon');
		try
		{
			$numberImportedCoupons = $model->import($inputFile['tmp_name']);
			$this->setRedirect('index.php?option=com_eventbooking&view=coupons', JText::sprintf('EB_NUMBER_COUPONS_IMPORTED', $numberImportedCoupons));
		}
		catch (Exception $e)
		{
			$this->setRedirect('index.php?option=com_eventbooking&view=coupon&layout=import');
			$this->setMessage($e->getMessage(), 'error');
		}
	}

	/**
	 * Export Coupons into a CSV file
	 */
	public function export()
	{
		set_time_limit(0);
		$model = $this->getModel('coupons');

		/* @var EventbookingModelCoupons $model */

		$model->setState('limitstart', 0)
			->setState('limit', 0)
			->setState('filter_order', 'tbl.id')
			->setState('filter_order_Dir', 'ASC');
		$rows = $model->getData();

		if (count($rows) == 0)
		{
			$this->setMessage(JText::_('There are no coupons to export'));
			$this->setRedirect('index.php?option=com_eventbooking&view=coupons');

			return;
		}

		$fields = array(
			'event',
			'code',
			'discount',
			'coupon_type',
			'times',
			'used',
			'valid_from',
			'valid_to',
			'published',
		);

		$db       = JFactory::getDbo();
		$query    = $db->getQuery(true);
		$nullDate = $db->getNullDate();

		// Prepare data
		foreach ($rows as $row)
		{
			if ($row->event_id == -1)
			{
				$row->event = '';
			}
			else
			{
				$query->clear()
					->select('a.id')
					->from('#__eb_events AS a')
					->leftJoin('#__eb_coupon_events AS b ON a.id = b.event_id')
					->where('b.coupon_id=' . (int) $row->id);
				$db->setQuery($query);
				$row->event = implode(',', $db->loadColumn());
			}

			$row->discount = round($row->discount, 2);

			if ($row->valid_from != $nullDate && $row->valid_from)
			{
				$row->valid_from = JHtml::_('date', $row->valid_from, 'Y-m-d', null);
			}
			else
			{
				$row->valid_from = '';
			}

			if ($row->valid_to != $nullDate && $row->valid_to)
			{
				$row->valid_to = JHtml::_('date', $row->valid_to, 'Y-m-d', null);
			}
			else
			{
				$row->valid_to = '';
			}
		}

		EventbookingHelperData::excelExport($fields, $rows, 'coupons_list');
	}

	/**
	 * Batch coupon generation
	 */
	public function batch()
	{
		$model = $this->getModel('Coupon');
		$model->batch($this->input);
		$this->setRedirect('index.php?option=com_eventbooking&view=coupons', JText::_('EB_COUPONS_SUCCESSFULLY_GENERATED'));
	}
}
