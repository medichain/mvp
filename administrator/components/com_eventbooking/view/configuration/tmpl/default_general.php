<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;
?>
<div class="span6">
	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('EB_GENERAL_SETTINGS'); ?></legend>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('download_id', JText::_('EB_DOWNLOAD_ID'), JText::_('EB_DOWNLOAD_ID_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="download_id" class="input-xlarge" value="<?php echo $config->get('download_id', ''); ?>" size="60" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('custom_field_by_category', JText::_('EB_CUSTOM_FIELD_BY_CATEGORY'), JText::_('EB_CUSTOM_FIELD_BY_CATEGORY_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('custom_field_by_category', $config->custom_field_by_category); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('load_bootstrap_css_in_frontend', JText::_('EB_LOAD_BOOTSTRAP_CSS_IN_FRONTEND'), JText::_('EB_LOAD_BOOTSTRAP_CSS_IN_FRONTEND_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('load_bootstrap_css_in_frontend', $config->get('load_bootstrap_css_in_frontend', 1)); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('twitter_bootstrap_version', JText::_('EB_TWITTER_BOOTSTRAP_VERSION'), JText::_('EB_TWITTER_BOOTSTRAP_VERSION_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['twitter_bootstrap_version'];?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('activate_recurring_event', JText::_('EB_ACTIVATE_RECURRING_EVENT'), JText::_('EB_ACTIVATE_RECURRING_EVENT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('activate_recurring_event', $config->activate_recurring_event); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_children_events_under_parent_event', JText::_('EB_SHOW_CHILDREN_EVENTS_UNDER_PARENT_EVENT'), JText::_('EB_SHOW_CHILDREN_EVENTS_UNDER_PARENT_EVENT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_children_events_under_parent_event', $config->show_children_events_under_parent_event); ?>
			</div>
		</div>
		<div class="control-group" data-showon='<?php echo EventbookingHelperHtml::renderShowOn(array('show_children_events_under_parent_event' => '1')); ?>'>
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('max_number_of_children_events', JText::_('EB_MAX_NUMBER_CHILDREN_EVENTS'), JText::_('EB_MAX_NUMBER_CHILDREN_EVENTS_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="max_number_of_children_events" class="input-small" value="<?php echo $config->get('max_number_of_children_events', 30); ?>" size="60" />
			</div>
		</div>

		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('use_https', JText::_('EB_ACTIVATE_HTTPS'), JText::_('EB_ACTIVATE_HTTPS_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('use_https', $config->use_https); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('hide_past_events', JText::_('EB_HIDE_PAST_EVENTS'), JText::_('EB_HIDE_PAST_EVENTS_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('hide_past_events', $config->hide_past_events); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_until_end_date', JText::_('EB_SHOW_UNTIL_END_DATE'), JText::_('EB_SHOW_UNTIL_END_DATE_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_until_end_date', $config->show_until_end_date); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('hide_past_events_from_events_dropdown', JText::_('EB_HIDE_PAST_EVENTS_FROM_DROPDOWN'), JText::_('EB_HIDE_PAST_EVENTS_FROM_DROPDOWN_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('hide_past_events_from_events_dropdown', $config->hide_past_events_from_events_dropdown); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('date_format', JText::_('EB_DATE_FORMAT'), JText::_('EB_DATE_FORMAT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="date_format" class="inputbox" value="<?php echo $config->date_format; ?>" size="20" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('event_date_format', JText::_('EB_EVENT_DATE_FORMAT'), JText::_('EB_EVENT_DATE_FORMAT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="event_date_format" class="inputbox" value="<?php echo $config->event_date_format; ?>" size="40" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('event_time_format', JText::_('EB_TIME_FORMAT'), JText::_('EB_TIME_FORMAT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="event_time_format" class="inputbox" value="<?php echo $config->event_time_format ? $config->event_time_format : '%I%P'; ?>" size="40" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('date_field_format', JText::_('EB_DATE_FIELD_FORMAT'), JText::_('EB_DATE_FIELD_FORMAT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['date_field_format']; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('currency_code', JText::_('EB_CURRENCY_CODE')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['currency_code']; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('currency_symbol', JText::_('EB_CURRENCY_SYMBOL')); ?>
			</div>
			<div class="controls">
				<input type="text" name="currency_symbol" class="inputbox" value="<?php echo $config->currency_symbol; ?>" size="10" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('decimals', JText::_('EB_DECIMALS'), JText::_('EB_DECIMALS_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="decimals" class="inputbox" value="<?php echo $config->get('decimals', 2); ?>" size="10" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('dec_point', JText::_('EB_DECIMAL_POINT'), JText::_('EB_DECIMAL_POINT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="dec_point" class="inputbox" value="<?php echo $this->config->get('dec_point', '.');?>" size="10" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('thousands_sep', JText::_('EB_THOUNSANDS_SEP'), JText::_('EB_THOUNSANDS_SEP_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="thousands_sep" class="inputbox" value="<?php echo $config->get('thousands_sep', ','); ?>" size="10" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('currency_position', JText::_('EB_CURRENCY_POSITION')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['currency_position']; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('event_custom_field', JText::_('EB_EVENT_CUSTOM_FIELD'), JText::_('EB_EVENT_CUSTOM_FIELD_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('event_custom_field', $config->event_custom_field); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('registrant_list_custom_field_ids', JText::_('EB_REGISTRANT_LIST_CUSTOM_FIELD_IDS'), JText::_('EB_REGISTRANT_LIST_CUSTOM_FIELD_IDS_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="registrant_list_custom_field_ids" value="<?php echo $config->registrant_list_custom_field_ids ; ?>" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('only_show_registrants_of_event_owner', JText::_('EB_ONLY_SHOW_REGISTRANTS_OF_EVENT_OWNER'), JText::_('EB_ONLY_SHOW_REGISTRANTS_OF_EVENT_OWNER_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('only_show_registrants_of_event_owner', $config->only_show_registrants_of_event_owner); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('enable_delete_registrants', JText::_('EB_ENABLE_REGISTRANTS_IN_FRONTEND'), JText::_('EB_ENABLE_REGISTRANTS_IN_FRONTEND_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('enable_delete_registrants', $config->get('enable_delete_registrants', 1)); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_all_locations_in_event_submission_form', JText::_('EB_SHOW_ALL_LOCATIONS_IN_EVENT_SUBMISSION_FORM'), JText::_('EB_SHOW_ALL_LOCATIONS_IN_EVENT_SUBMISSION_FORM_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_all_locations_in_event_submission_form', $config->show_all_locations_in_event_submission_form); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('submit_event_redirect_url', JText::_('EB_SUBMIT_EVENT_REDIRECT_URL'), JText::_('EB_SUBMIT_EVENT_REDIRECT_URL_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="submit_event_redirect_url" class="input-xlarge" value="<?php echo $config->get('submit_event_redirect_url'); ?>" size="50" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('default_country', JText::_('EB_DEFAULT_COUNTRY')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['country_list']; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_feed_link', JText::_('EB_SHOW_FEED_LINK'), JText::_('EB_SHOW_FEED_LINK_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_feed_link', $config->get('show_feed_link', 1)); ?>
			</div>
		</div>
	</fieldset>
	<fieldset class="form-horizontal" style="margin-top:103px;">
		<legend><?php echo JText::_('EB_MAIL_SETTINGS'); ?></legend>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('send_emails', JText::_('EB_SEND_NOTIFICATION_EMAILS')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['send_emails']; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('from_name', JText::_('EB_FROM_NAME'), JText::_('EB_FROM_NAME_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="from_name" class="inputbox" value="<?php echo $config->from_name; ?>" size="50" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('from_email', JText::_('EB_FROM_EMAIL'), JText::_('EB_FROM_EMAIL_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="from_email" class="inputbox" value="<?php echo $config->from_email; ?>" size="50" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('notification_emails', JText::_('EB_NOTIFICATION_EMAILS'), JText::_('EB_NOTIFICATION_EMAILS_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="notification_emails" class="inputbox" value="<?php echo $config->notification_emails; ?>" size="50" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('send_email_to_event_creator', JText::_('EB_SEND_EMAIL_TO_EVENT_CREATOR'), JText::_('EB_SEND_EMAIL_TO_EVENT_CREATOR_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('send_email_to_event_creator', $config->get('send_email_to_event_creator', 1)); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('send_email_to_group_members', JText::_('EB_SEND_CONFIRMATION_EMAIL_TO_GROUP_MEMBERS'), JText::_('EB_SEND_CONFIRMATION_EMAIL_TO_GROUP_MEMBERS_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('send_email_to_group_members', $config->send_email_to_group_members); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('send_attachments_to_admin', JText::_('EB_SEND_ATTACHMENTS_TO_ADMIN'), JText::_('EB_SEND_ATTACHMENTS_TO_ADMIN_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('send_attachments_to_admin', $config->send_attachments_to_admin); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('log_email_types', JText::_('EB_LOG_EMAIL_TYPES'), JText::_('EB_LOG_EMAIL_TYPES_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['log_email_types']; ?>
			</div>
		</div>
	</fieldset>
	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('EB_MAP_SETTINGS'); ?></legend>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('map_api_key', JText::_('EB_MAP_API_KEY')); ?>
			</div>
			<div class="controls">
				<input type="text" name="map_api_key" class="input-xlarge" value="<?php echo $config->get('map_api_key', 'AIzaSyDIq19TVV4qOX2sDBxQofrWfjeA7pebqy4'); ?>" size="60" />
				<p class="text-warning" style="margin-top: 10px;">
					Google now requires a KEY for their API . Events Booking ships with a default API Key. With this key, Google will allows 25,000 map loads per day for all the websites which has Events Booking installed. That should be OK, however, if you are worry about this limit, please
					<a href="https://console.developers.google.com/flows/enableapi?apiid=maps_backend,geocoding_backend,directions_backend,distance_matrix_backend,elevation_backend&keyType=CLIENT_SIDE&reusekey=true" target="_blank"><strong>CLICK HERE</strong></a> to register for an own API Key, then enter the received key into this config option.
				</p>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('center_coordinates', JText::_('EB_CENTER_COORDINATES'), JText::_('EB_CENTER_COORDINATES_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="center_coordinates" class="inputbox" value="<?php echo $config->get('center_coordinates'); ?>" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('zoom_level', JText::_('EB_ZOOM_LEVEL'), JText::_('EB_ZOOM_LEVEL_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo JHtml::_('select.integerlist', 1, 21, 1, 'zoom_level', 'class="inputbox"', $config->zoom_level); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('map_width', JText::_('EB_MAP_WIDTH'), JText::_('EB_MAP_WIDTH_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="map_width" class="inputbox" value="<?php echo $config->map_width ; ?>" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('map_height', JText::_('EB_MAP_HEIGHT'), JText::_('EB_MAP_HEIGHT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="map_height" class="inputbox" value="<?php echo $config->map_height ; ?>" />
			</div>
		</div>
	<fieldset>
</div>
<div class="span6">
	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('EB_REGISTRATION_SETTINGS'); ?></legend>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('user_registration', JText::_('EB_USER_REGISTRATION_INTEGRATION'), JText::_('EB_REGISTRATION_INTEGRATION_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('user_registration', $config->user_registration); ?>
			</div>
		</div>
		<?php
		if (JComponentHelper::isInstalled('com_comprofiler') && JPluginHelper::isEnabled('eventbooking', 'cb'))
		{
		?>
			<div class="control-group">
				<div class="control-label">
					<?php echo EventbookingHelperHtml::getFieldLabel('use_cb_api', JText::_('EB_USE_CB_API'), JText::_('EB_USE_CB_API_EXPLAIN')); ?>
				</div>
				<div class="controls">
					<?php echo EventbookingHelperHtml::getBooleanInput('use_cb_api', $config->use_cb_api); ?>
				</div>
			</div>
		<?php
		}
		?>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('auto_populate_form_data', JText::_('EB_AUTO_POPULATE_FORM_DATA'), JText::_('EB_AUTO_POPULATE_FORM_DATA_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('auto_populate_form_data', $config->get('auto_populate_form_data', 1)); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('populate_group_members_data', JText::_('EB_POPULATE_GROUP_MEMBERS_DATA'), JText::_('EB_POPULATE_GROUP_MEMBER_DATA_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('populate_group_members_data', $config->get('populate_group_members_data', 0)); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('allow_populate_group_member_data', JText::_('EB_ALLOW_POPULATE_GROUP_MEMBER_DATA'), JText::_('EB_ALLOW_POPULATE_GROUP_MEMBER_DATA_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('allow_populate_group_member_data', $config->get('allow_populate_group_member_data', 0)); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('multiple_booking', JText::_('EB_MULTIPLE_BOOKING'), JText::_('EB_MULTIPLE_BOOKING_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('multiple_booking', $config->multiple_booking); ?>
			</div>
		</div>
		<div class="control-group" data-showon='<?php echo EventbookingHelperHtml::renderShowOn(array('multiple_booking' => '1')); ?>'>
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('collect_member_information_in_cart', JText::_('EB_COLLECT_MEMBER_INFORMATION_IN_CART'), JText::_('EB_COLLECT_MEMBER_INFORMATION_IN_CART_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('collect_member_information_in_cart', $config->collect_member_information_in_cart); ?>
			</div>
		</div>
		<div class="control-group" data-showon='<?php echo EventbookingHelperHtml::renderShowOn(array('multiple_booking' => '0')); ?>'>
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('collect_member_information', JText::_('EB_COLLECT_MEMBER_INFORMATION'), JText::_('EB_COLLECT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('collect_member_information', $config->collect_member_information); ?>
			</div>
		</div>
		<div class="control-group" data-showon='<?php echo EventbookingHelperHtml::renderShowOn(array('multiple_booking' => '0')); ?>'>
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('auto_populate_billing_data', JText::_('EB_AUTO_POPULATE_BILLING_DATA')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['auto_populate_billing_data']; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('prevent_duplicate_registration', JText::_('EB_PREVENT_DUPLICATE'), JText::_('EB_PREVENT_DUPLICATE_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('prevent_duplicate_registration', $config->prevent_duplicate_registration); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('simply_registration_process', JText::_('EB_SIMPLY_REGISTRATION_PROCESS'), JText::_('EB_SIMPLY_REGISTRATION_PROCESS_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('simply_registration_process', $config->simply_registration_process); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('activate_deposit_feature', JText::_('EB_ACTIVATE_DEPOSIT_FEATURE'), JText::_('EB_ACTIVATE_DEPOSIT_FEATURE_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('activate_deposit_feature', $config->activate_deposit_feature); ?>
			</div>
		</div>
		<div class="control-group" data-showon='<?php echo EventbookingHelperHtml::renderShowOn(array('activate_deposit_feature' => '1')); ?>'>
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('default_payment_type', JText::_('EB_DEFAULT_PAYMENT_TYPE')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['default_payment_type']; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('activate_waitinglist_feature', JText::_('EB_ACTIVATE_WAITINGLIST_FEATURE'), JText::_('EB_ACTIVATE_WAITINGLIST_FEATURE_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('activate_waitinglist_feature', $config->activate_waitinglist_feature); ?>
			</div>
		</div>
		<div class="control-group" data-showon='<?php echo EventbookingHelperHtml::renderShowOn(array('activate_waitinglist_feature' => '1')); ?>'>
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('enable_waiting_list_payment', JText::_('EB_ENABLE_WAITING_LIST_PAYMENT'), JText::_('EB_ENABLE_WAITING_LIST_PAYMENT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('enable_waiting_list_payment', $config->enable_waiting_list_payment); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('unpublish_event_when_full', JText::_('EB_UNPUBLISH_EVENT_WHEN_FULL'), JText::_('EB_UNPUBLISH_EVENT_WHEN_FULL_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('unpublish_event_when_full', $config->unpublish_event_when_full); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('send_ics_file', JText::_('EB_SEND_ICS_FILE'), JText::_('EB_SEND_ICS_FILE_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('send_ics_file', $config->send_ics_file); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('enable_captcha', JText::_('EB_ENABLE_CAPTCHA'), JText::_('EB_CAPTCHA_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('enable_captcha', $config->enable_captcha); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('bypass_captcha_for_registered_user', JText::_('EB_BYPASS_CAPTCHA_FOR_REGISTERED_USER'), JText::_('EB_BYPASS_CAPTCHA_FOR_REGISTERED_USER_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('bypass_captcha_for_registered_user', $config->bypass_captcha_for_registered_user); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('enable_coupon', JText::_('EB_ENABLE_COUPON'), JText::_('EB_COUNPON_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('enable_coupon', $config->enable_coupon); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_pending_registrants', JText::_('EB_SHOW_PENDING_REGISTRANTS'), JText::_('EB_SHOW_PENDING_REGISTRANTS_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_pending_registrants', $config->show_pending_registrants); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_price_including_tax', JText::_('EB_SHOW_PRICE_INCLUDING_TAX'), JText::_('EB_SHOW_PRICE_INCLUDING_TAX_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_price_including_tax', $config->show_price_including_tax); ?>
			</div>
		</div>
		<div class="control-group" data-showon='<?php echo EventbookingHelperHtml::renderShowOn(array('show_price_including_tax' => '1')); ?>'>
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('setup_price', JText::_('EB_SETUP_PRICE'), JText::_('EB_SETUP_PRICE_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['setup_price'];?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('include_group_billing_in_registrants', JText::_('EB_INCLUDE_GROUP_BILLING_IN_REGISTRANTS_MANAGEMENT'), JText::_('EB_INCLUDE_GROUP_BILLING_IN_REGISTRANTS_MANAGEMENT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('include_group_billing_in_registrants', $config->get('include_group_billing_in_registrants', 1)); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('include_group_members_in_registrants', JText::_('EB_INCLUDE_GROUP_MEMBERS_IN_REGISTRANTS_MANAGEMENT'), JText::_('EB_INCLUDE_GROUP_MEMBERS_IN_REGISTRANTS_MANAGEMENT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('include_group_members_in_registrants', $config->get('include_group_members_in_registrants', 0)); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('show_billing_step_for_free_events', JText::_('EB_SHOW_BILLING_STEP_FOR_FREE_EVENTS'), JText::_('EB_SHOW_BILLING_STEP_FOR_FREE_EVENTS_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('show_billing_step_for_free_events', $config->show_billing_step_for_free_events); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('activate_checkin_registrants', JText::_('EB_ACTIVATE_CHECKIN_REGISTRANTS'), JText::_('EB_ACTIVATE_CHECKIN_REGISTRANTS_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('activate_checkin_registrants', $config->activate_checkin_registrants); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('accept_term', JText::_('EB_SHOW_TERM_AND_CONDITION'), JText::_('EB_SHOW_TERM_AND_CONDITION_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('accept_term', $config->accept_term); ?>
			</div>
		</div>
		<div class="control-group" data-showon='<?php echo EventbookingHelperHtml::renderShowOn(array('accept_term' => '1')); ?>'>
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('article_id', JText::_('EB_DEFAULT_TERM_AND_CONDITION')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelper::getArticleInput($config->article_id); ?>
			</div>
		</div>
	</fieldset>
	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('EB_IMAGE_SETTINGS'); ?></legend>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('resize_image_method', JText::_('EB_RESIZE_IMAGE_METHOD')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['resize_image_method'];?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('thumb_width', JText::_('EB_EVENT_THUMB_WIDTH'), JText::_('EB_EVENT_THUMB_WIDTH_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="thumb_width" class="inputbox" value="<?php echo $config->thumb_width ; ?>" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('thumb_height', JText::_('EB_EVENT_THUMB_HEIGHT'), JText::_('EB_EVENT_THUMB_HEIGHT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="thumb_height" class="inputbox" value="<?php echo $config->thumb_height ; ?>" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('category_thumb_width', JText::_('EB_CATEGORY_THUMB_WIDTH'), JText::_('EB_CATEGORY_THUMB_WIDTH_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="category_thumb_width" class="inputbox" value="<?php echo $config->category_thumb_width ; ?>" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('category_thumb_height', JText::_('EB_CATEGORY_THUMB_HEIGHT'), JText::_('EB_CATEGORY_THUMB_HEIGHT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="category_thumb_height" class="inputbox" value="<?php echo $config->category_thumb_height ; ?>" />
			</div>
		</div>
	</fieldset>
	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('EB_OTHER_SETTINGS'); ?></legend>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('registration_type', JText::_('EB_DEFAULT_REGISTRATION_TYPE')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['registration_type']; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('access', JText::_('EB_DEFAULT_ACCESS')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['access']; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('registration_access', JText::_('EB_DEFAULT_REGISTRATION_ACCESS')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['registration_access']; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('default_event_status', JText::_('EB_DEFAULT_EVENT_STATUS')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['default_event_status']; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('attachment_file_types', JText::_('EB_ATTACHMENT_FILE_TYPES'), JText::_('EB_ATTACHMENT_FILE_TYPES_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="attachment_file_types" class="inputbox" value="<?php echo strlen($config->attachment_file_types) ? $config->attachment_file_types : 'bmp|gif|jpg|png|swf|zip|doc|pdf|xls'; ?>" size="60" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('csv_delimiter', JText::_('EB_CSV_DELIMITTER')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['csv_delimiter']; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('export_data_format', JText::_('EB_EXPORT_DATA_FORMAT')); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['export_data_format']; ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('conversion_tracking_code', JText::_('EB_CONVERSION_TRACKING_CODE'), JText::_('EB_CONVERSION_TRACKING_CODE_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<textarea name="conversion_tracking_code" class="input-xlarge" rows="10"><?php echo $config->conversion_tracking_code;?></textarea>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('check_new_version_in_dashboard', JText::_('EB_CHECK_NEW_VERSION_IN_DASHBOARD'), JText::_('EB_SHOW_VERSION_CHECK_IN_DASHBOARD_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('check_new_version_in_dashboard', isset($config->check_new_version_in_dashboard) ? $config->check_new_version_in_dashboard : 1); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('debug', JText::_('EB_DEBUG'), JText::_('EB_DEBUG_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getBooleanInput('debug', $config->debug); ?>
			</div>
		</div>
	</fieldset>
</div>
