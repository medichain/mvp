<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die;
?>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('activate_invoice_feature', JText::_('EB_ACTIVATE_INVOICE_FEATURE'), JText::_('EB_ACTIVATE_INVOICE_FEATURE_EXPLAIN')); ?>
	</div>
	<div class="controls">
		<?php echo EventbookingHelperHtml::getBooleanInput('activate_invoice_feature', $config->activate_invoice_feature); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('always_generate_invoice', JText::_('EB_ALWAYS_GENERATE_INVOICE'), JText::_('EB_ALWAYS_GENERATE_INVOICE_EXPLAIN')); ?>
	</div>
	<div class="controls">
		<?php echo EventbookingHelperHtml::getBooleanInput('always_generate_invoice', $config->always_generate_invoice); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('send_invoice_to_customer', JText::_('EB_SEND_INVOICE_TO_SUBSCRIBERS'), JText::_('EB_SEND_INVOICE_TO_SUBSCRIBERS_EXPLAIN')); ?>
	</div>
	<div class="controls">
		<?php echo EventbookingHelperHtml::getBooleanInput('send_invoice_to_customer', $config->send_invoice_to_customer); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('send_invoice_to_admin', JText::_('EB_SEND_INVOICE_TO_ADMIN'), JText::_('EB_SEND_INVOICE_TO_ADMIN_EXPLAIN')); ?>
	</div>
	<div class="controls">
		<?php echo EventbookingHelperHtml::getBooleanInput('send_invoice_to_admin', $config->send_invoice_to_admin); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('invoice_start_number', JText::_('EB_INVOICE_START_NUMBER'), JText::_('EB_INVOICE_START_NUMBER_EXPLAIN')); ?>
	</div>
	<div class="controls">
		<input type="text" name="invoice_start_number" class="inputbox" value="<?php echo $config->invoice_start_number ? $config->invoice_start_number : 1; ?>" size="10" />
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('invoice_prefix', JText::_('EB_INVOICE_PREFIX'), JText::_('EB_INVOICE_PREFIX_EXPLAIN')); ?>
	</div>
	<div class="controls">
		<input type="text" name="invoice_prefix" class="inputbox" value="<?php echo $config->get('invoice_prefix', 'IV'); ?>" size="10" />
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('invoice_number_length', JText::_('EB_INVOICE_NUMBER_LENGTH'), JText::_('EB_INVOICE_NUMBER_LENGTH_EXPLAIN')); ?>
	</div>
	<div class="controls">
		<input type="text" name="invoice_number_length" class="inputbox" value="<?php echo $config->get('invoice_number_length', 5); ?>" size="10" />
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('pdf_font', JText::_('EB_PDF_FONT'), JText::_('EB_PDF_FONT_EXPLAIN')); ?>
		<p class="text-warning">
			<?php echo JText::_('EB_PDF_FONT_WARNING'); ?>
		</p>
	</div>
	<div class="controls">
		<?php echo $this->lists['pdf_font']; ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('invoice_format', JText::_('EB_INVOICE_FORMAT'), JText::_('EB_INVOICE_FORMAT_EXPLAIN')); ?>
	</div>
	<div class="controls">
		<?php echo $editor->display( 'invoice_format',  $config->invoice_format , '100%', '550', '75', '8' ) ;?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('invoice_format_cart', JText::_('EB_INVOICE_FORMAT_CART'), JText::_('EB_INVOICE_FORMAT_CART_EXPLAIN')); ?>
	</div>
	<div class="controls">
		<?php echo $editor->display( 'invoice_format_cart',  $config->invoice_format_cart , '100%', '550', '75', '8' ) ;?>
	</div>
</div>
