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

JHtml::_('formbehavior.chosen', 'select');
$colSpan = 10;
if (in_array('last_name', $this->coreFields))
{
	$colSpan++;
	$showLastName = true;
}
else
{
	$showLastName = false;
}

$nullDate = JFactory::getDbo()->getNullDate();
?>
<form action="index.php?option=com_eventbooking&view=registrants" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div id="j-main-container">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('EB_FILTER_SEARCH_REGISTRANTS_DESC');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->filter_search); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('EB_SEARCH_REGISTRANTS_DESC'); ?>" />
				<?php
					echo JHtml::_('calendar', $this->state->filter_from_date != $nullDate ? $this->state->filter_from_date : '', 'filter_from_date', 'filter_from_date', '%Y-%m-%d', array('class' => 'input-small', 'placeholder' => JText::_('EB_FROM')));
					echo JHtml::_('calendar', $this->state->filter_to_date != $nullDate ? $this->state->filter_to_date : '', 'filter_to_date', 'filter_to_date', '%Y-%m-%d', array('class' => 'input-small', 'placeholder' => JText::_('EB_TO')));
				?>
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search"></span></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><span class="icon-remove"></span></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<?php
					echo $this->lists['filter_event_id'];
					echo $this->lists['filter_published'];
					if ($this->config->activate_checkin_registrants)
					{
						echo $this->lists['filter_checked_in'];
					}
					echo $this->pagination->getLimitBox();
				?>
			</div>
		</div>
		<div class="clearfix"></div>
		<table class="adminlist table table-striped">
			<thead>
			<tr>
				<th width="2%" class="text_center">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
				</th>
				<th class="title" style="text-align: left;" width="10%">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_FIRST_NAME'), 'tbl.first_name', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
				</th>
				<?php
					if ($showLastName)
					{
					?>
						<th class="title" style="text-align: left;" width="10%">
							<?php echo JHtml::_('grid.sort',  JText::_('EB_LAST_NAME'), 'tbl.last_name', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
						</th>
					<?php
					}
				?>
				<th class="title" style="text-align: left;" width="15%">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_EVENT'), 'ev.title', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
				</th>
				<?php
				if ($this->config->show_event_date)
				{
					$colSpan++;
				?>
					<th width="7%" class="title" nowrap="nowrap">
						<?php echo JHtml::_('grid.sort',  JText::_('EB_EVENT_DATE'), 'ev.event_date', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
					</th>
				<?php
				}
				?>
				<th width="10%" class="title" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_EMAIL'), 'tbl.email', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
				</th>
				<th class="title" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_NUMBER_REGISTRANTS'), 'tbl.number_registrants', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
				</th>
				<?php
					if (count($this->tickets))
					{
						$colSpan++;
					?>
						<th width="10%" class="title" nowrap="nowrap">
							<?php echo JText::_('EB_TICKETS'); ?>
						</th>
					<?php
					}
				?>
				<th width="10%" class="title" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_REGISTRATION_DATE'), 'tbl.register_date', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
				</th>
				<th width="5%" class="title" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_AMOUNT'), 'tbl.amount', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
				</th>
				<?php
				foreach ($this->fields as $field)
				{
					$colSpan++;

					if ($field->is_core)
					{
					?>
						<th class="title" nowrap="nowrap">
							<?php echo JHtml::_('grid.sort', JText::_($field->title), 'tbl.' . $field->name, $this->state->filter_order_Dir, $this->state->filter_order); ?>
						</th>
					<?php
					}
					else
					{
					?>
						<th class="title" nowrap="nowrap"><?php echo $field->title; ?></th>
					<?php
					}
				}

				if ($this->config->activate_deposit_feature)
				{
					$colSpan++;
				?>
					<th width="5%" class="title" nowrap="nowrap">
						<?php echo JHtml::_('grid.sort',  JText::_('EB_PAYMENT_STATUS'), 'tbl.payment_status', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
					</th>
				<?php
				}
				if ($this->config->show_coupon_code_in_registrant_list)
				{
					$colSpan++;
				?>
					<th width="7%" class="title" nowrap="nowrap">
						<?php echo JHtml::_('grid.sort',  JText::_('EB_COUPON'), 'cp.code', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
					</th>
				<?php
				}
				if ($this->totalPlugins > 1)
				{
					$colSpan++;
				?>
					<th width="5%" class="title" nowrap="nowrap">
						<?php echo JHtml::_('grid.sort',  JText::_('EB_PAYMENT_METHOD'), 'tbl.payment_method', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
					</th>
				<?php
				}
				if ($this->config->activate_tickets_pdf)
				{
					$colSpan++;
				?>
					<th width="8%" class="center">
						<?php echo JHtml::_('grid.sort',  JText::_('EB_TICKET_NUMBER'), 'tbl.ticket_number', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
				<?php
				}
				?>
				<th width="5%" class="title">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_REGISTRATION_STATUS'), 'tbl.published', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
				</th>
				<?php
				if ($this->config->activate_checkin_registrants)
				{
					$colSpan++;
				?>
					<th width="8%" class="title">
						<?php echo JHtml::_('grid.sort',  JText::_('EB_CHECKED_IN'), 'tbl.checked_in', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
					</th>
				<?php
				}
				if ($this->config->activate_invoice_feature)
				{
					$colSpan++;
				?>
					<th width="8%" class="center">
						<?php echo JHtml::_('grid.sort',  JText::_('EB_INVOICE_NUMBER'), 'tbl.invoice_number', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
				<?php
				}
				?>
				<th width="3%" class="title" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_ID'), 'tbl.id', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="<?php echo $colSpan ; ?>">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count( $this->items ); $i < $n; $i++)
			{
				$row = $this->items[$i];
				$link 	= JRoute::_( 'index.php?option=com_eventbooking&view=registrant&id='. $row->id );
				$checked 	= JHtml::_('grid.id',   $i, $row->id );
				if ($row->published == 0 || $row->published == 1)
				{
					$published 	= JHtml::_('grid.published', $row, $i, 'tick.png', 'publish_x.png');
				}
				elseif($row->published == 3)
				{
					$published = JText::_('EB_WAITING_LIST');
				}
				else
				{
					$imageSrc = 'components/com_eventbooking/assets/icons/cancelled.jpg' ;
					$title = JText::_('EB_CANCELLED') ;
					$published = '<img src="'.$imageSrc.'" title="'.$title.'" />';
				}
				$isMember = $row->group_id > 0 ? true : false ;
				if ($isMember)
				{
					$groupLink = JRoute::_( 'index.php?option=com_eventbooking&view=registrant&id='. $row->group_id );
				}

				$img 	= $row->checked_in ? 'tick.png' : 'publish_x.png';
				$alt 	= $row->checked_in ? JText::_( 'EB_CHECKED_IN' ) : JText::_( 'EB_NOT_CHECKED_IN');
				$img = JHtml::_('image','admin/'.$img, $alt, array('border' => 0), true);
				$action = $row->checked_in ? JText::_( 'EB_UN_CHECKIN' ) : JText::_( 'EB_CHECKIN' );
				$task 	= $row->checked_in ? 'reset_check_in' : 'check_in';
				$href = '
					<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $task .'\')" title="'. $action .'">'.
						$img .'</a>'
				;
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td class="text_center">
						<?php echo $checked; ?>
					</td>
					<td>
						<a href="<?php echo $link; ?>">
							<?php
								echo $row->first_name;

								if ($row->username)
								{
								?>
									<a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id=' . $row->user_id); ?>" title="View Profile" target="_blank">&nbsp;<strong>[<?php echo $row->username ; ?>]</strong></a>
								<?php
								}
							?>
						</a>
						<?php
						if ($row->is_group_billing)
						{
							echo '<br />' ;
							echo JText::_('EB_GROUP_BILLING');
						}
						if ($isMember)
						{
							?>
							<br />
							<?php echo JText::_('EB_GROUP'); ?><a href="<?php echo $groupLink; ?>"><?php echo $row->group_name ;  ?></a>
						<?php
						}
						?>
					</td>
					<?php
						if ($showLastName)
						{
						?>
							<td>
								<?php echo $row->last_name ; ?>
							</td>
						<?php
						}
					?>
					<td>
						<a href="index.php?option=com_eventbooking&view=event&id=<?php echo $row->event_id; ?>"><?php echo $row->title ; ?></a>
					</td>
					<?php
					if ($this->config->show_event_date)
					{
					?>
						<td class="text_center">
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
					<td>
						<a href="mailto:<?php echo $row->email;?>"><?php echo $row->email;?></a>
					</td>
					<td class="center" style="font-weight: bold;">
						<?php echo $row->number_registrants; ?>
					</td>
					<?php
						if (count($this->tickets))
						{
							$ticketsOutput = array();

							if (!empty($this->tickets[$row->id]))
							{
								$tickets = $this->tickets[$row->id];

								foreach ($this->ticketTypes as $ticketType)
								{
									if (!empty($tickets[$ticketType->id]))
									{
										$ticketsOutput[] = $ticketType->title . ': ' . $tickets[$ticketType->id];
									}
								}
							}
						?>
							<td>
								<?php echo implode("<br />", $ticketsOutput); ?>
							</td>
						<?php
						}
					?>
					<td class="center">
						<?php echo JHtml::_('date', $row->register_date, $this->config->date_format); ?>
					</td>
					<td>
						<?php echo EventbookingHelper::formatAmount($row->amount, $this->config) ; ?>
					</td>
					<?php
					foreach ($this->fields as $field)
					{
						$fieldValue = isset($this->fieldsData[$row->id][$field->id]) ? $this->fieldsData[$row->id][$field->id] : '';
					?>
						<td>
							<?php echo $fieldValue; ?>
						</td>
					<?php
					}

					if ($this->config->activate_deposit_feature)
					{
						?>
						<td>
							<?php
							if($row->payment_status == 1)
							{
								echo JText::_('EB_FULL_PAYMENT');
							}
							elseif($row->payment_status == 2)
							{
								echo JText::_('EB_DEPOSIT_PAID');
							}
							else
							{
								echo JText::_('EB_PARTIAL_PAYMENT');
							}
							?>
						</td>
					<?php
					}
					if ($this->config->show_coupon_code_in_registrant_list)
					{
					?>
						<td>
							<a href="index.php?option=com_eventbooking&view=coupon&id=<?php echo $row->coupon_id; ?>" target="_blank"><?php echo $row->coupon_code ; ?></a>
						</td>
					<?php
					}
					if ($this->totalPlugins > 1)
					{
						$method = os_payments::getPaymentMethod($row->payment_method) ;
						?>
						<td>
							<?php if ($method) echo JText::_($method->getTitle()); ?>
						</td>
					<?php
					}

					if ($this->config->activate_tickets_pdf)
					{
					?>
						<td class="center">
							<?php
							if ($row->ticket_code)
							{
							?>
								<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=registrant.download_ticket&id='.$row->id); ?>" title="<?php echo JText::_('EB_DOWNLOAD'); ?>"><?php echo $row->ticket_number ? EventbookingHelperTicket::formatTicketNumber($row->ticket_prefix, $row->ticket_number, $this->config) : JText::_('EB_DOWNLOAD_TICKETS');?></a>
							<?php
							}
							?>
						</td>
					<?php
					}

					?>
					<td class="center">
						<?php
							echo $published ;
						?>
					</td>
					<?php
					if ($this->config->activate_checkin_registrants)
					{
					?>
						<td class="center"><?php echo $href; ?></td>
					<?php
					}
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
					?>
					<td class="center">
						<?php echo $row->id; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>

		<?php echo JHtml::_(
				'bootstrap.renderModal',
				'collapseModal',
				array(
						'title' => JText::_('EB_MASS_MAIL'),
						'footer' => $this->loadTemplate('batch_footer')
				),
				$this->loadTemplate('batch_body')
		); ?>

	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->filter_order_Dir; ?>" />	
	<?php echo JHtml::_( 'form.token' ); ?>
	<script type="text/javascript">
		Joomla.submitbutton = function(pressbutton) 
		{
			var form = document.adminForm;
			if (pressbutton == 'add') 
			{
				if (form.filter_event_id.value == 0)
				{
					alert("<?php echo JText::_("EB_SELECT_EVENT_TO_ADD_REGISTRANT"); ?>");
					form.filter_event_id.focus();
					return;	
				}					
			}
			Joomla.submitform( pressbutton );		
		}		
	</script>	
</form>