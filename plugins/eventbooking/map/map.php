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

class plgEventBookingMap extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		JFactory::getLanguage()->load('plg_eventbooking_map', JPATH_ADMINISTRATOR);
	}

	/**
	 * Display event location in a map
	 *
	 * @param $row
	 *
	 * @return array|string
	 */
	public function onEventDisplay($row)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.*')
			->from('#__eb_locations AS a')
			->innerJoin('#__eb_events AS b ON a.id = b.location_id')
			->where('b.id = ' . (int) $row->id);

		if ($fieldSuffix = EventbookingHelper::getFieldSuffix())
		{
			EventbookingHelperDatabase::getMultilingualFields($query, ['a.name', 'a.alias', 'a.description']);
		}

		$db->setQuery($query);
		$location = $db->loadObject();

		if (empty($location->address))
		{
			return '';
		}
		else
		{
			ob_start();
			$this->drawMap($location);
			$form = ob_get_clean();

			return array('title' => JText::_('PLG_EB_MAP'),
			             'form'  => $form,
			);
		}
	}

	/**
	 * Display event location in a map
	 *
	 * @param $location
	 */
	private function drawMap($location)
	{
		$config      = EventbookingHelper::getConfig();
		$zoomLevel   = $config->zoom_level ? (int) $config->zoom_level : 10;
		$disableZoom = $this->params->get('disable_zoom', 1) == 1 ? 'false' : 'true';
		$mapWidth    = $this->params->def('map_width', 700);
		$mapHeight   = $this->params->def('map_height', 500);
		$bubbleText  = "<ul class=\"bubble\">";
		$bubbleText .= "<li class=\"location_name\"><h4>";
		$bubbleText .= addslashes($location->name);
		$bubbleText .= "</h4></li>";
		$bubbleText .= "<li class=\"address\">" . addslashes($location->address) . "</li>";
		$getDirectionLink = 'https://maps.google.com/maps?f=d&daddr=' . $location->lat . ',' . $location->long . '(' . addslashes($location->address . ', ' . $location->city . ', ' . $location->state . ', ' . $location->zip . ', ' . $location->country) . ')';
		$bubbleText .= "<li class=\"address getdirection\"><a href=\"" . $getDirectionLink . "\" target=\"_blank\">" . JText::_('EB_GET_DIRECTION') . "</li>";
		$bubbleText .= "</ul>";
		$session = JFactory::getSession();
		JFactory::getDocument()->addScript('https://maps.googleapis.com/maps/api/js?key=' . $config->get('map_api_key', 'AIzaSyDIq19TVV4qOX2sDBxQofrWfjeA7pebqy4'));
		?>
		<script type="text/javascript">
			(function ($) {
				$(document).ready(function () {
					function initialize() {
						<?php if ($session->get('eb_device_type') == 'mobile') {?>
						var height = $(window).height() - 80;
						var width = $(window).width() - 80;
						<?php }else{?>
						var height = <?php echo $mapHeight;?>;
						var width = <?php echo $mapWidth;?>;
						<?php }?>
						$("#map_canvas").height(height);
						$("#map_canvas").width(width);
						var latlng = new google.maps.LatLng(<?php echo $location->lat ?>, <?php echo $location->long; ?>);
						var myOptions = {
							zoom: <?php echo $zoomLevel; ?>,
							streetViewControl: true,
							scrollwheel: <?php echo $disableZoom; ?>,
							center: latlng,
							mapTypeId: google.maps.MapTypeId.ROADMAP
						};
						var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);


						var marker = new google.maps.Marker({
							position: latlng,
							map: map,
							title: "<?php echo $location->name; ?>"
						});
						google.maps.event.trigger(map, "resize");
						var contentString = '<?php echo $bubbleText; ?>';
						var infowindow = new google.maps.InfoWindow({
							content: contentString,
							//maxWidth: 20
						});
						google.maps.event.addListener(marker, 'click', function () {
							infowindow.open(map, marker);
						});
						infowindow.open(map, marker);
					}

					initialize();
				});
			})(jQuery);
		</script>
		<div id="mapform">
			<div id="map_canvas" style="width: <?php echo $mapWidth; ?>px; height: <?php echo $mapHeight; ?>px"></div>
		</div>
		<?php
	}
}
