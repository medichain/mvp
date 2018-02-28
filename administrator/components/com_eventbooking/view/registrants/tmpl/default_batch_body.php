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

$editor = JFactory::getEditor();
?>
<div class="row-fluid form form-horizontal">
	<div class="control-group">
		<div class="control-label">
			<?php echo JText::_('EB_EMAIL_SUBJECT'); ?>
		</div>
		<div class="controls">
			<input type="text" name="subject" value="" size="70" class="input-xxlarge" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo JText::_('EB_ATTACHMENT'); ?>
		</div>
		<div class="controls">
			<input type="file" name="attachment" value="" size="70" class="input-xxlarge" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo JText::_('EB_EMAIL_MESSAGE'); ?>
		</div>
		<div class="controls">
			<?php echo $editor->display( 'message',  '' , '100%', '250', '75', '10' ) ; ?>
		</div>
	</div>
	<div class="control-group">
		<strong><?php echo JText::_('EB_AVAILABLE_TAGS'); ?> : [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [EVENT_TITLE], [EVENT_DATE],[EVENT_END_DATE], [SHORT_DESCRIPTION]</strong>
	</div>
</div>

