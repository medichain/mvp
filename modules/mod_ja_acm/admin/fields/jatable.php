<?php
/**
 * ------------------------------------------------------------------------
 * JA ACM Module
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

// Ensure this file is being included by a parent file
defined('_JEXEC') or die( 'Restricted access' );

require_once 'jafield.php';

/**
 * List of checkbox base on other fields
 *
 * @since      Class available since Release 1.2.0
 */
class JFormFieldJATable extends JAFormField
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $type = 'jatable';

	function getLabel() {
		return null;
	}
	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	function getInput()
	{
		$html = $this->renderLayout ('jatable', array('field' => $this, 'items' => $this->getItems()));
		return $html;
	}

} 