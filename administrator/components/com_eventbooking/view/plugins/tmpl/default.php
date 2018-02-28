<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->state->filter_order;
$listDirn	= $this->state->filter_order_Dir;
$saveOrder	= $listOrder == 'tbl.ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_eventbooking&task=plugin.save_order_ajax';
	JHtml::_('sortablelist.sortable', 'pluginList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$customOptions = array(
	'filtersHidden'       => true,
	'defaultLimit'        => JFactory::getApplication()->get('list_limit', 20),
	'searchFieldSelector' => '#filter_search',
	'orderFieldSelector'  => '#filter_full_ordering'
);
JHtml::_('searchtools.form', '#adminForm', $customOptions);
?>
<form action="index.php?option=com_eventbooking&view=plugins" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div id="j-main-container">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('EB_FILTER_SEARCH_PAYMENT_PLUGINS_DESC');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->filter_search); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('EB_SEARCH_PAYMENT_PLUGINS_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search"></span></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><span class="icon-remove"></span></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<?php
				echo $this->lists['filter_state'];
				echo $this->pagination->getLimitBox();
				?>
			</div>
		</div>
		<div class="clearfix"></div>
		<table class="adminlist table table-striped" id="pluginList">
			<thead>
			<tr>
				<th width="1%" class="nowrap center hidden-phone">
					<?php echo JHtml::_('searchtools.sort', '', 'tbl.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
				</th>
				<th width="2%" class="center">
					<?php echo JHtml::_('grid.checkall'); ?>
				</th>
				<th class="title">
					<?php echo JHtml::_('searchtools.sort',  JText::_('EB_NAME'), 'tbl.name', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
				</th>
				<th class="title" width="20%">
					<?php echo JHtml::_('searchtools.sort', JText::_('EB_TITLE'), 'tbl.title', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
				<th class="title">
					<?php echo JHtml::_('searchtools.sort', JText::_('EB_AUTHOR') , 'tbl.author', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
				<th class="title center">
					<?php echo JHtml::_('searchtools.sort', JText::_('EB_AUTHOR_EMAIL'), 'tbl.email', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
				<th class="title center">
					<?php echo JHtml::_('searchtools.sort', JText::_('EB_PUBLISHED') , 'tbl.published', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
				<th class="title center">
					<?php echo JHtml::_('searchtools.sort', JText::_('EB_ID') , 'tbl.id', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="8">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			for ($i = 0, $n = count($this->items); $i < $n; $i++)
			{
				$row = $this->items[$i];
				$link 	= JRoute::_( 'index.php?option=com_eventbooking&view=plugin&id='. $row->id );
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
							<?php echo $row->name; ?>
						</a>
					</td>
					<td>
						<?php echo $row->title; ?>
					</td>
					<td>
						<?php echo $row->author; ?>
					</td>
					<td class="center">
						<?php echo $row->author_email;?>
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
		<table class="adminform" style="margin-top: 50px;">
			<tr>
				<td>
					<fieldset class="adminform">
						<legend><?php echo JText::_('EB_INSTALL_PLUGIN'); ?></legend>
						<table>
							<tr>
								<td>
									<input type="file" name="plugin_package" id="plugin_package" size="50" class="inputbox" /> <input type="button" class="button" value="<?php echo JText::_('EB_INSTALL'); ?>" onclick="installPlugin();" />
								</td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->filter_order_Dir; ?>" />
	<input type="hidden" id="filter_full_ordering" name="filter_full_ordering" value="" />

	<script type="text/javascript">
		function installPlugin()
		{
			var form = document.adminForm ;
			if (form.plugin_package.value =="")
			{
				alert("<?php echo JText::_('EB_CHOOSE_PLUGIN'); ?>");
				return ;	
			}
			form.task.value = 'install';
			form.submit();
		}
	</script>
	<?php echo JHtml::_( 'form.token' ); ?>
</form>