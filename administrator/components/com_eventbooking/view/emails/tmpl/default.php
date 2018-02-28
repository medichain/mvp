<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2017 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;

JHtml::_('bootstrap.tooltip');
JHtml::_('formbehavior.chosen', 'select');

$config = EventbookingHelper::getConfig();
?>
<form action="index.php?option=com_eventbooking&view=emails" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('EB_FILTER_SEARCH_EMAILS_DESC');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->filter_search); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('EB_SEARCH_EMAILS_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search"></span></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><span class="icon-remove"></span></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<?php
					echo $this->lists['filter_sent_to'];
					echo $this->lists['filter_email_type'];
					echo $this->pagination->getLimitBox();
				?>
			</div>
		</div>
		<div class="clearfix"></div>
		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="20">
						<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
					</th>
					<th class="title" style="text-align: left;">
						<?php echo JHtml::_('grid.sort',  JText::_('EB_SUBJECT'), 'tbl.subject', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
					</th>
					<th class="title">
						<?php echo JHtml::_('grid.sort',  JText::_('EB_EMAIL'), 'tbl.email', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
					</th>
					<th class="center title" width="15%">
						<?php echo JHtml::_('grid.sort',  JText::_('EB_SENT_TO'), 'tbl.sent_to', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
					</th>			
					<th class="center title" width="15%">
						<?php echo JHtml::_('grid.sort',  JText::_('EB_SENT_AT'), 'tbl.sent_at', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
					</th>
					<th class="center title" width="15%">
						<?php echo JHtml::_('grid.sort',  JText::_('EB_TYPE'), 'tbl.email_type', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
					</th>
					<th class="center" width="5%">
						<?php echo JHtml::_('grid.sort',  JText::_('EB_ID'), 'tbl.id', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
					</th>													
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="7">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;

			$sentTos = array(
				1 => JText::_('EB_ADMIN'),
				2 => JText::_('EB_REGISTRANTS'),
			);

			$emailTypes = array(
				'new_registration_emails'      => JText::_('EB_NEW_REGISTRATION_EMAILS'),
				'reminder_emails' => JText::_('EB_REMINDER_EMAILS'),
			);
			
			for ($i=0, $n=count( $this->items ); $i < $n; $i++)
			{
				$row = &$this->items[$i];
				$link 	= JRoute::_( 'index.php?option=com_eventbooking&view=email&id='. $row->id);
				$checked 	= JHtml::_('grid.id',   $i, $row->id );
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $checked; ?>
					</td>
					<td>
						<a href="<?php echo $link; ?>">
							<?php echo $row->subject; ?>
						</a>
					</td>
					<td>
						<a href="mailto:<?php echo $row->email; ?>"><?php echo $row->email; ?></a>
					</td>
					<td class="center">
						<?php
							if (isset($sentTos[$row->sent_to]))
							{
								echo $sentTos[$row->sent_to];
							}
						?>
					</td>	
					<td class="center">
						<?php echo JHtml::_('date', $row->sent_at, $config->date_format.' H:i'); ?>
					</td>											
					<td class="center">
						<?php
						if (isset($emailTypes[$row->email_type]))
						{
							echo $emailTypes[$row->email_type];
						}
						?>
					</td>
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
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->filter_order_Dir; ?>" />	
	<?php echo JHtml::_( 'form.token' ); ?>
	</div>
</form>