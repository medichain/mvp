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

JHtml::_('behavior.keepalive');
if(version_compare(JVERSION, '3.0', 'lt')){
	JHtml::_('behavior.tooltip');
}
JHtml::_('behavior.formvalidation');
?>
<div class="remind <?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	</div>
	<?php endif; ?>

	<form id="user-registration" class="form-inline" action="<?php echo JRoute::_('index.php?option=com_users&task=remind.remind'); ?>" method="post" class="form-validate form-horizontal">

		<?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
		<div class="alert alert-warning"><?php echo JText::_($fieldset->label); ?></div>

		
			<?php foreach ($this->form->getFieldset($fieldset->name) as $name => $field) : ?>
				<div class="form-group <?php if(!$field->input) : ?>hidden<?php endif ;?>">
					<div class="col-sm-4 control-label">
					<?php echo $field->label; ?>
				</div>
					<div class="col-sm-8">
					<?php echo $field->input; ?>
				</div>
			</div>
			<?php endforeach; ?>
		
		<?php endforeach; ?>
		<button type="submit" class="btn btn-primary validate"><?php echo JText::_('JSUBMIT'); ?></button>
				<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
