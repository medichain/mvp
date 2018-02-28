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
<form action="index.php?option=com_eventbooking&view=country" method="post" name="adminForm" id="adminForm" class="form form-horizontal">
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_COUNTRY_NAME'); ?>
		</label>
		<div class="controls">
			<input class="text_area" type="text" name="name" id="name" size="40" maxlength="250" value="<?php echo $this->item->name;?>" />
		</div>
	</div>	
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_COUNTRY_CODE_3'); ?>
		</label>
		<div class="controls">
			<input class="text_area" type="text" name="country_3_code" id="country_3_code" maxlength="250" value="<?php echo $this->item->country_3_code;?>" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_COUNTRY_CODE_2'); ?>
		</label>
		<div class="controls">
			<input class="text_area" type="text" name="country_2_code" id="country_2_code" maxlength="250" value="<?php echo $this->item->country_2_code;?>" />
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
	<div class="clearfix"></div>
	<?php echo JHtml::_( 'form.token' ); ?>
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />
</form>