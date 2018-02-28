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
?>
<h2><?php echo JText::sprintf('EB_VENUE_INFORMATION', $location->name); ?></h2>

<?php
if ($location->image && file_exists(JPATH_ROOT . '/' . $location->image))
{
?>
	<img src="<?php echo JUri::root(true) . '/' . $location->image; ?>" class="eb-venue-image img-polaroid"/>
<?php
}

if (EventbookingHelper::isValidMessage($location->description))
{
?>
	<div class="eb-location-description"><?php echo $location->description; ?></div>
<?php
}

