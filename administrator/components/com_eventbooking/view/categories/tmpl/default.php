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
	$saveOrderingUrl = 'index.php?option=com_eventbooking&task=category.save_order_ajax';
	JHtml::_('sortablelist.sortable', 'categoryList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
}

$customOptions = array(
	'filtersHidden'       => true,
	'defaultLimit'        => JFactory::getApplication()->get('list_limit', 20),
	'searchFieldSelector' => '#filter_search',
	'orderFieldSelector'  => '#filter_full_ordering'
);
JHtml::_('searchtools.form', '#adminForm', $customOptions);
if (count($this->items))
{
	foreach ($this->items as &$item)
	{
		$this->ordering[$item->parent][] = $item->id;
	}
}
?>
<form action="index.php?option=com_eventbooking&view=categories" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('EB_FILTER_SEARCH_CATEGORIES_DESC');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->filter_search); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('EB_SEARCH_CATEGORIES_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search"></span></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><span class="icon-remove"></span></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<?php
					echo $this->lists['filter_state'];
					echo $this->lists['filter_access'];
					echo $this->pagination->getLimitBox();
				?>
			</div>
		</div>
		<div class="clearfix"></div>
		<table class="adminlist table table-striped" id="categoryList">
			<thead>
			<tr>
				<th width="1%" class="nowrap center hidden-phone">
					<?php echo JHtml::_('searchtools.sort', '', 'tbl.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
				</th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
				</th>
				<th class="title" style="text-align: left;">
					<?php echo JHtml::_('searchtools.sort',  JText::_('EB_NAME'), 'tbl.name', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
				</th>
				<th class="center title" width="15%">
					<?php echo JText::_('EB_NUMBER_EVENTS'); ?>
				</th>
				<th width="8%" class="nowrap hidden-phone">
					<?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'tbl.access', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('searchtools.sort',  JText::_('EB_PUBLISHED'), 'tbl.published', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
				</th>
				<th width="2%">
					<?php echo JHtml::_('searchtools.sort',  JText::_('EB_ID'), 'tbl.id', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
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
			for ($i=0, $n=count( $this->items ); $i < $n; $i++)
			{
				$row = $this->items[$i];
				$link 	= JRoute::_( 'index.php?option=com_eventbooking&view=category&id='. $row->id );
				$checked 	= JHtml::_('grid.id',   $i, $row->id );
				$published 	= JHtml::_('grid.published', $row, $i, 'tick.png', 'publish_x.png');
				// Get the parents of item for sorting
				if ($row->level > 1)
				{
					$parentsStr = "";
					$_currentParentId = $row->parent;
					$parentsStr = " " . $_currentParentId;
					for ($i2 = 0; $i2 < $row->level; $i2++)
					{
						foreach ($this->ordering as $k => $v)
						{
							$v = implode("-", $v);
							$v = "-" . $v . "-";
							if (strpos($v, "-" . $_currentParentId . "-") !== false)
							{
								$parentsStr .= " " . $k;
								$_currentParentId = $k;
								break;
							}
						}
					}
				}
				else
				{
					$parentsStr = "";
				}
				?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $row->parent; ?>" item-id="<?php echo $row->id ?>" parents="<?php echo $parentsStr ?>" level="<?php echo $row->level ?>">
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
							<?php echo $row->treename; ?>
						</a>
					</td>
					<td class="center">
						<?php echo $row->total_events; ?>
					</td>
					<td>
						<?php echo $row->access_level; ?>
					</td>
					<td class="center">
						<?php echo $published; ?>
					</td>
					<td class="center">
						<?php echo $row->id; ?>
					</td>
				</tr>
				<?php
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
</form>