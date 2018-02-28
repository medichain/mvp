<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

$pageHeading = $this->params->get('page_heading') ? $this->params->get('page_heading') : JText::_('EB_CALENDAR');
?>
<div id="eb-calendar-page" class="eb-container">
	<h1 class="eb-page-heading"><?php echo $pageHeading; ?></h1>
	<?php
	if (EventbookingHelper::isValidMessage($this->introText))
	{
	?>
		<div class="eb-description"><?php echo $this->introText; ?></div>
	<?php
	}
	?>
	<div id='eb_full_calendar'></div>
</div>

<script>
	var calendarOptions = <?php echo json_encode($this->getCalendarOptions()); ?>;
	(function ($) {
		eventRenderFunc = function (event, element) {
			if (event.thumb)
			{
				element.find('.fc-content').prepend('<img src="' + event.thumb + '" title="' + event.title + '" class="img-polaroid" border="0" align="top" />');
			}
		};

		calendarOptions['eventRender'] = eventRenderFunc;

		$(document).ready(function () {
			$('#eb_full_calendar').fullCalendar(
				calendarOptions
			);
		});
	}(jQuery));
</script>
