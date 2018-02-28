<?php
/**
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2017 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;
$getDirectionLink = 'http://maps.google.com/maps?f=d&daddr=' . $this->location->lat . ',' . $this->location->long . '(' . addslashes($this->location->address . ', ' . $this->location->city . ', ' . $this->location->state . ', ' . $this->location->zip . ', ' . $this->location->country) . ')';
$height           = (int) $this->config->map_height;

if (!$height)
{
	$height = 600;
}

$height += 20;
$zoomLevel = (int) $this->config->zoom_level;

if (!$zoomLevel)
{
	$zoomLevel = 8;
}

$config = EventbookingHelper::getConfig();
$doc    = JFactory::getDocument();
$doc->addScript('https://maps.google.com/maps/api/js?key=' . $config->get('map_api_key', 'AIzaSyDIq19TVV4qOX2sDBxQofrWfjeA7pebqy4'));
$doc->addScriptDeclaration('
	var geocoder, map;
	function initialize() {
		var latlng = new google.maps.LatLng("'.$this->location->lat.'", "'.$this->location->long.'");
		var options = {
			zoom: '.$zoomLevel.',
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		map = new google.maps.Map(document.getElementById("inline_map"), options);

		var marker = new google.maps.Marker({
			map: map,
			position: latlng,
		});
		google.maps.event.trigger(map, "resize");
		var windowContent = "<h4>'.addslashes($this->location->name).'</h4>" +
			"<ul>" +
				"<li>'.$this->location->address . "  " . $this->location->city. "  " . $this->location->state."  " . $this->location->zip."  " . $this->location->country.'</li>" +
				"<li class=\'address getdirection\'><a href=\"'.$getDirectionLink.'\" target=\"_blank\">'.JText::_('EB_GET_DIRECTION').'</li>" +
			"</ul>";

		var infowindow = new google.maps.InfoWindow({
			content: windowContent,
			maxWidth: 250
		});

		google.maps.event.addListener(marker, "click", function() {
			infowindow.open(map,marker);
		});
		 infowindow.open(map,marker);
	}
	jQuery(document).ready(function () {
			initialize();
	});
');

if ($this->location->image || EventbookingHelper::isValidMessage($this->location->description))
{
	$onPopup = false;
}
else
{
	$onPopup = true;
}
?>
<div id="eb-event-map-page" class="eb-container row-fluid">
	<?php
	if (!$onPopup)
	{
	?>
		<h1 class="eb-page-heading"><?php echo $this->location->name; ?></h1>
	<?php
	}

	if ($this->location->image && file_exists(JPATH_ROOT . '/' . $this->location->image))
	{
	?>
		<img src="<?php echo JUri::root(true) . '/' . $this->location->image; ?>" class="eb-venue-image img-polaroid" />
	<?php
	}

	if (EventbookingHelper::isValidMessage($this->location->description))
	{
	?>
		<div class="eb-location-description"><?php echo $this->location->description; ?></div>
	<?php
	}
	?>
	<div id="inline_map" style="height:<?php echo $height; ?>px; width:100%;"></div>
</div>
