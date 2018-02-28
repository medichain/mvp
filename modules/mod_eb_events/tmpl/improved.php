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

JHtml::_('script', JUri::root().'media/com_eventbooking/assets/js/eventbookingjq.js', false, false);	

if ($showLocation)
{
	$width = (int) $config->map_width ;

	if (!$width)
	{
		$width = 800 ;
	}

	$height = (int) $config->map_height ;

	if (!$height)
	{
		$height = 600 ;
	}

	$deviceType = EventbookingHelper::getDeviceType();

	if ($deviceType == 'mobile')
	{
		EventbookingHelperJquery::colorbox('eb-colorbox-map', '100%', $height . 'px', 'true', 'false');
	}
	else
	{
		EventbookingHelperJquery::colorbox('eb-colorbox-map', $width . 'px', $height . 'px', 'true', 'false');
	}
}

if (count($rows))
{
	$bootstrapHelper = new EventbookingHelperBootstrap($config->twitter_bootstrap_version);

	$span3Class         = $bootstrapHelper->getClassMapping('span3');
	$span9Class         = $bootstrapHelper->getClassMapping('span9');
	$iconFolderClass    = $bootstrapHelper->getClassMapping('icon-folder-open');
	$iconMapMarkerClass = $bootstrapHelper->getClassMapping('icon-map-marker');

    $monthNames = array(
        1 => JText::_('EB_JAN_SHORT'),
        2 => JText::_('EB_FEB_SHORT'),
        3 => JText::_('EB_MARCH_SHORT'),
        4 => JText::_('EB_APR_SHORT'),
        5 => JText::_('EB_MAY_SHORT'),
        6 => JText::_('EB_JUNE_SHORT'),
        7 => JText::_('EB_JULY_SHORT'),
        8 => JText::_('EB_AUG_SHORT'),
        9 => JText::_('EB_SEP_SHORT'),
        10 => JText::_('EB_OCT_SHORT'),
        11 => JText::_('EB_NOV_SHORT'),
        12 => JText::_('EB_DEC_SHORT')
    );
?>
    <div class="eb-event-list">
        <ul class="eventsmall">
            <?php
            $k = 0 ;
            $baseUri = JUri::base(true);
            foreach ($rows as  $row)
            {
                $k = 1 - $k ;
                $date = JHtml::_('date', $row->event_date, 'd', null);
                $month = JHtml::_('date', $row->event_date, 'n', null);
            ?>
                <li class="clearfix row-fluid">
                    <div class="<?php echo $span3Class; ?>">
                        <span class="event-date">
	                        <?php
	                            if ($row->event_date == '2099-12-31 00:00:00')
	                            {
	                            	echo JText::_('EB_TBC');
	                            }
	                            else
	                            {
	                            ?>
		                            <span title="">
		                                <span class="month"><?php echo $monthNames[$month];?></span>
		                                <span class="day"><?php echo $date; ?></span>
		                            </span>
		                        <?php
	                            }
	                        ?>
                        </span>
                    </div>
                    <div class="<?php echo $span9Class; ?>">
                    	<p>
                        <a class="url eb-event-link" href="<?php echo JRoute::_(EventbookingHelperRoute::getEventRoute($row->id, 0, $itemId), false); ?>">
                            <?php
                            if ($showThumb && $row->thumb && file_exists(JPATH_ROOT.'/media/com_eventbooking/images/thumbs/'.$row->thumb))
                            {
                            ?>
                                <img src="<?php echo $baseUri . '/media/com_eventbooking/images/thumbs/' . $row->thumb; ?>" class="eb_event_thumb" /> <br/>
                            <?php
                            }
                            ?>
                            <strong class="summary"><?php echo $row->title ; ?></strong>
                        </a>
                        </p>
                        <?php
                            if ($showCategory)
                            {
                            ?>
                                <p><small title="<?php echo JText::_('EB_CATEGORY'); ?>" class="category"><span>
									<i class="<?php echo $iconFolderClass; ?>"></i>
									<?php echo $row->categories ; ?></span></small></p>
                            <?php
                            }
                            if ($showLocation && strlen($row->location_name))
                            {
                            ?>
                                <p><small title="<?php echo JText::_('EB_LOCATION'); ?>" class="location">
								<i class="<?php echo $iconMapMarkerClass; ?>"></i>
								<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=map&location_id='.$row->location_id.'&tmpl=component&format=html&Itemid='.$itemId); ?>" class="eb-colorbox-map">								
								<strong><?php echo $row->location_name ; ?></strong>
                                </a></small></p>
                            <?php
                            }
                            ?>
                    </div>
                </li>
            <?php
            }
            ?>
        </ul>
    </div>
<?php
}
else
{
?>
    <div class="eb_empty"><?php echo JText::_('EB_NO_UPCOMING_EVENTS') ?></div>
<?php
}
?>