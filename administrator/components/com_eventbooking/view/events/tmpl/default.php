<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die ;
JToolbarHelper::custom('export', 'download', 'download', 'Export Events', false);

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');


$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->state->filter_order;
$listDirn	= $this->state->filter_order_Dir;
$saveOrder	= $listOrder == 'tbl.ordering';
$ordering = ($this->state->filter_order == 'tbl.ordering');

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_eventbooking&task=event.save_order_ajax';
	JHtml::_('sortablelist.sortable', 'eventList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$customOptions = array(
	'filtersHidden'       => true,
	'defaultLimit'        => JFactory::getApplication()->get('list_limit', 20),
	'searchFieldSelector' => '#filter_search',
	'orderFieldSelector'  => '#filter_full_ordering'
);
JHtml::_('searchtools.form', '#adminForm', $customOptions);
?>
<form action="index.php?option=com_eventbooking&view=events" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('EB_FILTER_SEARCH_EVENTS_DESC');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->filter_search); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('EB_SEARCH_EVENTS_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search"></span></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><span class="icon-remove"></span></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<?php
					echo $this->lists['filter_category_id'];
					echo $this->lists['filter_location_id'];
					echo $this->lists['filter_state'];
					echo $this->lists['filter_access'];
					echo $this->lists['filter_events'];
					echo $this->pagination->getLimitBox();
				?>
			</div>
		</div>
		<div class="clearfix"></div>
		<table class="adminlist table table-striped" id="eventList">
			<thead>
			<tr>
				<th width="1%" class="nowrap center hidden-phone">
					<?php echo JHtml::_('searchtools.sort', '', 'tbl.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
				</th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
				</th>
				<th class="title" style="text-align: left;">
					<?php echo JHtml::_('searchtools.sort',  JText::_('EB_TITLE'), 'tbl.title', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
				</th>
				<th class="title" width="18%" style="text-align: left;">
					<?php echo JText::_('EB_CATEGORY'); ?>
				</th>
				<th class="center title" width="10%">
					<?php echo JHtml::_('searchtools.sort',  JText::_('EB_EVENT_DATE'), 'tbl.event_date', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
				</th>
				<th class="title center" width="7%">
					<?php echo JHtml::_('searchtools.sort', JText::_('EB_PRICE'), 'tbl.individual_price', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
				</th>
				<th class="title center" width="7%">
					<?php echo JHtml::_('searchtools.sort', JText::_('EB_CAPACITY'), 'tbl.event_capacity', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
				</th>
				<th class="title" width="7%">
					<?php echo JHtml::_('searchtools.sort',  JText::_('EB_NUMBER_REGISTRANTS'), 'total_registrants', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
				</th>
				<?php
				if ($this->config->activate_recurring_event)
				{
				?>
					<th width="8%" nowrap="nowrap">
						<?php echo JHtml::_('searchtools.sort', JText::_('EB_EVENT_TYPE'), 'tbl.event_type', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
					</th>
				<?php
				}
				?>
				<th width="5%" class="nowrap hidden-phone">
					<?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'tbl.access', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" nowrap="nowrap" class="center">
					<?php echo JHtml::_('searchtools.sort', JText::_('EB_PUBLISHED'), 'tbl.published', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
				</th>
				<th width="2%" nowrap="nowrap" class="center">
					<?php echo JHtml::_('searchtools.sort',  JText::_('JGLOBAL_HITS'), 'tbl.hits', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
				</th>
				<th width="1%" nowrap="nowrap" class="center">
					<?php echo JHtml::_('searchtools.sort',  JText::_('EB_ID'), 'tbl.id', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
				</th>
			</tr>
			</thead>
			<?php
			if ($this->config->activate_recurring_event)
			{
				$colspan = 13 ;
			}
			else
			{
				$colspan = 12 ;
			}
			?>
			<tfoot>
			<tr>
				<td colspan="<?php echo $colspan ; ?>">
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
				$link 	= JRoute::_( 'index.php?option=com_eventbooking&view=event&id='. $row->id );
				$checked 	= JHtml::_('grid.id',   $i, $row->id );
				$published 	= JHtml::_('grid.published', $row, $i, 'tick.png', 'publish_x.png');
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td class="order nowrap center hidden-phone">
						<?php
						$iconClass = '';
						if (!$saveOrder)
						{
							$iconClass = ' inactive tip-top hasTooltip"';
						}
						?>
						<span class="sortable-handler<?php echo $iconClass ?>">
						<i class="icon-menu"></i>
						</span>
						<?php if ($saveOrder) : ?>
							<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $row->ordering ?>" class="width-20 text-area-order "/>
						<?php endif; ?>
					</td>
					<td>
						<?php echo $checked; ?>
					</td>
					<td>
						<a href="<?php echo $link; ?>">
							<?php echo $row->title ; ?>
						</a>
					</td>
					<td>
						<?php echo $row->category_name ; ?>
					</td>
					<td class="center">
						<?php echo JHtml::_('date', $row->event_date, $this->config->date_format, null); ?>
					</td>
					<td class="center">
						<?php
							if ($row->individual_price > 0)
							{
								echo EventbookingHelper::formatAmount($row->individual_price, $this->config);
							}
							else
							{
								echo JText::_('EB_FREE');
							}
						?>
					</td>
					<td class="center">
						<?php echo $row->event_capacity; ?>
					</td>
					<td class="center">
						<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=registrants&filter_event_id='.$row->id);?>"> <?php echo $row->total_registrants; ?></a>						
					</td>
					<?php
					if ($this->config->activate_recurring_event)
					{
					?>
						<td align="left">
							<?php
							if ($row->event_type == 0)
							{
								echo JText::_('EB_STANDARD_EVENT');
							}
							elseif($row->event_type == 1)
							{
								echo JText::_('EB_PARENT_EVENT');
							} else
							{
								echo JText::_('EB_CHILD_EVENT');
							}
							?>
						</td>
					<?php
					}
					?>
					<td>
						<?php echo $row->access_level; ?>
					</td>
					<td class="center">
						<?php echo $published; ?>
					</td>
					<td class="center">
						<?php echo $row->hits; ?>
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
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->filter_order_Dir; ?>" />
	<input type="hidden" id="filter_full_ordering" name="filter_full_ordering" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>

	<script type="text/javascript">
		(function($){
			$(document).ready(function(){
				$('#filter_state').addClass('input-medium').removeClass('inputbox');
			})
		})(jQuery);
	</script>
</form>