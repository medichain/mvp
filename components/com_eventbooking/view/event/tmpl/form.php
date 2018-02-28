<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.tabstate');
JHtml::_('behavior.modal');

$editor = JFactory::getEditor() ;
$bootstrapHelper   = new EventbookingHelperBootstrap($this->config->twitter_bootstrap_version);
$rowFluidClass     = $bootstrapHelper->getClassMapping('row-fluid');
?>
<script type="text/javascript">
	function checkData(pressbutton)
	{
		var form = document.adminForm;
		if (pressbutton == 'event.cancel')
		{
			Joomla.submitform( pressbutton );
			return;
		}
		else
		{
			//Should have some validations rule here
			//Check something here
			if (form.title.value == '')
			{
				alert("<?php echo JText::_('EB_PLEASE_ENTER_TITLE'); ?>");
				form.title.focus();
				return ;
			}
			if (form.event_date.value == '') {
				alert("<?php echo JText::_('EB_ENTER_EVENT_DATE'); ?>");
				form.event_date.focus();
				return ;
			}
			if (form.main_category_id.value == 0)
			{
				alert("<?php echo JText::_("EB_CHOOSE_CATEGORY");  ?>");
				return ;
			}
			//Check the price

			if (form.recurring_type) {
				//Check the recurring setting
				if (form.recurring_type[1].checked) {
					if (form.number_days.value == '') {
						alert("<?php echo JText::_("EB_ENTER_NUMBER_OF_DAYS"); ?>");
						form.number_days.focus();
						return ;
					}
					if (!parseInt(form.number_days.value)) {
						alert("<?php echo JText::_("EB_NUMBER_DAY_INTEGER"); ?>");
						form.number_days.focus();
						return ;
					}
				}else if (form.recurring_type[2].checked) {
					if (form.number_weeks.value == '') {
						alert("<?php echo JText::_("EB_ENTER_NUMBER_OF_WEEKS"); ?>");
						form.number_weeks.focus();
						return ;
					}
					if (!parseInt(form.number_weeks.value)) {
						alert("<?php echo JText::_("EB_NUMBER_WEEKS_INTEGER"); ?>");
						form.number_weeks.focus();
						return ;
					}
					//Check whether any days in the week
					var checked = false ;
					for (var i = 0 ; i < form['weekdays[]'].length ; i++) {
						if (form['weekdays[]'][i].checked)
							checked = true ;
					}
					if (!checked) {
						alert("<?php echo JText::_("EB_CHOOSE_ONEDAY"); ?>");
						form['weekdays[]'][0].focus();
						return ;
					}
				} else if (form.recurring_type[3].checked) {
					if (form.number_months.value == '') {
						alert("<?php echo JText::_("EB_ENTER_NUMBER_MONTHS"); ?>");
						form.number_months.focus();
						return ;
					}
					if (!parseInt(form.number_months.value)) {
						alert("<?php echo JText::_("EB_NUMBER_MONTH_INTEGER"); ?>");
						form.number_months.focus();
						return ;
					}
					if (form.monthdays.value == '') {
						alert("<?php echo JText::_("EB_ENTER_DAY_IN_MONTH"); ?>");
						form.monthdays.focus();
						return ;
					}
				}
			}
			Joomla.submitform( pressbutton );
		}
	}
</script>
<div class="eb_form_header" style="width:100%;">
	<div style="float: left; width: 40%;"><?php echo JText::_('EB_ADD_EDIT_EVENT'); ?></div>
	<div style="float: right; width: 50%; text-align: right;">
		<input type="button" name="btnSave" value="<?php echo JText::_('EB_SAVE'); ?>" onclick="checkData('event.save');" class="btn btn-primary" />
		<input type="button" name="btnSave" value="<?php echo JText::_('EB_CANCEL_EVENT'); ?>" onclick="checkData('event.cancel');" class="btn btn-primary" />
	</div>
</div>
<div class="clearfix"></div>
<form action="<?php echo JRoute::_('index.php?Itemid='.$this->Itemid); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form form-horizontal">
<div class="<?php echo $rowFluidClass; ?> eb-container">
	<?php
		echo JHtml::_('bootstrap.startTabSet', 'event', array('active' => 'basic-information-page'));
		echo JHtml::_('bootstrap.addTab', 'event', 'basic-information-page', JText::_('EB_BASIC_INFORMATION', true));
		echo $this->loadTemplate('general', array('editor' => $editor));
		echo JHtml::_('bootstrap.endTab');

		if ($this->config->activate_recurring_event && (!$this->item->id || $this->item->event_type == 1))
		{
			echo JHtml::_('bootstrap.addTab', 'event', 'recurring-settings-page', JText::_('EB_RECURRING_SETTINGS', true));
			echo $this->loadTemplate('recurring_settings');
			echo JHtml::_('bootstrap.endTab');
		}

		echo JHtml::_('bootstrap.addTab', 'event', 'group-registration-rates-page', JText::_('EB_GROUP_REGISTRATION_RATES', true));
		echo $this->loadTemplate('group_rates');
		echo JHtml::_('bootstrap.endTab');

		echo JHtml::_('bootstrap.addTab', 'event', 'misc-page', JText::_('EB_MISC', true));
		echo $this->loadTemplate('misc');
		echo JHtml::_('bootstrap.endTab');

		echo JHtml::_('bootstrap.addTab', 'event', 'discount-page', JText::_('EB_DISCOUNT_SETTING', true));
		echo $this->loadTemplate('discount_settings');
		echo JHtml::_('bootstrap.endTab');

		if ($this->config->event_custom_field)
		{
			echo JHtml::_('bootstrap.addTab', 'event', 'fields-page', JText::_('EB_EXTRA_INFORMATION', true));
			echo $this->loadTemplate('fields');
			echo JHtml::_('bootstrap.endTab');
		}

		echo JHtml::_('bootstrap.endTabSet');
	?>
</div>
	<input type="hidden" name="option" value="com_eventbooking" />
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
	<script type="text/javascript">
		function addRow() {
			var table = document.getElementById('price_list');
			var newRowIndex = table.rows.length - 1 ;
			var row = table.insertRow(newRowIndex);
			var registrantNumber = row.insertCell(0);
			var price = row.insertCell(1);
			registrantNumber.innerHTML = '<input type="text" class="inputbox" name="registrant_number[]" size="10" />';
			price.innerHTML = '<input type="text" class="inputbox" name="price[]" size="10" />';

		}
		function removeRow() {
			var table = document.getElementById('price_list');
			var deletedRowIndex = table.rows.length - 2 ;
			if (deletedRowIndex >= 1) {
				table.deleteRow(deletedRowIndex);
			} else {
				alert("<?php echo JText::_('EB_NO_ROW_TO_DELETE'); ?>");
			}
		}

		function setDefaultData() {
			var form = document.adminForm ;
			if (form.recurring_type[1].checked) {
				if (form.number_days.value == '') {
					form.number_days.value =1 ;
				}
			} else if (form.recurring_type[2].checked) {
				if (form.number_weeks.value == '') {
					form.number_weeks.value = 1 ;
				}
			} else if (form.recurring_type[3].checked) {
				if (form.number_months.value == '') {
					form.number_months.value = 1 ;
				}
			}
		}
	</script>
</form>