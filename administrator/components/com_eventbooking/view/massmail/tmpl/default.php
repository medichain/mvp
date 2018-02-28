<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined( '_JEXEC' ) or die;

JToolbarHelper::title( JText::_( 'EB_MASS_MAIL' ), 'massemail.png' );
JToolbarHelper::custom('send','envelope','envelope', JText::_('EB_SEND_MAILS'), false);
JToolbarHelper::cancel('cancel');
$editor = JFactory::getEditor();

JHtml::_('formbehavior.chosen', 'select');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			Joomla.submitform( pressbutton );
		} else {
			//Need to check something here
			if (form.event_id.value == 0) {
				alert("<?php echo JText::_("EB_CHOOSE_EVENT"); ?>");
				form.event_id.focus() ;
				return ;				
			}			
			Joomla.submitform( pressbutton );
		}
	}
</script>
<form action="index.php?option=com_eventbooking&view=massmail" method="post" name="adminForm" id="adminForm" class="form form-horizontal" enctype="multipart/form-data">
	<div class="control-group">
		<div class="control-label">
			<?php echo JText::_('EB_EVENT'); ?>
		</div>
		<div class="controls">
			<?php echo $this->lists['event_id'] ; ?>
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
			<?php echo JText::_('EB_EMAIL_SUBJECT'); ?>
		</div>
		<div class="controls">
			<input type="text" name="subject" value="" size="70" class="input-xxlarge" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo JText::_('EB_EMAIL_MESSAGE'); ?>
			<p class="eb-available-tags">
				<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[FIRST_NAME], [LAST_NAME], [EVENT_TITLE], [EVENT_DATE], [SHORT_DESCRIPTION], [EVENT_LOCATION]</strong>
			</p>
		</div>
		<div class="controls">
			<?php echo $editor->display( 'description',  '' , '100%', '250', '75', '10' ) ; ?>
		</div>
	</div>
	<div class="clearfix"></div>
	<?php echo JHtml::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_eventbooking" />	
	<input type="hidden" name="task" value="" />
</form>