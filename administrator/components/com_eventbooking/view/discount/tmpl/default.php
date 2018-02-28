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
JHtml::_('formbehavior.chosen', 'select');
?>
<script type="text/javascript">
	Joomla.submitbutton = function (pressbutton)
	{
		var form = document.adminForm;
		if (pressbutton == 'cancel')
		{
			Joomla.submitform(pressbutton);
			return;
		}
		else if (form.discount_amount.value == "")
		{
			alert("<?php echo JText::_("EB_ENTER_DISCOUNT_AMOUNT"); ?>");
			form.discount_amount.focus();
		}
		else
		{
			Joomla.submitform(pressbutton);
		}
	}
</script>
<form action="index.php?option=com_eventbooking&view=discount" method="post" name="adminForm" id="adminForm" class="form form-horizontal">
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_TITLE'); ?>
		</label>
		<div class="controls">
			<input class="input-xlarge" type="text" name="title" id="title" maxlength="250"
			       value="<?php echo $this->item->title; ?>"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_DISCOUNT_AMOUNT'); ?>
		</label>
		<div class="controls">
			<input class="input-small" type="text" name="discount_amount" id="discount_amount" size="10" maxlength="250"
			       value="<?php echo $this->item->discount_amount; ?>"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_EVENT'); ?>
		</label>
		<div class="controls">
			<?php echo $this->lists['event_id']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_TIMES'); ?>
		</label>
		<div class="controls">
			<input class="input-small" type="text" name="times" id="times" size="5" maxlength="250"
			       value="<?php echo $this->item->times; ?>"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_TIME_USED'); ?>
		</label>
		<div class="controls">
			<?php echo $this->item->used; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_FROM_DATE'); ?>
		</label>
		<div class="controls">
			<?php echo JHtml::_('calendar', $this->item->from_date != $this->nullDate ? $this->item->from_date : '', 'from_date', 'from_date'); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_TO_DATE'); ?>
		</label>
		<div class="controls">
			<?php echo JHtml::_('calendar', $this->item->to_date != $this->nullDate ? $this->item->to_date : '', 'to_date', 'to_date'); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_PUBLISHED'); ?>
		</label>
		<div class="controls">
			<?php echo $this->lists['published']; ?>
		</div>
	</div>
	<div class="clearfix"></div>
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>"/>
	<input type="hidden" name="task" value=""/>
	<?php
	if (!$this->item->used)
	{
	?>
		<input type="hidden" name="used" value="0"/>
	<?php
	}
	?>
</form>