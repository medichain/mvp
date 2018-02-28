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

class JFormFieldEBEvent extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	protected $type = 'ebevent';

	protected function getOptions()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id', 'value'))
			->select($db->quoteName('title', 'text'))
			->from('#__eb_events')
			->where('published = 1')
			->order('title');
		$db->setQuery($query);

		$options   = array();
		$options[] = JHtml::_('select.option', '0', JText::_('Select Event'));

		return array_merge($options, $db->loadObjectList());
	}
}
