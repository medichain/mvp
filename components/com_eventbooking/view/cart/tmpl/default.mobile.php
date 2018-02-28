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
JHtml::_('behavior.modal', 'a.eb-modal');
$popup = 'class="eb-modal" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"';
if ($this->config->prevent_duplicate_registration)
{
	$readOnly = ' readonly="readonly" ' ;
}
else
{
	$readOnly = '' ;
}
if ($this->config->use_https)
{
	$url = JRoute::_('index.php?option=com_eventbooking&Itemid='.$this->Itemid, false, 1);
}
else
{
	$url = JRoute::_('index.php?option=com_eventbooking&Itemid='.$this->Itemid, false);
}
$btnClass = $this->bootstrapHelper->getClassMapping('btn');
$span12  = $this->bootstrapHelper->getClassMapping('span12');
?>
<div id="eb-cart-page" class="eb-container eb-cart-content">
<h1 class="eb-page-heading"><?php echo JText::_('EB_ADDED_EVENTS'); ?></h1>
<?php
if (count($this->items))
{
?>
	<form method="post" name="adminForm" id="adminForm" action="<?php echo $url; ?>">
		<?php
		$total = 0 ;
		for ($i = 0 , $n = count($this->items) ; $i < $n; $i++)
		{
			$item = $this->items[$i];
			if ($this->config->show_discounted_price)
			{
				$item->rate = $item->discounted_rate;
			}
			$total += $item->quantity*$item->rate ;
			$url = JRoute::_('index.php?option=com_eventbooking&view=event&id='.$item->id.'&tmpl=component&Itemid='.$this->Itemid);
		?>
		<div class="well clearfix">
			<div class="row-fluid">
				<div class="<?php echo $span12; ?> eb-mobile-event-title">
					<a href="<?php echo $url; ?>" <?php echo $popup; ?>><?php echo $item->title; ?></a>
				</div>
				<?php
					if ($this->config->show_event_date)
					{
					?>
						<div class="<?php echo $span12; ?> eb-mobile-event-date">
							<strong><?php echo JText::_('EB_EVENT_DATE'); ?>: </strong>
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
						</div>
					<?php
					}
				?>
				<div class="<?php echo $span12; ?> eb-mobile-event-price">
					<strong><?php echo JText::_('EB_PRICE'); ?> :</strong>
					<?php echo EventbookingHelper::formatCurrency($item->rate, $this->config); ?>
				</div>
				<div class="<?php echo $span12; ?> eb-mobile-quantity">
					<strong><?php echo JText::_('EB_QUANTITY'); ?> :</strong>
					<div class="btn-wrapper input-append">
						<input type="text" class="input-mini inputbox quantity_box" size="3" value="<?php echo $item->quantity ; ?>" name="quantity[]" <?php echo $readOnly ; ?> />
						<button onclick="javascript:updateCart();" id="update_cart" class="<?php echo $btnClass; ?> btn-default" type="button">
							<i class="fa fa-refresh"></i>
						</button>
						<button onclick="javascript:removeItem(<?php echo $item->id; ?>);" id="update_cart" class="<?php echo $btnClass; ?> btn-default" type="button">
							<i class="fa fa-times-circle"></i>
						</button>
						<input type="hidden" name="event_id[]" value="<?php echo $item->id; ?>" />
					</div>
				</div>
				<div class="<?php echo $span12; ?> eb-mobile-sub-total">
					<strong><?php echo JText::_('EB_SUB_TOTAL'); ?> :</strong>
					<?php echo EventbookingHelper::formatCurrency($item->rate*$item->quantity, $this->config); ?>
				</div>
			</div>
		</div>
		<?php
		}
		?>
		<div style="text-align: center" class="totals clearfix">
			<div>
				<?php echo JText::_('EB_TOTAL') .' '. EventbookingHelper::formatCurrency($total, $this->config); ?>
			</div>
		</div>
		<div style="text-align: center;" class="bottom control-group">
			<div>
				<button onclick="continueShopping();" id="add_more_item" class="<?php echo $btnClass; ?> btn-success" type="button">
					<i class="icon-new"></i> <?php echo JText::_('EB_ADD_MORE_EVENTS'); ?>
				</button>
				<button onclick="updateCart();" id="update_cart" class="<?php echo $btnClass; ?> btn-primary" type="button">
					<i class="fa fa-refresh"></i> <?php echo JText::_('EB_UPDATE'); ?>
				</button>
				<button onclick="checkout();" id="check_out" class="<?php echo $btnClass; ?> btn-primary" type="button">
					<i class="fa fa-mail-forward"></i> <?php echo JText::_('EB_CHECKOUT'); ?>
				</button>
			</div>
		</div>
		<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
		<input type="hidden" name="category_id" value="<?php echo $this->categoryId; ?>" />
		<input type="hidden" name="option" value="com_eventbooking" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="" />
		<script type="text/javascript">
			<?php echo $this->jsString ; ?>
			function checkout() {
				var form = document.adminForm ;
				ret = checkQuantity() ;
				if (ret) {
					form.task.value = 'checkout';
					form.submit() ;
				}
			}

			function continueShopping()
			{
				document.location.href= "<?php echo $this->continueUrl; ?>";
			}

			function updateCart() {
				var form = document.adminForm ;
				var ret = checkQuantity();
				if (ret) {
					form.task.value = 'cart.update_cart';
					form.submit();
				}
			}
			function removeItem(id) {
				if (confirm("<?php echo JText::_('EB_REMOVE_CONFIRM'); ?>")) {
					var form = document.adminForm ;
					form.id.value = id ;
					form.task.value = 'cart.remove_cart' ;
					form.submit() ;
				}
			}
			function checkQuantity() {
				var form = document.adminForm ;
				var eventId ;
				var quantity ;
				var enteredQuantity ;
				var index ;
				var availableQuantity ;
				if (form['event_id[]'].length) {
					var length = form['event_id[]'].length ;
					//There are more than one events
					for (var  i = 0 ; i < length ; i++) {
						eventId = form['event_id[]'][i].value ;
						enteredQuantity = form['quantity[]'][i].value ;
						index = findIndex(eventId, arrEventIds);
						availableQuantity = arrQuantities[index] ;
						if ((availableQuantity != -1) && (enteredQuantity >availableQuantity)) {
							alert("<?php echo JText::_("EB_INVALID_QUANTITY"); ?>" + availableQuantity);
							form['event_id[]'][i].focus();
							return false ;
						}
					}
				} else {
					//There is only one event
					enteredQuantity = form['quantity[]'].value ;
					availableQuantity = arrQuantities[0] ;
					if ((availableQuantity != -1) && (enteredQuantity >availableQuantity)) {
						alert("<?php echo JText::_("EB_INVALID_QUANTITY"); ?>" + availableQuantity);
						form['event_id[]'].focus();
						return false ;
					}
				}
				return true ;
			}

			function findIndex(eventId, eventIds) {
				for (var i = 0 ; i < eventIds.length ; i++) {
					if (eventIds[i] == eventId) {
						return i ;
					}
				}
				return -1 ;
			}
		</script>
	</form>
<?php
}
else
{
?>
	<p class="eb-message"><?php echo JText::_('EB_NO_EVENTS_IN_CART'); ?></p>
<?php
}
?>
</div>