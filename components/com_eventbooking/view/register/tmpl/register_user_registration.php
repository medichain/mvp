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

/**
 * Layout variables
 * -----------------
 * @var   string $username
 * @var   string $controlGroupClass
 * @var   string $controlLabelClass
 * @var   string $controlsClass
 */

$params = JComponentHelper::getParams('com_users');
$minimumLength = $params->get('minimum_length', 4);
($minimumLength) ? $minSize = ",minSize[$minimumLength]" : $minSize = "";
?>
<div class="<?php echo $controlGroupClass;  ?>">
	<label class="<?php echo $controlLabelClass; ?>" for="username1">
		<?php echo  JText::_('EB_USERNAME') ?><span class="required">*</span>
	</label>
	<div class="<?php echo $controlsClass; ?>">
		<input type="text" name="username" id="username1" class="input-large validate[required,minSize[2],ajax[ajaxUserCall]]" value="<?php echo $this->escape($this->input->getUsername('username')); ?>" />
	</div>
</div>
<div class="<?php echo $controlGroupClass;  ?>">
	<label class="<?php echo $controlLabelClass; ?>" for="password1">
		<?php echo  JText::_('EB_PASSWORD') ?><span class="required">*</span>
	</label>
	<div class="<?php echo $controlsClass; ?>">
		<input type="password" name="password1" id="password1" class="input-large validate[required<?php echo $minSize;?>]" value=""/>
	</div>
</div>
<div class="<?php echo $controlGroupClass;  ?>">
	<label class="<?php echo $controlLabelClass; ?>" for="password2">
		<?php echo  JText::_('EB_RETYPE_PASSWORD') ?><span class="required">*</span>
	</label>
	<div class="<?php echo $controlsClass; ?>">
		<input type="password" name="password2" id="password2" class="input-large validate[required,equals[password1]]" value="" />
	</div>
</div>