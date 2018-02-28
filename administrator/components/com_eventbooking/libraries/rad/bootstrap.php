<?php
/**
 * Register the prefix so that the classes in RAD library can be auto-load
 */
defined('_JEXEC') or die;

define('EB_TBC_DATE', '2099-12-31 00:00:00');

JLoader::registerPrefix('RAD', dirname(__FILE__));
$app = JFactory::getApplication();
JLoader::registerPrefix('Eventbooking', JPATH_BASE . '/components/com_eventbooking');

if ($app->isAdmin())
{
	JLoader::register('EventbookingHelper', JPATH_ROOT . '/components/com_eventbooking/helper/helper.php');
	JLoader::register('EventbookingHelperIcs', JPATH_ROOT . '/components/com_eventbooking/helper/ics.php');
	JLoader::register('EventbookingHelperHtml', JPATH_ROOT . '/components/com_eventbooking/helper/html.php');
	JLoader::register('EventbookingHelperCart', JPATH_ROOT . '/components/com_eventbooking/helper/cart.php');
	JLoader::register('EventbookingHelperRoute', JPATH_ROOT . '/components/com_eventbooking/helper/route.php');
	JLoader::register('EventbookingHelperJquery', JPATH_ROOT . '/components/com_eventbooking/helper/jquery.php');
	JLoader::register('EventbookingHelperData', JPATH_ROOT . '/components/com_eventbooking/helper/data.php');
	JLoader::register('EventbookingHelperDatabase', JPATH_ROOT . '/components/com_eventbooking/helper/database.php');
	JLoader::register('EventbookingHelperMail', JPATH_ROOT . '/components/com_eventbooking/helper/mail.php');
	JLoader::register('EventbookingHelperTicket', JPATH_ROOT . '/components/com_eventbooking/helper/ticket.php');
	JLoader::register('EventbookingHelperAcl', JPATH_ROOT . '/components/com_eventbooking/helper/acl.php');
	JLoader::register('EventbookingHelperRegistration', JPATH_ROOT . '/components/com_eventbooking/helper/registration.php');

	// Register override classes
	$possibleOverrides = array(
		'EventbookingHelperOverrideHelper'       => 'helper.php',
		'EventbookingHelperOverrideMail'         => 'mail.php',
		'EventbookingHelperOverrideJquery'       => 'jquery.php',
		'EventbookingHelperOverrideData'         => 'data.php',
		'EventbookingHelperOverrideRegistration' => 'registration.php',
	);

	foreach ($possibleOverrides as $className => $filename)
	{
		JLoader::register($className, JPATH_ROOT . '/components/com_eventbooking/helper/override/' . $filename);
	}
}
else
{
	JLoader::register('EventbookingModelEvents', JPATH_ADMINISTRATOR . '/components/com_eventbooking/model/events.php');
	JLoader::register('EventbookingModelMassmail', JPATH_ADMINISTRATOR . '/components/com_eventbooking/model/massmail.php');
}

JLoader::register('os_payments', JPATH_ROOT . '/components/com_eventbooking/payments/os_payments.php');
JLoader::register('os_payment', JPATH_ROOT . '/components/com_eventbooking/payments/os_payment.php');
JLoader::register('JFile', JPATH_LIBRARIES . '/joomla/filesystem/file.php');

$config = EventbookingHelper::getConfig();

if ($config->debug)
{
	error_reporting(E_ALL);
	ini_set('display_errors', 'On');
}
else
{
	error_reporting(0);
}
