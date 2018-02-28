<?php
/**
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2017 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;
$return = base64_encode(JUri::getInstance()->toString());
$timeFormat        = $config->event_time_format ? $config->event_time_format : 'g:i a';
$dateFormat        = $config->date_format;

/* @var EventbookingHelperBootstrap $bootstrapHelper */
$rowFluidClass     = $bootstrapHelper->getClassMapping('row-fluid');
$btnClass          = $bootstrapHelper->getClassMapping('btn');
$btnInverseClass   = $bootstrapHelper->getClassMapping('btn-inverse');
$iconOkClass       = $bootstrapHelper->getClassMapping('icon-ok');
$iconRemoveClass   = $bootstrapHelper->getClassMapping('icon-remove');
$iconPencilClass   = $bootstrapHelper->getClassMapping('icon-pencil');
$iconDownloadClass = $bootstrapHelper->getClassMapping('icon-download');
$iconCalendarClass = $bootstrapHelper->getClassMapping('icon-calendar');
$iconMapMakerClass = $bootstrapHelper->getClassMapping('icon-map-marker');

$numberColumns = JFactory::getApplication()->getParams()->get('number_columns', 2);

if (!$numberColumns)
{
	$numberColumns = 2;
}

$baseUri = JUri::base(true);
$span = 'span'.intval(12 / $numberColumns);
$span = $bootstrapHelper->getClassMapping($span);
$numberEvents = count($events);
$count = 0;
?>
<div id="eb-events" class="<?php echo $rowFluidClass; ?> clearfix">
	<?php
		$loginLink          = 'index.php?option=com_users&view=login&return=' . base64_encode(JUri::getInstance()->toString());
		$loginToRegisterMsg = str_replace('[LOGIN_LINK]', $loginLink, JText::_('EB_LOGIN_TO_REGISTER'));
		$linkThumbToEvent   = $config->get('link_thumb_to_event_detail_page', 1);

		for ($i = 0 , $n = count($events) ;  $i < $n ; $i++)
		{
			$count++;
			$event = $events[$i] ;

			if ($event->activate_waiting_list == 2)
			{
				$activateWaitingList = $config->activate_waitinglist_feature;
			}
			else
			{
				$activateWaitingList = $event->activate_waiting_list;
			}

			$canRegister = EventbookingHelperRegistration::acceptRegistration($event);

			if ($event->cut_off_date != $nullDate)
			{
				$registrationOpen = ($event->cut_off_minutes < 0);
			}
			else
			{
				$registrationOpen = ($event->number_event_dates > 0);
			}

			$detailUrl = JRoute::_(EventbookingHelperRoute::getEventRoute($event->id, @$category->id, $Itemid));

			$waitingList = false;
			
			if (($event->event_capacity > 0) && ($event->event_capacity <= $event->total_registrants) && $activateWaitingList && !@$event->user_registered && $registrationOpen)
			{
				$waitingList = true;
			}

			$isMultipleDate = false;

			if ($config->show_children_events_under_parent_event && $event->event_type == 1)
			{
				$isMultipleDate = true;
			}
		?>
			<div class="<?php echo $span; ?> eb-category-<?php echo $event->category_id; ?><?php if ($event->featured) echo ' eb-featured-event'; ?> eb-event-box clearfix" itemscope itemtype="http://schema.org/Event">
				<h2 class="eb-event-title-container">
					<?php
					if ($config->hide_detail_button !== '1')
					{
					?>
						<a class="eb-event-title" href="<?php echo $detailUrl; ?>" itemprop="url"><span itemprop="name"><?php echo $event->title; ?></span></a>
					<?php
					}
					else
					{
						echo '<span itemprop="name">' . $event->title . '</span>';
					}
					?>
				</h2>

				<?php
				if ($event->thumb && file_exists(JPATH_ROOT . '/media/com_eventbooking/images/thumbs/' . $event->thumb))
				{
					if ($linkThumbToEvent)
					{
					?>
						<div class="clearfix">
							<a href="<?php echo $detailUrl; ?>"><img src="<?php echo $baseUri . '/media/com_eventbooking/images/thumbs/' . $event->thumb; ?>" class="eb-event-thumb" alt="<?php echo $event->title; ?>" /></a>
						</div>
					<?php
					}
					else
					{
						if ($event->image && file_exists(JPATH_ROOT . '/' . $event->image))
						{
							$largeImageUri = $baseUri . '/' . $event->image;
						}
						elseif (file_exists(JPATH_ROOT . '/media/com_eventbooking/images/' . $event->thumb))
						{
							$largeImageUri = $baseUri . '/media/com_eventbooking/images/' . $event->thumb;
						}
						else
						{
							$largeImageUri = $baseUri . '/media/com_eventbooking/images/thumbs/' . $event->thumb;
						}
						?>
							<div class="clearfix">
								<a href="<?php echo $largeImageUri; ?>" class="eb-modal"><img src="<?php echo $baseUri . '/media/com_eventbooking/images/thumbs/' . $event->thumb; ?>" class="eb-event-thumb" alt="<?php echo $event->title; ?>" /></a>
							</div>
						<?php
					}
				}
				?>
				<div class="eb-event-date-time clearfix">
					<?php
					if ($event->event_date != EB_TBC_DATE)
					{
					?>
						<meta itemprop="startDate" content="<?php echo JFactory::getDate($event->event_date)->format("Y-m-d\TH:i"); ?>">
					<?php
					}

					if ($event->event_end_date != $nullDate)
					{
					?>
						<meta itemprop="endDate" content="<?php echo JFactory::getDate($event->event_end_date)->format("Y-m-d\TH:i"); ?>">
					<?php
					}
					?>
					<i class="<?php echo $iconCalendarClass; ?>"></i>

					<?php
					if ($event->event_date != EB_TBC_DATE)
					{
						echo JHtml::_('date', $event->event_date, $dateFormat, null);
					}
					else
					{
						echo JText::_('EB_TBC');
					}

					if (strpos($event->event_date, '00:00:00') === false)
					{
					?>
						<span class="eb-time"><?php echo JHtml::_('date', $event->event_date, $timeFormat, null) ?></span>
					<?php
					}

					if ($event->event_end_date != $nullDate)
					{
						if (strpos($event->event_end_date, '00:00:00') === false)
						{
							$showTime = true;
						}
						else
						{
							$showTime = false;
						}

						$startDate =  JHtml::_('date', $event->event_date, 'Y-m-d', null);
						$endDate   = JHtml::_('date', $event->event_end_date, 'Y-m-d', null);

						if ($startDate == $endDate)
						{
							if ($showTime)
							{
							?>
								-<span class="eb-time"><?php echo JHtml::_('date', $event->event_end_date, $timeFormat, null) ?></span>
							<?php
							}
						}
						else
						{
							echo " - " .JHtml::_('date', $event->event_end_date, $dateFormat, null);
							if ($showTime)
							{
							?>
								<span class="eb-time"><?php echo JHtml::_('date', $event->event_end_date, $timeFormat, null) ?></span>
							<?php
							}
						}
					}
					?>
				</div>
				<div class="eb-event-location-price <?php echo $rowFluidClass; ?> clearfix">
					<?php
					if ($event->location_id)
					{
					?>
						<div class="eb-event-location <?php echo $bootstrapHelper->getClassMapping('span9'); ?>">
							<i class="icon-location <?php echo $iconMapMakerClass; ?>"></i>
							<?php
							if ($event->location_address)
							{
							?>
								<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=map&location_id='.$event->location_id.'&tmpl=component'); ?>" class="eb-colorbox-map"><span><?php echo $event->location_name ; ?></span></a>
							<?php
							}
							else
							{
								echo $event->location_name;
							}
							?>
						</div>
						<?php
					}

					if ($config->show_discounted_price)
					{
						$price = $event->discounted_price;
					}
					else
					{
						$price = $event->individual_price;
					}

					if ($event->price_text)
					{
						$priceDisplay = $event->price_text;
					}
					elseif ($price > 0)
					{
						$symbol        = $event->currency_symbol ? $event->currency_symbol : $config->currency_symbol;
						$priceDisplay  = EventbookingHelper::formatCurrency($price, $config, $symbol);
					}
					elseif ($config->show_price_for_free_event)
					{
						$priceDisplay = JText::_('EB_FREE');
					}
					else
					{
						$priceDisplay = '';
					}

					if ($priceDisplay)
					{
					?>
						<div class="eb-event-price btn-primary <?php echo $bootstrapHelper->getClassMapping('span3'); ?> pull-right">
							<span class="eb-individual-price"><?php echo $priceDisplay; ?></span>
						</div>
					<?php
					}
					?>
				</div>
				<div class="eb-event-short-description clearfix">
					<?php echo $event->short_description; ?>
				</div>
				<?php
				if (!$isMultipleDate)
				{
					if (!$canRegister && $event->registration_type != 3 && $config->display_message_for_full_event && !$waitingList && $event->registration_start_minutes >= 0)
					{
						if (@$event->user_registered)
						{
							$msg = JText::_('EB_YOU_REGISTERED_ALREADY');
						}
						elseif (!in_array($event->registration_access, $viewLevels))
						{
							if (JFactory::getUser()->id)
							{
								$msg = JText::_('EB_REGISTRATION_NOT_AVAILABLE_FOR_ACCOUNT');
							}
							else
							{
								$msg = $loginToRegisterMsg;
							}
						}
						else
						{
							$msg = JText::_('EB_NO_LONGER_ACCEPT_REGISTRATION');
						}
						?>
						<div class="clearfix">
							<p class="text-info eb-notice-message"><?php echo $msg; ?></p>
						</div>
						<?php
					}
				}
				?>
				<div class="eb-taskbar clearfix">
					<ul>
						<?php
						if (!$isMultipleDate)
						{
							if ($canRegister)
							{
								$registrationUrl = trim($event->registration_handle_url);

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
									if ($event->registration_type == 0 || $event->registration_type == 1)
									{
										if ($config->multiple_booking && !$event->has_multiple_ticket_types)
										{
											$url = 'index.php?option=com_eventbooking&task=cart.add_cart&id=' . (int) $event->id . '&Itemid=' . (int) $Itemid;

											if ($event->event_password)
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
											$url = JRoute::_('index.php?option=com_eventbooking&task=register.individual_registration&event_id=' . $event->id . '&Itemid=' . $Itemid, false, $ssl);

											if ($event->has_multiple_ticket_types)
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

									if (($event->registration_type == 0 || $event->registration_type == 2) && !$config->multiple_booking && !$event->has_multiple_ticket_types)
									{
										?>
										<li>
											<a class="<?php echo $btnClass; ?>"
											   href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=register.group_registration&event_id=' . $event->id . '&Itemid=' . $Itemid, false, $ssl); ?>"><?php echo JText::_('EB_REGISTER_GROUP');; ?></a>
										</li>
										<?php
									}
								}
							}
							elseif ($waitingList)
							{
								if ($event->registration_type == 0 || $event->registration_type == 1)
								{
									?>
									<li>
										<a class="<?php echo $btnClass; ?>"
										   href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=register.individual_registration&event_id=' . $event->id . '&Itemid=' . $Itemid, false, $ssl); ?>"><?php echo JText::_('EB_REGISTER_INDIVIDUAL_WAITING_LIST');; ?></a>
									</li>
									<?php
								}

								if (($event->registration_type == 0 || $event->registration_type == 2) && !$config->multiple_booking)
								{
									?>
									<li>
										<a class="<?php echo $btnClass; ?>" href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=register.group_registration&event_id=' . $event->id . '&Itemid=' . $Itemid, false, $ssl); ?>"><?php echo JText::_('EB_REGISTER_GROUP_WAITING_LIST');; ?></a>
									</li>
									<?php
								}
							}
						}

						if ($config->hide_detail_button !== '1' || $isMultipleDate)
						{
						?>
							<li>
								<a class="<?php echo $btnClass; ?> btn-primary" href="<?php echo $detailUrl; ?>">
									<?php echo $isMultipleDate ? JText::_('EB_CHOOSE_DATE_LOCATION') : JText::_('EB_DETAILS');?>
								</a>
							</li>
						<?php
						}
						?>
					</ul>
				</div>
			</div>
		<?php
			if ($count % $numberColumns == 0 && $count < $numberEvents)
			{
			?>
				</div>
				<div class="clearfix <?php echo $rowFluidClass; ?>">
			<?php
			}
		}
	?>
</div>

<script type="text/javascript">
	function cancelRegistration(registrantId) {
		var form = document.adminForm ;
		if (confirm("<?php echo JText::_('EB_CANCEL_REGISTRATION_CONFIRM'); ?>")) {
			form.task.value = 'registrant.cancel' ;
			form.id.value = registrantId ;
			form.submit() ;
		}
	}
</script>