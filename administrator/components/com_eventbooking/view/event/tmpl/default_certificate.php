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
		<?php echo EventbookingHelperHtml::getFieldLabel('activate_certificate_feature', JText::_('EB_ACTIVATE_CERTIFICATE_FEATURE'), JText::_('EB_ACTIVATE_TICKETS_PDF_EXPLAIN')); ?>
	</div>
	<div class="controls">
		<?php echo EventbookingHelperHtml::getBooleanInput('activate_certificate_feature', $this->item->id ? $this->item->activate_certificate_feature : $this->config->activate_certificate_feature); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('certificate_bg_image', JText::_('EB_CERTIFICATE_BG_IMAGE'), JText::_('EB_CERTIFICATE_BG_IMAGE_EXPLAIN')); ?>
	</div>
	<div class="controls">
		<?php echo EventbookingHelperHtml::getMediaInput($this->item->certificate_bg_image, 'certificate_bg_image'); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('certificate_bg_left', JText::_('EB_BG_POSSITION')); ?>
	</div>
	<div class="controls">
		<?php echo JText::_('EB_LEFT') . '    ';?><input type="text" name="certificate_bg_left" class="input-mini" value="<?php echo (int) $this->item->certificate_bg_left; ?>" />
		<?php echo JText::_('EB_TOP') . '    ';?><input type="text" name="certificate_bg_top" class="input-mini" value="<?php echo (int) $this->item->certificate_bg_top; ?>" />
	</div>
</div>
<div class="control-group">
	<label class="control-label">
		<?php echo EventbookingHelperHtml::getFieldLabel('certificate_layout', JText::_('EB_CERTIFICATE_LAYOUT')); ?>
	</label>
	<div class="controls">
		<?php echo $editor->display( 'certificate_layout',  $this->item->certificate_layout , '100%', '250', '90', '10' ) ; ?>
	</div>
</div>