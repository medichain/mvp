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
 * @var   EventbookingViewRegisterHtml $this
 * @var   string                       $controlGroupClass
 * @var   string                       $controlLabelClass
 * @var   string                       $controlsClass
 */

if (count($this->methods) > 1)
{
?>
	<div class="<?php echo $controlGroupClass;  ?> payment_information" id="payment_method_container">
		<label class="<?php echo $controlLabelClass; ?>" for="payment_method">
			<?php echo JText::_('EB_PAYMENT_OPTION'); ?>
			<span class="required">*</span>
		</label>
		<div class="<?php echo $controlsClass; ?>">
			<?php
			$method = null;
			for ($i = 0, $n = count($this->methods); $i < $n; $i++)
			{
				$paymentMethod = $this->methods[$i];

				if ($paymentMethod->getName() == $this->paymentMethod)
				{
					$checked = ' checked="checked" ';
					$method  = $paymentMethod;
				}
				else
				{
					$checked = '';
				}
				?>
					<label class="radio">
						<input onclick="changePaymentMethod('<?php echo $registrationType; ?>');" class="validate[required] radio"
							   type="radio" name="payment_method"
							   value="<?php echo $paymentMethod->getName(); ?>" <?php echo $checked; ?> />
						<?php
						if ($paymentMethod->iconUri)
						{
						?>
							<img class="eb-payment-method-icon clearfix" src="<?php echo $paymentMethod->iconUri; ?>"
							     title="<?php echo JText::_($paymentMethod->getTitle()); ?>" />
						<?php
						}
						else
						{
							echo JText::_($paymentMethod->getTitle());
						}
						?>
					</label>
				<?php
			}
			?>
		</div>
	</div>
	<?php
}
else
{
	$method = $this->methods[0];
?>
	<div class="<?php echo $controlGroupClass;  ?> payment_information" id="payment_method_container">
		<label class="<?php echo $controlLabelClass; ?>">
			<?php echo JText::_('EB_PAYMENT_OPTION'); ?>
		</label>

		<div class="<?php echo $controlsClass; ?>">
			<?php
			if ($method->iconUri)
			{
			?>
				<img class="eb-payment-method-icon clearfix" src="<?php echo $method->iconUri; ?>"
				     title="<?php echo JText::_($method->getTitle()); ?>"/>
			<?php
			}
			else
			{
				echo JText::_($method->getTitle());
			}
			?>
		</div>
	</div>
<?php
}

if ($method->getName() == 'os_squareup')
{
	$style = '';
}
else
{
	$style = 'style = "display:none"';
}
?>
<div class="<?php echo $controlGroupClass;  ?> payment_information" id="sq_field_zipcode" <?php echo $style; ?>>
	<label class="<?php echo $controlLabelClass; ?>" for="sq_billing_zipcode">
		<?php echo JText::_('EB_SQUAREUP_ZIPCODE'); ?><span class="required">*</span>
	</label>

	<div class="<?php echo $controlsClass; ?>" id="field_zip_input">
		<input type="text" id="sq_billing_zipcode" name="sq_billing_zipcode"
		       class="input-large"
		       value="<?php echo $this->escape($this->input->getString('sq_billing_zipcode')); ?>" />
	</div>
</div>
<?php
if ($method->getCreditCard())
{
	$style = '';
}
else
{
	$style = 'style = "display:none"';
}
?>
<div class="<?php echo $controlGroupClass;  ?> payment_information" id="tr_card_number" <?php echo $style; ?>>
	<label class="<?php echo $controlLabelClass; ?>" for="x_card_num">
		<?php echo JText::_('AUTH_CARD_NUMBER'); ?><span class="required">*</span>
	</label>

	<div class="<?php echo $controlsClass; ?>" id="sq-card-number">
		<input type="text" id="x_card_num" name="x_card_num"
			   class="input-large validate[required,creditCard]"
			   value="<?php echo $this->escape($this->input->getAlnum('x_card_num')); ?>" onchange="removeSpace(this);"/>
	</div>
</div>
<div class="<?php echo $controlGroupClass;  ?> payment_information" id="tr_exp_date" <?php echo $style; ?>>
	<label class="<?php echo $controlLabelClass; ?>">
		<?php echo JText::_('AUTH_CARD_EXPIRY_DATE'); ?><span class="required">*</span>
	</label>

	<div class="<?php echo $controlsClass; ?>" id="sq-expiration-date">
		<?php echo $this->lists['exp_month'] . '  /  ' . $this->lists['exp_year']; ?>
	</div>
</div>
<div class="<?php echo $controlGroupClass;  ?> payment_information" id="tr_cvv_code" <?php echo $style; ?>>
	<label class="<?php echo $controlLabelClass; ?>" for="x_card_code">
		<?php echo JText::_('AUTH_CVV_CODE'); ?><span class="required">*</span>
	</label>

	<div class="<?php echo $controlsClass; ?>" id="sq-cvv">
		<input type="text" id="x_card_code" name="x_card_code"
			   class="input-large validate[required,custom[number]]"
			   value="<?php echo $this->escape($this->input->getString('x_card_code')); ?>"/>
	</div>
</div>
<?php
if ($method->getCardType())
{
	$style = '';
}
else
{
	$style = ' style = "display:none;" ';
}
?>
<div class="<?php echo $controlGroupClass;  ?> payment_information" id="tr_card_type" <?php echo $style; ?>>
	<label class="<?php echo $controlLabelClass; ?>" for="card_type">
		<?php echo JText::_('EB_CARD_TYPE'); ?><span class="required">*</span>
	</label>

	<div class="<?php echo $controlsClass; ?>">
		<?php echo $this->lists['card_type']; ?>
	</div>
</div>
<?php
if ($method->getCardHolderName())
{
	$style = '';
}
else
{
	$style = ' style = "display:none;" ';
}
?>
<div class="<?php echo $controlGroupClass;  ?> payment_information" id="tr_card_holder_name" <?php echo $style; ?>>
	<label class="<?php echo $controlLabelClass; ?>" for="card_holder_name">
		<?php echo JText::_('EB_CARD_HOLDER_NAME'); ?><span class="required">*</span>
	</label>

	<div class="<?php echo $controlsClass; ?>">
		<input type="text" id="card_holder_name" name="card_holder_name"
			   class="input-large validate[required]"
			   value="<?php echo $this->escape($this->input->getString('card_holder_name')); ?>"/>
	</div>
</div>