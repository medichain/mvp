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

$document = JFactory::getDocument();
$rootUrl  = JUri::root(true);
$document->addStyleSheet($rootUrl . '/modules/mod_eb_googlemap/asset/style.css');
$document->addStyleSheet($rootUrl . '/media/com_eventbooking/assets/css/style.css');
require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/bootstrap.php';
require_once dirname(__FILE__) . '/helper.php';

$config = EventbookingHelper::getConfig();

if ($config->debug)
{
	error_reporting(E_ALL);
}
else
{
	error_reporting(0);
}


if ($config->load_bootstrap_css_in_frontend !== '0')
{
	$document->addStyleSheet($rootUrl . '/media/com_eventbooking/assets/bootstrap/css/bootstrap.css');
}

JHtml::_('jquery.framework');
JHtml::_('script', 'media/com_eventbooking/assets/js/eventbookingjq.js', false, false);

// params
$width  = $params->get('width', 100);
$height = $params->get('height', 400);
$ebMap  = new modEventBookingGoogleMapHelper($module, $params);

require JModuleHelper::getLayoutPath('mod_eb_googlemap');
