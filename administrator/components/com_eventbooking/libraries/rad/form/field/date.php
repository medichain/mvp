<?php

class RADFormFieldDate extends RADFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	protected $type = 'Date';

	/**
	 * Method to get the field input markup.
	 *
	 * @param EventbookingHelperBootstrap $bootstrapHelper
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput($bootstrapHelper = null)
	{
		$config       = EventbookingHelper::getConfig();
		$dateFormat   = $config->date_field_format ? $config->date_field_format : '%Y-%m-%d';
		$iconCalendar = $bootstrapHelper ? $bootstrapHelper->getClassMapping('icon-calendar') : 'icon-calendar';

		try
		{
			if (version_compare(JVERSION, '3.7.0', 'ge'))
			{
				return str_replace('icon-calendar', $iconCalendar, JHtml::_('calendar', $this->value, $this->name, $this->name, $dateFormat, $this->attributes));
			}
			else
			{
				$attributes = $this->buildAttributes();

				return str_replace('icon-calendar', $iconCalendar, JHtml::_('calendar', $this->value, $this->name, $this->name, $dateFormat, ".$attributes."));
			}
		}
		catch (Exception $e)
		{
			if (version_compare(JVERSION, '3.7.0', 'ge'))
			{
				return str_replace('icon-calendar', $iconCalendar, JHtml::_('calendar', '', $this->name, $this->name, $dateFormat, $this->attributes)) . ' Value <strong>' . $this->value . '</strong> is invalid. Please correct it with format YYYY-MM-DD';
			}
			else
			{
				$attributes = $this->buildAttributes();

				return str_replace('icon-calendar', $iconCalendar, JHtml::_('calendar', '', $this->name, $this->name, $dateFormat, ".$attributes.")) . ' Value <strong>' . $this->value . '</strong> is invalid. Please correct it with format YYYY-MM-DD';
			}

		}
	}
}

;
