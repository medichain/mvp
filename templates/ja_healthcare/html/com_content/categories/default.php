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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
JHtml::_('behavior.caption');
?>
<div class="categories-list<?php echo $this->pageclass_sfx; ?>">
<?php
echo JLayoutHelper::render('joomla.content.categories_default', $this);
echo $this->loadTemplate('items');
?>
</div>
