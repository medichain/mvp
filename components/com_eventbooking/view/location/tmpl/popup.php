<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;
JHtml::_('bootstrap.framework');
JHtml::_('behavior.framework') ;
EventbookingHelperJquery::validateForm();
$bootstrapHelper   = $this->bootstrapHelper;
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');
$span7Class          = $bootstrapHelper->getClassMapping('span7');
$span5Class          = $bootstrapHelper->getClassMapping('span5');

$config = EventbookingHelper::getConfig();
$mapApiKye = $config->get('map_api_key', 'AIzaSyDIq19TVV4qOX2sDBxQofrWfjeA7pebqy4');

if ($config->center_coordinates)
{
	$coordinates = $config->center_coordinates;
}
else
{
	$http     = JHttpFactory::getHttp();
	$url      = "https://maps.googleapis.com/maps/api/geocode/json?address=" . str_replace(' ', '+', $config->default_country) . "&key=" . $mapApiKye;
	$response = $http->get($url);

	if ($response->code == 200)
	{
		$output_deals = json_decode($response->body);
		$latLng       = $output_deals->results[0]->geometry->location;
		$coordinates  = $latLng->lat . ',' . $latLng->lng;
	}
	else
	{
		$coordinates = '37.09024,-95.712891';
	}
}
?>
<script src="https://maps.google.com/maps/api/js?key=<?php echo $mapApiKye; ?>" type="text/javascript"></script>
<script type="text/javascript">
	var map;
	var geocoder;
	var marker;
	function initialize() {
		geocoder = new google.maps.Geocoder();
		var mapDiv = document.getElementById('map-canvas');
		// Create the map object
		map = new google.maps.Map(mapDiv, {
				center: new google.maps.LatLng(<?php echo $coordinates; ?>),
				zoom: 10,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				streetViewControl: false
		});
		// Create the default marker icon
		marker = new google.maps.Marker({
			map: map,
			position: new google.maps.LatLng(<?php echo $coordinates; ?>),
			draggable: true
		});
		// Add event to the marker
		google.maps.event.addListener(marker, 'drag', function() {
			geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					if (results[0]) {
						document.getElementById('address').value = results[0].formatted_address;
						document.getElementById('coordinates').value = marker.getPosition().toUrlValue();
					}
				}
			});
		});
	}
	function getLocationFromAddress() {
		var address = document.getElementById('address').value;
		geocoder.geocode( { 'address': address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				map.setCenter(results[0].geometry.location);
				marker.setPosition(results[0].geometry.location);
				$('coordinates').value = results[0].geometry.location.lat().toFixed(7) + ',' + results[0].geometry.location.lng().toFixed(7);
			} else {
				alert('We\'re sorry but your location was not found.');
			}
		});
	}
	// Initialize google map
	google.maps.event.addDomListener(window, 'load', initialize);
	// Search for addresses
	function getLocations(term) {
		var content = $('eventmaps_results');
		address = $('address').getSize();
		$('eventmaps_results').setStyle('width', address.x - 21);
		$('eventmaps_results').style.display = 'none';
		$$('#eventmaps_results li').each(function(el) {
			el.dispose();
		});
		if (term != '') {
			geocoder.geocode( {'address': term }, function(results, status) {
				if (status == 'OK') {
					results.each(function(item) {
						theli = new Element('li');
						thea = new Element('a', {
							href: 'javascript:void(0)',
							'text': item.formatted_address
						});
						thea.addEvent('click', function() {
							$('address').value = item.formatted_address;
							$('coordinates').value = item.geometry.location.lat().toFixed(7) + ',' + item.geometry.location.lng().toFixed(7);
							var location = new google.maps.LatLng(item.geometry.location.lat().toFixed(7), item.geometry.location.lng().toFixed(7));
							marker.setPosition(location);
							map.setCenter(location);
							$('eventmaps_results').style.display = 'none';
						});
						thea.inject(theli);
						theli.inject(content);
					});
					$('eventmaps_results').style.display = '';
				}
			});
		}
	}
	function clearLocations() {
		setTimeout( function () {
			$('eventmaps_results').style.display = 'none';
		},1000);
	}
</script>
<form action="index.php?option=com_eventbooking&view=location" method="post" name="adminForm" id="adminForm" class="form-horizontal">
<h1 class="eb-page-heading"><?php echo JText::_('EB_ADD_EDIT_LOCATION'); ?></h1>
<div class="row-fluid">
    <div  class="<?php echo $span5Class ?>">
    	<div class="<?php echo $controlGroupClass;  ?>">
    		<label class="<?php echo $controlLabelClass; ?>">
    			<?php echo JText::_('EB_NAME'); ?>
    			<span class="required">*</span>
    		</label>
    		<div class="<?php echo $controlsClass; ?>">
    			<input class="input-large validate[required]" type="text" name="name" id="name" size="50" maxlength="250" value="" />
    		</div>
    	</div>
    
    	<div class="<?php echo $controlGroupClass;  ?>">
    		<label class="<?php echo $controlLabelClass; ?>">
    			<?php echo JText::_('EB_ADDRESS'); ?>
    			<span class="required">*</span>
    		</label>
    		<div class="<?php echo $controlsClass; ?>">
      	         <input class="input-large validate[required]" type="text" name="address" id="address" size="70" autocomplete="off" onkeyup="getLocations(this.value)" onblur="clearLocations();" maxlength="250" value="<?php echo $this->item->address;?>" />
    			<ul id="eventmaps_results" style="display:none;"></ul>
    		</div>
    	</div>
	    <?php
	    if (JModuleHelper::isEnabled('mod_eb_cities'))
	    {
		?>
		    <div class="control-group">
			    <label class="control-label">
				    <?php echo JText::_('EB_CITY'); ?>
			    </label>
			    <div class="controls">
				    <input class="text_area" type="text" name="city" id="city" size="30" maxlength="250" value="<?php echo $this->item->city;?>" />
			    </div>
		    </div>
		<?php
	    }

	    if (JModuleHelper::isEnabled('mod_eb_states'))
	    {
		?>
		    <div class="control-group">
			    <label class="control-label">
				    <?php echo JText::_('EB_STATE'); ?>
			    </label>
			    <div class="controls">
				    <input class="text_area" type="text" name="state" id="state" size="30" maxlength="250" value="<?php echo $this->item->state;?>" />
			    </div>
		    </div>
		<?php
	    }
	    ?>
    	<div class="<?php echo $controlGroupClass;  ?>">
    		<label class="<?php echo $controlLabelClass; ?>">
    			<?php echo JText::_('EB_COORDINATES'); ?>
    		</label>
    		<div class="<?php echo $controlsClass; ?>">
    			<input class="input-large validate[required]" type="text" name="coordinates" id="coordinates" size="30" maxlength="250" value="" />
    		</div>
    	</div>
	    <div class="<?php echo $controlGroupClass;  ?>">
		    <label class="<?php echo $controlLabelClass; ?>">
			    <?php echo JText::_('EB_PUBLISHED') ; ?>
		    </label>
		    <?php echo $this->lists['published']; ?>
	    </div>
     </div>
     <div class="<?php echo $span7Class ?>">
        <div class="<?php echo $controlGroupClass;  ?>">
    		<div id="map-canvas" style="width: 95%; height: 350px"></div><br>
    	</div>
     </div>
     <div class="row-fluid">
     	<button id="save_location" class="btn btn-primary" type="submit"><span class="icon-save"></span><?php echo JText::_('EB_SAVE'); ?></button>
   		<input type="button" class="btn btn-info" onclick="getLocationFromAddress();" value="<?php echo JText::_('EB_PINPOINT'); ?> &raquo;" />
   	</div>
</div>
	<div class="clearfix"></div>
	<input type="hidden" name="published" value="1" />
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$("#adminForm").validationEngine('attach', {
				onValidationComplete: function(form, status){
					if (status == true) {
						$.ajax({
							type:'POST',
							data: $('#adminForm input[type=\'radio\']:checked, #adminForm input[type=\'checkbox\']:checked, #adminForm input[type=\'text\'], #adminForm input[type=\'hidden\'],  #adminForm select'),
							dataType: 'json',
							url: siteUrl + 'index.php?option=com_eventbooking&task=location.save_ajax',
							beforeSend: function () {
								$('#save_location').prop('disabled',true);
							},
							success : function(json){
								$( "#adminForm" ).before( '<div class="alert alert-success"><h4 class="alert-heading">Message</h4><div class="alert-message">'+json['message']+'</div></div>' );
								$('#save_location').prop('disabled',false);
								var parentJQuery = parent.jQuery;
								parentJQuery('#location_id').append(parentJQuery('<option>', {
									value: json['id'],
									text: json['name']
								}));
								parentJQuery('#location_id').val(json['id']);
								parentJQuery.colorbox.close();
							}
						})
						return false;
					}
					return false;
				}
			});
		})
	})(jQuery)
</script>