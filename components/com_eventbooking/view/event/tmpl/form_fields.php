<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

$bootstrapHelper   = new EventbookingHelperBootstrap($this->config->twitter_bootstrap_version);
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');

foreach ($this->form->getFieldset('basic') as $field)
{
?>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo $field->label; ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php echo $field->input; ?>
		</div>
	</div>
<?php
}