<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined( '_JEXEC' ) or die ;
?>
<div class="span6">
	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('EB_CALENDAR'); ?></legend>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('calendar_theme', JText::_('EB_CALENDAR_THEME')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['calendar_theme']; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('calendar_start_date', JText::_('EB_CALENDAR_START_DATE')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['calendar_start_date']; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('activate_weekly_calendar_view', JText::_('EB_ACTIVATE_WEEKLY_CALENDAR_VIEW')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('activate_weekly_calendar_view', $config->activate_weekly_calendar_view); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('activate_daily_calendar_view', JText::_('EB_ACTIVATE_DAILY_CALENDAR_VIEW')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('activate_daily_calendar_view', $config->activate_daily_calendar_view); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_thumb_in_calendar', JText::_('EB_SHOW_EVENT_IMAGE_IN_CALENDAR')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_thumb_in_calendar', $config->show_thumb_in_calendar); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_event_time', JText::_('EB_SHOW_EVENT_TIME'), JText::_('EB_SHOW_EVENT_TIME_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_event_time', $config->show_event_time); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('display_event_in_tooltip', JText::_('EB_DISPLAY_EVENT_IN_TOOLTIP'), JText::_('EB_DISPLAY_EVENT_IN_TOOLTIP_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('display_event_in_tooltip', $config->display_event_in_tooltip); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_calendar_legend', JText::_('EB_SHOW_CALENDAR_LEGEND')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_calendar_legend', $config->show_calendar_legend); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_multiple_days_event_in_calendar', JText::_('EB_SHOW_MULTIPLE_DAYS_EVENT_IN_CALENDAR')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_multiple_days_event_in_calendar', $config->show_multiple_days_event_in_calendar); ?>
			</div>
		</div>
	</fieldset>
	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('EB_CATEGORIES'); ?></legend>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('number_categories', JText::_('EB_CATEGORIES_PER_PAGE')); ?>
			</div>
			<div class="controls">
				<input type="text" name="number_categories" class="inputbox" value="<?php echo $config->number_categories; ?>" size="10" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_empty_cat', JText::_('EB_SHOW_EMPTY_CATEGORIES')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_empty_cat', $config->show_empty_cat); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_number_events', JText::_('EB_SHOW_NUMBER_EVENTS')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_number_events', $config->show_number_events); ?>
			</div>
		</div>
	</fieldset>
	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('EB_EVENT_DETAIL'); ?></legend>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_image_in_event_detail', JText::_('EB_SHOW_EVENT_IMAGE_IN_EVENT_DETAIL')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_image_in_event_detail', $config->get('show_image_in_event_detail', 1)); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('display_large_image', JText::_('EB_DISPLAY_LARGE_IMAGE'), JText::_('EB_DISPLAY_LARGE_IMAGE_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('display_large_image', $config->display_large_image); ?>
			</div>
		</div>
		<div class="control-group" data-showon='<?php echo EventbookingHelperHtml::renderShowOn(array('display_large_image' => '1')); ?>'>
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('large_image_width', JText::_('EB_LARGE_IMAGE_WIDTH'), JText::_('EB_LARGE_IMAGE_WIDTH_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="large_image_width" class="input-small" value="<?php echo $config->large_image_width ; ?>" />
			</div>
		</div>
		<div class="control-group" data-showon='<?php echo EventbookingHelperHtml::renderShowOn(array('display_large_image' => '1')); ?>'>
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('large_image_height', JText::_('EB_LARGE_IMAGE_HEIGHT'), JText::_('EB_LARGE_IMAGE_HEIGHT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="large_image_height" class="input-small" value="<?php echo $config->large_image_height ; ?>" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_fb_like_button', JText::_('EB_SHOW_FACEBOOK_LIKE_BUTTON'), JText::_('EB_SHOW_FACEBOOKING_LIKE_BUTTON_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_fb_like_button', $config->show_fb_like_button); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_location_info_in_event_details', JText::_('EB_SHOW_LOCATION_INFO_ON_EVENT_DETAILS'), JText::_('EB_SHOW_LOCATION_INFO_ON_EVENT_DETAILS_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_location_info_in_event_details', $config->show_location_info_in_event_details); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_invite_friend', JText::_('EB_SHOW_INVITE_FRIEND'), JText::_('EB_SHOW_INVITE_FRIEND_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_invite_friend', $config->show_invite_friend); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_save_to_personal_calendar', JText::_('EB_SHOW_SAVE_TO_PERSONAL_CALENDAR'), JText::_('EB_SHOW_SAVE_TO_PERSONAL_CALENDAR_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_save_to_personal_calendar', $config->show_save_to_personal_calendar); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_social_bookmark', JText::_('EB_SHOW_SOCIAL_BOOKMARK'), JText::_('EB_SHOW_SOCIAL_BOOKMARK_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_social_bookmark', $config->show_social_bookmark); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('social_sharing_buttons', JText::_('EB_SOCIAL_SHARING_BUTTONS'), JText::_('EB_SOCIAL_SHARING_BUTTONS_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['social_sharing_buttons']; ?>
			</div>
		</div>
	</fieldset>
	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('EB_MISC'); ?></legend>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('submit_event_form_layout', JText::_('EB_FRONTEND_SUBMIT_EVENT_FORM_LAYOUT')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['submit_event_form_layout']; ?>
			</div>
		</div>
		<?php
		if (JPluginHelper::isEnabled('eventbooking', 'tickettypes'))
		{
			?>
			<div class="control-group">
				<div class="control-label">
					<?php echo EventbookingHelperHtml::getFieldLabel('display_ticket_types', JText::_('EB_DISPLAY_TICKET_TYPES'), JText::_('EB_DISPLAY_TICKET_TYPES_EXPLAIN')); ?>
				</div>
				<div class="controls">
					<?php echo EventbookingHelperHtml::getBooleanInput('display_ticket_types', $config->display_ticket_types); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo EventbookingHelperHtml::getFieldLabel('calculate_number_registrants_base_on_tickets_quantity', JText::_('EB_NUMBER_REGISTRANTS_CALCULATION'), JText::_('EB_NUMBER_REGISTRANTS_CALCULATION_EXPLAIN')); ?>
				</div>
				<div class="controls">
					<?php echo EventbookingHelperHtml::getBooleanInput('calculate_number_registrants_base_on_tickets_quantity', $config->calculate_number_registrants_base_on_tickets_quantity); ?>
				</div>
			</div>
			<?php
		}
		?>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('process_plugin', JText::_('EB_PROCESS_CONTENT_PLUGIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('process_plugin', $config->get('process_plugin', 1)); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_attachment_in_frontend', JText::_('EB_SHOW_ATTACHMENT'), JText::_('EB_SHOW_ATTACHMENT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_attachment_in_frontend', $config->show_attachment_in_frontend); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('display_message_for_full_event', JText::_('EB_DISPLAY_MESSAGE_FOR_FULL_EVENT'), JText::_('EB_DISPLAY_MESSAGE_FOR_FULL_EVENT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('display_message_for_full_event', $config->display_message_for_full_event); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_print_button', JText::_('EB_SHOW_PRINT_BUTTON'), JText::_('EB_SHOW_PRINT_BUTTON_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_print_button', $config->get('show_print_button', 1)); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_event_location_in_email', JText::_('EB_SHOW_LOCATION_IN_EMAIL')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_event_location_in_email', $config->show_event_location_in_email); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_event_date', JText::_('EB_SHOW_EVENT_DATE'), JText::_('EB_SHOW_EVENT_DATE_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_event_date', $config->show_event_date); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_coupon_code_in_registrant_list', JText::_('EB_SHOW_COUPON_CODE'), JText::_('EB_SHOW_COUPON_CODE_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_coupon_code_in_registrant_list', $config->show_coupon_code_in_registrant_list); ?>
			</div>
		</div>
	</fieldset>
</div>

<div class="span6">
	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('EB_EVENTS'); ?></legend>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('number_events', JText::_('EB_EVENTS_PER_PAGE')); ?>
			</div>
			<div class="controls">
				<input type="text" name="number_events" class="inputbox" value="<?php echo $config->number_events; ?>" size="10" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('order_events', JText::_('EB_EVENT_ORDER_BY')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['order_events'] ; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('order_direction', JText::_('EB_ORDER_DIRECTION')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['order_direction'] ; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('display_featured_events_on_top', JText::_('EB_DISPLAY_FEATURED_EVENTS_ON_TOP'), JText::_('EB_DISPLAY_FEATURED_EVENTS_ON_TOP_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('display_featured_events_on_top', $config->display_featured_events_on_top); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('register_buttons_position', JText::_('EB_REGISTER_BUTTONS_POSITION')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['register_buttons_position']; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('hide_detail_button', JText::_('EB_HIDE_DETAIL_BUTTON'), JText::_('EB_HIDE_DETAIL_BUTTON_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('hide_detail_button', $config->hide_detail_button); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_events_from_all_children_categories', JText::_('EB_SHOW_EVENTS_FROM_ALL_CHILDREN_CATEGORIES'), JText::_('EB_SHOW_EVENTS_FROM_ALL_CHILDREN_CATEGORIES_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_events_from_all_children_categories', $config->show_events_from_all_children_categories); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_event_custom_field_in_category_layout', JText::_('EB_SHOW_EVENT_CUSTOM_FIELDS_IN_CATEGORY_VIEW')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_event_custom_field_in_category_layout', $config->show_event_custom_field_in_category_layout); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_location_in_category_view', JText::_('EB_SHOW_LOCATION_IN_CATEGORY_VIEW')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_location_in_category_view', $config->show_location_in_category_view); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_cat_decription_in_table_layout', JText::_('EB_SHOW_CATEGORY_DESCRIPTION_IN_TABLE_LAYOUT')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_cat_decription_in_table_layout', $config->show_cat_decription_in_table_layout); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_image_in_table_layout', JText::_('EB_SHOW_EVENT_IMAGE_IN_TABLE_LAYOUT')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_image_in_table_layout', $config->show_image_in_table_layout); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_event_end_date_in_table_layout', JText::_('EB_SHOW_EVENT_END_DATE_IN_TABLE_LAYOUT')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_event_end_date_in_table_layout', $config->show_event_end_date_in_table_layout); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_price_in_table_layout', JText::_('EB_SHOW_PRICE_IN_TABLE_LAYOUT')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_price_in_table_layout', $config->show_price_in_table_layout); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('link_thumb_to_event_detail_page', JText::_('EB_LINK_THUMBNAIL_TO_EVENT_DETAIL'), JText::_('EB_LINK_THUMBNAIL_TO_EVENT_DETAIL_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('link_thumb_to_event_detail_page', $config->get('link_thumb_to_event_detail_page', 1)); ?>
			</div>
		</div>
	</fieldset>
	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('EB_EVENT_INFORMATION'); ?></legend>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_price_for_free_event', JText::_('EB_SHOW_PRICE_FOR_FREE_EVENT'), JText::_('EB_SHOW_PRICE_FOR_FREE_EVENT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_price_for_free_event', $config->show_price_for_free_event); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_discounted_price', JText::_('EB_SHOW_DISCOUNTED_PRICE'), JText::_('EB_SHOW_DISCOUNTED_PRICE_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_discounted_price', $config->show_discounted_price); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_capacity', JText::_('EB_SHOW_EVENT_CAPACITY')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['show_capacity']; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_registered', JText::_('EB_SHOW_NUMBER_REGISTERED_USERS')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_registered', $config->show_registered); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_available_place', JText::_('EB_SHOW_AVAILABLE_PLACES')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_available_place', $config->show_available_place); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_list_of_registrants', JText::_('EB_SHOW_LIST_OF_REGISTRANTS')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_list_of_registrants', $config->show_list_of_registrants); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_event_creator', JText::_('EB_SHOW_EVENT_CREATOR'), JText::_('EB_SHOW_EVENT_CREATOR_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_event_creator', $config->show_event_creator); ?>
			</div>
		</div>
	</fieldset>
</div>





