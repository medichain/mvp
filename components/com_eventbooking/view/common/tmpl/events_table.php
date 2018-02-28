<?php
/**
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2017 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */

defined( '_JEXEC' ) or die ;

$hiddenPhoneClass    = $bootstrapHelper->getClassMapping('hidden-phone');
$btnClass            = $bootstrapHelper->getClassMapping('btn');
$baseUri             = JUri::base(true);
?>
<table class="table table-striped table-bordered table-condensed eb-responsive-table">
	<thead>
		<tr>
		<?php
			if ($config->show_image_in_table_layout)
			{
			?>
				<th class="<?php echo $hiddenPhoneClass; ?>">
					<?php echo JText::_('EB_EVENT_IMAGE'); ?>
				</th>
			<?php
			}
		?>
		<th>
			<?php echo JText::_('EB_EVENT_TITLE'); ?>
		</th>
		<th class="date_col">
			<?php echo JText::_('EB_EVENT_DATE'); ?>
		</th>
		<?php
			if ($config->show_event_end_date_in_table_layout)
			{
			?>
				<th class="date_col">
					<?php echo JText::_('EB_EVENT_END_DATE'); ?>
				</th>
			<?php
			}

			if ($config->show_location_in_category_view)
			{
			?>
				<th class="location_col">
					<?php echo JText::_('EB_LOCATION'); ?>
				</th>
			<?php
			}

			if ($config->show_price_in_table_layout)
			{
			?>
				<th class="table_price_col">
					<?php echo JText::_('EB_INDIVIDUAL_PRICE'); ?>
				</th>
			<?php
			}

			if ($config->show_capacity)
			{
			?>
				<th class="capacity_col">
					<?php echo JText::_('EB_CAPACITY'); ?>
				</th>
			<?php
			}

			if ($config->show_registered)
			{
			?>
				<th class="registered_col">
					<?php echo JText::_('EB_REGISTERED'); ?>
				</th>
			<?php
			}

			if ($config->show_available_place)
			{
			?>
				<th class="center available-place-col">
					<?php echo JText::_('EB_AVAILABLE_PLACE'); ?>
				</th>
			<?php
			}
			?>
			<th class="center actions-col">
				<?php echo JText::_('EB_REGISTER'); ?>
			</th>
		</tr>
	</thead>
	<tbody>
	<?php
		$loginLink          = 'index.php?option=com_users&view=login&return=' . base64_encode(JUri::getInstance()->toString());
		$loginToRegisterMsg = str_replace('[LOGIN_LINK]', $loginLink, JText::_('EB_LOGIN_TO_REGISTER'));
		$linkThumbToEvent   = $config->get('link_thumb_to_event_detail_page', 1);

		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$item = $items[$i];

			if ($item->activate_waiting_list == 2)
			{
				$activateWaitingList = $config->activate_waitinglist_feature;
			}
			else
			{
				$activateWaitingList = $item->activate_waiting_list;
			}

			$canRegister = EventbookingHelperRegistration::acceptRegistration($item);

			if ($item->cut_off_date != $nullDate)
			{
				$registrationOpen = ($item->cut_off_minutes < 0);
			}
			else
			{
				$registrationOpen = ($item->number_event_dates > 0);
			}

			$waitingList = false ;

			if (($item->event_capacity > 0) && ($item->event_capacity <= $item->total_registrants) && $activateWaitingList && !$item->user_registered && $registrationOpen)
			{
				$waitingList = true ;
			}

			$isMultipleDate = false;

			if ($config->show_children_events_under_parent_event && $item->event_type == 1)
			{
				$isMultipleDate = true;
			}

			$detailUrl =  JRoute::_(EventbookingHelperRoute::getEventRoute($item->id, $categoryId, $Itemid));
		?>
			<tr class="eb-category-<?php echo $item->category_id; ?><?php if ($item->featured) echo ' eb-featured-event'; ?>">
				<?php
					if ($config->show_image_in_table_layout)
					{
					?>
						<td class="eb-image-column <?php echo $hiddenPhoneClass; ?>">
						<?php
							if ($item->thumb)
							{
								if ($linkThumbToEvent)
								{
								?>
									<a href="<?php echo $detailUrl; ?>"><img src="<?php echo $baseUri . '/media/com_eventbooking/images/thumbs/' . $item->thumb; ?>" class="eb_thumb-left" alt="<?php echo $item->title; ?>"/></a>
								<?php
								}
								else
								{
									if ($item->image && file_exists(JPATH_ROOT . '/' . $item->image))
									{
										$largeImageUri = $baseUri . '/' . $item->image;
									}
									elseif (file_exists(JPATH_ROOT . '/media/com_eventbooking/images/' . $item->thumb))
									{
										$largeImageUri = $baseUri . '/media/com_eventbooking/images/' . $item->thumb;
									}
									else
									{
										$largeImageUri = $baseUri . '/media/com_eventbooking/images/thumbs/' . $item->thumb;
									}
								?>
									<a href="<?php echo $largeImageUri; ?>" class="eb-modal"><img src="<?php echo $baseUri . '/media/com_eventbooking/images/thumbs/' . $item->thumb; ?>" class="eb_thumb-left" alt="<?php echo $item->title; ?>"/></a>
								<?php
								}
							}
							else
							{
								echo ' ';
							}
						?>
					</td>
					<?php
					}
				?>
				<td class="tdno<?php echo $i; ?>" data-content="<?php echo JText::_('EB_EVENT_TITLE'); ?>">
					<?php
						if ($config->hide_detail_button !== '1')
						{
						?>
							<a href="<?php echo JRoute::_(EventbookingHelperRoute::getEventRoute($item->id, $categoryId, $Itemid));?>" class="eb-event-link"><?php echo $item->title ; ?></a>
						<?php
						}
						else
						{
							echo $item->title;
						}
					?>
				</td>
				<td class="tdno<?php echo $i; ?>" data-content="<?php echo JText::_('EB_EVENT_DATE'); ?>">
					<?php
						if ($item->event_date == EB_TBC_DATE)
						{
							echo JText::_('EB_TBC');
						}
						elseif($item->event_date != $nullDate)
						{
							if (strpos($item->event_date, '00:00:00') !== false)
							{
								$dateFormat = $config->date_format;
							}
							else
							{
								$dateFormat = $config->event_date_format;
							}

							echo JHtml::_('date', $item->event_date, $dateFormat, null);
						}
					?>
				</td>
				<?php
					if ($config->show_event_end_date_in_table_layout)
					{
					?>
						<td class="tdno<?php echo $i; ?>" data-content="<?php echo JText::_('EB_EVENT_END_DATE'); ?>">
							<?php
								if ($item->event_end_date == EB_TBC_DATE)
								{
									echo JText::_('EB_TBC');
								}
								elseif($item->event_end_date != $nullDate)
								{
									if (strpos($item->event_end_date, '00:00:00') !== false)
									{
										$dateFormat = $config->date_format;
									}
									else
									{
										$dateFormat = $config->event_date_format;
									}

									echo JHtml::_('date', $item->event_end_date, $dateFormat, null);
								}
							?>
						</td>
					<?php
					}

					if ($config->show_location_in_category_view)
					{
					?>
					<td class="tdno<?php echo $i; ?>" data-content="<?php echo JText::_('EB_LOCATION'); ?>">
						<?php
							if ($item->location_id)
							{
								if ($item->location_address)
								{
									$location = $item->location;

									if ($location->image || EventbookingHelper::isValidMessage($location->description))
									{
									?>
										<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=map&location_id='.$item->location_id.'&Itemid='.$Itemid); ?>"><?php echo $item->location_name ; ?></a>
									<?php
									}
									else
									{
									?>
										<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=map&location_id='.$item->location_id.'&Itemid='.$Itemid.'&tmpl=component'); ?>" class="eb-colorbox-map"><?php echo $item->location_name ; ?></a>
									<?php
									}
								}
								else
								{
									echo $item->location_name;
								}
							}
							else
							{
								echo ' ';
							}
						?>
					</td>
					<?php
					}

					if ($config->show_price_in_table_layout)
					{
						if ($item->price_text)
						{
							$price = $item->price_text;
						}
						elseif ($config->show_discounted_price)
						{
							$price = EventbookingHelper::formatCurrency($item->discounted_price, $config, $item->currency_symbol);
						}
						else
						{
							$price = EventbookingHelper::formatCurrency($item->individual_price, $config, $item->currency_symbol);
						}
					?>
						<td class="tdno<?php echo $i; ?>" data-content="<?php echo JText::_('EB_INDIVIDUAL_PRICE'); ?>">
							<?php echo $price; ?>
						</td>
					<?php
					}

					if ($config->show_capacity)
					{
					?>
						<td class="center tdno<?php echo $i; ?>" data-content="<?php echo JText::_('EB_CAPACITY'); ?>">
							<?php
								if ($item->event_capacity)
								{
									echo $item->event_capacity ;
								}
								elseif ($config->show_capacity != 2)
								{
									echo JText::_('EB_UNLIMITED') ;
								}
							?>
						</td>
					<?php
					}

					if ($config->show_registered)
					{
					?>
						<td class="center tdno<?php echo $i; ?>" data-content="<?php echo JText::_('EB_REGISTERED'); ?>">
							<?php
								if ($item->registration_type != 3)
								{
									echo $item->total_registrants ;
								}
								else
								{
									echo ' ';
								}

							?>
						</td>
					<?php
					}

					if ($config->show_available_place)
					{
					?>
						<td class="center tdno<?php echo $i; ?>" data-content="<?php echo JText::_('EB_AVAILABLE_PLACE'); ?>">
							<?php
								if ($item->event_capacity)
								{
									echo $item->event_capacity - $item->total_registrants;
								}
							?>
						</td>
					<?php
					}
				?>
				<td class="center">
					<?php
						if (!$isMultipleDate && ($waitingList || $canRegister || ($item->registration_type != 3 && $config->display_message_for_full_event)))
						{
							if ($canRegister)
							{
							?>
							<div class="eb-taskbar">
								<ul>
									<?php
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
													$url        = 'index.php?option=com_eventbooking&task=cart.add_cart&id=' . (int) $item->id . '&Itemid=' . (int) $Itemid;

													if ($item->event_password)
													{
														$extraClass = '';
													}
													else
													{
														$extraClass = 'eb-colorbox-addcart';
													}
													$text       = JText::_('EB_REGISTER');
												}
												else
												{
													$url        = JRoute::_('index.php?option=com_eventbooking&task=register.individual_registration&event_id=' . $item->id . '&Itemid=' . $Itemid, false, $ssl);

													if ($item->has_multiple_ticket_types)
													{
														$text       = JText::_('EB_REGISTER');
													}
													else
													{
														$text       = JText::_('EB_REGISTER_INDIVIDUAL');
													}

													$extraClass = '';
												}
												?>
												<li>
													<a class="<?php echo $btnClass . ' ' . $extraClass;?>"
													   href="<?php echo $url; ?>"><?php echo $text; ?></a>
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
													<a class="<?php echo $btnClass; ?>" href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=register.group_registration&event_id='.$item->id.'&Itemid='.$Itemid, false, $ssl) ; ?>"><?php echo JText::_('EB_REGISTER_GROUP');; ?></a>
												</li>
											<?php
											}
										}
									?>
								</ul>
							</div>
							<?php
							}
							elseif ($item->registration_start_date != $nullDate && $item->registration_start_minutes < 0)
							{
								if (strpos($item->registration_start_date, '00:00:00') !== false)
								{
									$dateFormat = $config->date_format;
								}
								else
								{
									$dateFormat = $config->event_date_format;
								}

								echo JText::sprintf('EB_REGISTRATION_STARTED_ON', JHtml::_('date', $item->registration_start_date, $dateFormat));
							}
							elseif($waitingList)
							{
							?>
							<div class="eb-taskbar">
								<ul>
									<?php
									if ($item->registration_type == 0 || $item->registration_type == 1)
									{
									?>
										<li>
											<a class="<?php echo $btnClass; ?>" href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=register.individual_registration&event_id='.$item->id.'&Itemid='.$Itemid, false, $ssl);?>"><?php echo JText::_('EB_REGISTER_INDIVIDUAL_WAITING_LIST'); ; ?></a>
										</li>
									<?php
									}

									if (($item->registration_type == 0 || $item->registration_type == 2) && !$config->multiple_booking)
									{
										?>
										<li>
											<a class="<?php echo $btnClass; ?>" href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=register.group_registration&event_id='.$item->id.'&Itemid='.$Itemid, false, $ssl) ; ?>"><?php echo JText::_('EB_REGISTER_GROUP_WAITING_LIST'); ; ?></a>
										</li>
									<?php
									}
									?>
								</ul>
							</div>
							<?php
							}
							elseif($item->registration_type != 3 && $config->display_message_for_full_event && !$waitingList && $item->registration_start_minutes >= 0)
							{
								if (@$item->user_registered)
								{
									$msg = JText::_('EB_YOU_REGISTERED_ALREADY');
								}
								elseif (!in_array($item->registration_access, $viewLevels))
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
								<div class="eb-notice-message">
									<?php echo $msg ; ?>
								</div>
							<?php
							}
						}

						if ($isMultipleDate)
						{
						?>
							<div class="eb-taskbar">
								<li>
									<a class="<?php echo $btnClass; ?>" href="<?php echo JRoute::_(EventbookingHelperRoute::getEventRoute($item->id, $categoryId, $Itemid));?>"><?php echo JText::_('EB_CHOOSE_DATE_LOCATION'); ; ?></a>
								</li>
							</div>
						<?php
						}
					?>
				</td>
			</tr>
			<?php
		}
	?>
	</tbody>
</table>