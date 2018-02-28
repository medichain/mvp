<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined( '_JEXEC' ) or die;
?>
<script type="text/javascript">
	Eb.jQuery(document).ready(function($){
		$("#adminForm").validationEngine('attach', {
			onValidationComplete: function(form, status){
				if (status == true) {
					form.on('submit', function(e) {
						e.preventDefault();
					});
					form.find('#btn-submit').prop('disabled', true);

					if (typeof stripePublicKey !== 'undefined' && $('#x_card_num').is(":visible"))
					{
						if($('input:radio[name^=payment_method]').length)
						{
							var paymentMethod = $('input:radio[name^=payment_method]:checked').val();
						}
						else
						{
							var paymentMethod = $('input[name^=payment_method]').val();
						}

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
					return true;
				}
				return false;
			}
		});
		buildStateField('state', 'country', '<?php echo $selectedState; ?>');
	})
	<?php echo os_payments::writeJavascriptObjects(); ?>
</script>
