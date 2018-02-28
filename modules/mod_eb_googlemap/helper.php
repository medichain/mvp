<?php
/**
 * @package        Joomla
 * @subpackage     Event Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 - 2017 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

class modEventBookingGoogleMapHelper
{
	protected $module;
	protected $Itemid;
	protected $params;
	protected $location;

	/**
	 * initialization class
	 */
	public function __construct($module, $params)
	{
		$this->module = $module;
		$this->params = $params;
		$this->Itemid = $params->get('Itemid', EventbookingHelper::getItemid());

		JFactory::getLanguage()->load('com_eventbooking', JPATH_SITE, JFactory::getLanguage()->getTag(), true);

		$this->loadMapInListing();
	}

	/**
	 * Load all locations
	 */
	public function loadAllLocations()
	{
		$user   = JFactory::getUser();
		$config = EventbookingHelper::getConfig();
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);

		$categoryIds    = $this->params->get('category_ids');
		$numberEvents   = $this->params->get('number_events', 15);
		$hidePastEvents = $this->params->get('hide_past_events', 1);
		$currentDate    = JHtml::_('date', 'Now', 'Y-m-d');

		$nullDate    = $db->quote($db->getNullDate());
		$nowDate     = $db->quote(EventbookingHelper::getServerTimeFromGMTTime());
		$fieldSuffix = EventbookingHelper::getFieldSuffix();

		$query->select('id, `lat`, `long`, address, city, state, zip, country')
			->select($db->quoteName('name' . $fieldSuffix, 'name'))
			->from('#__eb_locations')
			->where('`lat` != ""')
			->where('`long` != ""')
			->where('published = 1');
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$query->clear()
			->select('a.id, a.title, c.category_id AS catid')
			->from('#__eb_events AS a')
			->innerJoin('#__eb_event_categories AS c ON a.id = c.event_id')
			->order('a.event_date');

		foreach ($rows as $row)
		{
			$query->clear('where')
				->where('a.location_id = ' . $row->id)
				->where('a.published = 1')
				->where('a.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')')
				->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')')
				->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')')
				->where('c.main_category = 1');

			if ($categoryIds)
			{
				$query->where('c.category_id IN (' . implode(',', $categoryIds) . ')');
			}

			if ($hidePastEvents)
			{
				if ($config->show_until_end_date)
				{
					$query->where('(DATE(a.event_date) >= ' . $db->quote($currentDate) . ' OR DATE(a.event_end_date) >= ' . $db->quote($currentDate) . ')');
				}
				else
				{
					$query->where('(DATE(a.event_date) >= ' . $db->quote($currentDate) . ' OR DATE(a.cut_off_date) >= ' . $db->quote($currentDate) . ')');
				}
			}

			$db->setQuery($query, 0, $numberEvents);
			$row->events = $db->loadObjectList();

			if (!$this->location && count($row->events))
			{
				$this->location = $row;
			}
		}

		return $rows;
	}

	/**
	 * general google map for event
	 */
	protected function loadMapInListing()
	{
		$config = EventbookingHelper::getConfig();

		$locations = $this->loadAllLocations();

		if (!$this->location)
		{
			echo JText::_('EB_NO_EVENTS');

			return;
		}

		$rootUri     = JUri::root();
		$zoomLevel   = $this->params->get('zoom_level', 10);
		$disableZoom = $this->params->get('disable_zoom', 1) == 1 ? 'false' : 'true';
		JFactory::getDocument()->addScript('https://maps.googleapis.com/maps/api/js?key=' . $config->get('map_api_key', 'AIzaSyDIq19TVV4qOX2sDBxQofrWfjeA7pebqy4'));

		if (trim($this->params->get('center_coordinates')))
		{
			$homeCoordinates = trim($this->params->get('center_coordinates'));
		}
		elseif (trim($config->center_coordinates))
		{
			$homeCoordinates = $config->center_coordinates;
		}
		else
		{
			$homeCoordinates = $this->location->lat . ',' . $this->location->long;
		}
		?>
		<script type="text/javascript">
			Eb.jQuery(document).ready(function ($) {
				var markerArray = [];
				var myHome = new google.maps.LatLng(<?php echo $homeCoordinates; ?>);
				<?php
				for($i = 0; $i < count($locations); $i++)
				{
				$location = $locations[$i];
				if (!count($location->events))
				{
					continue;
				}
				?>
				var eventListing<?php echo $location->id?> = new google.maps.LatLng(<?php echo $location->lat; ?>, <?php echo $location->long; ?>);
				<?php
				}
				?>
				var mapOptions = {
					zoom: <?php echo $zoomLevel; ?>,
					streetViewControl: true,
					scrollwheel: <?php echo $disableZoom; ?>,
					mapTypeControl: true,
					panControl: true,
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					center: myHome,
				};
				var map = new google.maps.Map(document.getElementById("map<?php echo $this->module->id; ?>"), mapOptions);
				var infoWindow = new google.maps.InfoWindow();

				function makeMarker(options) {
					var pushPin = new google.maps.Marker({map: map});
					pushPin.setOptions(options);
					google.maps.event.addListener(pushPin, 'click', function () {
						infoWindow.setOptions(options);
						infoWindow.open(map, pushPin);
					});
					markerArray.push(pushPin);
					return pushPin;
				}

				google.maps.event.addListener(map, 'click', function () {
					infoWindow.close();
				});
				<?php
				foreach($locations as $location)
				{
				$events = $location->events;
				if (!count($events))
				{
					continue;
				}
				?>
				makeMarker({
					position: eventListing<?php echo $location->id?>,
					title: "<?php echo addslashes($location->title);?>",
					content: '<div class="row-fluid"><ul><?php foreach ($events as $event)
					{
						echo '<li><h4>' . JHtml::link(EventbookingHelperRoute::getEventRoute($event->id, $event->catid, $this->Itemid), addslashes($event->title)) . '</h4></li>';
					}?></ul></div>',
					icon: new google.maps.MarkerImage('<?php echo $rootUri; ?>modules/mod_eb_googlemap/asset/marker/marker.png')
				});
				<?php
				}
				?>
			});
		</script>
		<?php
	}
}
