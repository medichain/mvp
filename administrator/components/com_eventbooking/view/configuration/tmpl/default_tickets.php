<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;
?>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('activate_tickets_pdf', JText::_('EB_ACTIVATE_TICKETS_PDF'), JText::_('EB_ACTIVATE_TICKETS_PDF_EXPLAIN')); ?>
	</div>
	<div class="controls">
		<?php echo EventbookingHelperHtml::getBooleanInput('activate_tickets_pdf', $config->activate_tickets_pdf); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('send_tickets_via_email', JText::_('EB_SEND_TICKETS_VIA_EMAIL'), JText::_('EB_SEND_TICKETS_VIA_EMAIL_EXPLAIN')); ?>
	</div>
	<div class="controls">
		<?php echo EventbookingHelperHtml::getBooleanInput('send_tickets_via_email', $config->get('send_tickets_via_email', 1)); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('ticket_number_length', JText::_('EB_TICKET_NUMBER_LENGTH'), JText::_('EB_TICKET_NUMBER_LENGTH_EXPLAIN')); ?>
	</div>
	<div class="controls">
		<input type="text" name="ticket_number_length" class="inputbox" value="<?php echo $config->get('ticket_number_length', 5); ?>" size="10" />
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('ticket_page_orientation', JText::_('EB_PAGE_ORIENTATION')); ?>
	</div>
	<div class="controls">
		<?php echo $this->lists['ticket_page_orientation']; ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('ticket_page_format', JText::_('EB_PAGE_FORMAT')); ?>
	</div>
	<div class="controls">
		<?php echo $this->lists['ticket_page_format']; ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('default_ticket_bg_image', JText::_('EB_DEFAULT_TICKET_BG_IMAGE'), JText::_('EB_DEFAULT_TICKET_BG_IMAGE_EXPLAIN')); ?>
	</div>
	<div class="controls">
		<?php echo EventbookingHelperHtml::getMediaInput($config->get('default_ticket_bg_image'), 'default_ticket_bg_image'); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('default_ticket_layout', JText::_('EB_DEFAULT_TICKET_LAYOUT'), JText::_('EB_DEFAULT_TICKET_LAYOUT_EXPLAIN')); ?>
	</div>
	<div class="controls">
		<?php echo $editor->display( 'default_ticket_layout',  $config->default_ticket_layout , '100%', '550', '75', '8' ) ;?>
	</div>
</div>