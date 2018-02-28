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

JHtml::_('formbehavior.chosen', 'select');
$selectedState = '';
JHtml::_('bootstrap.tooltip');
$document = JFactory::getDocument();
$document->addStyleDeclaration(".hasTip{display:block !important}");

// Add support for custom settings layout
if (file_exists(__DIR__ . '/default_custom_settings.php'))
{
	$hasCustomSettings = true;
	JHtml::_('behavior.tabstate');
}
else
{
	$hasCustomSettings = false;
}
?>
<form action="index.php?option=com_eventbooking&view=registrant" method="post" name="adminForm" id="adminForm" class="form form-horizontal" enctype="multipart/form-data">
	<?php
	if ($hasCustomSettings)
	{
		echo JHtml::_('bootstrap.startTabSet', 'registrant', array('active' => 'general-page'));
		echo JHtml::_('bootstrap.addTab', 'registrant', 'general-page', JText::_('EB_GENERAL', true));
	}
	?>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_EVENT'); ?>
		</label>
		<div class="controls">
			<?php echo $this->lists['event_id']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_NB_REGISTRANTS'); ?>
		</label>
		<div class="controls">
			<?php
				if ($this->item->number_registrants > 0)
				{
				?>
					<input type="text" name="number_registrants" value="<?php echo $this->item->number_registrants ?>" readonly="readonly" />
				<?php
				}
				else
				{
				?>
					<input class="text_area" type="text" name="number_registrants" id="number_registrants" size="40" maxlength="250" value="1" />
					<small><?php echo JText::_('EB_NUMBER_REGISTRANTS_EXPLAIN'); ?></small>
				<?php
				}
			?>
		</div>
	</div>
	<?php
		if (!empty($this->ticketTypes))
		{
		?>
			<h3><?php echo JText::_('EB_TICKET_INFORMATION'); ?></h3>
		<?php
			foreach($this->ticketTypes AS $ticketType)
			{
				$available = $ticketType->capacity - $ticketType->registered;
				$quantity = 0;
				if (!empty($this->registrantTickets[$ticketType->id]))
				{
					$quantity = $this->registrantTickets[$ticketType->id]->quantity;
				}
				?>
				<div class="control-group">
					<label class="control-label">
						<?php echo  $ticketType->title; ?>
					</label>
					<div class="controls">
						<?php
						if ($available > 0 || $quantity > 0)
						{
							$fieldName = 'ticket_type_'.$ticketType->id;
							if ($available < $quantity)
							{
								$available = $quantity;
							}
							echo JHtml::_('select.integerlist', 0, $available, 1, $fieldName, 'class="ticket_type_quantity input-small"', $quantity);
						}
						else
						{
							echo JText::_('EB_NA');
						}
						?>
					</div>
				</div>
				<?php
			}
		}
		?>
		<div class="control-group">
			<label class="control-label">
				<?php echo  JText::_('EB_USER'); ?>
			</label>
			<div class="controls">
				<?php echo EventbookingHelper::getUserInput($this->item->user_id,'user_id',(int) $this->item->id) ; ?>
			</div>
		</div>
		<?php
		$fields = $this->form->getFields();
		if (isset($fields['state']))
		{
			$selectedState = $fields['state']->value;
		}
		foreach ($fields as $field)
		{
			$fieldType = strtolower($field->type);
			switch ($fieldType)
			{
				case 'heading':
				case 'message':
					break;
				default:
					$controlGroupAttributes = 'id="field_' . $field->name . '" ';
					if ($field->hideOnDisplay)
					{
						$controlGroupAttributes .= ' style="display:none;" ';
					}
					$class = "";
					if ($field->isMasterField)
					{
						if ($field->suffix)
						{
							$class = ' master-field-' . $field->suffix;
						}
						else
						{
							$class = ' master-field';
						}
					}
			?>
			<div class="control-group<?php echo $class; ?>" <?php echo $controlGroupAttributes; ?>>
				<label class="control-label">
					<?php echo $field->title; ?>
				</label>
				<div class="controls">
					<?php echo $field->input; ?>
				</div>
			</div>
			<?php
			}
		}
	?>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_REGISTRATION_DATE'); ?>
		</label>
		<div class="controls">
			<?php echo  JHtml::_('date', $this->item->register_date, $this->config->date_format, null);?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_TOTAL_AMOUNT'); ?>
		</label>
		<div class="controls">
			<?php echo $this->config->currency_symbol?><input type="text" name="total_amount" class="input-medium" value="<?php echo $this->item->total_amount > 0 ? round($this->item->total_amount , 2) : null;?>" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_DISCOUNT_AMOUNT'); ?>
		</label>
		<div class="controls">
			<?php echo $this->config->currency_symbol?><input type="text" name="discount_amount" class="input-medium" value="<?php echo $this->item->discount_amount > 0 ? round($this->item->discount_amount , 2) : null;?>" />
		</div>
	</div>
	<?php
	if ($this->item->late_fee > 0)
	{
	?>
		<div class="control-group">
			<label class="control-label">
				<?php echo  JText::_('EB_LATE_FEE'); ?>
			</label>
			<div class="controls">
				<?php echo $this->config->currency_symbol?><input type="text" name="late_fee" class="input-medium" value="<?php echo $this->item->late_fee > 0 ? round($this->item->late_fee , 2) : null;?>" />
			</div>
		</div>
	<?php
	}

	if ($this->event->tax_rate > 0 || $this->item->tax_amount > 0)
	{
	?>
		<div class="control-group">
			<label class="control-label">
				<?php echo  JText::_('EB_TAX'); ?>
			</label>
			<div class="controls">
				<?php echo $this->config->currency_symbol?><input type="text" name="tax_amount" class="input-medium" value="<?php echo $this->item->tax_amount > 0 ? round($this->item->tax_amount , 2) : null;?>" />
			</div>
		</div>
	<?php
	}

	if ($this->showPaymentFee || $this->item->payment_processing_fee > 0)
	{
	?>
		<div class="control-group">
			<label class="control-label">
				<?php echo  JText::_('EB_PAYMENT_FEE'); ?>
			</label>
			<div class="controls">
				<?php echo $this->config->currency_symbol?><input type="text" name="payment_processing_fee" class="input-medium" value="<?php echo $this->item->payment_processing_fee > 0 ? round($this->item->payment_processing_fee , 2) : null;?>" />
			</div>
		</div>
	<?php
	}
	?>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_GROSS_AMOUNT'); ?>
		</label>
		<div class="controls">
			<?php echo $this->config->currency_symbol?><input type="text" name="amount" class="input-medium" value="<?php echo $this->item->amount > 0 ? round($this->item->amount , 2) : null;?>" />
		</div>
	</div>
	<?php
		if ($this->config->activate_deposit_feature)
		{
		?>
			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('EB_DEPOSIT_AMOUNT'); ?>
				</label>
				<div class="controls">
					<?php echo $this->config->currency_symbol?><input type="text" name="deposit_amount" value="<?php echo $this->item->deposit_amount > 0 ? round($this->item->deposit_amount , 2) : null;?>" />
				</div>
			</div>
			<?php
				if ($this->item->payment_status == 0 && $this->item->id)
				{
				?>
					<div class="control-group">
						<label class="control-label">
							<?php echo JText::_('EB_DUE_AMOUNT'); ?>
						</label>
						<div class="controls">
							<?php
							if ($this->item->payment_status == 1)
							{
								$dueAmount = 0;
							}
							else
							{
								$dueAmount = $this->item->amount - $this->item->deposit_amount;
							}
							echo $this->config->currency_symbol?><input type="text" name="due_amount" class="input-medium" value="<?php echo $dueAmount > 0 ? round($dueAmount , 2) : null;?>" />
						</div>
					</div>
				<?php
				}
			?>
			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('EB_PAYMENT_STATUS'); ?>
				</label>
				<div class="controls">
					<?php echo $this->lists['payment_status'];?>
				</div>
			</div>
		<?php
		}

		if ($this->item->id && $this->item->total_amount > 0)
		{
		?>
			<div class="control-group">
				<label class="control-label">
					<?php echo  EventbookingHelperHtml::getFieldLabel('re_calculate_fee', JText::_('EB_RE_CALCULATE_FEE'), JText::_('EB_RE_CALCULATE_FEE_EXPLAIN')); ?>
				</label>
				<div class="controls">
					<input type="checkbox" value="1" id="re_calculate_fee" name="re_calculate_fee" />
				</div>
			</div>
		<?php
		}

		if (!$this->item->id || $this->item->amount > 0)
		{
		?>
			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('EB_PAYMENT_METHOD'); ?>
				</label>
				<div class="controls">
					<?php echo $this->lists['payment_method']; ?>
				</div>
			</div>
		<?php
		}

		if ($this->item->amount > 0)
		{
		?>
			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('EB_TRANSACTION_ID'); ?>
				</label>
				<div class="controls">
					<input type="text" name="transaction_id" value="<?php echo $this->item->transaction_id;?>" />
				</div>
			</div>
		<?php
		}

		if ($this->item->deposit_payment_transaction_id)
		{
		?>
			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('EB_DEPOSIT_PAYMENT_TRANSACTION_ID'); ?>
				</label>
				<div class="controls">
					<input type="text" name="deposit_payment_transaction_id" value="<?php echo $this->item->deposit_payment_transaction_id;?>" />
				</div>
			</div>
		<?php
		}

		if ($this->item->payment_method == "os_offline_creditcard")
		{
			$params = new \Joomla\Registry\Registry($this->item->params);
		?>
			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('EB_FIRST_12_DIGITS_CREDITCARD_NUMBER'); ?>
				</label>
				<div class="controls">
					<?php echo $params->get('card_number'); ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('AUTH_CARD_EXPIRY_DATE'); ?>
				</label>
				<div class="controls">
					<?php echo $params->get('exp_date'); ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('AUTH_CVV_CODE'); ?>
				</label>
				<div class="controls">
					<?php echo $params->get('cvv'); ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('EB_CARD_HOLDER_NAME'); ?>
				</label>
				<div class="controls">
					<?php echo $params->get('card_holder_name'); ?>
				</div>
			</div>
		<?php
		}
		if ($this->config->activate_checkin_registrants)
		{
		?>
			<div class="control-group">
				<label class="control-label">
					<?php echo  JText::_('EB_CHECKED_IN'); ?>
				</label>
				<div class="controls">
					<?php echo EventbookingHelperHtml::getBooleanInput('checked_in', $this->item->checked_in); ?>
				</div>
			</div>
		<?php
		}
	?>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_REGISTRATION_STATUS'); ?>
		</label>
		<div class="controls">
			<?php echo $this->lists['published'] ; ?>
		</div>
	</div>
	<?php
	if ($this->item->user_ip)
	{
	?>
		<div class="control-group">
			<label class="control-label">
				<?php echo  JText::_('EB_USER_IP'); ?>
			</label>
			<div class="controls">
				<?php echo $this->item->user_ip; ?>
			</div>
		</div>
	<?php
	}
	if ($this->config->collect_member_information && count($this->rowMembers)) 
	{
	?>
		<h3 class="eb-heading"><?php echo JText::_('EB_MEMBERS_INFORMATION') ; ?> <button type="button" class="btn btn-small btn-success" onclick="addGroupMember();"><span class="icon-new icon-white"></span><?php echo JText::_('EB_ADD_MEMBER'); ?></button></h3>
	<?php
		$n = count($this->rowMembers) + 4;
		for ($i = 0 ; $i < $n ; $i++)
		{
			if (isset($this->rowMembers[$i]))
			{
				$rowMember = $this->rowMembers[$i] ;
				$memberId = $rowMember->id ;
				$memberData = EventbookingHelperRegistration::getRegistrantData($rowMember, $this->memberFormFields);
				$style = '';
			}
			else
			{
				$memberId = 0;
				$memberData = array();
				$style = ' style="display:none;"';
			}

			$form = new RADForm($this->memberFormFields);
			$form->setEventId($this->item->event_id);
			$form->bind($memberData);
			$form->setFieldSuffix($i+1);
			$form->prepareFormFields('setRecalculateFee();');
			$form->buildFieldsDependency();
			if ($i%2 == 0)
			{
				echo "<div class=\"row-fluid\">\n" ;
			}					
			?>
				<div class="span6" id="group_member_<?php echo $i + 1; ?>"<?php echo $style; ?>>
					<h4><?php echo JText::sprintf('EB_MEMBER_INFORMATION', $i + 1); ;?><button type="button" class="btn btn-small btn-danger" onclick="removeGroupMember(<?php echo $memberId; ?>);"><span class="icon-remove icon-white"></span><?php echo JText::_('EB_REMOVE'); ?></button></h4>
					<?php
						$fields = $form->getFields();
						foreach ($fields as $field)
						{
							if ($i > 0 && $field->row->only_show_for_first_member)
							{
								continue;
							}
							$fieldType = strtolower($field->type);
							switch ($fieldType)
							{
								case 'heading':
								case 'message':
									break;
								default:
									$controlGroupAttributes = 'id="field_' . $field->name . '" ';
									if ($field->hideOnDisplay)
									{
										$controlGroupAttributes .= ' style="display:none;" ';
									}
									$class = '';
									if ($field->isMasterField)
									{
										if ($field->suffix)
										{
											$class = ' master-field-' . $field->suffix;
										}
										else
										{
											$class = ' master-field';
										}
									}
								?>
								<div class="control-group<?php echo $class; ?>" <?php echo $controlGroupAttributes; ?>>
									<label class="control-label">
										<?php echo $field->title; ?>
									</label>
									<div class="controls">
										<?php echo $field->input; ?>
									</div>
								</div>
							<?php
							}
						}
					?>
					<input type="hidden" name="ids[]" value="<?php echo $memberId; ?>" />
				</div>
			<?php	
			if (($i + 1) %2 == 0)
			{
				echo "</div>\n" ;
			}
		}
		if ($i %2 != 0)
		{
			echo "</div>" ;
		}	
	?>				
	</table>	
	<?php	
	}

	// Add support for custom settings layout
	if ($hasCustomSettings)
	{
		echo JHtml::_('bootstrap.endTab');
		echo JHtml::_('bootstrap.addTab', 'registrant', 'custom-settings-page', JText::_('EB_REGISTRANT_CUSTOM_SETTINGS', true));
		echo $this->loadTemplate('custom_settings');
		echo JHtml::_('bootstrap.endTab');
		echo JHtml::_('bootstrap.endTabSet');
	}
	?>
	<div class="clearfix"></div>
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="group_member_id" value="0" />
	<?php echo JHtml::_( 'form.token' ); ?>
	<script type="text/javascript">
		var newMemberAdded = 0;
		var numberMembers = <?php echo (int) count($this->rowMembers); ?>;
		(function($){
			setRecalculateFee = (function() {
				$('#re_calculate_fee').prop('checked', true);
			});

			addGroupMember = (function() {
				if (newMemberAdded < 4)
				{
					newMemberAdded++;
					$('input[name=number_registrants]').val(newMemberAdded + numberMembers);
					var newMemberContainerId = 'group_member_' + (newMemberAdded + numberMembers);
					$('#' + newMemberContainerId).show();

					$('#re_calculate_fee').prop('checked', true);
				}
				else
				{
					alert('<?php echo JText::_('EB_ADD_MEMBER_MAXIMUM_WARNING'); ?>');
				}
			});

			removeGroupMember = (function(memberId) {
				if (memberId == 0)
				{
					var newMemberContainerId = 'group_member_' + (newMemberAdded + numberMembers);
					$('#' + newMemberContainerId).hide();
					newMemberAdded--;
					$('input[name=number_registrants]').val(newMemberAdded + numberMembers);
					$('#re_calculate_fee').prop('checked', true);
				}
				else
				{
					if (confirm('<?php echo JText::_('EB_REMOVE_EXISTING_MEMBER_CONFIRM'); ?>'))
					{
						var form = document.adminForm;
						form.group_member_id.value = memberId;
						form.task.value = 'registrant.remove_group_member';
						form.submit();
					}
				}
			});

			showHideDependFields = (function(fieldId, fieldName, fieldType, fieldSuffix) {
				$('#ajax-loading-animation').show();
				var masterFieldsSelector;
				if (fieldSuffix)
				{
					masterFieldsSelector = '.master-field-' + fieldSuffix + ' input[type=\'checkbox\']:checked,' + ' .master-field-' + fieldSuffix + ' input[type=\'radio\']:checked,' + ' .master-field-' + fieldSuffix + ' select';
				}
				else
				{
					masterFieldsSelector = '.master-field input[type=\'checkbox\']:checked, .master-field input[type=\'radio\']:checked, .master-field select';
				}
				$.ajax({
					type: 'POST',
					url: siteUrl + 'index.php?option=com_eventbooking&task=get_depend_fields_status&field_id=' + fieldId + '&field_suffix=' + fieldSuffix + langLinkForAjax,
					data: $(masterFieldsSelector),
					dataType: 'json',
					success: function(msg, textStatus, xhr) {
						$('#ajax-loading-animation').hide();
						var hideFields = msg.hide_fields.split(',');
						var showFields = msg.show_fields.split(',');
						for (var i = 0; i < hideFields.length ; i++)
						{
							$('#' + hideFields[i]).hide();
						}
						for (var i = 0; i < showFields.length ; i++)
						{
							$('#' + showFields[i]).show();
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						alert(textStatus);
					}
				});
			});
			buildStateField = (function(stateFieldId, countryFieldId, defaultState){
				if($('#' + stateFieldId).length && $('#' + stateFieldId).is('select'))
				{
					//set state
					if ($('#' + countryFieldId).length)
					{
						var countryName = $('#' + countryFieldId).val();
					}
					else 
					{
						var countryName = '';
					}			
					$.ajax({
						type: 'GET',
						url: siteUrl + 'index.php?option=com_eventbooking&task=get_states&country_name='+ countryName+'&field_name='+stateFieldId + '&state_name=' + defaultState,
						success: function(data) {
							$('#field_' + stateFieldId + ' .controls').html(data);
						},
						error: function(jqXHR, textStatus, errorThrown) {						
							alert(textStatus);
						}
					});			
					//Bind onchange event to the country 
					if ($('#' + countryFieldId).length)
					{
						$('#' + countryFieldId).change(function(){
							$.ajax({
								type: 'GET',
								url: siteUrl + 'index.php?option=com_eventbooking&task=get_states&country_name='+ $(this).val()+'&field_name=' + stateFieldId + '&state_name=' + defaultState,
								success: function(data) {
									$('#field_' + stateFieldId + ' .controls').html(data);
								},
								error: function(jqXHR, textStatus, errorThrown) {						
									alert(textStatus);
								}
							});
							
						});
					}						
				}//end check exits state
							
			});
			$(document).ready(function(){
				buildStateField('state', 'country', '<?php echo $selectedState; ?>');
			})
			populateRegistrantData = (function(){
				var userId = $('#user_id_id').val();
				var eventId = $('#event_id').val();
				$.ajax({
					type : 'GET',
					url : 'index.php?option=com_eventbooking&task=get_profile_data&user_id=' + userId + '&event_id=' +eventId,
					dataType: 'json',
					success : function(json){
						var selecteds = [];
						for (var field in json)
						{
							value = json[field];
							if ($("input[name='" + field + "[]']").length)
							{
								//This is a checkbox or multiple select
								if ($.isArray(value))
								{
									selecteds = value;
								}
								else
								{
									selecteds.push(value);
								}
								$("input[name='" + field + "[]']").val(selecteds);
							}
							else if ($("input[type='radio'][name='" + field + "']").length)
							{
								$("input[name="+field+"][value=" + value + "]").attr('checked', 'checked');
							}
							else
							{
								$('#' + field).val(value);
							}
						}						
					}
				})
			});
		})(jQuery);
	</script>
</form>