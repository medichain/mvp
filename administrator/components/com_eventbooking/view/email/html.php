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
 * HTML View class for Events Booking component
 *
 * @static
 * @package        Joomla
 * @subpackage     Events Booking
 */
class EventbookingViewEmailHtml extends RADViewItem
{
	/**
	 * Method to instantiate the view.
	 *
	 * @param array $config A named configuration array for object construction
	 */
	public function __construct($config = array())
	{
		$config['hide_buttons'] = array('save', 'save2new', 'save2copy');

		parent::__construct($config);
	}
}
