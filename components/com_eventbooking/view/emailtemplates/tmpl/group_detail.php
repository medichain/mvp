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
$rowFluidClass     = $bootstrapHelper->getClassMapping('row-fluid');
$span6Class        = $bootstrapHelper->getClassMapping('span6');
$nullDate          = JFactory::getDbo()->getNullDate();
?>
<form id="adminForm" class="form form-horizontal">
	<div class="<?php echo $controlGroupClass;  ?>">
		<h3 class="eb-heading"><?php echo JText::_('EB_GENERAL_INFORMATION') ; ?></h3>
	</div>
	<div class="<?php echo $controlGroupClass;  ?>">
		<label class="<?php echo $controlLabelClass;  ?>">
			<?php echo  JText::_('EB_EVENT_TITLE') ?>
		</label>
		<div class="<?php echo $controlsClass;  ?>">
			<?php echo $rowEvent->title ; ?>
		</div>
	</div>
	<?php
		if ($config->show_event_date)
		{
		?>
		<div class="<?php echo $controlGroupClass;  ?>">
			<label class="<?php echo $controlLabelClass;  ?>">
				<?php echo  JText::_('EB_EVENT_DATE') ?>
			</label>
			<div class="<?php echo $controlsClass;  ?>">
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
			<div class="<?php echo $controlGroupClass;  ?>">
				<label class="<?php echo $controlLabelClass;  ?>">
					<?php echo  JText::_('EB_LOCATION') ?>
				</label>
				<div class="<?php echo $controlsClass;  ?>">
					<?php echo $location->name.' ('.implode(', ', $locationInformation).')' ; ?>
				</div>
			</div>
		<?php
		}
	?>
	<div class="<?php echo $controlGroupClass;  ?>">
		<label class="<?php echo $controlLabelClass;  ?>">
			<?php echo  JText::_('EB_NUMBER_REGISTRANTS') ?>
		</label>
		<div class="<?php echo $controlsClass;  ?>">
			<?php echo $row->number_registrants ; ?>
		</div>
	</div>
	<?php
		$showBillingStep = EventbookingHelperRegistration::showBillingStep($row->event_id);
		if ($showBillingStep)
		{
		?>
			<div class="<?php echo $controlGroupClass;  ?>">
				<h3 class="eb-heading"><?php echo JText::_('EB_BILLING_INFORMATION') ; ?></h3>
			</div>
		<?php
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
				<div class="<?php echo $controlGroupClass;  ?>">
					<label class="<?php echo $controlLabelClass;  ?>">
						<?php echo JText::_('EB_AMOUNT'); ?>
					</label>
					<div class="<?php echo $controlsClass;  ?>">
						<?php echo EventbookingHelper::formatCurrency($row->total_amount, $config, $rowEvent->currency_symbol); ?>
					</div>
				</div>
				<?php
				if ($row->discount_amount > 0)
				{
					?>
					<div class="<?php echo $controlGroupClass;  ?>">
						<label class="<?php echo $controlLabelClass;  ?>">
							<?php echo  JText::_('EB_DISCOUNT_AMOUNT'); ?>
						</label>
						<div class="<?php echo $controlsClass;  ?>">
							<?php echo EventbookingHelper::formatCurrency($row->discount_amount, $config, $rowEvent->currency_symbol); ?>
						</div>
					</div>
				<?php
				}

				if ($row->late_fee > 0)
				{
				?>
					<div class="<?php echo $controlGroupClass;  ?>">
						<label class="<?php echo $controlLabelClass;  ?>">
							<?php echo  JText::_('EB_LATE_FEE'); ?>
						</label>
						<div class="<?php echo $controlsClass;  ?>">
							<?php echo EventbookingHelper::formatCurrency($row->late_fee, $config, $rowEvent->currency_symbol);?>
						</div>
					</div>
				<?php
				}

				if ($row->tax_amount > 0)
				{
					?>
					<div class="<?php echo $controlGroupClass;  ?>">
						<label class="<?php echo $controlLabelClass;  ?>">
							<?php echo  JText::_('EB_TAX'); ?>
						</label>
						<div class="<?php echo $controlsClass;  ?>">
							<?php echo EventbookingHelper::formatCurrency($row->tax_amount, $config, $rowEvent->currency_symbol); ?>
						</div>
					</div>
				<?php
				}
				if ($row->payment_processing_fee > 0)
				{
				?>
					<div class="<?php echo $controlGroupClass;  ?>">
						<label class="<?php echo $controlLabelClass;  ?>">
							<?php echo  JText::_('EB_PAYMENT_FEE'); ?>
						</label>
						<div class="<?php echo $controlsClass;  ?>">
							<?php echo EventbookingHelper::formatCurrency($row->payment_processing_fee, $config, $rowEvent->currency_symbol); ?>
						</div>
					</div>
				<?php
				}
				if ($row->discount_amount > 0 || $row->tax_amount > 0 || $row->payment_processing_fee > 0 || $row->late_fee > 0)
				{
				?>
					<div class="<?php echo $controlGroupClass;  ?>">
						<label class="<?php echo $controlLabelClass;  ?>">
							<?php echo  JText::_('EB_GROSS_AMOUNT'); ?>
						</label>
						<div class="<?php echo $controlsClass;  ?>">
							<?php echo EventbookingHelper::formatCurrency($row->amount, $config, $rowEvent->currency_symbol) ; ?>
						</div>
					</div>
				<?php
				}
			}
			if ($row->deposit_amount > 0)
			{
				?>
				<div class="<?php echo $controlGroupClass;  ?>">
					<label class="<?php echo $controlLabelClass;  ?>">
						<?php echo JText::_('EB_DEPOSIT_AMOUNT'); ?>
					</label>
					<div class="<?php echo $controlsClass;  ?>">
						<?php echo EventbookingHelper::formatCurrency($row->deposit_amount, $config, $rowEvent->currency_symbol); ?>
					</div>
				</div>
				<div class="<?php echo $controlGroupClass;  ?>">
					<label class="<?php echo $controlLabelClass;  ?>">
						<?php echo JText::_('EB_DUE_AMOUNT'); ?>
					</label>
					<div class="<?php echo $controlsClass;  ?>">
						<?php echo EventbookingHelper::formatCurrency($row->amount - $row->deposit_amount, $config, $rowEvent->currency_symbol); ?>
					</div>
				</div>
			<?php
			}
			if ($row->amount > 0)
			{
				?>
				<div class="<?php echo $controlGroupClass;  ?>">
					<label class="<?php echo $controlLabelClass;  ?>">
						<?php echo  JText::_('EB_PAYMEMNT_METHOD'); ?>
					</label>
					<div class="<?php echo $controlsClass;  ?>">
						<?php
						$method = os_payments::loadPaymentMethod($row->payment_method);
						if ($method)
						{
							echo JText::_($method->title) ;
						}
						?>
					</div>
				</div>
				<div class="<?php echo $controlGroupClass;  ?>">
					<label class="<?php echo $controlLabelClass;  ?>">
						<?php echo JText::_('EB_TRANSACTION_ID'); ?>
					</label>
					<div class="<?php echo $controlsClass;  ?>">
						<?php echo $row->transaction_id ; ?>
					</div>
				</div>
			<?php
			}
		}

		if ($rowEvent->collect_member_information === '')
		{
			$collectMemberInformation = $config->collect_member_information;
		}
		else
		{
			$collectMemberInformation = $rowEvent->collect_member_information;
		}

		if ($collectMemberInformation && count($rowMembers))
		{
		?>
			<div class="<?php echo $controlGroupClass;  ?>">
				<h3 class="eb-heading"><?php echo JText::_('EB_MEMBERS_INFORMATION') ; ?></h3>
			</div>
			<?php
				$rowFields = EventbookingHelperRegistration::getFormFields($row->event_id, 2);
				for ($i = 0 , $n  = count($rowMembers); $i < $n; $i++)
				{
					$memberForm = new RADForm($rowFields);
					$rowMember = $rowMembers[$i];
					if ($i %2 == 0)
					{
						echo "<div class=\"".$rowFluidClass."\">\n" ;
					}


					$memberData = EventbookingHelperRegistration::getRegistrantData($rowMember, $rowFields);
					$memberForm->bind($memberData);

					//Build dependency
					$memberForm->buildFieldsDependency();
					$fields = $memberForm->getFields();
					foreach ($fields as $field)
					{
						if ($field->hideOnDisplay)
						{
							unset($fields[$field->name]);
						}
					}
					$memberForm->setFields($fields);
				?>
				<div class="<?php echo $span6Class; ?>">
					<div class="<?php echo $controlGroupClass;  ?>">
						<h4 class="eb-heading"><?php echo JText::sprintf('EB_MEMBER_INFORMATION', $i + 1) ; ?></h4>
					</div>
					<?php
						$fields = $memberForm->getFields();
						foreach ($fields as $field)
						{
							if ($i > 0 && $field->row->only_show_for_first_member)
							{
								continue;
							}
							echo $field->getOutput(true, $bootstrapHelper);
						}
					?>
					</div>
				<?php
				if (($i + 1) %2 == 0)
				{
					echo "</div>\n" ;
				}
			}
			if ($i %2 != 0)
			{
				echo "</div>" ;
			}
		}
	?>
</form>