<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

class EventbookingViewCalendarHtml extends RADViewHtml
{
	public function display()
	{
		$config                 = EventbookingHelper::getConfig();
		$this->currentDateData  = EventbookingModelCalendar::getCurrentDateData();
		$this->showCalendarMenu = $config->activate_weekly_calendar_view || $config->activate_daily_calendar_view;
		$this->config           = $config;

		$this->findAndSetActiveMenuItem();

		#Support Weekly and Daily
		$layout = $this->getLayout();

		if ($layout == 'weekly')
		{
			$this->displayWeeklyView();

			return;
		}
		elseif ($layout == 'daily')
		{
			$this->displayDailyView();

			return;
		}

		$this->setLayout('default');

		/* @var EventbookingModelCalendar $model */
		$model = $this->getModel();
		$rows  = $model->getData();

		$state = $model->getState();
		$year  = $state->year;
		$month = $state->month;

		$this->data  = EventbookingHelperData::getCalendarData($rows, $year, $month);
		$this->month = $month;
		$this->year  = $year;

		$listMonth = array(
			JText::_('EB_JAN'),
			JText::_('EB_FEB'),
			JText::_('EB_MARCH'),
			JText::_('EB_APR'),
			JText::_('EB_MAY'),
			JText::_('EB_JUNE'),
			JText::_('EB_JULY'),
			JText::_('EB_AUG'),
			JText::_('EB_SEP'),
			JText::_('EB_OCT'),
			JText::_('EB_NOV'),
			JText::_('EB_DEC'),);
		$options   = array();

		foreach ($listMonth as $key => $monthName)
		{
			$value     = $key + 1;
			$options[] = JHtml::_('select.option', $value, $monthName);
		}

		$this->searchMonth = JHtml::_('select.genericlist', $options, 'month', 'class="input-medium" onchange="submit();" ', 'value', 'text', (int) $month);

		$options = array();

		for ($i = $year - 3; $i < ($year + 5); $i++)
		{
			$options[] = JHtml::_('select.option', $i, $i);
		}

		$this->searchYear = JHtml::_('select.genericlist', $options, 'year', 'class="input-small" onchange="submit();" ', 'value', 'text', $year);

		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$message     = EventbookingHelper::getMessages();

		$categoryIds = array_filter(ArrayHelper::toInteger($this->model->getParams()->get('category_ids')));

		if (count($categoryIds) == 1)
		{
			$categoryId = $categoryIds[0];
			$category   = EventbookingHelperDatabase::getCategory($categoryId);
			$introText  = $category->description;
		}
		elseif (strlen($message->{'intro_text' . $fieldSuffix}))
		{
			$introText = $message->{'intro_text' . $fieldSuffix};
		}
		else
		{
			$introText = $message->intro_text;
		}



		$this->listMonth = $listMonth;
		$this->params    = $this->getParams();
		$this->introText = $introText;

		$this->setDocumentMetadata();

		parent::display();
	}

	/**
	 * Display weekly events
	 */
	protected function displayWeeklyView()
	{
		/* @var EventbookingModelCalendar $model */
		$model = $this->getModel();

		$this->events            = $model->getEventsByWeek();
		$this->first_day_of_week = $model->getState('date');

		parent::display();
	}

	/**
	 * Display daily events
	 */
	protected function displayDailyView()
	{
		EventbookingHelperJquery::colorbox('eb-colorbox-addlocation');

		/* @var EventbookingModelCalendar $model */
		$model = $this->getModel();

		$this->events = $model->getEventsByDaily();
		$this->day    = $model->getState('day');

		parent::display();
	}
}
