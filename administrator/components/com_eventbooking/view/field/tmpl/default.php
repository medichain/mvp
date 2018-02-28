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

JHtml::_('bootstrap.tooltip');
$document = JFactory::getDocument();
$document->addStyleDeclaration(".hasTip{display:block !important}");

$translatable = JLanguageMultilang::isEnabled() && count($this->languages);
JHtml::_('formbehavior.chosen', 'select#event_id,select#category_id');

if ($translatable)
{
	JHtml::_('behavior.tabstate');
}
?>
<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton) {
		var form = document.adminForm;

		if (pressbutton == 'cancel')
		{
			Joomla.submitform( pressbutton );
			return;				
		} 
		else 
		{
			//Should validate the information here
			if (form.name.value == "") 
			{
				alert("<?php echo JText::_('EB_ENTER_FIELD_NAME'); ?>");
				form.name.focus();
				return ;
			}
			if (form.title.value == "") 
			{
				alert("<?php echo JText::_("EB_ENTER_FIELD_TITLE"); ?>");
				form.title.focus();
				return ; 
			}				
			Joomla.submitform( pressbutton );
		}
	};

	showHideEventsSelection = function(assignment)
	{
		if (assignment.value == 0)
		{
			jQuery('#events_selection_container').hide();
		}
		else
		{
			jQuery('#events_selection_container').show();
		}
	};
</script>
<form action="index.php?option=com_eventbooking&view=field" method="post" name="adminForm" id="adminForm" class="form form-horizontal">
<div class="row-fluid">
<?php
if ($translatable)
{
	echo JHtml::_('bootstrap.startTabSet', 'field', array('active' => 'general-page'));
	echo JHtml::_('bootstrap.addTab', 'field', 'general-page', JText::_('EB_GENERAL', true));
}
?>
	<div class="span6">
		<fieldset class="form-horizontal">
			<legend><?php echo JText::_('EB_BASIC'); ?></legend>
			<?php
			if ($this->config->custom_field_by_category)
			{
				?>
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('EB_CATEGORY'); ?>
					</label>
					<div class="controls">
						<?php echo $this->lists['category_id'] ; ?>
					</div>
				</div>
				<?php
			}
			else
			{
			?>
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('EB_FIELD_ASSIGNMENT'); ?>
					</label>
					<div class="controls">
						<?php echo $this->lists['assignment'] ; ?>
					</div>
				</div>
				<div class="control-group" id="events_selection_container"<?php if ($this->assignment == 0) echo 'style="display:none;"'; ?>>
					<label class="control-label">
						<?php echo JText::_('EB_EVENT'); ?>
					</label>
					<div class="controls">
						<?php echo $this->lists['event_id'] ; ?>
					</div>
				</div>
				<?php
			}
			?>

			<div class="control-group">
				<label class="control-label">
					<?php echo EventbookingHelperHtml::getFieldLabel('name', JText::_('EB_NAME'), JText::_('EB_FIELD_NAME_REQUIREMENT')); ?>
				</label>
				<div class="controls">
					<input class="text_area" type="text" name="name" id="name" size="50" maxlength="250" value="<?php echo $this->item->name;?>" onchange="checkFieldName();" <?php if ($this->item->is_core) echo 'readonly="readonly"' ;?> />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">
					<?php echo  JText::_('EB_TITLE'); ?>
				</label>
				<div class="controls">
					<input class="text_area" type="text" name="title" id="title" size="50" maxlength="250" value="<?php echo $this->item->title;?>" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('EB_DISPLAY_IN'); ?>
				</label>
				<div class="controls">
					<?php echo $this->lists['display_in']; ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('EB_ACCESS'); ?>
				</label>
				<div class="controls">
					<?php echo $this->lists['access']; ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('EB_REQUIRED'); ?>
				</label>
				<div class="controls">
					<?php echo EventbookingHelperHtml::getBooleanInput('required', $this->item->required); ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('EB_PUBLISHED'); ?>
				</label>
				<div class="controls">
					<?php echo $this->lists['published']; ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('EB_DATATYPE_VALIDATION') ; ?>
				</label>
				<div class="controls">
					<?php echo $this->lists['datatype_validation']; ?>
				</div>
			</div>
			<div class="control-group validation-rules">
				<label class="control-label">
					<?php echo EventbookingHelperHtml::getFieldLabel('validation_rules', JText::_('EB_VALIDATION_RULES'), JText::_('EB_VALIDATION_RULES_EXPLAIN')); ?>
				</label>
				<div class="controls">
					<input type="text" class="input-xlarge" size="50" name="validation_rules" value="<?php echo $this->item->validation_rules ; ?>" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">
					<?php echo EventbookingHelperHtml::getFieldLabel('only_show_for_first_member', JText::_('EB_ONLY_SHOW_FOR_FIRST_GROUP_MEMBER'), JText::_('EB_ONLY_SHOW_FOR_FIRST_GROUP_MEMBER_EXPLAIN')); ?>
				</label>
				<div class="controls">
					<?php echo EventbookingHelperHtml::getBooleanInput('only_show_for_first_member', $this->item->only_show_for_first_member); ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">
					<?php echo EventbookingHelperHtml::getFieldLabel('only_require_for_first_member', JText::_('EB_ONLY_REQUIRE_FOR_FIRST_GROUP_MEMBER'), JText::_('EB_ONLY_REQUIRE_FOR_FIRST_GROUP_MEMBER_EXPLAIN')); ?>
				</label>
				<div class="controls">
					<?php echo EventbookingHelperHtml::getBooleanInput('only_require_for_first_member', $this->item->only_require_for_first_member); ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">
					<?php echo EventbookingHelperHtml::getFieldLabel('show_on_registrants', JText::_('EB_SHOW_ON_REGISTRANTS'), JText::_('EB_SHOW_ON_REGISTRANTS_EXPLAIN')); ?>
				</label>
				<div class="controls">
					<?php echo EventbookingHelperHtml::getBooleanInput('show_on_registrants', $this->item->show_on_registrants); ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">
					<?php echo EventbookingHelperHtml::getFieldLabel('hide_on_email', JText::_('EB_HIDE_ON_EMAIL'), JText::_('EB_HIDE_ON_EMAIL_EXPLAIN')); ?>
				</label>
				<div class="controls">
					<?php echo EventbookingHelperHtml::getBooleanInput('hide_on_email', $this->item->hide_on_email); ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">
					<?php echo EventbookingHelperHtml::getFieldLabel('hide_on_export', JText::_('EB_HIDE_ON_EXPORT'), JText::_('EB_HIDE_ON_EXPORT_EXPLAIN')); ?>
				</label>
				<div class="controls">
					<?php echo EventbookingHelperHtml::getBooleanInput('hide_on_export', $this->item->hide_on_export); ?>
				</div>
			</div>

			<?php
				if ($this->item->id && in_array($this->item->display_in, array(0, 1, 2, 3)))
				{
				?>
					<div class="control-group">
						<label class="control-label">
							<?php echo EventbookingHelperHtml::getFieldLabel('receive_confirmation_email', JText::_('EB_RECEIVE_CONFIRMATION_EMAIL'), JText::_('EB_RECEIVE_CONFIRMATION_EMAIL_EXPLAIN')); ?>
						</label>
						<div class="controls">
							<?php echo EventbookingHelperHtml::getBooleanInput('receive_confirmation_email', $this->item->receive_confirmation_email); ?>
						</div>
					</div>
				<?php
				}
			?>
			<div class="control-group">
				<label class="control-label">
					<?php echo  JText::_('EB_DESCRIPTION'); ?>
				</label>
				<div class="controls">
					<textarea rows="5" cols="50" name="description" class="input-xlarge"><?php echo $this->item->description;?></textarea>
				</div>
			</div>
			<?php
			if (isset($this->lists['field_mapping']))
			{
			?>
				<div class="control-group">
					<label class="control-label">
						<?php echo EventbookingHelperHtml::getFieldLabel('field_mapping', JText::_('EB_FIELD_MAPPING'), JText::_('EB_FIELD_MAPPING_EXPLAIN')); ?>
					</label>
					<div class="controls">
						<?php echo $this->lists['field_mapping'] ; ?>
					</div>
				</div>
			<?php
			}
			?>
		</fieldset>
	</div>
	<div class="span6">
		<fieldset class="form-horizontal">
			<legend><?php echo JText::_('EB_FIELD_SETTINGS'); ?></legend>
			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('EB_FIELD_TYPE'); ?>
				</label>
				<div class="controls">
					<?php echo $this->lists['fieldtype']; ?>
				</div>
			</div>
			<div class="control-group eb-field eb-list">
				<label class="control-label">
					<?php echo JText::_('EB_MULTIPLE'); ?>
				</label>
				<div class="controls">
					<?php echo EventbookingHelperHtml::getBooleanInput('multiple', $this->item->multiple); ?>
				</div>
			</div>
			<div class="control-group eb-field eb-list eb-checkboxes eb-radio">
				<label class="control-label">
					<?php echo EventbookingHelperHtml::getFieldLabel('values', JText::_('EB_VALUES'), JText::_('EB_EACH_ITEM_LINE')); ?>
				</label>
				<div class="controls">
					<textarea rows="5" cols="50" name="values" class="input-xlarge"><?php echo $this->item->values; ?></textarea>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">
					<?php echo EventbookingHelperHtml::getFieldLabel('default_values', JText::_('EB_DEFAULT_VALUES'), JText::_('EB_EACH_ITEM_LINE')); ?>
				</label>
				<div class="controls">
					<textarea rows="5" cols="50" name="default_values" class="input-xlarge"><?php echo $this->item->default_values; ?></textarea>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?php echo JText::_('EB_FEE_FIELD') ; ?></label>
				<div class="controls">
					<?php echo EventbookingHelperHtml::getBooleanInput('fee_field', $this->item->fee_field); ?>
				</div>
			</div>
			<div class="control-group eb-field eb-list eb-checkboxes eb-radio">
				<label class="control-label">
					<?php echo EventbookingHelperHtml::getFieldLabel('fee_values', JText::_('EB_FEE_VALUES'), JText::_('EB_EACH_ITEM_LINE')); ?>
				</label>
				<div class="controls">
					<textarea rows="5" cols="50" name="fee_values" class="input-xlarge"><?php echo $this->item->fee_values; ?></textarea>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">
					<?php echo EventbookingHelperHtml::getFieldLabel('fee_formula', JText::_('EB_FEE_FORMULA'), JText::_('EB_FEE_FORMULA_EXPLAIN')); ?>
				</label>
				<div class="controls">
					<input type="text" class="inputbox" size="50" name="fee_formula" value="<?php echo $this->item->fee_formula ; ?>" />
				</div>
			</div>

			<div class="control-group eb-field eb-list eb-radio eb-checkboxes">
				<label class="control-label">
					<?php echo EventbookingHelperHtml::getFieldLabel('quantity_field', JText::_('EB_QUANTITY_FIELD')); ?>
				</label>
				<div class="controls">
					<?php echo EventbookingHelperHtml::getBooleanInput('quantity_field', $this->item->quantity_field); ?>
				</div>
			</div>
			<div class="control-group eb-field eb-list eb-radio eb-checkboxes">
				<label class="control-label">
					<?php echo EventbookingHelperHtml::getFieldLabel('quantity_values', JText::_('EB_QUANITY_VALUES')); ?>
				</label>
				<div class="controls">
					<textarea rows="5" cols="50" name="quantity_values" class="input-xlarge"><?php echo $this->item->quantity_values; ?></textarea>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('EB_DEPEND_ON_FIELD');?>
				</label>
				<div class="controls">
					<?php echo $this->lists['depend_on_field_id']; ?>
				</div>
			</div>
			<div class="control-group" id="depend_on_options_container" style="display: <?php echo $this->item->depend_on_field_id ? '' : 'none'; ?>">
				<label class="control-label">
					<?php echo JText::_('EB_DEPEND_ON_OPTIONS');?>
				</label>
				<div class="controls" id="options_container">
					<?php
					if (count($this->dependOptions))
					{
						?>
						<table cellspacing="3" cellpadding="3" width="100%">
							<?php
							$optionsPerLine = 3;
							for ($i = 0 , $n = count($this->dependOptions) ; $i < $n ; $i++)
							{
								$value = $this->dependOptions[$i] ;
								if ($i % $optionsPerLine == 0) {
									?>
									<tr>
									<?php
								}
								?>
								<td>
									<input class="inputbox" value="<?php echo $value; ?>" type="checkbox" name="depend_on_options[]" <?php if (in_array($value, $this->dependOnOptions)) echo 'checked="checked"'; ?>><?php echo $value;?>
								</td>
								<?php
								if (($i+1) % $optionsPerLine == 0)
								{
									?>
									</tr>
									<?php
								}
							}
							if ($i % $optionsPerLine != 0)
							{
								$colspan = $optionsPerLine - $i % $optionsPerLine ;
								?>
								<td colspan="<?php echo $colspan; ?>">&nbsp;</td>
								</tr>
							<?php
							}
							?>
						</table>
						<?php
					}
					?>
				</div>
			</div>
		</fieldset>
		<fieldset class="form-horizontal">
			<legend><?php echo JText::_('EB_DISPLAY_SETTINGS'); ?></legend>
			<div class="control-group">
				<label class="control-label">
					<?php echo  JText::_('EB_CSS_CLASS'); ?>
				</label>
				<div class="controls">
					<input class="text_area" type="text" name="css_class" id="css_class" size="10" maxlength="250" value="<?php echo $this->item->css_class;?>" />
				</div>
			</div>
			<div class="control-group eb-field eb-text eb-textarea">
				<label class="control-label">
					<?php echo  JText::_('EB_PLACE_HOLDER'); ?>
				</label>
				<div class="controls">
					<input class="text_area" type="text" name="place_holder" id="place_holder" size="50" maxlength="250" value="<?php echo $this->item->place_holder;?>" />
				</div>
			</div>
			<div class="control-group eb-field eb-text eb-checkboxes eb-radio eb-list">
				<label class="control-label">
					<?php echo  JText::_('EB_SIZE'); ?>
				</label>
				<div class="controls">
					<input class="text_area" type="text" name="size" id="size" size="10" maxlength="250" value="<?php echo $this->item->size;?>" />
				</div>
			</div>
			<div class="control-group eb-field eb-text eb-textarea">
				<label class="control-label">
					<?php echo  JText::_('EB_MAX_LENGTH'); ?>
				</label>
				<div class="controls">
					<input class="text_area" type="text" name="max_length" id="max_lenth" size="50" maxlength="250" value="<?php echo $this->item->max_length;?>" />
				</div>
			</div>
			<div class="control-group eb-field eb-textarea">
				<label class="control-label">
					<?php echo  JText::_('EB_ROWS'); ?>
				</label>
				<div class="controls">
					<input class="text_area" type="text" name="rows" id="rows" size="10" maxlength="250" value="<?php echo $this->item->rows;?>" />
				</div>
			</div>
			<div class="control-group eb-field eb-textarea">
				<label class="control-label">
					<?php echo  JText::_('EB_COLS'); ?>
				</label>
				<div class="controls">
					<input class="text_area" type="text" name="cols" id="cols" size="10" maxlength="250" value="<?php echo $this->item->cols;?>" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">
					<?php echo  JText::_('EB_EXTRA'); ?>
				</label>
				<div class="controls">
					<input class="text_area" type="text" name="extra_attributes" id="extra" size="40" maxlength="250" value="<?php echo $this->item->extra_attributes;?>" />
				</div>
			</div>
		</fieldset>
	</div>
<?php
if ($translatable)
{
	echo JHtml::_('bootstrap.endTab');
	echo JHtml::_('bootstrap.addTab', 'field', 'translation-page', JText::_('EB_TRANSLATION', true));
	echo JHtml::_('bootstrap.startTabSet', 'field-translation', array('active' => 'translation-page-'.$this->languages[0]->sef));
	foreach ($this->languages as $language)
	{
		$sef = $language->sef;
		echo JHtml::_('bootstrap.addTab', 'field-translation', 'translation-page-' . $sef, $language->title . ' <img src="' . JUri::root() . 'media/com_eventbooking/flags/' . $sef . '.png" />');
		?>
		<div class="control-group">
			<label class="control-label">
				<?php echo  JText::_('EB_TITLE'); ?>
			</label>
			<div class="controls">
				<input class="input-xlarge" type="text" name="title_<?php echo $sef; ?>" id="title_<?php echo $sef; ?>" size="" maxlength="250" value="<?php echo $this->item->{'title_'.$sef}; ?>" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('EB_DESCRIPTION'); ?>
			</label>
			<div class="controls">
				<textarea rows="5" cols="50" name="description_<?php echo $sef; ?>"><?php echo $this->item->{'description_'.$sef};?></textarea>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('EB_VALUES'); ?>
			</label>
			<div class="controls">
				<textarea rows="5" cols="50" name="values_<?php echo $sef; ?>"><?php echo $this->item->{'values_'.$sef}; ?></textarea>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('EB_DEFAULT_VALUES'); ?>
			</label>
			<div class="controls">
				<textarea rows="5" cols="50" name="default_values_<?php echo $sef; ?>"><?php echo $this->item->{'default_values_'.$sef}; ?></textarea>
			</div>
		</div>
		<?php
		echo JHtml::_('bootstrap.endTab');
	}
	echo JHtml::_('bootstrap.endTabSet');
	echo JHtml::_('bootstrap.endTab');
	echo JHtml::_('bootstrap.endTabSet');
}
?>
	<div class="clearfix"></div>
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />	
	<?php echo JHtml::_( 'form.token' ); ?>

	<script type="text/javascript">
        var siteUrl = "<?php echo JUri::base(); ?>";

        (function($)
        {
			$(document).ready(function(){				
				var validateEngine = <?php  echo EventbookingHelper::validateEngine(); ?>;
				$("input[name='required']").bind( "click", function() {
					var change = 1;
					validateRules(change);
				});
				$( "#datatype_validation" ).bind( "change", function() {	
					var change = 1;				
					validateRules(change);
				});

				$( "#fieldtype" ).bind( "change", function() {
						changeFiledType($(this).val());
				});
				
				changeFiledType('<?php echo $this->item->fieldtype;  ?>');
				function validateRules(change)
				{			
					var validationString;
					if ($("input[name='name']").val() == 'email')
					{
						//Hardcode the validation rule for email
						validationString = 'validate[required,custom[email],ajax[ajaxEmailCall]]';
					}	
					else 
					{
						var validateType = parseInt($('#datatype_validation').val());
						validationString = validateEngine[validateType];
						var required = $("input[name='required']:checked").val();					
						if (required == 1)
						{
							if (validationString == '')
							{
								validationString = 'validate[required]';
							}
							else 
							{
								if (validationString.indexOf('required') == -1)
								{
									validationString = [validationString.slice(0, 9), 'required,', validationString.slice(9)].join('');
								}
							}
						}
						else 
						{						
							if (validationString == 'validate[required]')
							{
								validationString = '';
							}
							else 
							{							
								validationString = validationString.replace('validate[required', 'validate[');
							}
						}		
					}							
					if(change == 1)
					{																
						$("input[name='validation_rules']").val(validationString);
					}
				}			
				validateRules();	
				function changeFiledType(fieldType)
				{			
					if (fieldType == '')
					{
						$('div.eb-field').hide();
					}
					else 
					{
						var cssClass = '.eb-' + fieldType.toLowerCase();	
						$('div.eb-field').show();
						$('div.eb-field').not(cssClass).hide();
					}																												
				}
			});
		})(jQuery);
        
		function checkFieldName() 
		{
			var form = document.adminForm ;
			var name = form.name.value ;
			var oldValue = name ;			
			name = name.replace('eb_','');			
			name = name.replace(/[^a-zA-Z0-9_]*/ig, '');
			form.name.value = name;							
		}

        function updateDependOnOptions()
        {
            (function($) {
                var fieldId = $('#depend_on_field_id').val();
                if (fieldId > 0) {
                    $.ajax({
                        type: 'POST',
                        url: siteUrl + 'index.php?option=com_eventbooking&view=field&format=raw&field_id=' + fieldId,
                        dataType: 'html',
                        success: function(msg, textStatus, xhr) {
                            $('#options_container').html(msg);
                            $('#depend_on_options_container').show();
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            alert(textStatus);
                        }
                    });

                }
                else
                {
                    $('#options_container').html('');
                    $('#depend_on_options_container').hide();
                }
            })(jQuery);
        }
	</script>
</form>