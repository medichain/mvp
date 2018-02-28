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

//Calculate next and previous month, year
if ($this->month == 12)
{
	$nextMonth = 1 ;
	$nextYear = $this->year + 1 ;
	$previousMonth = 11 ;
	$previousYear = $this->year ;
}
elseif ($this->month == 1)
{
	$nextMonth = 2 ;
	$nextYear = $this->year ;
	$previousMonth = 12 ;
	$previousYear = $this->year - 1 ;
}
else
{
	$nextMonth = $this->month + 1 ;
	$nextYear = $this->year ;
	$previousMonth = $this->month - 1 ;
	$previousYear = $this->year ;
}
?>
<div id="eb-calendar-page" class="eb-container">
	<?php
		$pageHeading = $this->params->get('page_heading') ? $this->params->get('page_heading') : JText::_('EB_CALENDAR');
	?>
	<h1 class="eb-page-heading"><?php echo $pageHeading; ?></h1>
	<?php
	if (EventbookingHelper::isValidMessage($this->introText))
	{
	?>
		<div class="eb-description"><?php echo $this->introText;?></div>
	<?php
	}
	?>
	<form method="post" name="adminForm" id="adminForm" action="<?php echo JRoute::_('index.php?option=com_eventbooking&view=calendar&Itemid=' . $this->Itemid); ?>">
			<div id="eb-calendarwrap">
				<?php
						if ($this->showCalendarMenu)
						{
								echo EventbookingHelperHtml::loadCommonLayout('common/tmpl/calendar_navigation.php', array('Itemid' => $this->Itemid, 'config' => $this->config, 'layout' => 'default', 'currentDateData' => $this->currentDateData));
						}

						echo EventbookingHelperHtml::loadCommonLayout('common/tmpl/calendar.php',
							array(
									'Itemid' => $this->Itemid,
									'config' => $this->config,
									'month' => $this->month,
									'previousMonth' => $previousMonth,
									'nextMonth' => $nextMonth,
									'previousMonthLink' => JRoute::_('index.php?option=com_eventbooking&view=calendar&month=' . $previousMonth . '&year=' . $previousYear . '&Itemid=' . $this->Itemid),
									'nextMonthLink' => JRoute::_('index.php?option=com_eventbooking&view=calendar&month=' . $nextMonth . '&year=' . $nextYear . '&next=1&Itemid=' . $this->Itemid),
									'listMonth' => $this->listMonth,
									'searchMonth' => $this->searchMonth,
									'searchYear' => $this->searchYear,
									'data'    => $this->data
							));
				?>
			</div>
	</form>
</div>