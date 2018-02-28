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
	<label class="control-label">
		<?php echo JText::_('EB_PAGE_TITLE'); ?>
	</label>
	<div class="controls">
		<input class="input-xlarge" type="text" name="page_title" id="page_title" size="" maxlength="250" value="<?php echo $this->item->page_title; ?>"/>
	</div>
</div>
<div class="control-group">
	<label class="control-label">
		<?php echo JText::_('EB_PAGE_HEADING'); ?>
	</label>
	<div class="controls">
		<input class="input-xlarge" type="text" name="page_heading" id="page_heading" size="" maxlength="250" value="<?php echo $this->item->page_heading; ?>"/>
	</div>
</div>
<div class="control-group">
	<label class="control-label">
		<?php echo  JText::_('EB_META_DESCRIPTION'); ?>
	</label>
	<div class="controls">
		<textarea rows="5" cols="80" class="input-lage" name="meta_description"><?php echo $this->item->meta_description; ?></textarea>
	</div>
</div>
<div class="control-group">
	<label class="control-label">
		<?php echo  JText::_('EB_META_KEYWORDS'); ?>
	</label>
	<div class="controls">
		<textarea rows="5" cols="80" class="input-lage" name="meta_keywords"><?php echo $this->item->meta_keywords; ?></textarea>
	</div>
</div>

