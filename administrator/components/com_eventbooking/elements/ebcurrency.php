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

class JFormFieldEBCurrency extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	protected $type = 'ebcurrency';

	protected function getOptions()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('currency_code', 'value'))
			->select($db->quoteName('currency_name', 'text'))
			->from('#__eb_currencies')
			->order('currency_name');
		$db->setQuery($query);
		$options   = array();
		$options[] = JHtml::_('select.option', '', JText::_('Select Currency'));

		return array_merge($options, $db->loadObjectList());
	}
}
