<?php
class RADFormFieldFile extends RADFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	protected $type = 'File';

	/**
	 * Method to instantiate the form field object.
	 *
	 * @param   JTable $row   the table object store form field definitions
	 * @param    mixed $value the initial value of the form field
	 */
	public function __construct($row, $value = null, $fieldSuffix = null)
	{
		parent::__construct($row, $value, $fieldSuffix);

		if ($row->size)
		{
			$this->attributes['size'] = $row->size;
		}
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput($bootstrapHelper = null)
	{
		$html = '<input type="button" value="' . JText::_('EB_SELECT_FILE') . '" id="button-file-' . $this->name . '" class="btn btn-primary" />';

		if ($this->value && file_exists(JPATH_ROOT . '/media/com_eventbooking/files/' . $this->value))
		{
			$html .= '<span class="eb-uploaded-file" id="uploaded-file-' . $this->name . '"><a href="' . JRoute::_('index.php?option=com_eventbooking&task=controller.download_file&file_name=' . $this->value) . '"><i class="icon-donwload"></i><strong>' . $this->value . '</strong></a></span>';
		}
		else
		{
			$html .= '<span class="eb-uploaded-file" id="uploaded-file-' . $this->name . '"></span>';
		}

		$html .= '<input type="hidden" name="' . $this->name . '"  value="' . $this->value . '" />';

		ob_start();
		?>
		<script language="javascript">
			new AjaxUpload('#button-file-<?php echo $this->name; ?>', {
				action: siteUrl + 'index.php?option=com_eventbooking&task=upload_file',
				name: 'file',
				autoSubmit: true,
				responseType: 'json',
				onSubmit: function (file, extension) {
					jQuery('#button-file-<?php echo $this->name; ?>').after('<span class="wait">&nbsp;<img src="<?php echo JUri::root(true);?>/media/com_eventbooking/ajax-loadding-animation.gif" alt="" /></span>');
					jQuery('#button-file-<?php echo $this->name; ?>').attr('disabled', true);
				},
				onComplete: function (file, json) {
					jQuery('#button-file-<?php echo $this->name; ?>').attr('disabled', false);
					jQuery('.error').remove();
					if (json['success'])
					{
						jQuery('#uploaded-file-<?php echo $this->name; ?>').html(file);
						jQuery('input[name="<?php echo $this->name; ?>"]').attr('value', json['file']);
					}
					if (json['error'])
					{
						jQuery('#button-file-<?php echo $this->name; ?>').after('<span class="error">' + json['error'] + '</span>');
					}

					jQuery('.wait').remove();
				}
			});
		</script>
		<?php
		$html .= ob_get_clean();

		return $html;
	}
}
