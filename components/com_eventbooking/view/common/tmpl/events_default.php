<?php
/**
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2017 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */

defined( '_JEXEC' ) or die ;

$return = base64_encode(JUri::getInstance()->toString());
$baseUri = JUri::base(true);
?>
<div id="eb-events">
	<?php
		/* @var EventbookingHelperBootstrap $bootstrapHelper */
		$rowFluidClass     = $bootstrapHelper->getClassMapping('row-fluid');
		$span7Class        = $bootstrapHelper->getClassMapping('span7');
		$span5Class        = $bootstrapHelper->getClassMapping('span5');
		$btnClass          = $bootstrapHelper->getClassMapping('btn');
		$iconPencilClass   = $bootstrapHelper->getClassMapping('icon-pencil');
		$iconOkClass       = $bootstrapHelper->getClassMapping('icon-ok');
		$iconRemoveClass   = $bootstrapHelper->getClassMapping('icon-remove');
		$iconDownloadClass = $bootstrapHelper->getClassMapping('icon-download');

		$loginLink          = 'index.php?option=com_users&view=login&return=' . base64_encode(JUri::getInstance()->toString());
		$loginToRegisterMsg = str_replace('[LOGIN_LINK]', $loginLink, JText::_('EB_LOGIN_TO_REGISTER'));
		$linkThumbToEvent   = $config->get('link_thumb_to_event_detail_page', 1);

		for ($i = 0 , $n = count($events) ;  $i < $n ; $i++)
		{
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

			$layoutData = array(
				'item'              => $event,
				'config'            => $config,
				'isMultipleDate'    => $isMultipleDate,
				'canRegister'       => $canRegister,
				'registrationOpen'  => $registrationOpen,
				'return'            => $return,
				'showInviteFriend'  => false,
				'waitingList'       => $waitingList,
				'ssl'               => $ssl,
				'Itemid'            => $Itemid,
				'btnClass'          => $btnClass,
				'iconOkClass'       => $iconOkClass,
				'iconRemoveClass'   => $iconRemoveClass,
				'iconDownloadClass' => $iconDownloadClass,
				'iconPencilClass'   => $iconPencilClass,
			);

			$registerButtons = EventbookingHelperHtml::loadCommonLayout('common/tmpl/buttons.php', $layoutData);
		?>
			<div class="eb-category-<?php echo $event->category_id; ?> eb-event<?php if ($event->featured) echo ' eb-featured-event'; ?> clearfix" itemscope itemtype="http://schema.org/Event">
				<div class="eb-box-heading clearfix">
					<h2 class="eb-event-title pull-left">
						<?php
						if ($config->hide_detail_button !== '1')
						{
						?>
							<a href="<?php echo $detailUrl; ?>" title="<?php echo $event->title; ?>" class="eb-event-title-link" itemprop="url">
								<span itemprop="name"><?php echo $event->title; ?></span>
							</a>
						<?php
						}
						else
						{
						?>
							<span itemprop="name"><?php echo $event->title; ?></span>
						<?php
						}
						?>
					</h2>
				</div>
				<div class="eb-description clearfix">
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
											<?php echo $isMultipleDate ? JText::_('EB_CHOOSE_DATE_LOCATION') : JText::_('EB_DETAILS');?>
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
					<div class="<?php echo $rowFluidClass; ?>">
					<div class="eb-description-details <?php echo $span7Class; ?>" itemprop="description">
						<?php
							if ($event->thumb && file_exists(JPATH_ROOT . '/media/com_eventbooking/images/thumbs/' . $event->thumb))
							{
								if ($linkThumbToEvent)
								{
								?>
									<a href="<?php echo $detailUrl; ?>"><img src="<?php echo $baseUri . '/media/com_eventbooking/images/thumbs/' . $event->thumb; ?>" class="eb-thumb-left" alt="<?php echo $event->title; ?>"/></a>
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
										<a href="<?php echo $largeImageUri; ?>" class="eb-modal"><img src="<?php echo $baseUri . '/media/com_eventbooking/images/thumbs/' . $event->thumb; ?>" class="eb-thumb-left" alt="<?php echo $event->title; ?>"/></a>
									<?php
								}
							}

							echo $event->short_description;
						?>
					</div>
						<div class="<?php echo $span5Class; ?>">
							<?php
								$layoutData = array(
									'item'  => $event,
									'config' => $config,
									'location' => $event->location,
									'showLocation'=> $config->show_location_in_category_view,
									'isMultipleDate' => $isMultipleDate,
									'nullDate' => $nullDate,
									'Itemid'    => $Itemid,
								);

								echo EventbookingHelperHtml::loadCommonLayout('common/tmpl/event_properties.php', $layoutData);
							?>
						</div>
				</div>
				<?php
				if (!empty($event->ticketTypes))
				{
					echo EventbookingHelperHtml::loadCommonLayout('common/tmpl/tickettypes.php', array('ticketTypes' => $event->ticketTypes, 'config' => $config));
				?>
					<div class="clearfix"></div>
				<?php
				}

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

				if (in_array($config->get('register_buttons_position', 0), array(0,2)))
				{
				?>
					<div class="eb-taskbar clearfix">
						<ul>
							<?php
							echo $registerButtons;

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
				<?php
				}
				?>
				</div>
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