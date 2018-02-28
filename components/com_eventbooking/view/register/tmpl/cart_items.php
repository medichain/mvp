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
$popup = 'class="eb-modal" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"';
?>
<div class="eb-cart-content">
	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr>
				<th class="col_event">
					<?php echo JText::_('EB_EVENT'); ?>
				</th>
				<?php
				if ($this->config->show_event_date)
				{
				?>
					<th class="col_event_date">
						<?php echo JText::_('EB_EVENT_DATE'); ?>
					</th>
				<?php
				}
				?>
				<th class="col_price">
					<?php echo JText::_('EB_PRICE'); ?>
				</th>
				<th class="col_quantity">
					<?php echo JText::_('EB_QUANTITY'); ?>
				</th>
				<th class="col_subtotal">
					<?php echo JText::_('EB_SUB_TOTAL'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$total = 0 ;

		for ($i = 0 , $n = count($this->items) ; $i < $n; $i++)
		{
			$item = $this->items[$i] ;

			if ($this->config->show_discounted_price)
			{
				$item->rate = $item->discounted_rate;
			}

			$total += $item->quantity*$item->rate ;
			$url = JRoute::_('index.php?option=com_eventbooking&view=event&id='.$item->id.'&tmpl=component&Itemid='.$this->Itemid);
		?>
			<tr>
				<td class="col_event">
					<a href="<?php echo $url; ?>" <?php echo $popup; ?>><?php echo $item->title; ?></a>
				</td>
				<?php
				if ($this->config->show_event_date)
				{
				?>
					<td class="col_event_date">
						<?php
						if ($item->event_date == EB_TBC_DATE)
						{
							echo JText::_('EB_TBC');
						}
						else
						{
							echo JHtml::_('date', $item->event_date, $this->config->event_date_format, null);
						}
						?>
					</td>
				<?php
				}
				?>
				<td class="col_price">
					<?php echo EventbookingHelper::formatCurrency($item->rate, $this->config); ?>
				</td>
				<td class="col_quantity">
					<?php echo $item->quantity ; ?>
				</td>
				<td class="col_price">
					<?php echo EventbookingHelper::formatCurrency($item->rate*$item->quantity, $this->config); ?>
				</td>
			</tr>
		<?php
		}

		if ($this->config->show_event_date)
		{
			$cols = 5 ;
		}
		else
		{
			$cols = 4 ;
		}
		?>
		<tr>
			<td colspan="<?php echo $cols ; ?>" style="text-align: right;">
				<input type="button" class="btn" value="<?php echo JText::_('EB_MODIFY_CART'); ?>" onclick="updateCart();" />
			</td>
		</tr>
		</tbody>
	</table>
</div>
