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
<fieldset class="adminform">
	<legend class="adminform"><?php echo JText::_('EB_GROUP_REGISTRATION_RATES'); ?></legend>
	<table class="adminlist" id="price_list" width="100%">
		<tr>
			<th width="50%" class="eb-left-align">
				<?php echo JText::_('EB_REGISTRANT_NUMBER'); ?>
			</th>
			<th class="eb-left-align">
				<?php echo JText::_('EB_RATE'); ?>
			</th>
		</tr>
		<?php
		$n = max(count($this->prices), 3);

		for ($i = 0; $i < $n; $i++)
		{
			if (isset($this->prices[$i]))
			{
				$price            = $this->prices[$i];
				$registrantNumber = $price->registrant_number;
				$price            = $price->price;
			}
			else
			{
				$registrantNumber = null;
				$price            = null;
			}
		?>
			<tr>
				<td class="eb-left-align">
					<input type="text" class="input-mini" name="registrant_number[]" size="10" value="<?php echo $registrantNumber; ?>"/>
				</td>
				<td class="eb-left-align">
					<input type="text" class="input-mini" name="price[]" size="10" value="<?php echo $price; ?>"/>
				</td>
			</tr>
		<?php
		}
		?>
		<tr>
			<td colspan="3">
				<input type="button" class="button" value="<?php echo JText::_('EB_ADD'); ?>" onclick="addRow();"/>&nbsp;
				<input type="button" class="button" value="<?php echo JText::_('EB_REMOVE'); ?>" onclick="removeRow();"/>
			</td>
		</tr>
	</table>
</fieldset>
