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

$rootUri = JUri::root();
echo JHtml::_('bootstrap.addTab', 'event', 'translation-page', JText::_('EB_TRANSLATION', true));
echo JHtml::_('bootstrap.startTabSet', 'event-translation', array('active' => 'translation-page-'.$this->languages[0]->sef));

foreach ($this->languages as $language)
{
	$sef = $language->sef;
	echo JHtml::_('bootstrap.addTab', 'event-translation', 'translation-page-' . $sef, $language->title . ' <img src="' . $rootUri . 'media/com_eventbooking/flags/' . $sef . '.png" />');
	?>
	<div class="control-group">
		<label class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('use_data_from_default_language_'.$sef, JText::_('EB_USE_DATA_FROM_DEFAULT_LANGUAGE'), JText::_('EB_USE_DATA_FROM_DEFAULT_LANGUAGE_EXPLAIN')) ?>
		</label>
		<div class="controls">
			<input type="checkbox" name="use_data_from_default_language_<?php echo $sef; ?>" value="1" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_TITLE'); ?>
		</label>
		<div class="controls">
			<input class="input-xlarge" type="text" name="title_<?php echo $sef; ?>" id="title_<?php echo $sef; ?>" size="" maxlength="250" value="<?php echo $this->item->{'title_'.$sef}; ?>" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_ALIAS'); ?>
		</label>
		<div class="controls">
			<input class="input-xlarge" type="text" name="alias_<?php echo $sef; ?>" id="alias_<?php echo $sef; ?>" size="" maxlength="250" value="<?php echo $this->item->{'alias_'.$sef}; ?>" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_PRICE_TEXT'); ?>
		</label>
		<div class="controls">
			<input class="input-xlarge" type="text" name="price_text_<?php echo $sef; ?>" id="price_text_<?php echo $sef; ?>" size="" maxlength="250" value="<?php echo $this->item->{'price_text_'.$sef}; ?>" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_CUSTOM_REGISTRATION_HANDLE_URL'); ?>
		</label>
		<div class="controls">
			<input class="input-xlarge" type="text" name="registration_handle_url_<?php echo $sef; ?>" id="registration_handle_url_<?php echo $sef; ?>" size="" maxlength="250" value="<?php echo $this->item->{'registration_handle_url_'.$sef}; ?>" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_SHORT_DESCRIPTION'); ?>
		</label>
		<div class="controls">
			<?php echo $editor->display( 'short_description_'.$sef,  $this->item->{'short_description_'.$sef} , '100%', '250', '75', '10' ) ; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_DESCRIPTION'); ?>
		</label>
		<div class="controls">
			<?php echo $editor->display( 'description_'.$sef,  $this->item->{'description_'.$sef} , '100%', '250', '75', '10' ) ; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_PAGE_TITLE'); ?>
		</label>
		<div class="controls">
			<input class="input-xlarge" type="text" name="page_title_<?php echo $sef; ?>" id="page_title_<?php echo $sef; ?>" size="" maxlength="250" value="<?php echo $this->item->{'page_title_'.$sef}; ?>" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_PAGE_HEADING'); ?>
		</label>
		<div class="controls">
			<input class="input-xlarge" type="text" name="page_heading_<?php echo $sef; ?>" id="page_heading_<?php echo $sef; ?>" size="" maxlength="250" value="<?php echo $this->item->{'page_heading_'.$sef}; ?>" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_META_DESCRIPTION'); ?>
		</label>
		<div class="controls">
			<textarea rows="5" cols="30" class="input-lage" name="meta_description_<?php echo $sef; ?>"><?php echo $this->item->{'meta_description_'.$sef}; ?></textarea>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_META_KEYWORDS'); ?>
		</label>
		<div class="controls">
			<textarea rows="5" cols="30" class="input-lage" name="meta_keywords_<?php echo $sef; ?>"><?php echo $this->item->{'meta_keywords_'.$sef}; ?></textarea>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_META_DESCRIPTION'); ?>
		</label>
		<div class="controls">
			<textarea rows="5" cols="30" class="input-lage" name="meta_description_<?php echo $sef; ?>"><?php echo $this->item->{'meta_description_'.$sef}; ?></textarea>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('registration_form_message_'.$sef, JText::_('EB_REGISTRATION_FORM_MESSAGE'), JText::_('EB_AVAILABLE_TAGS').': [EVENT_TITLE]'); ?>
		</label>
		<div class="controls">
			<?php echo $editor->display('registration_form_message_' . $sef, $this->item->{'registration_form_message_' . $sef}, '100%', '250', '90', '10'); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('registration_form_message_group_'.$sef, JText::_('EB_REGISTRATION_FORM_MESSAGE_GROUP'), JText::_('EB_AVAILABLE_TAGS').': [EVENT_TITLE]'); ?>
		</label>
		<div class="controls">
			<?php echo $editor->display('registration_form_message_group_' . $sef, $this->item->{'registration_form_message_group_' . $sef}, '100%', '250', '90', '10'); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('user_email_body', JText::_('EB_USER_EMAIL_BODY'), JText::_('EB_AVAILABLE_TAGS').': [REGISTRATION_DETAIL], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [CITY], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT]'); ?>
		</label>
		<div class="controls">
			<?php echo $editor->display('user_email_body_' . $sef, $this->item->{'user_email_body_' . $sef}, '100%', '250', '90', '10'); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('user_email_body_offline_'.$sef, JText::_('EB_USER_EMAIL_BODY_OFFLINE'), JText::_('EB_AVAILABLE_TAGS').': [REGISTRATION_DETAIL], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [CITY], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT]'); ?>
		</label>
		<div class="controls">
			<?php echo $editor->display('user_email_body_offline_' . $sef, $this->item->{'user_email_body_offline_' . $sef}, '100%', '250', '90', '10'); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_THANKYOU_MESSAGE'); ?>
		</label>
		<div class="controls">
			<?php echo $editor->display( 'thanks_message_'.$sef,  $this->item->{'thanks_message_'.$sef} , '100%', '180', '90', '6' ) ; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_THANKYOU_MESSAGE_OFFLINE'); ?>
		</label>
		<div class="controls">
			<?php echo $editor->display( 'thanks_message_offline_'.$sef,  $this->item->{'thanks_message_offline_'.$sef} , '100%', '180', '90', '6' ) ; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_REGISTRATION_APPROVED_EMAIL_BODY'); ?>
		</label>
		<div class="controls">
			<?php echo $editor->display( 'registration_approved_email_body_'.$sef,  $this->item->{'registration_approved_email_body_'.$sef} , '100%', '180', '90', '6' ) ; ?>
		</div>
	</div>
	<?php
	echo JHtml::_('bootstrap.endTab');
}
echo JHtml::_('bootstrap.endTabSet');
echo JHtml::_('bootstrap.endTab');

