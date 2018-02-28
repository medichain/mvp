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

/**
 * Layout variables
 * -----------------
 * @var   EventbookingTableEvent $item
 * @var   RADConfig              $config
 * @var   boolean                $showInviteFriend
 * @var   boolean                $canRegister
 * @var   boolean                $registrationOpen
 * @var   int                    $ssl
 * @var   int                    $Itemid
 * @var   string                 $return
 * @var   string                 $btnClass
 * @var   string                 $iconOkClass
 * @var   string                 $iconRemoveClass
 * @var   string                 $iconDownloadClass
 * @var   string                 $iconPencilClass
 */


if (!$isMultipleDate)
{
	if ($canRegister)
	{
		$registrationUrl = trim($item->registration_handle_url);

		if ($registrationUrl)
		{
		?>
			<li>
				<a class="<?php echo $btnClass; ?>" href="<?php echo $registrationUrl; ?>" target="_blank"><?php echo JText::_('EB_REGISTER');; ?></a>
			</li>
		<?php
		}
		else
		{
			if ($item->registration_type == 0 || $item->registration_type == 1)
			{
				if ($config->multiple_booking && !$item->has_multiple_ticket_types)
				{
					$url = 'index.php?option=com_eventbooking&task=cart.add_cart&id=' . (int) $item->id . '&Itemid=' . (int) $Itemid;

					if ($item->event_password)
					{
						$extraClass = '';
					}
					else
					{
						$extraClass = 'eb-colorbox-addcart';
					}

					$text = JText::_('EB_REGISTER');
				}
				else
				{
					$url = JRoute::_('index.php?option=com_eventbooking&task=register.individual_registration&event_id=' . $item->id . '&Itemid=' . $Itemid, false, $ssl);

					if ($item->has_multiple_ticket_types)
					{
						$text = JText::_('EB_REGISTER');
					}
					else
					{
						$text = JText::_('EB_REGISTER_INDIVIDUAL');
					}

					$extraClass = '';
				}
				?>
				<li>
					<a class="<?php echo $btnClass . ' ' . $extraClass; ?>" href="<?php echo $url; ?>"><?php echo $text; ?></a>
				</li>
				<?php
			}

			if ($item->min_group_number > 0)
			{
				$minGroupNumber = $item->min_group_number;
			}
			else
			{
				$minGroupNumber = 2;
			}

			if ($item->event_capacity > 0 && (($item->event_capacity - $item->total_registrants) < $minGroupNumber))
			{
				$groupRegistrationAvailable = false;
			}
			else
			{
				$groupRegistrationAvailable = true;
			}

			if ($groupRegistrationAvailable && ($item->registration_type == 0 || $item->registration_type == 2) && !$config->multiple_booking && !$item->has_multiple_ticket_types)
			{
			?>
				<li>
					<a class="<?php echo $btnClass; ?>"
					   href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=register.group_registration&event_id=' . $item->id . '&Itemid=' . $Itemid, false, $ssl); ?>"><?php echo JText::_('EB_REGISTER_GROUP');; ?></a>
				</li>
			<?php
			}
		}
	}
	elseif ($waitingList)
	{
		if ($item->registration_type == 0 || $item->registration_type == 1)
		{
		?>
			<li>
				<a class="<?php echo $btnClass; ?>"
				   href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=register.individual_registration&event_id=' . $item->id . '&Itemid=' . $Itemid, false, $ssl); ?>"><?php echo JText::_('EB_REGISTER_INDIVIDUAL_WAITING_LIST');; ?></a>
			</li>
		<?php
		}

		if (($item->registration_type == 0 || $item->registration_type == 2) && !$config->multiple_booking)
		{
		?>
			<li>
				<a class="<?php echo $btnClass; ?>" href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=register.group_registration&event_id=' . $item->id . '&Itemid=' . $Itemid, false, $ssl); ?>"><?php echo JText::_('EB_REGISTER_GROUP_WAITING_LIST');; ?></a>
			</li>
		<?php
		}
	}
}

if ($config->show_save_to_personal_calendar)
{
?>
	<li>
		<?php echo EventbookingHelperHtml::loadCommonLayout('common/tmpl/save_calendar.php', array('item' => $item, 'Itemid' => $Itemid)); ?>
	</li>
<?php
}

if ($showInviteFriend && $config->show_invite_friend && $registrationOpen)
{
?>
	<li>
		<a class="<?php echo $btnClass; ?> eb-colorbox-invite" href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=invite&tmpl=component&id=' . $item->id . '&Itemid=' . $Itemid, false); ?>"><?php echo JText::_('EB_INVITE_FRIEND'); ?></a>
	</li>
<?php
}

$registrantId = EventbookingHelperAcl::canCancelRegistration($item->id);

if ($registrantId !== false)
{
?>
	<li>
		<a class="<?php echo $btnClass; ?>"
		   href="javascript:cancelRegistration(<?php echo $registrantId; ?>)"><?php echo JText::_('EB_CANCEL_REGISTRATION'); ?></a>
	</li>
<?php
}

if (EventbookingHelperAcl::checkEditEvent($item->id))
{
?>
	<li>
		<a class="<?php echo $btnClass; ?>"
		   href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=event&layout=form&id=' . $item->id . '&Itemid=' . $Itemid . '&return=' . $return, false); ?>">
			<i class="<?php echo $iconPencilClass; ?>"></i>
			<?php echo JText::_('EB_EDIT'); ?>
		</a>
	</li>
<?php
}

if (EventbookingHelperAcl::canChangeEventStatus($item->id))
{
	if ($item->published == 1)
	{
		$link  = JRoute::_('index.php?option=com_eventbooking&task=event.unpublish&id=' . $item->id . '&Itemid=' . $Itemid . '&return=' . $return, false);
		$text  = JText::_('EB_UNPUBLISH');
		$class = $iconRemoveClass;
	}
	else
	{
		$link  = JRoute::_('index.php?option=com_eventbooking&task=event.publish&id=' . $item->id . '&Itemid=' . $Itemid . '&return=' . $return, false);
		$text  = JText::_('EB_PUBLISH');
		$class = $iconOkClass;
	}
	?>
	<li>
		<a class="<?php echo $btnClass; ?>" href="<?php echo $link; ?>">
			<i class="<?php echo $class; ?>"></i>
			<?php echo $text; ?>
		</a>
	</li>
	<?php
}

if ($item->total_registrants && EventbookingHelperAcl::canExportRegistrants($item->id))
{
?>
	<li>
		<a class="<?php echo $btnClass; ?>" href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=registrant.export&event_id=' . $item->id . '&Itemid=' . $Itemid); ?>">
			<i class="<?php echo $iconDownloadClass; ?>"></i>
			<?php echo JText::_('EB_EXPORT_REGISTRANTS'); ?>
		</a>
	</li>
<?php
}