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
<table class="table table-striped table-bordered table-condensed" cellspacing="0" cellpadding="0">
	<thead>
	<tr>		
		<th class="col_event text-left">
			<?php echo JText::_('EB_EVENT'); ?>
		</th>		
		<?php
			if ($config->show_event_date) 
			{
			?>
				<th class="col_event_date text-center">
					<?php echo JText::_('EB_EVENT_DATE'); ?>
				</th>
			<?php		
			}
		?>
		<th class="col_price text-right">
			<?php echo JText::_('EB_PRICE'); ?>
		</th>									
		<th class="col_quantity text-center">
			<?php echo JText::_('EB_QUANTITY'); ?>
		</th>																
		<th class="col_quantity text-right">
			<?php echo JText::_('EB_SUB_TOTAL'); ?>
		</th>
	</tr>
	</thead>
	<tbody>
	<?php
		$total = 0 ;
		$k = 0 ;					
		for ($i = 0 , $n = count($items) ; $i < $n; $i++) 
		{
			$item = $items[$i] ;			
			$rate = EventbookingHelper::getRegistrationRate($item->event_id, $item->number_registrants);
			$total += $item->number_registrants*$rate ;
            $url = JUri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')).JRoute::_(EventbookingHelperRoute::getEventRoute($item->event_id, 0, $Itemid));
		?>
			<tr>								
				<td class="col_event">
					<a href="<?php echo $url; ?>"><?php echo $item->title; ?></a>								
				</td>				
				<?php
					if ($config->show_event_date) 
					{
					?>
						<td class="col_event_date text-center">
							<?php 
							    if ($item->event_date == EB_TBC_DATE) 
								{
							        echo JText::_('EB_TBC');
							    } 
							    else 
								{
									if (strpos($item->event_date, '00:00:00') !== false)
									{
										$dateFormat = $config->date_format;
									}
									else
									{
										$dateFormat = $config->event_date_format;
									}

							        echo JHtml::_('date', $item->event_date,  $dateFormat, null);
							    }    
							?>							
						</td>	
					<?php	
					}
				?>
				<td class="col_price text-right">
					<?php echo EventbookingHelper::formatAmount($rate, $config); ?>
				</td>
				<td class="col_quantity text-center">
					<?php echo $item->number_registrants ; ?>
				</td>																										
				<td class="col_price text-right">
					<?php echo EventbookingHelper::formatAmount($rate*$item->number_registrants, $config); ?>
				</td>						
			</tr>
		<?php				
			$k = 1 - $k ;				
		}
	?>			
	</tbody>					
</table>	
<table width="100%" class="os_table" cellspacing="0" cellpadding="0">	
<?php
	if ($config->collect_member_information_in_cart)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		foreach ($items as $item)
		{
			$rowFields = EventbookingHelperRegistration::getFormFields($item->event_id, 2);
			$query->clear()
					->select('*')
					->from('#__eb_registrants')
					->where('group_id = ' . $item->id);
			$db->setQuery($query);
			$rowMembers = $db->loadObjectList();
			?>
			<tr><td colspan="2"><h3 class="eb-heading"><?php echo JText::sprintf('EB_EVENT_REGISTRANTS_INFORMATION', $item->title); ?></h3></td></tr>
			<?php
			$i = 0;
			foreach ($rowMembers as $rowMember)
			{
				$i++;
				$memberForm = new RADForm($rowFields);
				$memberData = EventbookingHelperRegistration::getRegistrantData($rowMember, $rowFields);
				$memberForm->bind($memberData);
				$memberForm->buildFieldsDependency();
				$fields = $memberForm->getFields();
				?>
				<tr><td colspan="2"><h4 class="eb-heading"><?php echo JText::sprintf('EB_MEMBER_INFORMATION', $i); ?></h4></td></tr>
				<?php
				foreach ($fields as $field)
				{
					if ($field->hideOnDisplay)
					{
						continue;
					}
					echo $field->getOutput(false);
				}
			}
		}
		?>
		<tr><td colspan="2"><h3 class="eb-heading"><?php echo JText::_('EB_BILLING_INFORMATION'); ?></h3></td></tr>
		<?php
	}
	$fields = $form->getFields();
	foreach ($fields as $field)
	{
		if ($field->hideOnDisplay || $field->row->hide_on_email)
		{
			continue;
		}
		echo $field->getOutput(false);						
	}
	if ($totalAmount > 0)
	{
	?>
	<tr>
		<td class="title_cell">
			<?php echo JText::_('EB_AMOUNT'); ?>
		</td>
		<td class="field_cell">
			<?php echo EventbookingHelper::formatCurrency($totalAmount, $config); ?>
		</td>
	</tr>
	<?php	
		if ($discountAmount > 0)
		{
		?>
			<tr>
				<td class="title_cell">
					<?php echo  JText::_('EB_DISCOUNT_AMOUNT'); ?>
				</td>
				<td class="field_cell">
					<?php echo EventbookingHelper::formatCurrency($discountAmount, $config); ?>
				</td>
			</tr>
		<?php
		}

		if ($lateFee > 0)
		{
		?>
			<tr>
				<td class="title_cell">
					<?php echo  JText::_('EB_LATE_FEE'); ?>
				</td>
				<td class="field_cell">
					<?php echo EventbookingHelper::formatCurrency($lateFee, $config); ?>
				</td>
			</tr>
		<?php
		}

		if ($taxAmount > 0)
		{
		?>
			<tr>
				<td class="title_cell">
					<?php echo  JText::_('EB_TAX'); ?>
				</td>
				<td class="field_cell">
					<?php echo EventbookingHelper::formatCurrency($taxAmount, $config); ?>
				</td>
			</tr>
		<?php
		}

		if ($paymentProcessingFee > 0)
		{
		?>
			<tr>
				<td class="title_cell">
					<?php echo  JText::_('EB_PAYMENT_FEE'); ?>
				</td>
				<td class="field_cell">
					<?php echo EventbookingHelper::formatCurrency($paymentProcessingFee, $config); ?>
				</td>
			</tr>
		<?php
		}
		if ($discountAmount > 0 || $taxAmount > 0 || $paymentProcessingFee > 0)
		{
		?>                
			<tr>
				<td class="title_cell">
					<?php echo  JText::_('EB_GROSS_AMOUNT'); ?>
				</td>
				<td class="field_cell">
					<?php echo EventbookingHelper::formatCurrency($amount, $config);?>
				</td>
			</tr>
		<?php
		}            
	}
	if ($depositAmount > 0)
	{
	?>
	<tr>
		<td class="title_cell">
			<?php echo JText::_('EB_DEPOSIT_AMOUNT'); ?>
		</td>
		<td class="field_cell">
			<?php echo EventbookingHelper::formatCurrency($depositAmount, $config); ?>
		</td>
	</tr>
	<tr>
		<td class="title_cell">
			<?php echo JText::_('EB_DUE_AMOUNT'); ?>
		</td>
		<td class="field_cell">
			<?php echo EventbookingHelper::formatCurrency($amount - $depositAmount, $config); ?>
		</td>
	</tr>
	<?php
	}
	if ($amount > 0)
	{
	?>
	<tr>
		<td class="title_cell">
			<?php echo  JText::_('EB_PAYMEMNT_METHOD'); ?>
		</td>
		<td class="field_cell">
		<?php
			$method = os_payments::loadPaymentMethod($row->payment_method);
			if ($method)
			{
				echo JText::_($method->title) ;
			}
		?>
		</td>
	</tr>
	<?php
	if (!empty($last4Digits))
	{
	?>
		<tr>
			<td class="title_cell">
				<?php echo JText::_('EB_LAST_4DIGITS'); ?>
			</td>
			<td class="field_cell">
				<?php echo $last4Digits; ?>
			</td>
		</tr>
	<?php
	}
	?>
	<tr>
		<td class="title_cell">
			<?php echo JText::_('EB_TRANSACTION_ID'); ?>
		</td>
		<td class="field_cell">
			<?php echo $row->transaction_id ; ?>
		</td>
	</tr>
	<?php
	}       	
?>																	
</table>	