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
		<?php echo EventbookingHelperHtml::getBooleanInput('activate_tickets_pdf', $this->item->id ? $this->item->activate_tickets_pdf : $this->config->activate_tickets_pdf); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('ticket_start_number', JText::_('EB_TICKET_START_NUMBER'), JText::_('EB_TICKET_START_NUMBER_EXPLAIN')); ?>
	</div>
	<div class="controls">
		<input type="text" name="ticket_start_number" class="inputbox" value="<?php echo $this->item->ticket_start_number ? $this->item->ticket_start_number : 1; ?>" size="10" />
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('ticket_prefix', JText::_('EB_TICKET_PREFIX'), JText::_('EB_TICKET_PREFIX_EXPLAIN')); ?>
	</div>
	<div class="controls">
		<input type="text" name="ticket_prefix" class="inputbox" value="<?php echo $this->item->ticket_prefix ? $this->item->ticket_prefix : 'TK';; ?>" size="10" />
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('ticket_bg_image', JText::_('EB_TICKET_BG_IMAGE'), JText::_('EB_TICKET_BG_IMAGE_EXPLAIN')); ?>
	</div>
	<div class="controls">
		<?php echo EventbookingHelperHtml::getMediaInput($this->item->ticket_bg_image, 'ticket_bg_image'); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('ticket_bg_left', JText::_('EB_BG_POSSITION')); ?>
	</div>
	<div class="controls">
		<?php echo JText::_('EB_LEFT') . '    ';?><input type="text" name="ticket_bg_left" class="input-mini" value="<?php echo (int) $this->item->ticket_bg_left; ?>" />
		<?php echo JText::_('EB_TOP') . '    ';?><input type="text" name="ticket_bg_top" class="input-mini" value="<?php echo (int) $this->item->ticket_bg_top; ?>" />
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('ticket_layout', JText::_('EB_TICKET_LAYOUT'), JText::_('EB_TICKET_LAYOUT_EXPLAIN')); ?>
	</div>
	<div class="controls">
		<?php echo $editor->display( 'ticket_layout',  $this->item->ticket_layout, '100%', '550', '75', '8' ); ?>
	</div>
</div>