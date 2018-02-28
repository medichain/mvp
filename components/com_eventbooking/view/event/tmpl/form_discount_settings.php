<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;
?>
<table class="admintable">
	<tr>
		<td class="key" width="30%">
			<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_MEMBER_DISCOUNT' );?>::<?php echo JText::_('EB_MEMBER_DISCOUNT_EXPLAIN'); ?>"><?php echo JText::_('EB_MEMBER_DISCOUNT'); ?></span>
		</td>
		<td>
			<input type="text" name="discount_amounts" id="discount_amounts" class="input-mini" size="5" value="<?php echo $this->item->discount_amounts; ?>" />&nbsp;&nbsp;<?php echo $this->lists['discount_type'] ; ?>
		</td>
	</tr>
	<tr>
		<td class="key" width="30%">
			<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_EARLY_BIRD_DISCOUNT' );?>::<?php echo JText::_('EB_EARLY_BIRD_DISCOUNT_EXPLAIN'); ?>"><?php echo JText::_('EB_EARLY_BIRD_DISCOUNT'); ?></span>
		</td>
		<td>
			<input type="text" name="early_bird_discount_amount" id="early_bird_discount_amount" class="input-mini" size="5" value="<?php echo $this->item->early_bird_discount_amount; ?>" />&nbsp;&nbsp;<?php echo $this->lists['early_bird_discount_type'] ; ?>
		</td>
	</tr>
	<tr>
		<td class="key" width="30%">
			<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_EARLY_BIRD_DISCOUNT_DATE' );?>::<?php echo JText::_('EB_EARLY_BIRD_DISCOUNT_DATE_EXPLAIN'); ?>"><?php echo JText::_('EB_EARLY_BIRD_DISCOUNT_DATE'); ?></span>
		</td>
		<td>
			<?php echo JHtml::_('calendar', $this->item->early_bird_discount_date != $this->nullDate ? JHtml::_('date', $this->item->early_bird_discount_date, 'Y-m-d', null) : '', 'early_bird_discount_date', 'early_bird_discount_date'); ?>
		</td>
	</tr>
</table>