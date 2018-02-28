<?php
/**
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2017 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */

defined( '_JEXEC' ) or die ;

$getDirectionLink = 'http://maps.google.com/maps?f=d&daddr='.$this->location->lat.','.$this->location->long.'('.addslashes($this->location->address.', '.$this->location->city.', '.$this->location->state.', '.$this->location->zip.', '.$this->location->country).')' ;
EventbookingHelperJquery::colorbox('eb-modal');
?>
<h1 class="eb-page-heading"><?php echo JText::sprintf('EB_EVENTS_FROM_LOCATION', $this->location->name); ?><a href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=map&location_id='.$this->location->id.'&tmpl=component&format=html'); ?>"  title="<?php echo $this->location->name ; ?>" class="eb-colorbox-map view_map_link"><?php echo JText::_('EB_VIEW_MAP'); ?></a><a class="view_map_link" href="<?php echo $getDirectionLink ; ?>" target="_blank"><?php echo JText::_('EB_GET_DIRECTION'); ?></a></h1>
<form method="post" name="adminForm" id="adminForm" action="<?php echo JRoute::_('index.php?option=com_eventbooking&view=location&location_id='.$this->location->id.'&Itemid='.$this->Itemid) ; ?>">
	<?php
	if (count($this->items))
	{
		echo EventbookingHelperHtml::loadCommonLayout('common/tmpl/events_table.php', array('items' => $this->items, 'config' => $this->config, 'Itemid' => $this->Itemid, 'nullDate' => $this->nullDate, 'ssl' => (int) $this->config->use_https, 'viewLevels' => $this->viewLevels, 'categoryId' => 0, 'bootstrapHelper' => $this->bootstrapHelper));
	}
	else 
	{
	?>
		<p class="text-info"><?php echo JText::_('EB_NO_EVENTS_FOUND') ?></p>
	<?php	
	}

	if ($this->pagination->total > $this->pagination->limit)
	{
	?>
		<div class="pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php
	}
	?>
</form>