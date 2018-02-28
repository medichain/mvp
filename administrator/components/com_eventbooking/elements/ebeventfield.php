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

JFormHelper::loadFieldClass('list');

class JFormFieldEbeventfield extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	protected $type = 'ebeventfield';

	protected function getOptions()
	{
		require_once JPATH_ROOT . '/components/com_eventbooking/helper/helper.php';
		$config = EventbookingHelper::getConfig();

		$options = array();

		if ($config->event_custom_field)
		{
			// Get List Of defined custom fields
			$xml    = JFactory::getXML(JPATH_ROOT . '/components/com_eventbooking/fields.xml');
			$fields = $xml->fields->fieldset->children();
			foreach ($fields as $field)
			{
				$name      = $field->attributes()->name;
				$label     = JText::_($field->attributes()->label);
				$options[] = JHtml::_('select.option', $name, $label);
			}
		}

		return $options;
	}
}
