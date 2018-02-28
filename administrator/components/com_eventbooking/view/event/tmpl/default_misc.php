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
$format = 'Y-m-d';
?>
<fieldset class="adminform">
	<legend class="adminform"><?php echo JText::_('EB_MISC'); ?></legend>
	<div class="control-group">
		<label class="control-label">
			<span class="editlinktip hasTip" title="<?php echo JText::_('EB_EVENT_PASSWORD'); ?>::<?php echo JText::_('EB_EVENT_PASSWORD_EXPLAIN'); ?>"><?php echo JText::_('EB_EVENT_PASSWORD'); ?></span>
		</label>
		<div class="controls">
			<input type="text" name="event_password" id="event_password" class="input-small" size="10" value="<?php echo $this->item->event_password; ?>"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<span class="editlinktip hasTip" title="<?php echo JText::_('EB_ACCESS'); ?>::<?php echo JText::_('EB_ACCESS_EXPLAIN'); ?>"><?php echo JText::_('EB_ACCESS'); ?></span>
		</label>
		<div class="controls">
			<?php echo $this->lists['access']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<span class="editlinktip hasTip" title="<?php echo JText::_('EB_REGISTRATION_ACCESS'); ?>::<?php echo JText::_('EB_REGISTRATION_ACCESS_EXPLAIN'); ?>"><?php echo JText::_('EB_REGISTRATION_ACCESS'); ?></span>
		</label>
		<div class="controls">
			<?php echo $this->lists['registration_access']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_FEATURED'); ?>
		</label>
		<div class="controls">
			<?php echo EventbookingHelperHtml::getBooleanInput('featured', $this->item->featured); ?>
		</div>
	</div>
	<?php
	if (JLanguageMultilang::isEnabled())
	{
	?>
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('EB_LANGUAGE'); ?>
			</label>
			<div class="controls">
				<?php echo $this->lists['language']; ?>
			</div>
		</div>
	<?php
	}
	?>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_PUBLISHED'); ?>
		</label>
		<div class="controls">
			<?php echo $this->lists['published']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('EB_CREATED_BY'); ?></label>
		<div class="controls">
			<?php echo EventbookingHelper::getUserInput($this->item->created_by, 'created_by', 1); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<span class="editlinktip hasTip" title="<?php echo JText::_('EB_MIN_NUMBER_REGISTRANTS'); ?>::<?php echo JText::_('EB_MIN_NUMBER_REGISTRANTS_EXPLAIN'); ?>"><?php echo JText::_('EB_MIN_NUMBER_REGISTRANTS'); ?></span>
		</label>
		<div class="controls">
			<input type="text" name="min_group_number" id="min_group_number" class="input-mini" size="10" value="<?php echo $this->item->min_group_number; ?>"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<span class="editlinktip hasTip" title="<?php echo JText::_('EB_MAX_NUMBER_REGISTRANTS'); ?>::<?php echo JText::_('EB_MAX_NUMBER_REGISTRANTS_EXPLAIN'); ?>"><?php echo JText::_('EB_MAX_NUMBER_REGISTRANT_GROUP'); ?></span>
		</label>
		<div class="controls">
			<input type="text" name="max_group_number" id="max_group_number" class="input-mini" size="10" value="<?php echo $this->item->max_group_number; ?>"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('ENABLE_COUPON'); ?>
		</label>
		<div class="controls">
			<?php echo $this->lists['enable_coupon']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_ENABLE_WAITING_LIST'); ?>
		</label>
		<div class="controls">
			<?php echo $this->lists['activate_waiting_list']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_COLLECT_MEMBER_INFORMATION'); ?>
		</label>
		<div class="controls">
			<?php echo $this->lists['collect_member_information']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_PREVENT_DUPLICATE'); ?>
		</label>
		<div class="controls">
			<?php echo $this->lists['prevent_duplicate_registration']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_SEND_NOTIFICATION_EMAILS'); ?>
		</label>
		<div class="controls">
			<?php echo $this->lists['send_emails']; ?>
		</div>
	</div>
	<?php
	if ($this->config->activate_deposit_feature)
	{
		?>
		<div class="control-group">
			<label class="control-label">
				<span class="editlinktip hasTip" title="<?php echo JText::_('EB_DEPOSIT_AMOUNT'); ?>::<?php echo JText::_('EB_DEPOSIT_AMOUNT_EXPLAIN'); ?>"><?php echo JText::_('EB_DEPOSIT_AMOUNT'); ?></span>
			</label>
			<div class="controls">
				<input type="text" name="deposit_amount" id="deposit_amount" class="input-mini" size="5" value="<?php echo $this->item->deposit_amount; ?>"/>&nbsp;&nbsp;<?php echo $this->lists['deposit_type']; ?>
			</div>
		</div>
		<?php
	}
	?>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_ENABLE_CANCEL'); ?>
		</label>
		<div class="controls">
			<?php echo EventbookingHelperHtml::getBooleanInput('enable_cancel_registration', $this->item->enable_cancel_registration); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_CANCEL_BEFORE_DATE'); ?>
		</label>
		<div class="controls">
			<?php echo JHtml::_('calendar', $this->item->cancel_before_date != $this->nullDate ? JHtml::_('date', $this->item->cancel_before_date, $format, null) : '', 'cancel_before_date', 'cancel_before_date', '%Y-%m-%d', array('class' => 'input-small')); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_PUBLISH_UP'); ?>
		</label>
		<div class="controls">
			<?php echo JHtml::_('calendar', ($this->item->publish_up && $this->item->publish_up != $this->nullDate) ? JHtml::_('date', $this->item->publish_up, 'Y-m-d H:i:s', null) : '', 'publish_up', 'publish_up', '%Y-%m-%d %H:%M:%S', array('class' => 'input-medium')); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_PUBLISH_DOWN'); ?>
		</label>
		<div class="controls">
			<?php echo JHtml::_('calendar', ($this->item->publish_down && $this->item->publish_down != $this->nullDate) ? JHtml::_('date', $this->item->publish_down, 'Y-m-d H:i:s', null) : '', 'publish_down', 'publish_down', '%Y-%m-%d %H:%M:%S', array('class' => 'input-medium')); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_AUTO_REMINDER'); ?>
		</label>
		<div class="controls">
			<?php echo EventbookingHelperHtml::getBooleanInput('enable_auto_reminder', $this->item->enable_auto_reminder); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_REMIND_BEFORE'); ?>
		</label>
		<div class="controls">
			<input type="text" name="remind_before_x_days" class="input-mini" size="5" value="<?php echo $this->item->remind_before_x_days; ?>"/> days
		</div>
	</div>
	<div class="control-group">
			<label class="control-label">
			<?php echo JText::_('EB_ENABLE_TERMS_CONDITIONS'); ?>
		</label>
		<div class="controls">
			<?php echo $this->lists['enable_terms_and_conditions']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_TERMS_CONDITIONS'); ?>
		</label>
		<div class="controls">
			<?php echo EventbookingHelper::getArticleInput($this->item->article_id); ?>
		</div>
	</div>
</fieldset>
