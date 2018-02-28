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
<fieldset class="form-horizontal">
	<legend><?php echo JText::_('EB_EVENT_DETAIL'); ?></legend>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('EB_TITLE'); ?></label>
		<div class="controls">
			<input type="text" name="title" value="<?php echo htmlspecialchars($this->item->title, ENT_COMPAT, 'UTF-8'); ?>" class="input-xlarge" size="70"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('EB_ALIAS'); ?></label>
		<div class="controls">
			<input type="text" name="alias" value="<?php echo $this->item->alias; ?>" class="input-xlarge" size="70"/>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"><?php echo JText::_('EB_MAIN_EVENT_CATEGORY'); ?></label>
		<div class="controls">
			<?php echo $this->lists['main_category_id']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_ADDITIONAL_CATEGORIES'); ?>
		</label>
		<div class="controls">
			<?php echo $this->lists['category_id']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('EB_IMAGE'); ?></label>
		<div class="controls">
			<?php echo EventbookingHelperHtml::getMediaInput($this->item->image, 'image'); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('EB_LOCATION'); ?></label>
		<div class="controls">
			<?php echo $this->lists['location_id']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_EVENT_START_DATE'); ?>
		</label>
		<div class="controls eb-date-time-container">
			<?php echo JHtml::_('calendar', ($this->item->event_date == $this->nullDate) ? '' : JHtml::_('date', $this->item->event_date, $format, null), 'event_date', 'event_date', '%Y-%m-%d', array('class' => 'input-small')); ?>
			<?php echo $this->lists['event_date_hour'] . ' ' . $this->lists['event_date_minute']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_EVENT_END_DATE'); ?>
		</label>
		<div class="controls eb-date-time-container">
			<?php echo JHtml::_('calendar', ($this->item->event_end_date == $this->nullDate) ? '' : JHtml::_('date', $this->item->event_end_date, $format, null), 'event_end_date', 'event_end_date', '%Y-%m-%d', array('class' => 'input-small')); ?>
			<?php echo $this->lists['event_end_date_hour'] . ' ' . $this->lists['event_end_date_minute']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_REGISTRATION_START_DATE'); ?>
		</label>
		<div class="controls eb-date-time-container">
			<?php echo JHtml::_('calendar', ($this->item->registration_start_date == $this->nullDate) ? '' : JHtml::_('date', $this->item->registration_start_date, $format, null), 'registration_start_date', 'registration_start_date', '%Y-%m-%d', array('class' => 'input-small')); ?>
			<?php echo $this->lists['registration_start_hour'] . ' ' . $this->lists['registration_start_minute']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<span class="editlinktip hasTip" title="<?php echo JText::_('EB_CUT_OFF_DATE'); ?>::<?php echo JText::_('EB_CUT_OFF_DATE_EXPLAIN'); ?>"><?php echo JText::_('EB_CUT_OFF_DATE'); ?></span>
		</label>
		<div class="controls eb-date-time-container">
			<?php echo JHtml::_('calendar', ($this->item->cut_off_date == $this->nullDate) ? '' : JHtml::_('date', $this->item->cut_off_date, $format, null), 'cut_off_date', 'cut_off_date', '%Y-%m-%d', array('class' => 'input-small')); ?>
			<?php echo $this->lists['cut_off_hour'] . ' ' . $this->lists['cut_off_minute']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_PRICE'); ?>
		</label>
		<div class="controls">
			<input type="text" name="individual_price" id="individual_price" class="input-small" size="10" value="<?php echo $this->item->individual_price; ?>"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('price_text', JText::_('EB_PRICE_TEXT'), JText::_('EB_PRICE_TEXT_EXPLAIN')); ?>
		</label>
		<div class="controls">
			<input type="text" name="price_text" id="price_text" class="input-xlarge" value="<?php echo $this->item->price_text; ?>"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_TAX_RATE'); ?>
		</label>
		<div class="controls">
			<input type="text" name="tax_rate" id="tax_rate" class="input-small" size="10" value="<?php echo $this->item->tax_rate; ?>"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<span class="editlinktip hasTip" title="<?php echo JText::_('EB_EVENT_CAPACITY'); ?>::<?php echo JText::_('EB_CAPACITY_EXPLAIN'); ?>"><?php echo JText::_('EB_CAPACITY'); ?></span>
		</label>
		<div class="controls">
			<input type="text" name="event_capacity" id="event_capacity" class="input-small" size="10" value="<?php echo $this->item->event_capacity; ?>"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('EB_REGISTRATION_TYPE'); ?></label>
		<div class="controls">
			<?php echo $this->lists['registration_type']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<span class="editlinktip hasTip" title="<?php echo JText::_('EB_CUSTOM_REGISTRATION_HANDLE_URL'); ?>::<?php echo JText::_('EB_CUSTOM_REGISTRATION_HANDLE_URL_EXPLAIN'); ?>"><?php echo JText::_('EB_CUSTOM_REGISTRATION_HANDLE_URL'); ?></span>
		</label>
		<div class="controls">
			<input type="text" name="registration_handle_url" id="registration_handle_url"
			       class="input-xxlarge" size="10"
			       value="<?php echo $this->item->registration_handle_url; ?>"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<span class="editlinktip hasTip" title="<?php echo JText::_('EB_ATTACHMENT'); ?>::<?php echo JText::_('EB_ATTACHMENT_EXPLAIN'); ?>"><?php echo JText::_('EB_ATTACHMENT'); ?></span>
		</label>
		<div class="controls">
			<input type="file" name="attachment" />
			<?php
			echo $this->lists['available_attachment'];

			if ($this->item->attachment)
			{
				JText::_('EB_CURRENT_ATTACHMENT');
			?>
				<a href="<?php echo JURI::root() . 'media/com_eventbooking/' . $this->item->attachment; ?>" target="_blank"><?php echo $this->item->attachment; ?></a>
				<input type="checkbox" name="del_attachment" value="1"/><?php echo JText::_('EB_DELETE_CURRENT_ATTACHMENT'); ?>
			<?php
			}
			?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_SHORT_DESCRIPTION'); ?>
		</label>
		<div class="controls">
			<?php echo $editor->display('short_description', $this->item->short_description, '100%', '180', '90', '6'); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_DESCRIPTION'); ?>
		</label>
		<div class="controls">
			<?php echo $editor->display('description', $this->item->description, '100%', '250', '90', '10'); ?>
		</div>
	</div>
</fieldset>
