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
if ($this->config->prevent_duplicate_registration)
{
	$readOnly = ' readonly="readonly" ' ;
}
else
{
	$readOnly = '' ;
}
$btnClass = $this->bootstrapHelper->getClassMapping('btn');
?>
<h1 class="eb-page-heading"><?php echo JText::_('EB_ADDED_EVENTS'); ?></h1>
<div id="eb-mini-cart-page" class="eb-container eb-cart-content">
<?php
if (count($this->items))
{
?>
	<form method="post" name="adminForm" id="adminForm" action="index.php">
		<table class="table table-striped table-bordered table-condensed">
			<thead>
				<tr>
					<th class="col_event">
						<?php echo JText::_('EB_EVENT'); ?>
					</th>
					<?php
						if ($this->config->show_event_date)
						{
						?>
							<th class="col_event_date">
								<?php echo JText::_('EB_EVENT_DATE'); ?>
							</th>
						<?php
						}
					?>
					<th class="col_price">
						<?php echo JText::_('EB_PRICE'); ?>
					</th>
					<th class="col_quantity">
						<?php echo JText::_('EB_QUANTITY'); ?>
					</th>
					<th class="col_price">
						<?php echo JText::_('EB_SUB_TOTAL'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
				$total = 0 ;
				for ($i = 0 , $n = count($this->items) ; $i < $n; $i++)
				{
					$item = $this->items[$i] ;
					$rate = $this->config->show_discounted_price ? $item->discounted_rate : $item->rate;
					$total += $item->quantity*$rate;
					$url = JRoute::_('index.php?option=com_eventbooking&view=event&id='.$item->id.'&tmpl=component&Itemid='.$this->Itemid);
				?>
					<tr>
						<td class="col_event">
							<a href="<?php echo $url; ?>"><?php echo $item->title; ?></a>
						</td>
						<?php
							if ($this->config->show_event_date) {
							?>
								<td class="col_event_date">
									<?php
										if ($item->event_date == EB_TBC_DATE)
										{
											echo JText::_('EB_TBC');
										}
										else
										{
											echo JHtml::_('date', $item->event_date, $this->config->event_date_format, null);
										}
									?>
								</td>
							<?php
							}
						?>
						<td class="col_price">
							<?php echo EventbookingHelper::formatCurrency($rate, $this->config); ?>
						</td>
						<td class="col_quantity">
							<div class="btn-wrapper input-append">
								<input id="quantity" type="text" class="input-mini inputbox quantity_box" size="3" value="<?php echo $item->quantity ; ?>" name="quantity[]" <?php echo $readOnly ; ?> />
								<button onclick="javascript:updateCart();" id="update_cart" class="<?php echo $btnClass; ?> btn-default" type="button">
									<i class="fa fa-refresh"></i>
								</button>
								<button onclick="javascript:removeCart(<?php echo $item->id; ?>);" id="update_cart" class="<?php echo $btnClass; ?> btn-default" type="button">
									<i class="fa fa-times-circle"></i>
								</button>
								<input id="event_id" type="hidden" name="event_id[]" value="<?php echo $item->id; ?>" />
							</div>
						</td>
						<td class="col_price">
							<?php echo EventbookingHelper::formatCurrency($rate*$item->quantity, $this->config); ?>
						</td>
					</tr>
				<?php
				}
				if ($this->config->show_event_date)
				{
					$cols = 5 ;
				}
				else
				{
					$cols = 4 ;
				}
				?>
				<tr>
					<td class="col_price" colspan="<?php echo $cols; ?>">
						<span class="total_amount"><?php echo JText::_('EB_TOTAL'); ?>:  </span>
						<?php echo EventBookingHelper::formatCurrency($total, $this->config); ?>
					</td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" name="option" value="com_eventbooking" />
		<input type="hidden" name="task" value="cart.update_cart" />
		<div style="text-align: center;" class="form-actions">
			<div class="controls">
				<button onclick="javascript:colorbox();" id="add_more_item" class="<?php echo $btnClass; ?> btn-success" type="button">
					<i class="icon-new"></i> <?php echo JText::_('EB_ADD_MORE_EVENTS'); ?>
				</button>
				<button onclick="javascript:updateCart();" id="update_cart" class="<?php echo $btnClass; ?> btn-primary" type="button">
					<i class="fa fa-refresh"></i> <?php echo JText::_('EB_UPDATE'); ?>
				</button>
				<button onclick="javascript:checkOut();" id="check_out" class="<?php echo $btnClass; ?> btn-primary" type="button">
					<i class="fa fa-mail-forward"></i> <?php echo JText::_('EB_CHECKOUT'); ?>
				</button>
			</div>
		</div>
	</form>
<?php
}
else
{
?>
	<p class="message"><?php echo JText::_('EB_NO_EVENTS_IN_CART'); ?></p>
<?php
}

if ($this->config->use_https)
{
	$checkoutUrl = JRoute::_('index.php?option=com_eventbooking&task=view_checkout&Itemid='.$this->Itemid, false, 1);
}
else
{
	$checkoutUrl = JRoute::_('index.php?option=com_eventbooking&task=view_checkout&Itemid='.$this->Itemid, false, 0);
}
?>
</div>
<script type="text/javascript">
	<?php echo $this->jsString ; ?>
	function colorbox()
	{
		jQuery.colorbox.close();
	}

	function checkOut() {
		document.location.href = "<?php echo $checkoutUrl; ?>";
	}

	function updateCart() {
		Eb.jQuery(function ($) {
			var ret = checkQuantity();
			if (ret) {
				var eventId = $("input[id='event_id']").map(function () {
					return $(this).val();
				}).get();
				var quantity = $("input[id='quantity']").map(function () {
					return $(this).val();
				}).get();
				$.ajax({
					type: 'POST',
					url: 'index.php?option=com_eventbooking&task=cart.update_cart&Itemid=<?php echo $this->Itemid ?>&redirect=0&event_id=' + eventId + '&quantity=' + quantity,
					dataType: 'html',
					beforeSend: function () {
						$('#add_more_item').before('<span class="wait"><i class="fa fa-2x fa-refresh fa-spin"></i></span>');
					},
					success: function (html) {
						$('#cboxLoadedContent').html(html);
						$('.wait').remove();
					},
					error: function (xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		})
	}

	function removeCart(id) {
		Eb.jQuery(function ($) {
			$.ajax({
				type: 'POST',
				url: 'index.php?option=com_eventbooking&task=cart.remove_cart&id=' + id + '&Itemid=<?php echo $this->Itemid ?>&redirect=0',
				dataType: 'html',
				beforeSend: function () {
					$('#add_more_item').before('<span class="wait"><i class="fa fa-2x fa-refresh fa-spin"></i></span>');
				},
				success: function (html) {
					$('#cboxLoadedContent').html(html);
					jQuery.colorbox.resize();
					$('.wait').remove();
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		})
	}

	function checkQuantity() {
		var eventId;
		var quantity;
		var enteredQuantity;
		var index;
		var availableQuantity;
		var length = jQuery('input[name="event_id[]"]').length;
		if (length) {
			//There are more than one events
			for (var i = 0; i < length; i++) {
				eventId = jQuery('input[name="event_id[]"]')[i].value;
				enteredQuantity = jQuery('input[name="quantity[]"]')[i].value;
				index = findIndex(eventId, arrEventIds);
				availableQuantity = arrQuantities[index];
				if ((availableQuantity != -1) && (enteredQuantity > availableQuantity)) {
					alert("<?php echo JText::_("EB_INVALID_QUANTITY"); ?>" + availableQuantity);
					jQuery('input[name="event_id[]"]')[i].focus();
					return false;
				}
			}
		} else {
			//There is only one event
			enteredQuantity = jQuery('input[name="quantity[]"]').value;
			availableQuantity = arrQuantities[0];
			if ((availableQuantity != -1) && (enteredQuantity > availableQuantity)) {
				alert("<?php echo JText::_("EB_INVALID_QUANTITY"); ?>" + availableQuantity);
				jQuery('input[name="event_id[]"]').focus();
				return false;
			}
		}
		return true;
	}

	function findIndex(eventId, eventIds) {
		for (var i = 0; i < eventIds.length; i++) {
			if (eventIds[i] == eventId) {
				return i;
			}
		}
		return -1;
	}
</script>