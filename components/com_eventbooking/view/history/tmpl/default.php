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
$cols = 6;
$return = base64_encode(JUri::getInstance()->toString());
JHtml::_('formbehavior.chosen', 'select');
?>
<div id="eb-registration-history-page" class="eb-container row-fluid eb-event">
<h1 class="eb-page-heading"><?php echo JText::_('EB_REGISTRATION_HISTORY'); ?></h1>
<form action="<?php echo JRoute::_('index.php?option=com_eventbooking&view=history&Itemid='.$this->Itemid); ; ?>" method="post" name="adminForm"  id="adminForm">
	<fieldset class="filters btn-toolbar clearfix">
		<div class="filter-search btn-group pull-left">
			<label for="filter_search" class="element-invisible"><?php echo JText::_('EB_FILTER_SEARCH_REGISTRATION_RECORDS_DESC');?></label>
			<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->lists['search']); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('EB_SEARCH_REGISTRATION_RECORDS_DESC'); ?>" />
		</div>
		<div class="btn-group pull-left">
			<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search"></span></button>
			<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><span class="icon-remove"></span></button>
		</div>
		<div class="btn-group pull-left hidden-phone">
			<?php echo $this->lists['filter_event_id'] ; ?>
		</div>
	</fieldset>
<?php
	if (count($this->items))
	{
	?>
		<table class="table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<th width="5" class="hidden-phone">
					<?php echo JText::_( 'NUM' ); ?>
				</th>
				<th class="list_event">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_EVENT'), 'ev.title', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>
				<?php
					if ($this->config->show_event_date)
					{
						$cols++;
					?>
						<th class="list_event_date">
							<?php echo JHtml::_('grid.sort',  JText::_('EB_EVENT_DATE'), 'ev.event_date', $this->lists['order_Dir'], $this->lists['order'] ); ?>
						</th>
					<?php
					}
				?>
				<th class="list_event_date">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_REGISTRATION_DATE'), 'tbl.register_date', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>
				<th class="list_registrant_number hidden-phone">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_REGISTRANTS'), 'tbl.number_registrants', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>
				<th class="list_amount hidden-phone">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_AMOUNT'), 'tbl.amount', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>
				<?php
					if ($this->config->activate_deposit_feature && $this->showDueAmountColumn)
					{
						$cols++;
					?>
						<th style="text-align: right;">
							<?php echo JText::_('EB_DUE_AMOUNT'); ?>
						</th>
					<?php
					}
				?>
				<th class="list_id">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_REGISTRATION_STATUS'), 'tbl.published', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>
				<?php
					if ($this->config->activate_invoice_feature)
					{
						$cols++;
					?>
						<th class="center">
							<?php echo JHtml::_('grid.sort',  JText::_('EB_INVOICE_NUMBER'), 'tbl.invoice_number', $this->lists['order_Dir'], $this->lists['order'] ); ?>
						</th>
					<?php
					}

					if ($this->showDownloadTicket)
					{
						$cols++;
					?>
						<th class="center">
							<?php echo JText::_('EB_TICKET'); ?>
						</th>
					<?php
					}

					if ($this->showDownloadCertificate)
					{
						$cols++;
					?>
						<th class="center">
							<?php echo JText::_('EB_CERTIFICATE'); ?>
						</th>
					<?php
					}
				?>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<?php
					if ($this->pagination->total > $this->pagination->limit)
					{
					?>
						<td colspan="<?php echo $cols; ?>">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					<?php
					}
				?>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		for ($i=0, $n=count( $this->items ); $i < $n; $i++)
		{
			$row = $this->items[$i];
			$link 	= JRoute::_( 'index.php?option=com_eventbooking&view=registrant&id='. $row->id.'&Itemid='.$this->Itemid.'&return='.$return);
			?>
			<tr>
				<td class="hidden-phone">
					<?php echo $this->pagination->getRowOffset( $i ); ?>
				</td>
				<td>
					<a href="<?php echo $link; ?>"><?php echo $row->title ; ?></a>
				</td>
				<?php
					if ($this->config->show_event_date)
					{
					?>
						<td>
							<?php
							if ($row->event_date == EB_TBC_DATE)
							{
								echo JText::_('EB_TBC');
							}
							else
							{
								echo JHtml::_('date', $row->event_date, $this->config->date_format, null);
							}
							?>
						</td>
					<?php
					}
				?>
				<td class="center">
					<?php echo JHtml::_('date', $row->register_date, $this->config->date_format) ; ?>
				</td>
				<td class="center hidden-phone" style="font-weight: bold;">
					<?php echo $row->number_registrants; ?>
				</td>
				<td align="right" class="hidden-phone">
					<?php echo EventbookingHelper::formatCurrency($row->amount, $this->config) ; ?>
				</td>
				<?php
				if ($this->config->activate_deposit_feature && $this->showDueAmountColumn)
				{
				?>
					<td style="text-align: right;">
						<?php
						if ($row->payment_status != 1)
						{
							// Check to see if there is an online payment method available for this event
							if ($row->payment_methods)
							{
								$hasOnlinePaymentMethods = count(array_intersect($this->onlinePaymentPlugins, explode(',', $row->payment_methods)));
							}
							else
							{
								$hasOnlinePaymentMethods = count($this->onlinePaymentPlugins);
							}

							echo EventbookingHelper::formatCurrency($row->amount - $row->deposit_amount, $this->config);

							if ($hasOnlinePaymentMethods)
							{
							?>
								<a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=payment&registrant_id='.$row->id.'&Itemid='.$this->Itemid); ?>"><?php echo JText::_('EB_MAKE_PAYMENT'); ?></a>
							<?php
							}
						}
						?>
					</td>
				<?php
				}
				?>
				<td class="center">
					<?php
						switch($row->published)
						{
							case 0 :
								echo JText::_('EB_PENDING');
								break ;
							case 1 :
								echo JText::_('EB_PAID');
								break ;
							case 2 :
								echo JText::_('EB_CANCELLED');
								break;
							case 3:
								echo JText::_('EB_WAITING_LIST');

								// If there is space, we will display payment link here to allow users to make payment to become registrants
								if ($this->config->enable_waiting_list_payment && $row->group_id == 0)
								{
									$event = EventbookingHelperDatabase::getEvent($row->event_id);

									if ($event->event_capacity == 0 || ($event->event_capacity - $event->total_registrants >= $row->number_registrants))
									{
										// Check to see if there is an online payment method available for this event
										if ($row->payment_methods)
										{
											$hasOnlinePaymentMethods = count(array_intersect($this->onlinePaymentPlugins, explode(',', $row->payment_methods)));
										}
										else
										{
											$hasOnlinePaymentMethods = count($this->onlinePaymentPlugins);
										}

										if ($hasOnlinePaymentMethods)
										{
										?>
											<a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=payment&layout=registration&order_number='.$row->registration_code.'&Itemid='.$this->Itemid); ?>"><?php echo JText::_('EB_MAKE_PAYMENT'); ?></a>
										<?php
										}
									}
								}


								break;
						}
					?>
				</td>
				<?php
					if ($this->config->activate_invoice_feature)
					{
					?>
						<td class="center">
							<?php
							if ($row->invoice_number)
							{
							?>
								<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=registrant.download_invoice&id='.($row->cart_id ? $row->cart_id : ($row->group_id ? $row->group_id : $row->id))); ?>" title="<?php echo JText::_('EB_DOWNLOAD'); ?>"><?php echo EventbookingHelper::formatInvoiceNumber($row->invoice_number, $this->config) ; ?></a>
							<?php
							}
							?>
						</td>
					<?php
					}

					if ($this->showDownloadTicket)
					{
					?>
						<td class="center">
							<?php
							if ($row->ticket_code && $row->payment_status == 1)
							{
							?>
								<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=registrant.download_ticket&id='.$row->id); ?>" title="<?php echo JText::_('EB_DOWNLOAD'); ?>"><?php echo $row->ticket_number ? EventbookingHelperTicket::formatTicketNumber($row->ticket_prefix, $row->ticket_number, $this->config) : JText::_('EB_DOWNLOAD_TICKETS');?></a>
							<?php
							}
							?>
						</td>
					<?php
					}

					if ($this->showDownloadCertificate)
					{
					?>
						<td class="center">
							<?php
							if ($row->show_download_certificate)
							{
							?>
								<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=registrant.download_certificate&id='.$row->id); ?>" title="<?php echo JText::_('EB_DOWNLOAD'); ?>"><?php echo EventbookingHelper::formatCertificateNumber($row->id, $this->config);?></a>
							<?php
							}
							?>
						</td>
					<?php
					}
				?>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>
	<?php
	}
	else
	{
		echo '<div class="text-info">'.JText::_('EB_NO_REGISTRATION_RECORDS').'</div>' ;
	}
?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
</div>