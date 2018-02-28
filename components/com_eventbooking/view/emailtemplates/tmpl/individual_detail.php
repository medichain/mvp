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
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');
$nullDate          = JFactory::getDbo()->getNullDate();
?>
<form id="adminForm" class="form form-horizontal">
	<?php
		if (!empty($ticketTypes))
		{
		?>
			<h3 class="eb-heading"><?php echo JText::_('EB_TICKET_INFORMATION'); ?></h3>
			<table class="table table-striped table-bordered table-condensed">
				<thead>
				<tr>
					<th>
						<?php echo JText::_('EB_TICKET_TYPE'); ?>
					</th>
					<th class="eb-text-right">
						<?php echo JText::_('EB_PRICE'); ?>
					</th>
					<th class="center">
						<?php echo JText::_('EB_QUANTITY'); ?>
					</th>
					<th class="eb-text-right">
						<?php echo JText::_('EB_SUB_TOTAL'); ?>
					</th>
				</tr>
				</thead>
				<tbody>
				<?php
				foreach ($ticketTypes as $ticketType)
				{
				?>
					<tr>
						<td>
							<?php echo JText::_($ticketType->title); ?>
						</td>
						<td class="eb-text-right">
							<?php echo EventbookingHelper::formatCurrency($ticketType->price, $config); ?>
						</td>
						<td class="center">
							<?php echo $ticketType->quantity; ?>
						</td>
						<td class="eb-text-right">
							<?php echo EventbookingHelper::formatCurrency($ticketType->price*$ticketType->quantity, $config); ?>
						</td>
					</tr>
				<?php
				}
				?>
				</tbody>
			</table>
		<?php
		}
	?>
	<div class="<?php echo $controlGroupClass; ?>">
		<label class="<?php echo $controlLabelClass; ?>">
			<?php echo  JText::_('EB_EVENT_TITLE') ?>
		</label>
		<div class="<?php echo $controlsClass; ?>">
			<?php echo $rowEvent->title ; ?>
		</div>
	</div>
	<?php
		if ($config->show_event_date)
		{
		?>
		<div class="<?php echo $controlGroupClass; ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo  JText::_('EB_EVENT_DATE') ?>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php
					if ($rowEvent->event_date == EB_TBC_DATE)
					{
						echo JText::_('EB_TBC');
					}
					else
					{
						if (strpos($rowEvent->event_date, '00:00:00') !== false)
						{
							$dateFormat = $config->date_format;
						}
						else
						{
							$dateFormat = $config->event_date_format;
						}

						echo JHtml::_('date', $rowEvent->event_date, $dateFormat, null) ;
					}
				?>
			</div>
		</div>
		<?php
			if ($rowEvent->event_end_date != $nullDate)
			{
				if (strpos($rowEvent->event_end_date, '00:00:00') !== false)
				{
					$dateFormat = $config->date_format;
				}
				else
				{
					$dateFormat = $config->event_date_format;
				}
			?>
				<div class="<?php echo $controlGroupClass; ?>">
					<label class="<?php echo $controlLabelClass; ?>">
						<?php echo JText::_('EB_EVENT_END_DATE') ?>
					</label>
					<div class="<?php echo $controlsClass; ?>">
						<?php echo JHtml::_('date', $rowEvent->event_end_date, $dateFormat, null); ?>
					</div>
				</div>
			<?php
			}
		}
		if ($config->show_event_location_in_email && $rowLocation)
		{
			$location = $rowLocation ;
			$locationInformation = array();
			if ($location->address)
			{
				$locationInformation[] = $location->address;
			}
		?>
			<div class="<?php echo $controlGroupClass; ?>">
				<label class="<?php echo $controlLabelClass; ?>">
					<?php echo  JText::_('EB_LOCATION') ?>
				</label>
				<div class="<?php echo $controlsClass; ?>">
					<?php echo $location->name.' ('.implode(', ', $locationInformation).')' ; ?>
				</div>
			</div>
		<?php
		}

		//Show data for form
		$fields = $form->getFields();
		foreach ($fields as $field)
		{
			if ($field->hideOnDisplay || $field->row->hide_on_email)
			{
				continue;
			}
			echo $field->getOutput(true, $bootstrapHelper);
		}
		if ($row->total_amount > 0)
		{
		?>
		<div class="<?php echo $controlGroupClass; ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_AMOUNT'); ?>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php echo EventbookingHelper::formatCurrency($row->total_amount, $config, $rowEvent->currency_symbol); ?>
			</div>
		</div>
		<?php
			if ($row->discount_amount > 0)
			{
			?>
				<div class="<?php echo $controlGroupClass; ?>">
					<label class="<?php echo $controlLabelClass; ?>">
						<?php echo  JText::_('EB_DISCOUNT_AMOUNT'); ?>
					</label>
					<div class="<?php echo $controlsClass; ?>">
						<?php echo EventbookingHelper::formatCurrency($row->discount_amount, $config, $rowEvent->currency_symbol); ?>
					</div>
				</div>
			<?php
			}
			if ($row->late_fee > 0)
			{
			?>
				<div class="<?php echo $controlGroupClass; ?>">
					<label class="<?php echo $controlLabelClass; ?>">
						<?php echo  JText::_('EB_LATE_FEE'); ?>
					</label>
					<div class="<?php echo $controlsClass; ?>">
						<?php echo EventbookingHelper::formatCurrency($row->late_fee, $config, $rowEvent->currency_symbol); ?>
					</div>
				</div>
			<?php
			}
			if ($row->tax_amount > 0)
			{
			?>
				<div class="<?php echo $controlGroupClass; ?>">
					<label class="<?php echo $controlLabelClass; ?>">
						<?php echo  JText::_('EB_TAX'); ?>
					</label>
					<div class="<?php echo $controlsClass; ?>">
						<?php echo EventbookingHelper::formatCurrency($row->tax_amount, $config, $rowEvent->currency_symbol); ?>
					</div>
				</div>
			<?php
			}
			if ($row->payment_processing_fee > 0)
			{
			?>
				<div class="<?php echo $controlGroupClass; ?>">
					<label class="<?php echo $controlLabelClass; ?>">
						<?php echo  JText::_('EB_PAYMENT_FEE'); ?>
					</label>
					<div class="<?php echo $controlsClass; ?>">
						<?php echo EventbookingHelper::formatCurrency($row->payment_processing_fee, $config, $rowEvent->currency_symbol); ?>
					</div>
				</div>
			<?php
			}
			if ($row->discount_amount > 0 || $row->tax_amount > 0 || $row->payment_processing_fee > 0 || $row->late_fee > 0)
			{
			?>
				<div class="<?php echo $controlGroupClass; ?>">
					<label class="<?php echo $controlLabelClass; ?>">
						<?php echo  JText::_('EB_GROSS_AMOUNT'); ?>
					</label>
					<div class="<?php echo $controlsClass; ?>">
						<?php echo EventbookingHelper::formatCurrency($row->amount, $config, $rowEvent->currency_symbol) ; ?>
					</div>
				</div>
			<?php
			}
		}
		if ($row->deposit_amount > 0)
		{
		?>
		<div class="<?php echo $controlGroupClass; ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_DEPOSIT_AMOUNT'); ?>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php echo EventbookingHelper::formatCurrency($row->deposit_amount, $config, $rowEvent->currency_symbol); ?>
			</div>
		</div>
		<div class="<?php echo $controlGroupClass; ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_DUE_AMOUNT'); ?>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php echo EventbookingHelper::formatCurrency($row->amount - $row->deposit_amount, $config, $rowEvent->currency_symbol); ?>
			</div>
		</div>
		<?php
		}
		if ($row->amount > 0 && $row->published != 3)
		{
		?>
		<div class="<?php echo $controlGroupClass; ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo  JText::_('EB_PAYMEMNT_METHOD'); ?>
			</label>
			<div class="<?php echo $controlsClass; ?>">
			<?php
				$method = os_payments::loadPaymentMethod($row->payment_method);
				if ($method)
				{
					echo JText::_($method->title) ;
				}
			?>
			</div>
		</div>
		<div class="<?php echo $controlGroupClass; ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_TRANSACTION_ID'); ?>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php echo $row->transaction_id ; ?>
			</div>
		</div>
		<?php
		}
	?>
</form>