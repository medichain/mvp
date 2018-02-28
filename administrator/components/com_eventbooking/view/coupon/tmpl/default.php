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

if (!empty($this->registrants))
{
	JHtml::_('behavior.tabstate');
}
?>
<script type="text/javascript">
	Joomla.submitbutton = function (pressbutton)
	{
		var form = document.adminForm;

		if (pressbutton == 'cancel')
		{
			Joomla.submitform(pressbutton);
		}
		else if (form.code.value == "")
		{
			alert("<?php echo JText::_("EB_ENTER_COUPON"); ?>");
			form.code.focus();
		}
		else if (form.discount.value == "")
		{
			alert("<?php echo JText::_("EN_ENTER_DISCOUNT_AMOUNT"); ?>");
			form.discount.focus();
		}
		else
		{
			Joomla.submitform(pressbutton);
		}
	};

	showHideEventsSelection = function(assignment)
	{
		if (assignment.value == 0)
		{
			jQuery('#events_selection_container').hide();
		}
		else
		{
			jQuery('#events_selection_container').show();
		}
	};
</script>
<form action="index.php?option=com_eventbooking&view=coupon" method="post" name="adminForm" id="adminForm" class="form form-horizontal">
	<?php
	if (!empty($this->registrants))
	{
		echo JHtml::_('bootstrap.startTabSet', 'coupon', array('active' => 'coupon-page'));
		echo JHtml::_('bootstrap.addTab', 'coupon', 'coupon-page', JText::_('EB_BASIC_INFORMATION', true));
	}
	?>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_CODE'); ?>
		</label>
		<div class="controls">
			<input class="text_area" type="text" name="code" id="code" size="15" maxlength="250"
			       value="<?php echo $this->item->code; ?>"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_DISCOUNT'); ?>
		</label>
		<div class="controls">
			<input class="input-small" type="text" name="discount" id="discount" size="10" maxlength="250"
			       value="<?php echo $this->item->discount; ?>"/>&nbsp;&nbsp;<?php echo $this->lists['coupon_type']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_COUPON_ASSIGNMENT'); ?>
		</label>
		<div class="controls">
			<?php echo $this->lists['assignment'] ; ?>
		</div>
	</div>
	<div class="control-group" id="events_selection_container"<?php if ($this->assignment == 0) echo 'style="display:none;"'; ?>>
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
	<?php
		if ($this->item->coupon_type == 2)
		{
		?>
			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('EB_USED_AMOUNT'); ?>
				</label>
				<div class="controls">
					<input class="input-small" type="text" name="used_amount" id="used_amount" size="5" maxlength="250"
			       value="<?php echo $this->item->used_amount; ?>" />					
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('EB_REMAINING_AMOUNT'); ?>
				</label>
				<div class="controls">
					<?php echo round($this->item->discount - $this->item->used_amount, 2); ?>
				</div>
			</div>
		<?php
		}
	?>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_VALID_FROM_DATE'); ?>
		</label>
		<div class="controls">
			<?php echo JHtml::_('calendar', $this->item->valid_from != $this->nullDate ? $this->item->valid_from : '', 'valid_from', 'valid_from'); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_VALID_TO_DATE'); ?>
		</label>
		<div class="controls">
			<?php echo JHtml::_('calendar', $this->item->valid_to != $this->nullDate ? $this->item->valid_to : '', 'valid_to', 'valid_to'); ?>
		</div>
	</div>
	<?php
		if (!$this->config->multiple_booking)
		{
		?>
			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('EB_APPLY_TO'); ?>
				</label>
				<div class="controls">
					<?php echo $this->lists['apply_to']; ?>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('EB_ENABLE_FOR'); ?>
				</label>
				<div class="controls">
					<?php echo $this->lists['enable_for']; ?>
				</div>
			</div>
		<?php
		}
	?>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_USER'); ?>
		</label>
		<div class="controls">
			<?php // Note, the third parameter of the method is hardcoded to prevent onchange event, do not remove it.?>
			<?php echo EventbookingHelper::getUserInput($this->item->user_id, 'user_id', 100); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_ACCESS'); ?>
		</label>
		<div class="controls">
			<?php echo $this->lists['access']; ?>
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
	<?php
	if (!empty($this->registrants))
	{
		echo JHtml::_('bootstrap.endTab');
		echo JHtml::_('bootstrap.addTab', 'coupon', 'registrants-page', JText::_('EB_COUPON_USAGE', true));
		echo $this->loadTemplate('registrants');
		echo JHtml::_('bootstrap.endTab');
		echo JHtml::_('bootstrap.endTabSet');
	}
	?>
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