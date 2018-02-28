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
if (!function_exists('curl_init')) 
{
	JFactory::getApplication()->enqueueMessage(JText::_('EB_CURL_NOT_INSTALLED'), 'warning');
}
?>
<form action="index.php?option=com_eventbooking&view=locations" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('EB_FILTER_SEARCH_LOCATIONS_DESC');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->filter_search); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('EB_SEARCH_LOCATIONS_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search"></span></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><span class="icon-remove"></span></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<?php
				if (JLanguageMultilang::isEnabled())
				{
					echo $this->lists['filter_language'];
				}
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
				<th class="text_left">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_NAME'), 'tbl.name', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
				<th class="text_left">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_ADDRESS'), 'tbl.address', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
				<th class="title center">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_LATITUDE'), 'tbl.lat', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
				<th class="title center">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_LONGITUDE'), 'tbl.long', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
				<th class="title center">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_PUBLISHED'), 'tbl.published', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
				<th width="1%" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_ID'), 'tbl.id', $this->state->filter_order_Dir, $this->state->filter_order); ?>
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
			for ($i=0, $n=count( $this->items ); $i < $n; $i++)
			{
				$row = $this->items[$i];
				$link 	= JRoute::_( 'index.php?option=com_eventbooking&view=location&id='. $row->id );
				$checked 	= JHtml::_('grid.id',   $i, $row->id );
				$published = JHtml::_('grid.published', $row, $i, 'tick.png', 'publish_x.png');
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $checked; ?>
					</td>
					<td>
						<a href="<?php echo $link; ?>">
							<?php echo $row->name; ?>
						</a>
					</td>
					<td>
						<?php echo $row->address ; ?>
					</td>
					<td class="center">
						<?php echo $row->lat ; ?>
					</td>
					<td class="center">
						<?php echo $row->long ; ?>
					</td>
					<td class="center">
						<?php echo $published ; ?>
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
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->filter_order_Dir;?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>