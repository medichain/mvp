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

class EventbookingViewFieldRaw extends RADViewHtml
{
	public function display()
	{
		$this->setLayout('options');
		$db      = JFactory::getDbo();
		$query   = $db->getQuery(true);
		$fieldId = JFactory::getApplication()->input->getInt('field_id');
		$query->select('`values`')
			->from('#__eb_fields')
			->where('id=' . $fieldId);
		$db->setQuery($query);
		$options       = explode("\r\n", $db->loadResult());
		$this->options = $options;

		parent::display();
	}
}
