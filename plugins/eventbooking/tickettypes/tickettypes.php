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

class plgEventBookingTicketTypes extends JPlugin
{
	protected $table = '#__eb_ticket_types';

	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Render setting form
	 *
	 * @param JTable $row
	 *
	 * @return array
	 */
	public function onEditEvent($row)
	{
		ob_start();
		$this->drawSettingForm($row);

		return array(
			'title' => JText::_('EB_TICKET_TYPES'),
			'form'  => ob_get_clean(),
		);
	}

	/**
	 * Store setting into database, in this case, use params field of plans table
	 *
	 * @param EventbookingTableEvent $row
	 * @param Boolean                $isNew true if create new plan, false if edit
	 */
	public function onAfterSaveEvent($row, $data, $isNew)
	{
		// The plugin will only be available in the backend
		$app = JFactory::getApplication();

		if ($app->isSite())
		{
			return;
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$ids                   = $data['ticket_type_id'];
		$titles                = $data['ticket_type_title'];
		$prices                = $data['ticket_type_price'];
		$descriptions          = $data['ticket_type_description'];
		$capacities            = $data['ticket_type_capacity'];
		$maxTicketsPerBookings = $data['ticket_type_max_tickets_per_booking'];

		$hasMultipleTicketTypes = 0;
		$ticketTypeIds          = array();

		if ($isNew)
		{
			// Reset ID of ticket type
			for ($i = 0, $n = count($ids); $i < $n; $i++)
			{
				$ids[$i] = 0;
			}
		}

		for ($i = 0, $n = count($titles); $i < $n; $i++)
		{
			$id = $ids[$i];

			if (empty($titles[$i]))
			{
				continue;
			}

			$title                = $db->quote(trim($titles[$i]));
			$description          = $db->quote(trim($descriptions[$i]));
			$price                = (float) $prices[$i];
			$capacity             = (int) $capacities[$i];
			$maxTicketsPerBooking = (int) $maxTicketsPerBookings[$i];

			$query->clear();

			if ($id)
			{
				$query->update('#__eb_ticket_types')
					->set('title = ' . $title)
					->set('description = ' . $description)
					->set('price = ' . $price)
					->set('capacity = ' . $capacity)
					->set('max_tickets_per_booking = ' . $maxTicketsPerBooking)
					->where('id = ' . $id);
			}
			else
			{
				$query->insert('#__eb_ticket_types')
					->columns('event_id, title, description, price, capacity, max_tickets_per_booking')
					->values("$row->id, $title, $description ,$price, $capacity, $maxTicketsPerBooking");
			}

			$db->setQuery($query)
				->execute();

			if ($id)
			{
				$ticketTypeIds[] = $id;
			}
			else
			{
				$ticketTypeIds[] = $db->insertid();
			}

			$hasMultipleTicketTypes = 1;
		}

		$query->clear()
			->update('#__eb_events')
			->set('has_multiple_ticket_types = ' . $hasMultipleTicketTypes)
			->where('id = ' . $row->id);
		$db->setQuery($query);
		$db->execute();

		if ($row->event_type == 1)
		{
			// Insert Ticket Type Data for children event
			$query->clear()
				->select('id')
				->from('#__eb_events')
				->where('parent_id = ' . $row->id);
			$db->setQuery($query);
			$childEventIds = $db->loadColumn();

			if ($isNew)
			{
				foreach ($childEventIds as $childEventId)
				{
					$sql = 'INSERT INTO #__eb_ticket_types (event_id, title, description, price, capacity, max_tickets_per_booking, parent_ticket_type_id)'
						. " SELECT $childEventId, title, description, price, capacity, max_tickets_per_booking, id FROM #__eb_ticket_types WHERE event_id = $row->id";
					$db->setQuery($sql);
					$db->execute();
				}
			}
			else
			{
				foreach ($childEventIds as $childEventId)
				{
					foreach ($ticketTypeIds as $ticketTypeId)
					{
						$query->clear()
							->select('*')
							->from('#__eb_ticket_types')
							->where('id = ' . $ticketTypeId);
						$db->setQuery($query);
						$rowParentTicketType = $db->loadObject();

						$query->clear()
							->select('id')
							->from('#__eb_ticket_types')
							->where('event_id = '. $childEventId)
							->where('parent_ticket_type_id = ' . $rowParentTicketType->id);
						$db->setQuery($query);
						$childEventTicketTypeId = (int) $db->loadResult();

						if ($childEventTicketTypeId)
						{
							// Update data of existing ticket type
							$query->clear()
								->update('#__eb_ticket_types')
								->set('title = ' . $db->quote($rowParentTicketType->title))
								->set('description = ' . $db->quote($rowParentTicketType->description))
								->set('price = ' . $db->quote($rowParentTicketType->price))
								->set('capacity = ' . $db->quote($rowParentTicketType->capacity))
								->set('max_tickets_per_booking = ' . $db->quote($rowParentTicketType->max_tickets_per_booking))
								->where('id = ' . $childEventTicketTypeId);
							$db->setQuery($query);
							$db->execute();
						}
						else
						{
							$title                = $db->quote($rowParentTicketType->title);
							$description          = $db->quote($rowParentTicketType->description);
							$price                = $db->quote($rowParentTicketType->price);
							$capacity             = $db->quote($rowParentTicketType->capacity);
							$maxTicketsPerBooking = $db->quote($rowParentTicketType->max_tickets_per_booking);

							// Insert new Ticket type data
							$query->clear()
								->insert('#__eb_ticket_types')
								->columns('event_id, title, description, price, capacity, max_tickets_per_booking, parent_ticket_type_id')
								->values("$childEventId, $title, $description ,$price, $capacity, $maxTicketsPerBooking, $rowParentTicketType->id");
							$db->setQuery($query);
							$db->execute();
						}

						echo $db->getQuery();
					}
				}
			}
		}

		$row->has_multiple_ticket_types = $hasMultipleTicketTypes;

		if ($row->event_type == 1)
		{
			$query->clear()
				->update('#__eb_events')
				->set('has_multiple_ticket_types = ' . $hasMultipleTicketTypes)
				->where('parent_id = ' . $row->id);
			$db->setQuery($query);
			$db->execute();
		}

		if (count($ticketTypeIds))
		{
			$query->clear()
				->delete('#__eb_ticket_types')
				->where('event_id = ' . $row->id)
				->where('id NOT IN (' . implode(',', $ticketTypeIds) . ')');
			$db->setQuery($query)
				->execute();

			if (!empty($childEventIds))
			{
				$query->clear()
					->delete('#__eb_ticket_types')
					->where('event_id IN (' . implode(',', $childEventIds) . ')')
					->where('parent_ticket_type_id NOT IN (' . implode(',', $ticketTypeIds) . ')');
				$db->setQuery($query)
					->execute();
			}
		}

		if (!$hasMultipleTicketTypes)
		{
			if (empty($childEventIds))
			{
				$eventIds = array($row->id);
			}
			else
			{
				$eventIds   = $childEventIds;
				$eventIds[] = $row->id;
			}

			$query->clear()
				->delete('#__eb_ticket_types')
				->where('event_id IN (' . implode(', ', $eventIds) . ')');
			$db->setQuery($query)
				->execute();
		}
	}

	/**
	 * Generate invoice number after registrant complete payment for registration
	 *
	 * @param EventbookingTableRegistrant $row
	 *
	 * @return bool
	 */
	public function onAfterPaymentSuccess($row)
	{
		if (strpos($row->payment_method, 'os_offline') === false)
		{
			$this->processTicketTypes($row);
		}
	}

	/**
	 * Generate invoice number after registrant complete registration in case he uses offline payment
	 *
	 * @param EventbookingTableRegistrant $row
	 */
	public function onAfterStoreRegistrant($row)
	{
		if (strpos($row->payment_method, 'os_offline') !== false)
		{
			$this->processTicketTypes($row);
		}
	}

	/**
	 * Process ticket types data after registration is completed:
	 *
	 * @param EventbookingTableRegistrant $row
	 */
	private function processTicketTypes($row)
	{
		$config = EventbookingHelper::getConfig();
		$event  = EventbookingHelperDatabase::getEvent($row->event_id);

		if ($event->has_multiple_ticket_types && $config->calculate_number_registrants_base_on_tickets_quantity)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('SUM(quantity)')
				->from('#__eb_registrant_tickets')
				->where('registrant_id = ' . $row->id);
			$db->setQuery($query);
			$numberRegistrants = (int) $db->loadResult();

			$row->number_registrants = $numberRegistrants;
			$row->store();
		}
	}

	/**
	 * Display form allows users to change settings on subscription plan add/edit screen
	 *
	 * @param object $row
	 */
	private function drawSettingForm($row)
	{
		$ticketTypes = array();

		if ($row->id)
		{
			$ticketTypes = EventbookingHelperData::getTicketTypes($row->id);
		}
		?>
		<div class="row-fluid">
			<div class="span5">
				<table class="adminlist table table-striped" id="adminForm">
					<thead>
					<tr>
						<th class="nowrap center"><?php echo JText::_('EB_TITLE'); ?></th>
						<th class="nowrap center"><?php echo JText::_('EB_PRICE'); ?></th>
						<th class="nowrap center"><?php echo JText::_('EB_CAPACITY'); ?></th>
						<th class="nowrap center"><?php echo JText::_('EB_MAX_TICKETS_PER_BOOKING'); ?></th>
						<th class="nowrap center"><?php echo JText::_('EB_DESCRIPTION'); ?></th>
						<th class="nowrap center"><?php echo JText::_('EB_REGISTERED'); ?></th>
						<th class="nowrap center"><?php echo JText::_('EB_REMOVE'); ?></th>
					</tr>
					</thead>
					<tbody id="additional_options">
					<?php
					$numberTicketTypes = max(count($ticketTypes), 4);
					for ($i = 0; $i < $numberTicketTypes; $i++)
					{
						if (isset($ticketTypes[$i]))
						{
							$ticketType = $ticketTypes[$i];
						}
						else
						{
							$ticketType              = new stdClass;
							$ticketType->id          = 0;
							$ticketType->title       = '';
							$ticketType->price       = '';
							$ticketType->description = '';
							$ticketType->registered  = 0;
							$ticketType->capacity    = '';
							$ticketType->max_tickets_per_booking = '';		
						}
						?>
						<tr id="option_<?php echo $i; ?>">
							<td>
								<input type="hidden" name="ticket_type_id[]" value="<?php echo $ticketType->id; ?>"/>
								<input type="text" class="input-medium" name="ticket_type_title[]"
								       value="<?php echo $ticketType->title; ?>"/>
							</td>
							<td><input type="text" class="input-mini" name="ticket_type_price[]"
							           value="<?php echo $ticketType->price; ?>"/></td>
							<td><input type="text" class="input-mini" name="ticket_type_capacity[]"
							           value="<?php echo $ticketType->capacity; ?>"/></td>
							<td><input type="text" class="input-mini" name="ticket_type_max_tickets_per_booking[]"
							           value="<?php echo $ticketType->max_tickets_per_booking; ?>"/></td>
							<td><input type="text" class="input-xlarge" name="ticket_type_description[]"
							           value="<?php echo $ticketType->description; ?>"/></td>
							<td class="center"><?php echo $ticketType->registered; ?></td>
							<td>
								<button type="button" class="btn btn-danger"
								        onclick="removeOptionContainer(<?php echo $i; ?>)"><i
										class="icon-remove"></i><?php echo JText::_('EB_REMOVE'); ?></button>
							</td>
						</tr>
						<?php
					}
					?>
					</tbody>
				</table>
				<button type="button" class="btn btn-success" onclick="addOptionContainer()"><i
						class="icon-new icon-white"></i><?php echo JText::_('EB_ADD'); ?></button>
			</div>
		</div>
		<script language="JavaScript">
			function removeOptionContainer(id) {
				if (confirm('<?php echo JText::_('EB_REMOVE_ITEM_CONFIRM'); ?>')) {
					jQuery('#option_' + id).remove();
				}
			}
			(function ($) {
				var countOption = <?php echo $numberTicketTypes; ?>;
				addOptionContainer = (function () {
					var html = '<tr id="option_' + countOption + '">'
					html += '<td><input type="hidden" name="ticket_type_id[]" value = "0" /><input type="text" class="input-medium" name="ticket_type_title[]" value="" /></td>';
					html += '<td><input type="text" class="input-mini" name="ticket_type_price[]" value="" /></td>';
					html += '<td><input type="text" class="input-mini" name="ticket_type_capacity[]" value="" /></td>';
					html += '<td><input type="text" class="input-mini" name="ticket_type_max_tickets_per_booking[]" value="" /></td>';
					html += '<td><input type="text" class="input-xlarge" name="ticket_type_description[]" value="" /></td>';
					html += '<td class="center">0</td>';
					html += '<td><button type="button" class="btn btn-danger" onclick="removeOptionContainer(' + countOption + ')"><i class="icon-remove"></i><?php echo JText::_('EB_REMOVE'); ?></button></td>';
					html += '</tr>';
					$('#additional_options').append(html);
					countOption++;
				})
			})(jQuery)
		</script>
		<?php
	}
}
