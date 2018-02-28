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
$editor = JEditor::getInstance(JFactory::getConfig()->get('editor'));
$fields = EventbookingHelperHtml::getAvailableMessagesTags();
JHtml::_('behavior.tabstate');
?>
<form action="index.php?option=com_eventbooking&view=message" method="post" name="adminForm" id="adminForm" class="form-horizontal eb-configuration">
	<?php echo JHtml::_('bootstrap.startTabSet', 'message', array('active' => 'registration-form-messages-page')); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'message', 'registration-form-messages-page', JText::_('EB_REGISTRATION_FORM_MESSAGES', true)); ?>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('intro_text', JText::_('EB_INTRO_TEXT'), JText::_('EB_INTRO_TEXT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'intro_text',  $this->message->intro_text , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('registration_form_message', JText::_('EB_REGISTRATION_FORM_MESSAGE'), JText::_('EB_REGISTRATION_FORM_MESSAGE_EXPLAIN')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[EVENT_TITLE]</strong>
				</p>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'registration_form_message',  $this->message->registration_form_message , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('registration_form_message_group', JText::_('EB_REGISTRATION_FORM_MESSAGE_GROUP'), JText::_('EB_REGISTRATION_FORM_MESSAGE_GROUP_EXPLAIN')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[EVENT_TITLE]</strong>
				</p>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'registration_form_message_group',  $this->message->registration_form_message_group , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('number_members_form_message', JText::_('EB_NUMBER_OF_MEMBERS_FORM_MESSAGE'), JText::_('EB_NUMBER_OF_MEMBERS_FORM_MESSAGE_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'number_members_form_message',  $this->message->number_members_form_message , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('member_information_form_message', JText::_('EB_MEMBER_INFORMATION_FORM_MESSAGE'), JText::_('EB_MEMBER_INFORMATION_FORM_MESSAGE_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'member_information_form_message',  $this->message->member_information_form_message , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('thanks_message', JText::_('EB_THANK_YOU_MESSAGE'), JText::_('EB_THANK_YOU_MESSAGE_EXPLAIN')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong><?php echo $fields; ?></strong>
				</p>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'thanks_message',  $this->message->thanks_message , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('thanks_message_offline', JText::_('EB_THANK_YOU_MESSAGE_OFFLINE'), JText::_('EB_THANK_YOU_MESSAGE_OFFLINE_EXPLAIN')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong><?php echo $fields; ?></strong>
				</p>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'thanks_message_offline',  $this->message->thanks_message_offline , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<?php
		if (count($this->extraOfflinePlugins))
		{
			foreach ($this->extraOfflinePlugins as $offlinePaymentPlugin)
			{
				$name   = $offlinePaymentPlugin->name;
				$title  = $offlinePaymentPlugin->title;
				$prefix = str_replace('os_offline', '', $name);
				?>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_('User email body (' . $title . ')'); ?>
						<p class="eb-available-tags">
							<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong><?php echo $fields; ?></strong>
						</p>
					</div>
					<div class="controls">
						<?php echo $editor->display('user_email_body_offline' . $prefix, $this->message->{'user_email_body_offline' . $prefix}, '100%', '250', '75', '8'); ?>
					</div>
				</div>

				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_('Thank you message (' . $title . ')'); ?>
						<p>
							<strong>This message will be displayed on the thank you page after users complete an offline
								payment</strong>
						</p>
					</div>
					<div class="controls">
						<?php echo $editor->display('thanks_message_offline' . $prefix, $this->message->{'thanks_message_offline' . $prefix}, '100%', '250', '75', '8'); ?>
					</div>
				</div>
				<?php
			}
		}
		?>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('cancel_message', JText::_('EB_CANCEL_MESSAGE'), JText::_('EB_CANCEL_MESSAGE_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'cancel_message',  $this->message->cancel_message , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('registration_cancel_message_free', JText::_('EB_REGISTRATION_CANCEL_MESSAGE_FREE'), JText::_('EB_REGISTRATION_CANCEL_MESSAGE_FREE_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'registration_cancel_message_free',  $this->message->registration_cancel_message_free , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('registration_cancel_message_paid', JText::_('EB_REGISTRATION_CANCEL_MESSAGE_PAID'), JText::_('EB_REGISTRATION_CANCEL_MESSAGE_PAID_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'registration_cancel_message_paid',  $this->message->registration_cancel_message_paid, '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('user_registration_cancel_subject', JText::_('EB_USER_REGISTRATION_CANCEL_SUBJECT')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[EVENT_TITLE]</strong>
				</p>
			</div>
			<div class="controls">
				<input type="text" name="user_registration_cancel_subject" class="input-xlarge" value="<?php echo $this->message->user_registration_cancel_subject; ?>" size="50" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('user_registration_cancel_message', JText::_('EB_USER_REGISTRATION_CANCEL_MESSAGE'), JText::_('EB_USER_REGISTRATION_CANCEL_MESSAGE_EXPLAIN'));?>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'user_registration_cancel_message',  $this->message->user_registration_cancel_message, '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'message', 'registration-email-messages-page', JText::_('EB_REGISTRATION_EMAIL_MESSAGES', true)); ?>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('admin_email_subject', JText::_('EB_ADMIN_EMAIL_SUBJECT')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[EVENT_TITLE]</strong>
				</p>
			</div>
			<div class="controls">
				<input type="text" name="admin_email_subject" class="input-xlarge" value="<?php echo $this->message->admin_email_subject; ?>" size="80" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('admin_email_body', JText::_('EB_ADMIN_EMAIL_BODY')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong><?php echo $fields; ?></strong>
				</p>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'admin_email_body',  $this->message->admin_email_body , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('user_email_subject', JText::_('EB_USER_EMAIL_SUBJECT')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[EVENT_TITLE]</strong>
				</p>
			</div>
			<div class="controls">
				<input type="text" name="user_email_subject" class="input-xlarge" value="<?php echo $this->message->user_email_subject; ?>" size="50" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('user_email_body', JText::_('EB_USER_EMAIL_BODY')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong><?php echo $fields; ?></strong>
				</p>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'user_email_body',  $this->message->user_email_body , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('user_email_body_offline', JText::_('EB_USER_EMAIL_BODY_OFFLINE')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong><?php echo $fields; ?></strong>
				</p>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'user_email_body_offline',  $this->message->user_email_body_offline , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('group_member_email_subject', JText::_('EB_GROUP_MEMBER_EMAIL_SUBJECT')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[EVENT_TITLE]</strong>
				</p>
			</div>
			<div class="controls">
				<input type="text" name="group_member_email_subject" class="input-xlarge" value="<?php echo $this->message->group_member_email_subject; ?>" size="50" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('group_member_email_body', JText::_('EB_GROUP_MEMBER_EMAIL_BODY')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[MEMBER_DETAIL], <?php echo EventbookingHelperHtml::getAvailableMessagesTags(false); ?></strong>
				</p>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'group_member_email_body',  $this->message->group_member_email_body , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('reminder_email_subject', JText::_('EB_REMINDER_EMAIL_SUBJECT')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: [EVENT_TITLE]
				</p>
			</div>
			<div class="controls">
				<input type="text" name="reminder_email_subject" class="input-xlarge" value="<?php echo $this->message->reminder_email_subject; ?>" size="50" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('reminder_email_body', JText::_('EB_REMINDER_EMAIL_BODY')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[REGISTRATION_DETAIL], [EVENT_DATE], [FIRST_NAME], [LAST_NAME], [EVENT_TITLE]</strong>
				</p>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'reminder_email_body',  $this->message->reminder_email_body , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('registration_cancel_email_subject', JText::_('EB_CANCEL_NOTIFICATION_EMAIL_SUBJECT')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[EVENT_TITLE]</strong>
				</p>
			</div>
			<div class="controls">
				<input type="text" name="registration_cancel_email_subject" class="input-xlarge" value="<?php echo $this->message->registration_cancel_email_subject; ?>" size="50" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('registration_cancel_email_body', JText::_('EB_CANCEL_NOTIFICATION_EMAIL_BODY')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[REGISTRATION_DETAIL], <?php echo $fields; ?></strong>
				</p>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'registration_cancel_email_body',  $this->message->registration_cancel_email_body , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('registration_approved_email_subject', JText::_('EB_REGISTRATION_APPROVED_EMAIL_SUBJECT')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[EVENT_TITLE]</strong>
				</p>
			</div>
			<div class="controls">
				<input type="text" name="registration_approved_email_subject" class="input-xlarge" value="<?php echo $this->message->registration_approved_email_subject; ?>" size="50" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('registration_approved_email_body', JText::_('EB_REGISTRATION_APPROVED_EMAIL_BODY')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong><?php echo $fields; ?></strong>
				</p>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'registration_approved_email_body',  $this->message->registration_approved_email_body , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'message', 'submit-event-email-messages-page', JText::_('EB_SUBMIT_EVENT_EMAIL_MESSAGES', true)); ?>
	<div class="control-group">
		<div class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('submit_event_user_email_subject', JText::_('EB_SUBMIT_EVENT_USER_EMAIL_SUBJECT')); ?>
		</div>
		<div class="controls">
			<input type="text" name="submit_event_user_email_subject" class="input-xlarge" value="<?php echo $this->message->submit_event_user_email_subject; ?>" size="80" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('submit_event_user_email_body', JText::_('EB_SUBMIT_EVENT_USER_EMAIL_BODY')); ?>
			<p class="eb-available-tags">
				<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[NAME], [USERNAME], [EVENT_TITLE], [EVENT_DATE], [EVENT_ID], [EVENT_LINK]</strong>
			</p>
		</div>
		<div class="controls">
			<?php echo $editor->display( 'submit_event_user_email_body',  $this->message->submit_event_user_email_body , '100%', '250', '75', '8' ) ;?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('submit_event_admin_email_subject', JText::_('EB_SUBMIT_EVENT_ADMIN_EMAIL_SUBJECT')); ?>
		</div>
		<div class="controls">
			<input type="text" name="submit_event_admin_email_subject" class="input-xlarge" value="<?php echo $this->message->submit_event_admin_email_subject; ?>" size="50" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('submit_event_admin_email_body', JText::_('EB_SUBMIT_EVENT_ADMIN_EMAIL_BODY')); ?>
			<p class="eb-available-tags">
				<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[NAME], [USERNAME], [EVENT_TITLE], [EVENT_DATE], [EVENT_ID], [EVENT_LINK]</strong>
			</p>
		</div>
		<div class="controls">
			<?php echo $editor->display( 'submit_event_admin_email_body',  $this->message->submit_event_admin_email_body , '100%', '250', '75', '8' ) ;?>
		</div>
	</div>
	<?php echo JHtml::_('bootstrap.endTab');?>

	<?php echo JHtml::_('bootstrap.addTab', 'message', 'invitation-messages-page', JText::_('EB_INVITATION_MESSAGES', true)); ?>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('invitation_form_message', JText::_('EB_INVITATION_FORM_MESSAGE'), JText::_('EB_INVITATION_FORM_MESSAGE_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'invitation_form_message',  $this->message->invitation_form_message, '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('invitation_complete', JText::_('EB_INVITATION_COMPLETE_MESSAGE'), JText::_('EB_INVITATION_COMPLETE_MESSAGE_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'invitation_complete',  $this->message->invitation_complete , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('invitation_email_subject', JText::_('EB_INVITATION_EMAIL_SUBJECT')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[EVENT_TITLE]</strong>
				</p>
			</div>
			<div class="controls">
				<input type="text" name="invitation_email_subject" class="input-xlarge" value="<?php echo $this->message->invitation_email_subject; ?>" size="50" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('invitation_email_body', JText::_('EB_INVITATION_EMAIL_BODY')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[SENDER_NAME],[NAME], [EVENT_TITLE], [INVITATION_NAME], [EVENT_DETAIL_LINK], [PERSONAL_MESSAGE]</strong>
				</p>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'invitation_email_body',  $this->message->invitation_email_body , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'message', 'waitinglist-messages-page', JText::_('EB_WAITINGLIST_MESSAGES', true)); ?>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('waitinglist_form_message', JText::_('EB_WAITINGLIST_FORM_MESSAGE'), JText::_('EB_WAITINGLIST_FORM_MESSAGE_EXPLAIN')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[EVENT_TITLE]</strong>
				</p>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'waitinglist_form_message',  $this->message->waitinglist_form_message , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('waitinglist_complete_message', JText::_('EB_WAITINGLIST_COMPLETE_MESSAGE'), JText::_('EB_WAITINGLIST_COMPLETE_MESSAGE_EXPLAIN')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[EVENT_TITLE], [FIRST_NAME], [LAST_NAME]</strong>
				</p>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'waitinglist_complete_message',  $this->message->waitinglist_complete_message , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('watinglist_confirmation_subject', JText::_('EB_WAITINGLIST_CONFIRMATION_SUBJECT'), JText::_('EB_WAITINGLIST_CONFIRMATION_SUBJECT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="watinglist_confirmation_subject" class="input-xlarge" size="70" value="<?php echo $this->message->watinglist_confirmation_subject ; ?>" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('watinglist_confirmation_body', JText::_('EB_WAITINGLIST_CONFIRMATION_BODY'), JText::_('EB_WAITINGLIST_COMPLETE_MESSAGE_EXPLAIN')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[EVENT_TITLE], [FIRST_NAME], [LAST_NAME]</strong>
				</p>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'watinglist_confirmation_body',  $this->message->watinglist_confirmation_body , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('watinglist_notification_subject', JText::_('EB_WAITINGLIST_NOTIFICATION_SUBJECT'), JText::_('EB_WAITINGLIST_NOTIFICATION_SUBJECT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="watinglist_notification_subject" class="input-xlarge" size="70" value="<?php echo $this->message->watinglist_notification_subject ; ?>" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('watinglist_notification_body', JText::_('EB_WAITINGLIST_NOTIFICATION_BODY'), JText::_('EB_WAITINGLIST_NOTIFICATION_BODY_EXPLAIN')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[EVENT_TITLE], [FIRST_NAME], [LAST_NAME]</strong>
				</p>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'watinglist_notification_body',  $this->message->watinglist_notification_body , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('registrant_waitinglist_notification_subject', JText::_('EB_REGISTRANT_WAITINGLIST_NOTIFICATION_SUBJECT'), JText::_('EB_REGISTRANT_WAITINGLIST_NOTIFICATION_SUBJECT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="registrant_waitinglist_notification_subject" class="input-xlarge" size="70" value="<?php echo $this->message->registrant_waitinglist_notification_subject ; ?>" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('registrant_waitinglist_notification_body', JText::_('EB_REGISTRANT_WAITINGLIST_NOTIFICATION_BODY'), JText::_('EB_REGISTRANT_WAITINGLIST_NOTIFICATION_BODY_EXPLAIN')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[REGISTRANT_FIRST_NAME], [REGISTRANT_LAST_NAME],[EVENT_TITLE], [FIRST_NAME], [LAST_NAME], [EVENT_LINK]</strong>
				</p>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'registrant_waitinglist_notification_body',  $this->message->registrant_waitinglist_notification_body , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('request_payment_email_subject', JText::_('EB_REQUEST_PAYMENT_EMAIL_SUBJECT'), JText::_('EB_REQUEST_PAYMENT_EMAIL_SUBJECT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" name="request_payment_email_subject" class="input-xlarge" size="70" value="<?php echo $this->message->request_payment_email_subject ; ?>" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('request_payment_email_body', JText::_('EB_REQUEST_PAYMENT_EMAIL_BODY'), JText::_('EB_REQUEST_PAYMENT_EMAIL_BODY_EXPLAIN')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[FIRST_NAME], [LAST_NAME],[EVENT_TITLE] , [EVENT_DATE], [PAYMENT_LINK]</strong>
				</p>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'request_payment_email_body',  $this->message->request_payment_email_body , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('registration_payment_form_message', JText::_('EB_REGISTRATION_PAYMENT_FORM_MESSAGE'), JText::_('EB_REGISTRATION_PAYMENT_FORM_MESSAGE_EXPLAIN')); ?>
				<p class="eb-available-tags">
					<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[REGISTRATION_ID],[EVENT_TITLE], [EVENT_DATE], [AMOUNT], [REGISTRATION_ID]</strong>
				</p>
			</div>
			<div class="controls">
				<?php echo $editor->display( 'registration_payment_form_message',  $this->message->registration_payment_form_message , '100%', '250', '75', '8' ) ;?>
			</div>
		</div>
	<?php
	echo JHtml::_('bootstrap.endTab');
	echo JHtml::_('bootstrap.addTab', 'message', 'pay-deposit-form-messages-page', JText::_('EB_DEPOSIT_PAYMENT_MESSAGES', true));
	?>
	<div class="control-group">
		<div class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('deposit_payment_form_message', JText::_('EB_DEPOSIT_PAYMENT_FORM_MESSAGE'), JText::_('EB_DEPOSIT_PAYMENT_FORM_MESSAGE_EXPLAIN')); ?>
			<p class="eb-available-tags">
				<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[EVENT_TITLE], [REGISTRATION_ID], [AMOUNT]</strong>
			</p>
		</div>
		<div class="controls">
			<?php echo $editor->display( 'deposit_payment_form_message',  $this->message->deposit_payment_form_message , '100%', '250', '75', '8' ) ;?>
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('deposit_payment_thanks_message', JText::_('EB_DEPOSIT_PAYMENT_THANK_YOU_MESSAGE'), JText::_('EB_DEPOSIT_PAYMENT_THANK_YOU_MESSAGE_EXPLAIN')); ?>
			<p class="eb-available-tags">
				<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[EVENT_TITLE], [REGISTRATION_ID], [PAYMENT_AMOUNT], [PAYMENT_METHOD]</strong>
			</p>
		</div>
		<div class="controls">
			<?php echo $editor->display( 'deposit_payment_thanks_message',  $this->message->deposit_payment_thanks_message , '100%', '250', '75', '8' ) ;?>
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('deposit_payment_admin_email_subject', JText::_('EB_DEPOSIT_PAYMENT_ADMIN_EMAIL_SUBJECT')); ?>			
		</div>
		<div class="controls">
			<input type="text" name="deposit_payment_admin_email_subject" class="input-xxlarge" value="<?php echo $this->message->deposit_payment_admin_email_subject; ?>" size="80" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('deposit_payment_admin_email_body', JText::_('EB_DEPOSIT_PAYMENT_ADMIN_EMAIL_BODY')); ?>
			<p class="eb-available-tags">
				<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[EVENT_TITLE], [REGISTRATION_ID], [PAYMENT_AMOUNT], [PAYMENT_METHOD]</strong>
			</p>
		</div>
		<div class="controls">
			<?php echo $editor->display( 'deposit_payment_admin_email_body',  $this->message->deposit_payment_admin_email_body , '100%', '250', '75', '8' ) ;?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('deposit_payment_user_email_subject', JText::_('EB_DEPOSIT_PAYMENT_USER_EMAIL_SUBJECT')); ?>			
		</div>
		<div class="controls">
			<input type="text" name="deposit_payment_user_email_subject" class="input-xxlarge" value="<?php echo $this->message->deposit_payment_user_email_subject; ?>" size="50" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('deposit_payment_user_email_body', JText::_('EB_DEPOSIT_PAYMENT_USER_EMAIL_BODY')); ?>
			<p class="eb-available-tags">
				<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[EVENT_TITLE], [REGISTRATION_ID], [PAYMENT_AMOUNT], [PAYMENT_METHOD]</strong>
			</p>
		</div>
		<div class="controls">
			<?php echo $editor->display( 'deposit_payment_user_email_body',  $this->message->deposit_payment_user_email_body , '100%', '250', '75', '8' ) ;?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('deposit_payment_reminder_email_subject', JText::_('EB_DEPOSIT_PAYMENT_REMINDER_EMAIL_SUBJECT')); ?>
		</div>
		<div class="controls">
			<input type="text" name="deposit_payment_reminder_email_subject" class="input-xxlarge" value="<?php echo $this->message->deposit_payment_reminder_email_subject; ?>" size="50" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('deposit_payment_reminder_email_body', JText::_('EB_DEPOSIT_PAYMENT_REMINDER_EMAIL_BODY')); ?>
			<p class="eb-available-tags">
				<?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: <strong>[FIRST_NAME], [LAST_NAME], [EVENT_DATE], [EVENT_TITLE], [REGISTRATION_ID], [PAYMENT_AMOUNT], [PAYMENT_METHOD]</strong>
			</p>
		</div>
		<div class="controls">
			<?php echo $editor->display( 'deposit_payment_reminder_email_body',  $this->message->deposit_payment_reminder_email_body , '100%', '250', '75', '8' ) ;?>
		</div>
	</div>
	<?php
	echo JHtml::_('bootstrap.endTab');

	// Add support for custom settings layout
	if (file_exists(__DIR__ . '/default_custom_settings.php'))
	{
		echo JHtml::_('bootstrap.addTab', 'message', 'custom-settings-page', JText::_('EB_MESSAGE_CUSTOM_SETTINGS', true));
		echo $this->loadTemplate('custom_settings', array('editor' => $editor));
		echo JHtml::_('bootstrap.endTab');
	}

	if ($translatable)
	{
		echo JHtml::_('bootstrap.addTab', 'message', 'translation-page', JText::_('EB_TRANSLATION', true));
		echo JHtml::_('bootstrap.startTabSet', 'message-translation', array('active' => 'translation-page-'.$this->languages[0]->sef));
		foreach ($this->languages as $language)
		{
			$sef = $language->sef;
			echo JHtml::_('bootstrap.addTab', 'message-translation', 'translation-page-' . $sef, $language->title . ' <img src="' . JUri::root() . 'media/com_eventbooking/flags/' . $sef . '.png" />');
		?>
			<table class="admintable adminform" style="width:100%;">
				<tr>
					<td class="key">
						<?php echo JText::_('EB_INTRO_TEXT'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'intro_text_'.$sef,  $this->message->{'intro_text_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						&nbsp;
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_ADMIN_EMAIL_SUBJECT'); ?>
					</td>
					<td>
						<input type="text" name="admin_email_subject_<?php echo $sef; ?>" class="input-xlarge" value="<?php echo $this->message->{'admin_email_subject_'.$sef}; ?>" size="80" />
					</td>
					<td width="35%">
						<strong><?php echo JText::_('EB_AVAILABLE_TAGS'); ?> : [EVENT_TITLE]</strong>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_ADMIN_EMAIL_BODY'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'admin_email_body_'.$sef,  $this->message->{'admin_email_body_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_AVAILABLE_TAGS'); ?> :[REGISTRATION_DETAIL], [EVENT_TITLE], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [CITY], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT]</strong>
					</td>
				</tr>
				<tr>
					<td width="30%" class="key">
						<?php echo JText::_('EB_USER_EMAIL_SUBJECT'); ?>
					</td>
					<td>
						<input type="text" name="user_email_subject_<?php echo $sef; ?>" class="input-xlarge" value="<?php echo $this->message->{'user_email_subject_'.$sef}; ?>" size="50" />
					</td>
					<td>
						<strong><?php echo JText::_('EB_AVAILABLE_TAGS'); ?> : [EVENT_TITLE]</strong>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_USER_EMAIL_BODY'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'user_email_body_'.$sef,  $this->message->{'user_email_body_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_AVAILABLE_TAGS'); ?> :[REGISTRATION_DETAIL], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [CITY], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT]</strong>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_USER_EMAIL_BODY_OFFLINE'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'user_email_body_offline_'.$sef,  $this->message->{'user_email_body_offline_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_AVAILABLE_TAGS'); ?> :[REGISTRATION_DETAIL], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [CITY], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT]</strong>
					</td>
				</tr>
				<tr>
					<td width="30%" class="key">
						<?php echo JText::_('EB_GROUP_MEMBER_EMAIL_SUBJECT'); ?>
					</td>
					<td>
						<input type="text" name="group_member_email_subject_<?php echo $sef; ?>" class="input-xlarge" value="<?php echo $this->message->{'group_member_email_subject_'.$sef}; ?>" size="50" />
					</td>
					<td>
						<strong><?php echo JText::_('EB_AVAILABLE_TAGS'); ?> : [EVENT_TITLE]</strong>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_GROUP_MEMBER_EMAIL_BODY'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'group_member_email_body_'.$sef,  $this->message->{'group_member_email_body_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_AVAILABLE_TAGS'); ?> :[MEMBER_DETAIL], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [CITY], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT]</strong>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_REGISTRATION_FORM_MESSAGE'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'registration_form_message_'.$sef,  $this->message->{'registration_form_message_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_REGISTRATION_FORM_MESSAGE_EXPLAIN'); ?> <?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: [EVENT_TITLE]</strong>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_REGISTRATION_FORM_MESSAGE_GROUP'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'registration_form_message_group_'.$sef,  $this->message->{'registration_form_message_group_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_REGISTRATION_FORM_MESSAGE_GROUP_EXPLAIN'); ?> <?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: [EVENT_TITLE]</strong>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_NUMBER_OF_MEMBERS_FORM_MESSAGE'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'number_members_form_message_'.$sef,  $this->message->{'number_members_form_message_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_NUMBER_OF_MEMBERS_FORM_MESSAGE_EXPLAIN'); ?></strong>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_MEMBER_INFORMATION_FORM_MESSAGE'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'member_information_form_message_'.$sef,  $this->message->{'member_information_form_message_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_MEMBER_INFORMATION_FORM_MESSAGE_EXPLAIN'); ?></strong>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_CONFIRMATION_MESSAGE'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'confirmation_message_'.$sef,  $this->message->{'confirmation_message_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_CONFIRMATION_MESSAGE_EXPLAIN'); ?>. <?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: [EVENT_TITLE], [AMOUNT]</strong>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_THANK_YOU_MESSAGE'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'thanks_message_'.$sef,  $this->message->{'thanks_message_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_THANK_YOU_MESSAGE_EXPLAIN'); ?></strong>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_THANK_YOU_MESSAGE_OFFLINE'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'thanks_message_offline_'.$sef,  $this->message->{'thanks_message_offline_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_THANK_YOU_MESSAGE_OFFLINE_EXPLAIN'); ?></strong>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_CANCEL_MESSAGE'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'cancel_message_'.$sef,  $this->message->{'cancel_message_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_CANCEL_MESSAGE_EXPLAIN') ; ?></strong>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_REGISTRATION_CANCEL_MESSAGE_FREE'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'registration_cancel_message_free_'.$sef,  $this->message->{'registration_cancel_message_free_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_REGISTRATION_CANCEL_MESSAGE_FREE_EXPLAIN'); ?></strong>
					</td>
				</tr>

				<tr>
					<td class="key">
						<?php echo JText::_('EB_REGISTRATION_CANCEL_MESSAGE_PAID'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'registration_cancel_message_paid_'.$sef,  $this->message->{'registration_cancel_message_paid_'.$sef}, '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_REGISTRATION_CANCEL_MESSAGE_PAID_EXPLAIN'); ?></strong>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_USER_REGISTRATION_CANCEL_SUBJECT'); ?>
					</td>
					<td class="controls">
						<input type="text" name="user_registration_cancel_subject_<?php echo $sef; ?>" class="input-xlarge" value="<?php echo $this->message->{'user_registration_cancel_subject_'.$sef}; ?>" size="50" />
					</td>
					<td>
						&nbsp;
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_USER_REGISTRATION_CANCEL_MESSAGE'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'user_registration_cancel_message_'.$sef,  $this->message->{'user_registration_cancel_message_'.$sef}, '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						&nbsp;
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_INVITATION_FORM_MESSAGE'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'invitation_form_message_'.$sef,  $this->message->{'invitation_form_message_'.$sef}, '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_INVITATION_FORM_MESSAGE_EXPLAIN'); ?></strong>
					</td>
				</tr>
				<tr>
					<td width="30%" class="key">
						<?php echo JText::_('EB_INVITATION_EMAIL_SUBJECT'); ?>
					</td>
					<td>
						<input type="text" name="invitation_email_subject_<?php echo $sef ?>" class="input-xlarge" value="<?php echo $this->message->{'invitation_email_subject_'.$sef}; ?>" size="50" />
					</td>
					<td>
						<strong><?php echo JText::_('EB_AVAILABLE_TAGS'); ?> : [EVENT_TITLE]</strong>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_INVITATION_EMAIL_BODY'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'invitation_email_body_'.$sef,  $this->message->{'invitation_email_body_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong>[SENDER_NAME],[NAME], [EVENT_TITLE], [INVITATION_NAME], [EVENT_DETAIL_LINK], [PERSONAL_MESSAGE]</strong>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_INVITATION_COMPLETE_MESSAGE'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'invitation_complete_'.$sef,  $this->message->{'invitation_complete_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<?php echo JText::_('EB_INVITATION_COMPLETE_MESSAGE_EXPLAIN'); ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_REMINDER_EMAIL_SUBJECT'); ?>
					</td>
					<td>
						<input type="text" name="reminder_email_subject_<?php echo $sef; ?>" class="input-xlarge" value="<?php echo $this->message->{'reminder_email_subject_'.$sef}; ?>" size="50" />
					</td>
					<td>
						<strong><?php echo JText::_('EB_AVAILABLE_TAGS'); ?> : [EVENT_TITLE]</strong>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_REMINDER_EMAIL_BODY'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'reminder_email_body_'.$sef,  $this->message->{'reminder_email_body_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_AVAILABLE_TAG'); ?> :[REGISTRATION_DETAIL], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [CITY], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT]</strong>
					</td>
				</tr>
				<tr>
					<td  class="key">
						<?php echo JText::_('EB_CANCEL_NOTIFICATION_EMAIL_SUBJECT'); ?>
					</td>
					<td>
						<input type="text" name="registration_cancel_email_subject_<?php echo $sef; ?>" class="input-xlarge" value="<?php echo $this->message->{'registration_cancel_email_subject_'.$sef}; ?>" size="50" />
					</td>
					<td>
						<strong><?php echo JText::_('EB_AVAILABLE_TAGS'); ?> : [EVENT_TITLE]</strong>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_CANCEL_NOTIFICATION_EMAIL_BODY'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'registration_cancel_email_body_'.$sef,  $this->message->{'registration_cancel_email_body_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_AVAILABLE_TAGS'); ?> :[REGISTRATION_DETAIL], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [CITY], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT]</strong>
					</td>
				</tr>

				<tr>
					<td  class="key">
						<?php echo JText::_('EB_REGISTRATION_APPROVED_EMAIL_SUBJECT'); ?>
					</td>
					<td>
						<input type="text" name="registration_approved_email_subject_<?php echo $sef; ?>" class="input-xlarge" value="<?php echo $this->message->{'registration_approved_email_subject_'.$sef}; ?>" size="50" />
					</td>
					<td>
						<strong><?php echo JText::_('EB_AVAILABLE_TAGS'); ?> : [EVENT_TITLE]</strong>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_REGISTRATION_APPROVED_EMAIL_BODY'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'registration_approved_email_body_'.$sef,  $this->message->{'registration_approved_email_body_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_AVAILABLE_TAGS'); ?> :[REGISTRATION_DETAIL], [FIRST_NAME], [LAST_NAME], [ORGANIZATION], [ADDRESS], [ADDRESS2], [CITY], [STATE], [CITY], [ZIP], [COUNTRY], [PHONE], [FAX], [EMAIL], [COMMENT], [AMOUNT]</strong>
					</td>
				</tr>

				<tr>
					<td class="key">
						<?php echo JText::_('EB_WAITINGLIST_FORM_MESSAGE'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'waitinglist_form_message_'.$sef,  $this->message->{'waitinglist_form_message_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_WAITINGLIST_FORM_MESSAGE_EXPLAIN'); ?> <?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: [EVENT_TITLE]</strong>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_WAITINGLIST_COMPLETE_MESSAGE'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'waitinglist_complete_message_'.$sef,  $this->message->{'waitinglist_complete_message_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_WAITINGLIST_COMPLETE_MESSAGE_EXPLAIN'); ?> <?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: [EVENT_TITLE], [FIRST_NAME], [LAST_NAME]</strong>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_WAITINGLIST_CONFIRMATION_SUBJECT');  ?>
					</td>
					<td>
						<input type="text" name="watinglist_confirmation_subject_<?php echo $sef; ?>" class="input-xlarge" size="70" value="<?php echo $this->message->{'watinglist_confirmation_subject_'.$sef} ; ?>" />
					</td>
					<td>
						<?php echo JText::_('EB_WAITINGLIST_CONFIRMATION_SUBJECT_EXPLAIN');  ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_WAITINGLIST_CONFIRMATION_BODY'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'watinglist_confirmation_body_'.$sef,  $this->message->{'watinglist_confirmation_body_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_WAITINGLIST_COMPLETE_MESSAGE_EXPLAIN'); ?> <?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: [EVENT_TITLE], [FIRST_NAME], [LAST_NAME]</strong>
					</td>
				</tr>

				<tr>
					<td class="key">
						<?php echo JText::_('EB_WAITINGLIST_NOTIFICATION_SUBJECT');  ?>
					</td>
					<td>
						<input type="text" name="watinglist_notification_subject_<?php echo $sef; ?>" class="input-xlarge" size="70" value="<?php echo $this->message->{'watinglist_notification_subject_'.$sef} ; ?>" />
					</td>
					<td>
						<?php echo JText::_('EB_WAITINGLIST_NOTIFICATION_SUBJECT_EXPLAIN');  ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_WAITINGLIST_NOTIFICATION_BODY'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'watinglist_notification_body_'.$sef,  $this->message->{'watinglist_notification_body_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_WAITINGLIST_NOTIFICATION_BODY_EXPLAIN'); ?> <?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: [EVENT_TITLE], [FIRST_NAME], [LAST_NAME]</strong>
					</td>
				</tr>

				<tr>
					<td class="key">
						<?php echo JText::_('EB_REGISTRANT_WAITINGLIST_NOTIFICATION_SUBJECT');  ?>
					</td>
					<td>
						<input type="text" name="registrant_waitinglist_notification_subject_<?php echo $sef; ?>" class="input-xlarge" size="70" value="<?php echo $this->message->{'registrant_waitinglist_notification_subject_'.$sef} ; ?>" />
					</td>
					<td>
						<?php echo JText::_('EB_REGISTRANT_WAITINGLIST_NOTIFICATION_SUBJECT_EXPLAIN');  ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_('EB_REGISTRANT_WAITINGLIST_NOTIFICATION_BODY'); ?>
					</td>
					<td>
						<?php echo $editor->display( 'registrant_waitinglist_notification_body_'.$sef,  $this->message->{'registrant_waitinglist_notification_body_'.$sef} , '100%', '250', '75', '8' ) ;?>
					</td>
					<td>
						<strong><?php echo JText::_('EB_REGISTRANT_WAITINGLIST_NOTIFICATION_BODY_EXPLAIN'); ?> <?php echo JText::_('EB_AVAILABLE_TAGS'); ?>: [EVENT_TITLE], [FIRST_NAME], [LAST_NAME]</strong>
					</td>
				</tr>
			</table>
		<?php
			echo JHtml::_('bootstrap.endTab');
		}
		echo JHtml::_('bootstrap.endTabSet');
		echo JHtml::_('bootstrap.endTab');
	}
	echo JHtml::_('bootstrap.endTabSet');
	?>
	<div class="clearfix"></div>
	<input type="hidden" name="task" value="" />
</form>