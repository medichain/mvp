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
$timeFormat        = $config->event_time_format ? $config->event_time_format : 'g:i a';
$dateFormat        = $config->date_format;
$rowFluidClass     = $bootstrapHelper->getClassMapping('row-fluid');
$span8Class        = $bootstrapHelper->getClassMapping('span8');
$span4Class        = $bootstrapHelper->getClassMapping('span4');
$btnClass          = $bootstrapHelper->getClassMapping('btn');
$btnInverseClass   = $bootstrapHelper->getClassMapping('btn-inverse');
$iconOkClass       = $bootstrapHelper->getClassMapping('icon-ok');
$iconRemoveClass   = $bootstrapHelper->getClassMapping('icon-remove');
$iconPencilClass   = $bootstrapHelper->getClassMapping('icon-pencil');
$iconDownloadClass = $bootstrapHelper->getClassMapping('icon-download');
$iconCalendarClass = $bootstrapHelper->getClassMapping('icon-calendar');
$iconMapMakerClass = $bootstrapHelper->getClassMapping('icon-map-marker');
$return = base64_encode(JUri::getInstance()->toString());
$baseUri = JUri::base(true);
$linkThumbToEvent   = $config->get('link_thumb_to_event_detail_page', 1);
?>
<div id="eb-events" class="eb-events-timeline">
	<?php
		for ($i = 0 , $n = count($events) ;  $i < $n ; $i++)
		{
			$event = $events[$i];

			if ($event->activate_waiting_list == 2)
			{
				$activateWaitingList = $config->activate_waitinglist_feature;
			}
			else
			{
				$activateWaitingList = $event->activate_waiting_list;
			}

			$canRegister = EventbookingHelperRegistration::acceptRegistration($event);
			$detailUrl = JRoute::_(EventbookingHelperRoute::getEventRoute($event->id, @$category->id, $Itemid));

			if ($event->cut_off_date != $nullDate)
			{
				$registrationOpen = ($event->cut_off_minutes < 0);
			}
			else
			{
				$registrationOpen = ($event->number_event_dates > 0);
			}

			$waitingList = false ;

			if ($event->activate_waiting_list == 2)
			{
				$activateWaitingList = $config->activate_waitinglist_feature;
			}
			else
			{
				$activateWaitingList = $event->activate_waiting_list;
			}

			if ($event->event_capacity > 0 && $event->event_capacity <= $event->total_registrants && $activateWaitingList && !@$event->user_registered && $registrationOpen)
			{
				$waitingList = true ;
			}

			$isMultipleDate = false;

			if ($config->show_children_events_under_parent_event && $event->event_type == 1)
			{
				$isMultipleDate = true;
			}

			$layoutData = array(
				'item'              => $event,
				'config'            => $config,
				'isMultipleDate'    => $isMultipleDate,
				'canRegister'       => $canRegister,
				'Itemid'            => $Itemid,
				'waitingList'       => $waitingList,
				'ssl'               => $ssl,
				'btnClass'          => $btnClass,
				'iconOkClass'       => $iconOkClass,
				'iconRemoveClass'   => $iconRemoveClass,
				'iconDownloadClass' => $iconDownloadClass,
				'registrationOpen'  => $registrationOpen,
				'return'            => $return,
				'iconPencilClass'   => $iconPencilClass,
				'showInviteFriend'  => false,
			);

			$registerButtons = EventbookingHelperHtml::loadCommonLayout('common/tmpl/buttons.php', $layoutData);
		?>
		<div class="eb-category-<?php echo $event->category_id; ?> eb-event-container<?php if ($event->featured) echo ' eb-featured-event'; ?>" itemscope itemtype="http://schema.org/Event">
			<div class="eb-event-date-container">
				<div class="eb-event-date <?php echo $btnInverseClass; ?>">
					<?php
						if ($event->event_date != EB_TBC_DATE)
						{
						?>
							<div class="eb-event-date-day">
								<?php echo JHtml::_('date', $event->event_date, 'd', null); ?>
							</div>
							<div class="eb-event-date-month">
								<?php echo JHtml::_('date', $event->event_date, 'M', null); ?>
							</div>
							<div class="eb-event-date-year">
								<?php echo JHtml::_('date', $event->event_date, 'Y', null); ?>
							</div>
						<?php
						}
						else
						{
							echo JText::_('EB_TBC');
						}
					?>
				</div>
			</div>
			<h2 class="eb-even-title-container">
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
			<div class="eb-event-information <?php echo $rowFluidClass; ?>">
				<div class="<?php echo $span8Class; ?>">
					<div class="clearfix">
						<span class="eb-event-date-info">
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
						</span>
					</div>
					<?php
						if ($event->location_id)
						{
							$location = $event->location;
						?>
						<div class="clearfix">
							<i class="<?php echo $iconMapMakerClass; ?>"></i>
							<?php
								if ($event->location_address)
								{
									if ($location->image || EventbookingHelper::isValidMessage($location->description))
									{
									?>
										<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=map&location_id='.$event->location_id.'&Itemid='.$Itemid); ?>"><span><?php echo $event->location_name ; ?></span></a>
									<?php
									}
									else
									{
									?>
										<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=map&location_id='.$event->location_id.'&tmpl=component'); ?>" class="eb-colorbox-map"><span><?php echo $event->location_name ; ?></span></a>
									<?php
									}
								?>
									<div style="display:none" itemprop="location" itemscope itemtype="http://schema.org/Place">
									<div itemprop="name"><?php echo $location->name; ?></div>
									<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
										<span itemprop="streetAddress"><?php echo $location->address; ?></span>
									</div>
								</div>
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
					?>
				</div>
				<?php
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
					<div class="<?php echo $span4Class; ?>">
						<div class="eb-event-price-container btn-primary">
							<span class="eb-individual-price"><?php echo $priceDisplay; ?></span>
						</div>
					</div>
				<?php
				}
				?>
			</div>
			<?php
				if (in_array($config->get('register_buttons_position', 0), array(1,2)))
				{
				?>
					<div class="eb-taskbar eb-register-buttons-top clearfix">
						<ul>
							<?php
								echo $registerButtons;

								if ($config->hide_detail_button !== '1' || $isMultipleDate)
								{
								?>
									<li>
										<a class="<?php echo $btnClass; ?> btn-primary" href="<?php echo $detailUrl; ?>">
											<?php echo $isMultipleDate ? JText::_('EB_CHOOSE_DATE_LOCATION') : JText::_('EB_DETAILS'); ?>
										</a>
									</li>
								<?php
								}
							?>
						</ul>
					</div>
				<?php
				}
			?>
			<div class="eb-description-details clearfix" itemprop="description">
				<?php
					if ($event->thumb && file_exists(JPATH_ROOT . '/media/com_eventbooking/images/thumbs/' . $event->thumb))
					{
						if ($linkThumbToEvent)
						{
						?>
							<a href="<?php echo $detailUrl; ?>"><img src="<?php echo $baseUri . '/media/com_eventbooking/images/thumbs/' . $event->thumb; ?>" class="eb-thumb-left" alt="<?php echo $event->title; ?>" /></a>
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
								<a href="<?php echo $largeImageUri; ?>" class="eb-modal"><img src="<?php echo $baseUri . '/media/com_eventbooking/images/thumbs/' . $event->thumb; ?>" class="eb-thumb-left" alt="<?php echo $event->title; ?>" /></a>
							<?php
						}
					}

					echo $event->short_description;
				?>
			</div>
			<?php
				$ticketsLeft = $event->event_capacity - $event->total_registrants ;

				if ($event->individual_price > 0 || $ticketsLeft > 0)
				{
				?>
					<div style="display:none;" itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer">
						<?php
						if ($event->individual_price > 0)
						{
						?>
							<span itemprop="lowPrice"><?php echo EventbookingHelper::formatCurrency($event->individual_price, $config, $event->currency_symbol); ?></span>
						<?php
						}

						if ($ticketsLeft > 0)
						{
						?>
							<span itemprop="offerCount"><?php echo $ticketsLeft;?></span>
						<?php
						}
						?>
					</div>
				<?php
				}

				if (!empty($event->ticketTypes))
				{
					echo EventbookingHelperHtml::loadCommonLayout('common/tmpl/tickettypes.php', array('ticketTypes' => $event->ticketTypes, 'config' => $config));
				?>
					<div class="clearfix"></div>
				<?php
				}

				if (in_array($config->get('register_buttons_position', 0), array(0,2)))
				{
				?>
					<div class="eb-taskbar eb-register-buttons-bottom clearfix">
						<ul>
							<?php
							echo $registerButtons;

							if ($config->hide_detail_button !== '1' || $isMultipleDate)
							{
							?>
								<li>
									<a class="<?php echo $btnClass; ?> btn-primary" href="<?php echo $detailUrl; ?>">
										<?php echo $isMultipleDate ? JText::_('EB_CHOOSE_DATE_LOCATION') : JText::_('EB_DETAILS'); ?>
									</a>
								</li>
							<?php
							}
							?>
						</ul>
					</div>
				<?php
				}
			?>
		</div>
		<?php
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