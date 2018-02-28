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
JHtml::_('formbehavior.chosen', 'select');

JToolbarHelper::title(JText::_('EB_BATCH_COUPONS_TITLE'));
JToolbarHelper::custom('coupon.batch', 'upload', 'upload', 'EB_GENERATE_COUPONS', false);
JToolbarHelper::cancel('coupon.cancel');
?>
<form action="index.php?option=com_eventbooking&view=coupon" method="post" name="adminForm" id="adminForm" class="form form-horizontal">		
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_NUMBER_COUPONS'); ?>
		</label>
		<div class="controls">
			<input class="input-mini" type="text" name="number_coupon" id="number_coupon" size="15" maxlength="250" value="" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_EVENTS'); ?>
		</label>
		<div class="controls">
			<?php echo $this->lists['event_id']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_DISCOUNT'); ?>
		</label>
		<div class="controls">
			<input class="text_area" type="text" name="discount" id="discount" size="10" maxlength="250" value="" />&nbsp;&nbsp;<?php echo $this->lists['coupon_type'] ; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_CHARACTERS_SET'); ?>
		</label>
		<div class="controls">
			<input class="text_area" type="text" name="characters_set" id="characters_set" size="15" maxlength="250" value="" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_PREFIX'); ?>
		</label>
		<div class="controls">
			<input class="text_area" type="text" name="prefix" id="prefix" size="15" maxlength="250" value="" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_COUPON_LENGTH'); ?>
		</label>
		<div class="controls">
			<input class="text_area" type="text" name="length" id="length" size="15" maxlength="250" value="" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_VALID_FROM_DATE'); ?>
		</label>
		<div class="controls">
			<?php echo JHtml::_('calendar', '', 'valid_from', 'valid_from') ; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_VALID_TO_DATE'); ?>
		</label>
		<div class="controls">
			<?php echo JHtml::_('calendar', '', 'valid_to', 'valid_to') ; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_TIMES'); ?>
		</label>
		<div class="controls">
			<input class="text_area" type="text" name="times" id="times" size="5" maxlength="250" value="" />
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
	<input type="hidden" name="used" value="0"/>
	<input type="hidden" name="task" value="" />
</form>