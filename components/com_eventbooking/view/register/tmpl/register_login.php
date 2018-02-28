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
 * @var   string $controlGroupClass
 * @var   string $controlLabelClass
 * @var   string $controlsClass
 */
?>
<h3 class="eb-heading"><?php echo JText::_('EB_EXISTING_USER_LOGIN'); ?></h3>
<form method="post" action="<?php echo JRoute::_('index.php?option=com_users&task=user.login'); ?>" name="eb-login-form" id="eb-login-form" autocomplete="off" class="form form-horizontal">
	<div class="<?php echo $controlGroupClass;  ?>">
		<label class="<?php echo $controlLabelClass; ?>" for="username">
			<?php echo  JText::_('EB_USERNAME') ?><span class="required">*</span>
		</label>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" name="username" id="username" class="input-large validate[required]" value=""/>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass;  ?>">
		<label class="<?php echo $controlLabelClass; ?>" for="password">
			<?php echo  JText::_('EB_PASSWORD') ?><span class="required">*</span>
		</label>
		<div class="<?php echo $controlsClass; ?>">
			<input type="password" id="password" name="password" class="input-large validate[required]" value="" />
		</div>
	</div>
	<div class="<?php echo $controlGroupClass;  ?>">
		<div class="<?php echo $controlsClass; ?>">
			<input type="submit" value="<?php echo JText::_('EB_LOGIN'); ?>" class="button btn btn-primary" />
		</div>
	</div>
	<?php
	if (JPluginHelper::isEnabled('system', 'remember'))
	{
	?>
		<input type="hidden" name="remember" value="1" />
	<?php
	}
	?>
	<input type="hidden" name="return" id="return_url" value="<?php echo base64_encode(JUri::getInstance()->toString()); ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
<h3 class="eb-heading"><?php echo JText::_('EB_NEW_USER_REGISTER'); ?></h3>
