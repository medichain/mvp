<?php
/**
 * ------------------------------------------------------------------------
 * JA Healthcare Template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
*/

defined('_JEXEC') or die;

if (is_array($this->getParam('skip_component_content')) && 
    in_array(JFactory::getApplication()->input->getInt('Itemid'), $this->getParam('skip_component_content'))) 
  return;
?>

<?php
	$this->loadBlock('mainbody/no-sidebar');
?>