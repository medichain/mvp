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
 * @property EventbookingModelFullcalendar $model
 */
class EventbookingViewFullcalendarRaw extends RADView
{
	public function display()
	{
		$rootUri = JUri::root(true);
		$rows    = $this->model->getData();
		$config  = EventbookingHelper::getConfig();

		for ($i = 0, $n = count($rows); $i < $n; $i++)
		{
			$row      = $rows[$i];
			$row->url = JRoute::_(EventbookingHelperRoute::getEventRoute($row->id, 0, $this->Itemid));

			if ($row->color_code)
			{
				$row->backgroundColor = '#' . $row->color_code;
			}

			if ($row->text_color)
			{
				$row->textColor = '#' . $row->text_color;
			}

			if ($config->show_thumb_in_calendar && $row->thumb)
			{
				$row->thumb = $rootUri . '/media/com_eventbooking/images/thumbs/' . $row->thumb;
			}
			else
			{
				$row->thumb = '';
			}
		}

		echo json_encode($rows);
	}
}
