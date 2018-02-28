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
?>
<div class="control-group">
	<label class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('registration_form_message', JText::_('EB_REGISTRATION_FORM_MESSAGE'), JText::_('EB_AVAILABLE_TAGS').': [EVENT_TITLE]'); ?>
	</label>
	<div class="controls">
		<?php echo $editor->display( 'registration_form_message',  $this->item->registration_form_message , '100%', '250', '90', '10' ) ; ?>
	</div>
</div>
<div class="control-group">
	<label class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('registration_form_message_group', JText::_('EB_REGISTRATION_FORM_MESSAGE_GROUP'), JText::_('EB_AVAILABLE_TAGS').': [EVENT_TITLE]'); ?>
	</label>
	<div class="controls">
		<?php echo $editor->display( 'registration_form_message_group',  $this->item->registration_form_message_group , '100%', '250', '90', '10' ) ; ?>
	</div>
</div>
<div class="control-group">
	<label class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('user_email_body', JText::_('EB_USER_EMAIL_BODY'), JText::_('EB_AVAILABLE_TAGS').': [REGISTRATION_DETAIL], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [CITY], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT]'); ?>
	</label>
	<div class="controls">
		<?php echo $editor->display( 'user_email_body',  $this->item->user_email_body , '100%', '250', '90', '10' ) ; ?>
	</div>
</div>
<div class="control-group">
	<label class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('user_email_body_offline', JText::_('EB_USER_EMAIL_BODY_OFFLINE'), JText::_('EB_AVAILABLE_TAGS').': [REGISTRATION_DETAIL], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [CITY], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT]'); ?>
	</label>
	<div class="controls">
		<?php echo $editor->display( 'user_email_body_offline',  $this->item->user_email_body_offline , '100%', '250', '90', '10' ) ; ?>
	</div>
</div>
<div class="control-group">
	<label class="control-label">
		<?php echo  JText::_('EB_THANKYOU_MESSAGE'); ?>
	</label>
	<div class="controls">
		<?php echo $editor->display( 'thanks_message',  $this->item->thanks_message , '100%', '180', '90', '6' ) ; ?>
	</div>
</div>
<div class="control-group">
	<label class="control-label">
		<?php echo  JText::_('EB_THANKYOU_MESSAGE_OFFLINE'); ?>
	</label>
	<div class="controls">
		<?php echo $editor->display( 'thanks_message_offline',  $this->item->thanks_message_offline , '100%', '180', '90', '6' ) ; ?>
	</div>
</div>
<div class="control-group">
	<label class="control-label">
		<?php echo  JText::_('EB_REGISTRATION_APPROVED_EMAIL_BODY'); ?>
	</label>
	<div class="controls">
		<?php echo $editor->display( 'registration_approved_email_body',  $this->item->registration_approved_email_body , '100%', '180', '90', '6' ) ; ?>
	</div>
</div>
<div class="control-group">
	<label class="control-label">
		<?php echo  JText::_('EB_REMINDER_EMAIL_BODY'); ?>
	</label>
	<div class="controls">
		<?php echo $editor->display( 'reminder_email_body',  $this->item->reminder_email_body , '100%', '180', '90', '6' ) ; ?>
	</div>
</div>


