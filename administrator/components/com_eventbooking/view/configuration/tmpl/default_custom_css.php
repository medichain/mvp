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

$editorPlugin = null;

if (JPluginHelper::isEnabled('editors', 'codemirror'))
{
	$editorPlugin = 'codemirror';
}
elseif (JPluginHelper::isEnabled('editor', 'none'))
{
	$editorPlugin = 'none';
}

if ($editorPlugin)
{
	echo JHtml::_('bootstrap.addTab', 'configuration', 'custom-css', JText::_('EB_CUSTOM_CSS', true));

	$customCss = '';

	if (file_exists(JPATH_ROOT . '/media/com_eventbooking/assets/css/custom.css'))
	{
		$customCss = file_get_contents(JPATH_ROOT . '/media/com_eventbooking/assets/css/custom.css');
	}

	echo JEditor::getInstance($editorPlugin)->display('custom_css', $customCss, '100%', '550', '75', '8', false, null, null, null, array('syntax' => 'css'));

	echo JHtml::_('bootstrap.endTab');
}