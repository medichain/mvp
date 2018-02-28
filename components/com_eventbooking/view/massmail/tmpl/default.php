<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined( '_JEXEC' ) or die;

require_once JPATH_ADMINISTRATOR . '/includes/toolbar.php';
JToolbarHelper::custom('send', 'envelope', 'envelope', 'EB_SEND_MAILS', false);
$editor = JFactory::getEditor(); 	
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

			if (form.subject.value == '') {
				alert("<?php echo JText::_("EB_ENTER_MASSMAIL_SUBJECT"); ?>");
				form.subject.focus() ;
				return ;
			}

			Joomla.submitform( pressbutton );
		}
	}
</script>
<h1 class="eb-page-heading"><?php echo JText::_('EB_MASS_MAIL'); ?></h1>
<form action="<?php echo JRoute::_('index.php?option=com_eventbooking&view=massmail&Itemid='.$this->Itemid); ?>" method="post" name="adminForm" id="adminForm" class="form form-horizontal">
	<div class="btn-toolbar" id="btn-toolbar">
		<?php echo JToolbar::getInstance('toolbar')->render('toolbar'); ?>
	</div>
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
			<?php echo JText::_('EB_EMAIL_SUBJECT'); ?>
		</div>
		<div class="controls">
			<input type="text" name="subject" value="" size="70" class="input-xlarge" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo JText::_('EB_EMAIL_MESSAGE'); ?>
			<p class="eb-available-tags">
				<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[FIRST_NAME], [LAST_NAME], [EVENT_TITLE], [EVENT_DATE], [SHORT_DESCRIPTION], [DESCRIPTION], [EVENT_LOCATION], [REGISTRATION_DETAIL]</strong>
			</p>
		</div>
		<div class="controls">
			<?php echo $editor->display( 'description',  '' , '100%', '250', '75', '10' ) ; ?>
		</div>
	</div>
	<div class="clearfix"></div>
	<?php echo JHtml::_( 'form.token' ); ?>
	<input type="hidden" name="task" value="" />
</form>