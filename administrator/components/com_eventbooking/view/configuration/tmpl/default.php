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

JHtml::_('bootstrap.tooltip');
$document = JFactory::getDocument();
$document->addStyleDeclaration(".hasTip{display:block !important}");
$translatable = JLanguageMultilang::isEnabled() && count($this->languages);
$editor       = JEditor::getInstance(JFactory::getConfig()->get('editor'));
$config       = $this->config;
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.tabstate');
JHtml::_('jquery.framework');
JHtml::_('script', 'jui/cms.js', false, true);
/* @var EventbookingViewConfigurationHtml $this */
?>
<div class="row-fluid">
	<form action="index.php?option=com_eventbooking&view=configuration" method="post" name="adminForm" id="adminForm"
	      class="form-horizontal eb-configuration">
		<?php
		echo JHtml::_('bootstrap.startTabSet', 'configuration', array('active' => 'general-page'));

		echo JHtml::_('bootstrap.addTab', 'configuration', 'general-page', JText::_('EB_GENERAL', true));
		echo $this->loadTemplate('general', array('config' => $config));
		echo JHtml::_('bootstrap.endTab');

		echo JHtml::_('bootstrap.addTab', 'configuration', 'theme-page', JText::_('EB_THEMES', true));
		echo $this->loadTemplate('themes', array('config' => $config));
		echo JHtml::_('bootstrap.endTab');

		echo JHtml::_('bootstrap.addTab', 'configuration', 'sef-setting-page', JText::_('EB_SEF_SETTING', true));
		echo $this->loadTemplate('sef', array('config' => $config));
		echo JHtml::_('bootstrap.endTab');

		echo JHtml::_('bootstrap.addTab', 'configuration', 'invoice-page', JText::_('EB_INVOICE_SETTINGS', true));
		echo $this->loadTemplate('invoice', array('config' => $config, 'editor' => $editor));
		echo JHtml::_('bootstrap.endTab');

		echo JHtml::_('bootstrap.addTab', 'configuration', 'tickets-page', JText::_('EB_TICKETS_SETTINGS', true));
		echo $this->loadTemplate('tickets', array('config' => $config, 'editor' => $editor));
		echo JHtml::_('bootstrap.endTab');

		echo JHtml::_('bootstrap.addTab', 'configuration', 'certificate-page', JText::_('EB_CERTIFICATE_SETTINGS', true));
		echo $this->loadTemplate('certificate', array('config' => $config, 'editor' => $editor));
		echo JHtml::_('bootstrap.endTab');

		if ($translatable)
		{
			echo $this->loadTemplate('translation', array('config' => $config, 'editor' => $editor));
		}

		echo $this->loadTemplate('custom_css');

		// Add support for custom settings layout
		if (file_exists(__DIR__ . '/default_custom_settings.php'))
		{
			echo JHtml::_('bootstrap.addTab', 'configuration', 'custom-settings-page', JText::_('EB_CUSTOM_SETTINGS', true));
			echo $this->loadTemplate('custom_settings', array('config' => $config, 'editor' => $editor));
			echo JHtml::_('bootstrap.endTab');
		}

		echo JHtml::_('bootstrap.endTabSet');
		?>
		<div class="clearfix"></div>
		<input type="hidden" name="task" value=""/>
	</form>
</div>