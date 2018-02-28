<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die;

/* @var EventbookingViewRegisterHtml $this */

if ($this->config->use_https)
{
	$url = JRoute::_('index.php?option=com_eventbooking&task=register.process_group_registration&Itemid=' . $this->Itemid, false, 1);
}
else
{
	$url = JRoute::_('index.php?option=com_eventbooking&task=register.process_group_registration&Itemid=' . $this->Itemid, false);
}

$selectedState = '';

$bootstrapHelper   = $this->bootstrapHelper;
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$inputPrependClass = $bootstrapHelper->getClassMapping('input-prepend');
$inputAppendClass  = $bootstrapHelper->getClassMapping('input-append');
$addOnClass        = $bootstrapHelper->getClassMapping('add-on');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');
$btnClass          = $bootstrapHelper->getClassMapping('btn');

$layoutData = array(
	'controlGroupClass' => $controlGroupClass,
	'controlLabelClass' => $controlLabelClass,
	'controlsClass'     => $controlsClass,
);

if (!$this->userId && $this->config->user_registration)
{
	$validateLoginForm = true;
	echo $this->loadCommonLayout('register/tmpl/register_login.php', $layoutData);
}
else
{
	$validateLoginForm = false;
}
?>
<form method="post" name="adminForm" id="adminForm" action="<?php echo $url; ?>" autocomplete="off" class="form form-horizontal" enctype="multipart/form-data">
<?php
	if (!$this->userId && $this->config->user_registration)
	{
		echo $this->loadCommonLayout('register/tmpl/register_user_registration.php', $layoutData);
	}

	$fields = $this->form->getFields();

	if (isset($fields['state']))
	{
		$selectedState = $fields['state']->value;
	}

	$dateFields = array();

	foreach ($fields as $field)
	{
		echo $field->getControlGroup($bootstrapHelper);

		if ($field->type == "Date")
		{
			$dateFields[] = $field->name;
		}
	}

	if (($this->totalAmount > 0) || $this->form->containFeeFields())
	{
	?>
		<h3 class="eb-heading"><?php echo JText::_('EB_PAYMENT_INFORMATION'); ?></h3>
	<?php
		$layoutData['currencySymbol']     = $this->event->currency_symbol ?: $this->config->currency_symbol;
		$layoutData['onCouponChange']     = 'calculateGroupRegistrationFee();';
		$layoutData['addOnClass']         = $addOnClass;
		$layoutData['inputPrependClass']  = $inputPrependClass;
		$layoutData['inputAppendClass']   = $inputAppendClass;
		$layoutData['showDiscountAmount'] = ($this->enableCoupon || $this->discountAmount > 0 || $this->bundleDiscountAmount > 0);
		$layoutData['showTaxAmount']      = ($this->event->tax_rate > 0);
		$layoutData['showGrossAmount']    = ($this->enableCoupon || $this->discountAmount > 0 || $this->bundleDiscountAmount > 0 || $this->event->tax_rate > 0 || $this->showPaymentFee);

		echo $this->loadCommonLayout('register/tmpl/register_payment_amount.php', $layoutData);

		if (!$this->waitingList)
		{
			$layoutData['registrationType'] = 'group';
			echo $this->loadCommonLayout('register/tmpl/register_payment_methods.php', $layoutData);
		}
	}

	$articleId = $this->event->article_id ? $this->event->article_id : $this->config->article_id;

	if ($this->event->enable_terms_and_conditions != 2)
	{
		$enableTermsAndConditions =  $this->event->enable_terms_and_conditions;
	}
	else
	{
		$enableTermsAndConditions = $this->config->accept_term;
	}

	if ($this->event->collect_member_information === '')
	{
		$collectMemberInformation = $this->config->collect_member_information;
	}
	else
	{
		$collectMemberInformation = $this->event->collect_member_information;
	}

	if ($enableTermsAndConditions && $articleId)
	{
		$layoutData['articleId'] = $articleId;

		echo $this->loadCommonLayout('register/tmpl/register_terms_and_conditions.php', $layoutData);
	}

	if ($this->showCaptcha)
	{
	?>
		<div class="<?php echo $controlGroupClass; ?>">
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_CAPTCHA'); ?><span class="required">*</span>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php echo $this->captcha; ?>
			</div>
		</div>
	<?php
	}

	if ($this->waitingList)
	{
		$buttonText = JText::_('EB_PROCESS');
	}
	else
	{
		$buttonText = JText::_('EB_PROCESS_REGISTRATION');
	}
	?>
	<div class="form-actions">
		<input type="button" class="btn btn-primary" name="btn-group-billing-back" id="btn-group-billing-back" value="<?php echo  JText::_('EB_BACK') ;?>">
		<input type="submit" class="btn btn-primary" name="btn-process-group-billing" id="btn-process-group-billing" value="<?php echo $buttonText;?>">
		<img id="ajax-loading-animation" src="<?php echo JUri::base(true);?>/media/com_eventbooking/ajax-loadding-animation.gif" style="display: none;"/>
	</div>
	<?php
		if (count($this->methods) == 1)
		{
		?>
			<input type="hidden" name="payment_method" value="<?php echo $this->methods[0]->getName(); ?>" />
		<?php
		}
	?>
	<input type="hidden" name="event_id" value="<?php echo $this->event->id; ?>" />
	<input type="hidden" name="show_payment_fee" value="<?php echo (int)$this->showPaymentFee ; ?>" />
	<input type="hidden" id="card-nonce" name="nonce" />
	<script type="text/javascript">
		var eb_current_page = 'group_billing';
		<?php echo os_payments::writeJavascriptObjects();?>
			Eb.jQuery(document).ready(function($){
				<?php
					if (count($dateFields))
					{
						echo EventbookingHelperHtml::getCalendarSetupJs($dateFields);
					}
					if ($this->amount == 0)
					{
					?>
						$('.payment_information').css('display', 'none');
					<?php
					}
				?>
				$("#adminForm").validationEngine('attach', {
					onValidationComplete: function(form, status){
						if (status == true) {
							form.on('submit', function(e) {
								e.preventDefault();
							});

							if($('input:radio[name^=payment_method]').length)
							{
								var paymentMethod = $('input:radio[name^=payment_method]:checked').val();
							}
							else
							{
								var paymentMethod = $('input[name^=payment_method]').val();
							}

							if (typeof stripePublicKey !== 'undefined' && $('#tr_card_number').is(":visible"))
							{
								if (paymentMethod.indexOf('os_stripe') == 0)
								{
									Stripe.card.createToken({
										number: $('#x_card_num').val(),
										cvc: $('#x_card_code').val(),
										exp_month: $('select[name^=exp_month]').val(),
										exp_year: $('select[name^=exp_year]').val(),
										name: $('#card_holder_name').val()
									}, stripeResponseHandler);

									return false;
								}
							}

							if (paymentMethod == 'os_squareup' && $('#tr_card_number').is(':visible'))
							{
								sqPaymentForm.requestCardNonce();

								return false;
							}

							return true;
						}
						return false;
					}
				});
				<?php
					if ($validateLoginForm)
					{
					?>
						$("#eb-login-form").validationEngine();
					<?php
					}

				?>
				buildStateField('state', 'country', '<?php echo $selectedState; ?>');
				<?php
					if ($this->showCaptcha && $this->captchaPlugin == 'recaptcha')
					{
						$captchaPlugin = JPluginHelper::getPlugin('captcha', 'recaptcha');
						$params = $captchaPlugin->params;
						$version    = $params->get('version', '1.0');
						$pubkey = $params->get('public_key', '');

						if ($version == '1.0')
						{
							$theme  = $params->get('theme', 'clean');
						?>
							Recaptcha.create("<?php echo $pubkey; ?>", "dynamic_recaptcha_1", {theme: "<?php echo $theme; ?>"});
						<?php
						}
						else
						{							
						?>
							JoomlaInitReCaptcha2();
						<?php							
						}
					}
				?>
				$('#btn-group-billing-back').click(function(){
					$.ajax({
						url: siteUrl + 'index.php?option=com_eventbooking&view=register&layout=group_members&event_id=<?php echo $this->event->id; ?>&Itemid=<?php echo $this->Itemid; ?>&format=raw' + langLinkForAjax,
						type: 'post',
						dataType: 'html',
						beforeSend: function() {
							$('#btn-group-billing-back').attr('disabled', true);
						},
						complete: function() {
							$('#btn-group-billing-back').attr('disabled', false);
						},
						success: function(html) {
							$('#eb-group-members-information .eb-form-content').html(html);
							$('#eb-group-billing .eb-form-content').slideUp('slow');
							<?php ($collectMemberInformation) ? $idAjax = 'eb-group-members-information' : $idAjax = 'eb-number-group-members';?>
							$('#<?php echo $idAjax; ?> .eb-form-content').slideDown('slow');
						},
						error: function(xhr, ajaxOptions, thrownError) {
							alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});
				});
				//term colorbox term
				 $(".eb-colorbox-term").colorbox({
					 href: $(this).attr('href'),
					 innerHeight: '80%',
					 innerWidth: '80%',
					 overlayClose: true,
					 iframe: true,
					 opacity: 0.3
				});
				<?php
					if ($collectMemberInformation)
					{
					?>
						$('html, body').animate({scrollTop:$('#eb-group-members-information').position().top}, 'slow');
					<?php
					}
				?>
			})
	</script>
</form>
