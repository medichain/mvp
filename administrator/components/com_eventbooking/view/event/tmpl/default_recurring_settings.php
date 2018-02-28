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

$format = 'Y-m-d';
?>
<fieldset class="adminform">
	<legend class="adminform"><?php echo JText::_('EB_RECURRING_SETTINGS'); ?></legend>
	<div class="control-group">
		<div class="control-label">
			<strong><?php echo JText::_('EB_REPEAT_TYPE'); ?></strong>
		</div>
		<div class="controls">
			<table style="width: 100%;">
				<tr>
					<td>
						<input type="radio" name="recurring_type"
						       value="0" <?php if ($this->item->recurring_type == 0) echo ' checked="checked" '; ?>
						       onclick="setDefaultDate();"/> <?php echo JText::_('EB_NO_REPEAT'); ?>
					</td>
				</tr>
				<tr>
					<td>
						<input type="radio" name="recurring_type"
						       value="1" <?php if ($this->item->recurring_type == 1) echo ' checked="checked" '; ?>
						       onclick="setDefaultData();"/> <?php echo JText::_('EB_REPEAT_EVERY'); ?>
						<input type="text" name="number_days" size="5" class="input-mini clearfloat"
						       value="<?php echo $this->item->number_days; ?>"/> <?php echo JText::_('EB_DAYS'); ?>
					</td>
				</tr>
				<tr>
					<td>
						<input type="radio" name="recurring_type"
						       value="2" <?php if ($this->item->recurring_type == 2) echo ' checked="checked" '; ?>
						       onclick="setDefaultData();"/> <?php echo JText::_('EB_REPEAT_EVERY'); ?>
						<input type="text" name="number_weeks" size="5" class="input-mini clearfloat"
						       value="<?php echo $this->item->number_weeks; ?>"/> <?php echo JText::_('EB_WEEKS'); ?>
						<br/>
						<strong><?php echo JText::_('EB_ON'); ?></strong>
						<?php
						$weekDays   = explode(',', $this->item->weekdays);
						$daysOfWeek = array(0 => 'EB_SUN', 1 => 'EB_MON', 2 => 'EB_TUE', 3 => 'EB_WED', 4 => 'EB_THUR', 5 => 'EB_FRI', 6 => 'EB_SAT');
						foreach ($daysOfWeek as $key => $value)
						{
							?>
							<input type="checkbox" class="inputbox clearfloat"
							       value="<?php echo $key; ?>"
							       name="weekdays[]" <?php if (in_array($key, $weekDays)) echo ' checked="checked"'; ?> /> <?php echo JText::_($value); ?>&nbsp;&nbsp;
							<?php
						}
						?>
					</td>
				</tr>
				<tr>
					<td>
						<input type="radio" name="recurring_type"
						       value="3" <?php if ($this->item->recurring_type == 3) echo ' checked="checked" '; ?>
						       onclick="setDefaultData();"/> <?php echo JText::_('EB_REPEAT_EVERY'); ?>
						<input type="text" name="number_months" size="5" class="input-mini clearfloat"
						       value="<?php echo $this->item->number_months; ?>"/> <?php echo JText::_('EB_MONTHS'); ?>
						<?php echo JText::_('EB_ON'); ?> <input type="text" name="monthdays"
						                                        class="input-mini clearfloat" size="10"
						                                        value="<?php echo $this->item->monthdays; ?>"/>
					</td>
				</tr>

				<tr>
					<td>
						<?php
						$params     = new \Joomla\Registry\Registry($this->item->params);
						$options    = array();
						$options[]  = JHtml::_('select.option', 'first', JText::_('EB_FIRST'));
						$options[]  = JHtml::_('select.option', 'second', JText::_('EB_SECOND'));
						$options[]  = JHtml::_('select.option', 'third', JText::_('EB_THIRD'));
						$options[]  = JHtml::_('select.option', 'fourth', JText::_('EB_FOURTH'));
						$options[]  = JHtml::_('select.option', 'fifth', JText::_('EB_FIFTH'));
						$daysOfWeek = array(
							'Sun' => JText::_('EB_SUNDAY'),
							'Mon' => JText::_('EB_MONDAY'),
							'Tue' => JText::_('EB_TUESDAY'),
							'Wed' => JText::_('EB_WEDNESDAY'),
							'Thu' => JText::_('EB_THURSDAY'),
							'Fri' => JText::_('EB_FRIDAY'),
							'Sat' => JText::_('EB_SATURDAY')
						);
						?>
						<input type="radio" name="recurring_type"
						       value="4" <?php if ($this->item->recurring_type == 4) echo ' checked="checked" '; ?>
						       onclick="setDefaultData();"/>
						<?php echo JText::_('EB_REPEAT_EVERY'); ?>
						<input type="text" name="weekly_number_months" size="5"
						       class="input-mini clearfloat"
						       value="<?php echo $params->get('weekly_number_months', ''); ?>"/>
						<?php echo JText::_('EB_MONTHS'); ?>
						<?php echo JText::_('EB_ON'); ?>
						<?php echo JHtml::_('select.genericlist', $options, 'week_in_month', ' class="input-small" ', 'value', 'text', $params->get('week_in_month', 'first')); ?>
						<?php echo JHtml::_('select.genericlist', $daysOfWeek, 'day_of_week', ' class="input-small" ', 'value', 'text', $params->get('day_of_week', 'Sun')); ?>
						of the month
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<strong><?php echo JText::_('EB_RECURRING_ENDING'); ?></strong>
		</div>
		<div class="controls">
			<table style="width: 100%;">
				<tr>
					<td>
						<input type="radio" name="repeat_until"
						       value="1" <?php if (($this->item->recurring_occurrencies > 0) || ($this->item->recurring_end_date == '') || ($this->item->recurring_end_date == '0000-00-00 00:00:00')) echo ' checked="checked" '; ?> /> <?php echo JText::_('EB_AFTER'); ?>
						<input type="text" name="recurring_occurrencies" size="5"
						       class="input-small clearfloat"
						       value="<?php echo $this->item->recurring_occurrencies; ?>"/> <?php echo JText::_('EB_OCCURENCIES'); ?>
						<br/>
						<input type="radio" name="repeat_until"
						       value="2" <?php if (($this->item->recurring_end_date != '') && ($this->item->recurring_end_date != '0000-00-00 00:00:00')) echo ' checked="checked"'; ?> /> <?php echo JText::_('EB_AFTER_DATE') ?> <?php echo JHtml::_('calendar', $this->item->recurring_end_date != '0000-00-00 00:00:00' ? JHtml::_('date', $this->item->recurring_end_date, $format, null) : '', 'recurring_end_date', 'recurring_end_date', '%Y-%m-%d', array('class' => 'input-small')); ?>
						<br/>
					</td>
				</tr>
				<?php
				if ($this->item->id)
				{
				?>
					<tr>
						<td class="key">
							<strong><?php echo JText::_('EB_UPDATE_CHILD_EVENT'); ?></strong></td>
						<td>
							<input type="checkbox" name="update_children_event" value="1"
							       class="inputbox"/>
						</td>
					</tr>
				<?php
				}
				?>
			</table>
		</div>
	</div>

</fieldset>
