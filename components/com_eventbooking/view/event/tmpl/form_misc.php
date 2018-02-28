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
?>
<table class="admintable">
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_ACCESS' );?>::<?php echo JText::_('EB_ACCESS_EXPLAIN'); ?>"><?php echo JText::_('EB_ACCESS'); ?></span>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php echo $this->lists['access']; ?>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_REGISTRATION_ACCESS' );?>::<?php echo JText::_('EB_REGISTRATION_ACCESS_EXPLAIN'); ?>"><?php echo JText::_('EB_REGISTRATION_ACCESS'); ?></span>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php echo $this->lists['registration_access']; ?>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo JText::_('EB_PAYPAL_EMAIL'); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" name="paypal_email" class="inputbox" size="50" value="<?php echo $this->item->paypal_email ; ?>" />
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo JText::_('EB_NOTIFICATION_EMAILS'); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" name="notification_emails" class="inputbox" size="70" value="<?php echo $this->item->notification_emails ; ?>" />
		</div>
	</div>
	<?php
	if ($this->config->activate_deposit_feature)
	{
	?>
		<div class="<?php echo $controlGroupClass; ?>">
			<div class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_DEPOSIT_AMOUNT'); ?>
			</div>
			<div class="<?php echo $controlsClass; ?>">
				<input type="text" name="deposit_amount" id="deposit_amount" class="input-mini" size="5" value="<?php echo $this->item->deposit_amount; ?>"/>&nbsp;&nbsp;<?php echo $this->lists['deposit_type']; ?>
			</div>
		</div>
	<?php
	}
	?>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo JText::_('EB_ENABLE_CANCEL'); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php
				if (isset($this->lists['enable_cancel_registration']))
				{
					echo $this->lists['enable_cancel_registration'];
				}
				else
				{
					echo EventbookingHelperHtml::getBooleanInput('enable_cancel_registration', $this->item->enable_cancel_registration);
				}
			?>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo JText::_('EB_CANCEL_BEFORE_DATE'); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php echo JHtml::_('calendar', $this->item->cancel_before_date != $this->nullDate ? JHtml::_('date', $this->item->cancel_before_date, 'Y-m-d', null) : '', 'cancel_before_date', 'cancel_before_date'); ?>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo JText::_('EB_AUTO_REMINDER'); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php
				if (isset($this->lists['enable_auto_reminder']))
				{
					echo $this->lists['enable_auto_reminder'];
				}
				else
				{
					echo EventbookingHelperHtml::getBooleanInput('enable_auto_reminder', $this->item->enable_auto_reminder);
				}
			?>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo JText::_('EB_REMIND_BEFORE'); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" name="remind_before_x_days" class="input-mini" size="5" value="<?php echo $this->item->remind_before_x_days; ?>" /> days
		</div>
	</div>
	<?php
	if ($this->config->term_condition_by_event)
	{
	?>
		<div class="<?php echo $controlGroupClass; ?>">
			<div class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_TERMS_CONDITIONS'); ?>
			</div>
			<div class="<?php echo $controlsClass; ?>">
				<?php echo $this->lists['article_id'] ; ?>
			</div>
		</div>
	<?php
	}
	?>

	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo  JText::_('EB_META_KEYWORDS'); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<textarea rows="5" cols="30" class="input-lage" name="meta_keywords"><?php echo $this->item->meta_keywords; ?></textarea>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo  JText::_('EB_META_DESCRIPTION'); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<textarea rows="5" cols="30" class="input-lage" name="meta_description"><?php echo $this->item->meta_description; ?></textarea>
		</div>
	</div>
</table>