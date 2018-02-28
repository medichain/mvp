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

class JFormFieldEBLocation extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	protected $type = 'eblocation';

	protected function getOptions()
	{
		require_once JPATH_ROOT . '/components/com_eventbooking/helper/database.php';

		$options   = array();
		$options[] = JHtml::_('select.option', '0', JText::_('Select Location'));

		$locations = EventbookingHelperDatabase::getAllLocations();
		foreach ($locations as $location)
		{
			$options[] = JHtml::_('select.option', $location->id, $location->name);
		}

		return $options;
	}
}
