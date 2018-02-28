<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

$bootstrapHelper = new EventbookingHelperBootstrap($this->config->twitter_bootstrap_version);
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');
$iconCalendar      = $bootstrapHelper->getClassMapping('icon-calendar');
?>
<div class="<?php echo $controlGroupClass;?>">
	<div class="<?php echo $controlLabelClass; ?>"><?php echo JText::_('EB_TITLE') ; ?></div>
	<div class="<?php echo $controlsClass; ?>">
		<input type="text" name="title" value="<?php echo $this->item->title; ?>" class="input-xlarge" size="70" />
	</div>
</div>
<div class="<?php echo $controlGroupClass;?>">
	<div class="<?php echo $controlLabelClass; ?>"><?php echo JText::_('EB_ALIAS') ; ?></div>
	<div class="<?php echo $controlsClass; ?>">
		<input type="text" name="alias" value="<?php echo $this->item->alias; ?>" class="input-xlarge" size="70" />
	</div>
</div>
	<div class="<?php echo $controlGroupClass;?>">
	<div class="<?php echo $controlLabelClass; ?>"><?php echo JText::_('EB_MAIN_EVENT_CATEGORY') ; ?></div>
	<div class="<?php echo $controlsClass; ?>">
		<div style="float: left;"><?php echo $this->lists['main_category_id'] ; ?></div>
	</div>
</div>
<div class="<?php echo $controlGroupClass;?>">
	<div class="<?php echo $controlLabelClass; ?>"><?php echo JText::_('EB_ADDITIONAL_CATEGORIES') ; ?></div>
	<div class="<?php echo $controlsClass; ?>">
		<div style="float: left;"><?php echo $this->lists['category_id'] ; ?></div>
		<div style="float: left; padding-top: 25px; padding-left: 10px;">Press <strong>Ctrl</strong> to select multiple categories</div>
	</div>
</div>
<div class="<?php echo $controlGroupClass;?>">
	<div class="<?php echo $controlLabelClass; ?>"><?php echo JText::_('EB_THUMB_IMAGE') ; ?></div>
	<div class="<?php echo $controlsClass; ?>">
		<input type="file" class="inputbox" name="thumb_image" size="60" />
		<?php
		if ($this->item->thumb && file_exists(JPATH_ROOT . '/media/com_eventbooking/images/thumbs/' . $this->item->thumb))
		{
			$baseUri = JUri::base(true);

			if ($this->item->image && file_exists(JPATH_ROOT . '/' . $this->item->image))
			{
				$largeImageUri = $baseUri . '/' . $this->item->image;
			}
			elseif (file_exists(JPATH_ROOT . '/media/com_eventbooking/images/' . $this->item->thumb))
			{
				$largeImageUri = $baseUri . '/media/com_eventbooking/images/' . $this->item->thumb;
			}
			else
			{
				$largeImageUri = $baseUri . '/media/com_eventbooking/images/thumbs/' . $this->item->thumb;
			}
		?>
			<a href="<?php echo $largeImageUri; ?>" class="modal"><img src="<?php echo $baseUri . '/media/com_eventbooking/images/thumbs/' . $this->item->thumb; ?>" class="img_preview" /></a>
			<input type="checkbox" name="del_thumb" value="1" /><?php echo JText::_('EB_DELETE_CURRENT_THUMB'); ?>
		<?php
		}
		?>
	</div>
</div>
<div class="<?php echo $controlGroupClass;?>">
	<div class="<?php echo $controlLabelClass; ?>"><?php echo JText::_('EB_LOCATION') ; ?></div>
	<div class="<?php echo $controlsClass; ?>">
		<?php
		echo $this->lists['location_id'];
		if (JFactory::getUser()->authorise('eventbooking.addlocation', 'com_eventbooking'))
		{
		?>
			<button type="button" class="btn btn-small btn-success eb-colorbox-addlocation" href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=location&layout=popup&tmpl=component&Itemid='.$this->Itemid)?>"><span class="icon-new icon-white"></span><?php echo JText::_('EB_ADD_NEW_LOCATION') ; ?></button>
		<?php
		}
		?>
	</div>
</div>
<div class="<?php echo $controlGroupClass;?>">
	<div class="<?php echo $controlLabelClass; ?>">
		<?php echo JText::_('EB_EVENT_START_DATE'); ?>
	</div>
	<div class="<?php echo $controlsClass; ?>">
		<?php echo str_replace('icon-calendar', $iconCalendar, JHtml::_('calendar', ($this->item->event_date == $this->nullDate) ? '' : JHtml::_('date', $this->item->event_date, 'Y-m-d', null), 'event_date', 'event_date')); ?>
		<?php echo $this->lists['event_date_hour'].' '.$this->lists['event_date_minute']; ?>
	</div>
</div>
<div class="<?php echo $controlGroupClass;?>">
	<div class="<?php echo $controlLabelClass; ?>">
		<?php echo JText::_('EB_EVENT_END_DATE'); ?>
	</div>
	<div class="<?php echo $controlsClass; ?>">
		<?php echo str_replace('icon-calendar', $iconCalendar, JHtml::_('calendar', ($this->item->event_end_date == $this->nullDate) ? '' : JHtml::_('date', $this->item->event_end_date, 'Y-m-d', null), 'event_end_date', 'event_end_date')); ?>
		<?php echo $this->lists['event_end_date_hour'].' '.$this->lists['event_end_date_minute'] ; ?>
	</div>
</div>
<div class="<?php echo $controlGroupClass;?>">
	<div class="<?php echo $controlLabelClass; ?>">
		<?php echo JText::_('EB_REGISTRATION_START_DATE'); ?>
	</div>
	<div class="<?php echo $controlsClass; ?>">
		<?php echo str_replace('icon-calendar', $iconCalendar, JHtml::_('calendar', ($this->item->registration_start_date == $this->nullDate) ? '' : JHtml::_('date', $this->item->registration_start_date, 'Y-m-d', null), 'registration_start_date', 'registration_start_date')); ?>
		<?php echo $this->lists['registration_start_hour'].' '.$this->lists['registration_start_minute'] ; ?>
	</div>
</div>
<div class="<?php echo $controlGroupClass;?>">
	<div class="<?php echo $controlLabelClass; ?>">
		<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_CUT_OFF_DATE' );?>::<?php echo JText::_('EB_CUT_OFF_DATE_EXPLAIN'); ?>"><?php echo JText::_('EB_CUT_OFF_DATE') ; ?></span>
	</div>
	<div class="<?php echo $controlsClass; ?>">
		<?php echo str_replace('icon-calendar', $iconCalendar, JHtml::_('calendar', ($this->item->cut_off_date == $this->nullDate) ? '' : JHtml::_('date', $this->item->cut_off_date, 'Y-m-d', null), 'cut_off_date', 'cut_off_date')); ?>
		<?php echo $this->lists['cut_off_hour'].' '.$this->lists['cut_off_minute']; ?>
	</div>
</div>
<div class="<?php echo $controlGroupClass;?>">
	<div class="<?php echo $controlLabelClass; ?>">
		<?php echo JText::_('EB_PRICE'); ?>
	</div>
	<div class="<?php echo $controlsClass; ?>">
		<input type="text" name="individual_price" id="individual_price" class="input-mini" size="10" value="<?php echo $this->item->individual_price; ?>" />
	</div>
</div>
<div class="<?php echo $controlGroupClass;?>">
	<div class="<?php echo $controlLabelClass; ?>">
		<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_PRICE_TEXT' );?>::<?php echo JText::_('EB_PRICE_TEXT_EXPLAIN'); ?>"><?php echo JText::_('EB_PRICE_TEXT'); ?></span>
	</div>
	<div class="<?php echo $controlsClass; ?>">
		<input type="text" name="price_text" id="price_text" class="input-xlarge" value="<?php echo $this->item->price_text; ?>" />
	</div>
</div>
<div class="<?php echo $controlGroupClass;?>">
	<div class="<?php echo $controlLabelClass; ?>">
		<?php echo JText::_('EB_TAX_RATE'); ?>
	</div>
	<div class="<?php echo $controlsClass; ?>">
		<input type="text" name="tax_rate" id="tax_rate" class="input-small" size="10" value="<?php echo $this->item->tax_rate; ?>" />
	</div>
</div>
<div class="<?php echo $controlGroupClass;?>">
	<div class="<?php echo $controlLabelClass; ?>">
		<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_EVENT_CAPACITY' );?>::<?php echo JText::_('EB_CAPACITY_EXPLAIN'); ?>"><?php echo JText::_('EB_CAPACITY'); ?></span>
	</div>
	<div class="<?php echo $controlsClass; ?>">
		<input type="text" name="event_capacity" id="event_capacity" class="input-mini" size="10" value="<?php echo $this->item->event_capacity; ?>" />
	</div>
</div>
<div class="<?php echo $controlGroupClass;?>">
	<div class="<?php echo $controlLabelClass; ?>"><?php echo JText::_('EB_REGISTRATION_TYPE'); ?></div>
	<div class="<?php echo $controlsClass; ?>">
		<?php echo $this->lists['registration_type'] ; ?>
	</div>
</div>
<div class="<?php echo $controlGroupClass;?>">
	<div class="<?php echo $controlLabelClass; ?>">
		<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_CUSTOM_REGISTRATION_HANDLE_URL' );?>::<?php echo JText::_('EB_CUSTOM_REGISTRATION_HANDLE_URL_EXPLAIN'); ?>"><?php echo JText::_('EB_CUSTOM_REGISTRATION_HANDLE_URL'); ?></span>
	</div>
	<div class="<?php echo $controlsClass; ?>">
		<input type="text" name="registration_handle_url" id="registration_handle_url"
		       class="input-xxlarge" size="10" value="<?php echo $this->item->registration_handle_url; ?>" />
	</div>
</div>
<div class="<?php echo $controlGroupClass;?>">
	<div class="<?php echo $controlLabelClass; ?>">
		<span class="editlinktip hasTip" title="<?php echo JText::_( 'EB_MAX_NUMBER_REGISTRANTS' );?>::<?php echo JText::_('EB_MAX_NUMBER_REGISTRANTS_EXPLAIN'); ?>"><?php echo JText::_('EB_MAX_NUMBER_REGISTRANTS'); ?></span>
	</div>
	<div class="<?php echo $controlsClass; ?>">
		<input type="text" name="max_group_number" id="max_group_number" class="input-mini" size="10" value="<?php echo $this->item->max_group_number; ?>" />
	</div>
</div>
<?php
if (EventbookingHelperAcl::canChangeEventStatus($this->item->id))
{
?>
	<div class="<?php echo $controlGroupClass;?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo JText::_('EB_PUBLISHED'); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php
				if (isset($this->lists['published']))
				{
					echo $this->lists['published'];
				}
				else
				{
					echo EventbookingHelperHtml::getBooleanInput('published', $this->item->published);
				}
			?>
		</div>
	</div>
<?php
}
?>
<div class="<?php echo $controlGroupClass;?>">
	<div class="<?php echo $controlLabelClass; ?>">
		<?php echo  JText::_('EB_SHORT_DESCRIPTION'); ?>
	</div>
	<div class="<?php echo $controlsClass; ?>">
		<?php echo $editor->display( 'short_description',  $this->item->short_description , '100%', '180', '90', '6' ) ; ?>
	</div>
</div>
<div class="<?php echo $controlGroupClass;?>">
	<div class="<?php echo $controlLabelClass; ?>">
		<?php echo  JText::_('EB_DESCRIPTION'); ?>
	</div>
	<div class="<?php echo $controlsClass; ?>">
		<?php echo $editor->display( 'description',  $this->item->description , '100%', '250', '90', '10' ) ; ?>
	</div>
</div>
<?php
if ($this->showCaptcha)
{
?>
	<div class="<?php echo $controlGroupClass;?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo  JText::_('EB_CAPTCHA'); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php echo $this->captcha; ?>
		</div>
	</div>
<?php
}
