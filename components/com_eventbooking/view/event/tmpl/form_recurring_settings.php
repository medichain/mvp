<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

$bootstrapHelper = new EventbookingHelperBootstrap($this->config->twitter_bootstrap_version);
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');
?>
<div class="<?php echo $controlGroupClass;?>">
	<div class="<?php echo $controlLabelClass; ?>"
		<strong><?php echo JText::_('EB_REPEAT_TYPE'); ?></strong>
	</div>
	<div class="<?php echo $controlsClass; ?>">
		<input type="radio" name="recurring_type" value="0" <?php if ($this->item->recurring_type == 0) echo ' checked="checked" ' ; ?> onclick="setDefaultDate();" /> <?php echo JText::_('EB_NO_REPEAT'); ?>
		<p>
			<input type="radio" name="recurring_type" value="1" <?php if ($this->item->recurring_type == 1) echo ' checked="checked" ' ; ?> onclick="setDefaultData();" /> <?php echo JText::_('EB_REPEAT_EVERY'); ?> <input type="text" name="number_days" size="5" class="input-mini" value="<?php echo $this->item->number_days ; ?>" /> <?php echo JText::_('EB_DAYS'); ?>
		</p>
		<p>
			<input type="radio" name="recurring_type" value="2" <?php if ($this->item->recurring_type == 2) echo ' checked="checked" ' ; ?> onclick="setDefaultData();" /> <?php echo JText::_('EB_REPEAT_EVERY'); ?> <input type="text" name="number_weeks" size="5" class="input-mini" value="<?php echo $this->item->number_weeks ; ?>" /> <?php echo JText::_('EB_WEEKS'); ?>
		<div style="padding-left:20px;">
			<strong><?php echo JText::_('EB_ON'); ?></strong>
			<?php
			$weekDays = explode(',', $this->item->weekdays) ;
			$daysOfWeek = array(0=> 'EB_SUN', 1 => 'EB_MON', 2=> 'EB_TUE', 3=>'EB_WED', 4 => 'EB_THUR', 5=>'EB_FRI', 6=> 'EB_SAT') ;
			foreach ($daysOfWeek as $key=>$value) {
			?>
				<input type="checkbox" class="inputbox" value="<?php echo $key; ?>" name="weekdays[]" <?php if (in_array($key, $weekDays)) echo ' checked="checked"' ; ?> /> <?php echo JText::_($value); ?>&nbsp;&nbsp;
				<?php
				if ($key == 4)
				{
					echo '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				}
			}
			?>
		</div>
		</p>
		<p>
			<input type="radio" name="recurring_type" value="3" <?php if ($this->item->recurring_type == 3) echo ' checked="checked" ' ; ?> onclick="setDefaultData();" /> <?php echo JText::_('EB_REPEAT_EVERY'); ?> <input type="text" name="number_months" size="5" class="input-mini" value="<?php echo $this->item->number_months ; ?>" /> <?php echo JText::_('EB_MONTHS'); ?>
			<strong><?php echo JText::_('EB_ON'); ?></strong>&nbsp;<input type="text" name="monthdays" class="input-small" size="10" value="<?php echo $this->item->monthdays; ?>" />
		</p>
	</div>
</div>
<div class="<?php echo $controlGroupClass;?>">
	<div class="<?php echo $controlLabelClass; ?>"
		<strong><?php echo JText::_('EB_RECURRING_ENDING'); ?></strong>
	</div>
	<div class="<?php echo $controlsClass; ?>">
		<input type="radio" name="repeat_until" value="1"  <?php if ($this->item->recurring_occurrencies > 0 || $this->item->recurring_end_date == '') echo ' checked="checked" ' ; ?> /> <?php echo JText::_('EB_AFTER'); ?> <input type="text" name="recurring_occurrencies" size="5" class="inputbox" value="<?php echo $this->item->recurring_occurrencies ; ?>" /> <?php echo JText::_('EB_OCCURENCIES'); ?>
		<br />
		<input type="radio" name="repeat_until" value="2" <?php if ($this->item->recurring_end_date != '') echo ' checked="checked"' ; ?> /> <?php echo JText::_('EB_AFTER_DATE') ?> <?php echo JHtml::_('calendar', $this->item->recurring_end_date != '0000-00-00 00:00:00' ? JHtml::_('date', $this->item->recurring_end_date, 'Y-m-d', null) : '', 'recurring_end_date', 'recurring_end_date'); ?>
		<br />
	</div>
</div>
<?php
if ($this->item->id)
{
?>
	<div class="<?php echo $controlGroupClass;?>">
		<div class="<?php echo $controlLabelClass; ?>"<strong><?php echo JText::_('EB_UPDATE_CHILD_EVENT');?></strong></div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="checkbox" name="update_children_event" value="1" class="inputbox" />
		</div>
	</div>
<?php
}