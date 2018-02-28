<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;
?>
<table class="adminlist table table-striped">
	<thead>
		<th>
			<?php echo JText::_('EB_ID'); ?>
		</th>
		<th>
			<?php echo JText::_('EB_FIRST_NAME'); ?>
		</th>
		<th>
			<?php echo JText::_('EB_LAST_NAME'); ?>
		</th>
		<th>
			<?php echo JText::_('EB_EMAIL'); ?>
		</th>
		<th class="center">
			<?php echo JText::_('EB_REGISTRATION_DATE'); ?>
		</th>
		<th class="text_right">
			<?php echo JText::_('EB_DISCOUNT_AMOUNT'); ?>
		</th>
	</thead>
	<tbody>
		<?php
			foreach($this->registrants as $registrant)
			{
			?>
				<tr>
					<td><a href="index.php?option=com_eventbooking&view=registrant&id=<?php echo $registrant->id; ?>" target="_blank"><?php echo $registrant->id; ?></a></td>
					<td><?php echo $registrant->first_name; ?></td>
					<td><?php echo $registrant->last_name; ?></td>
					<td><a href="mailto:<?php echo $registrant->email; ?>"><?php echo $registrant->email; ?></a></td>
					<td class="center"><?php echo JHtml::_('date', $registrant->register_date, $this->config->date_format); ?></td>
					<td class="text_right">
						<?php
							if ($this->item->coupon_type == 1)
							{
								echo EventbookingHelper::formatAmount($this->item->discount, $this->config);
							}
							else
							{
								echo EventbookingHelper::formatAmount($registrant->total_amount*$this->item->discount/100, $this->config);
							}
						?>
					</td>
				</tr>

			<?php
			}
		?>
	</tbody>
</table>