<?php
/**
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2017 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

$description = $this->category ? $this->category->description: $this->introText;
?>
<div id="eb-upcoming-events-page-timeline" class="eb-container">
	<?php
	if ($this->params->get('show_page_heading'))
	{
	?>
		<h1 class="eb-page-heading"><?php echo $this->params->get('page_heading');?></h1>
	<?php
	}

	if ($description)
	{
	?>
		<div class="eb-description"><?php echo $description;?></div>
	<?php
	}

	if (count($this->items))
	{
		echo EventbookingHelperHtml::loadCommonLayout('common/tmpl/events_timeline.php', array('events' => $this->items, 'config' => $this->config, 'Itemid' => $this->Itemid, 'nullDate' => $this->nullDate , 'ssl' => (int) $this->config->use_https, 'viewLevels' => $this->viewLevels, 'category' => $this->category, 'Itemid' => $this->Itemid, 'bootstrapHelper' => $this->bootstrapHelper));
	}
	else
	{
	?>
		<p class="text-info"><?php echo JText::_('EB_NO_UPCOMING_EVENTS') ?></p>
	<?php
	}

	if ($this->pagination->total > $this->pagination->limit)
	{
	?>
		<div class="pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php
	}
	?>

	<form method="post" name="adminForm" id="adminForm" action="<?php echo JRoute::_('index.php?option=com_eventbooking&view=upcomingevents&layout=timeline&Itemid='.$this->Itemid); ?>">
		<input type="hidden" name="id" value="0" />
		<input type="hidden" name="task" value="" />
		<script type="text/javascript">
			function cancelRegistration(registrantId)
			{
				var form = document.adminForm ;

				if (confirm("<?php echo JText::_('EB_CANCEL_REGISTRATION_CONFIRM'); ?>"))
				{
					form.task.value = 'registrant.cancel' ;
					form.id.value = registrantId ;
					form.submit() ;
				}
			}
		</script>
	</form>
</div>