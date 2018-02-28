<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2017 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;

/**
 * HTML View class for Membership Pro component
 *
 * @static
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class EventbookingViewEmailsHtml extends RADViewList
{
	/**
	 * Method to instantiate the view.
	 *
	 * @param   array $config The configuration data for the view
	 *
	 * @since  1.0
	 */
	public function __construct($config = array())
	{
		$config['hide_buttons'] = array('add', 'edit', 'publish');

		parent::__construct($config);
	}

	/**
	 * Build necessary data for the view before it is being displayed
	 *
	 */
	protected function prepareView()
	{
		parent::prepareView();

		$options   = array();
		$options[] = JHtml::_('select.option', '', JText::_('EB_EMAIL_TYPE'));
		$options[] = JHtml::_('select.option', 'new_registration_emails', JText::_('EB_NEW_REGISTRATION_EMAILS'));
		$options[] = JHtml::_('select.option', 'reminder_emails', JText::_('EB_REMINDER_EMAILS'));

		$this->lists['filter_email_type'] = JHtml::_('select.genericlist', $options, 'filter_email_type', ' onchange="submit();" ', 'value', 'text', $this->state->filter_email_type);

		$options   = array();
		$options[] = JHtml::_('select.option', 0, JText::_('EB_SENT_TO'));
		$options[] = JHtml::_('select.option', 1, JText::_('EB_ADMIN'));
		$options[] = JHtml::_('select.option', 2, JText::_('EB_REGISTRANTS'));

		$this->lists['filter_sent_to'] = JHtml::_('select.genericlist', $options, 'filter_sent_to', ' onchange="submit();" ', 'value', 'text', $this->state->filter_sent_to);
	}

	/**
	 * Method to add toolbar buttons
	 */
	protected function addToolbar()
	{
		parent::addToolbar();

		JToolbarHelper::trash('delete_all', 'EB_DELETE_ALL', false);
	}
}
