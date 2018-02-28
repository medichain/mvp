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

class EventbookingHelperTicket
{
	/**
	 * Format ticket number
	 *
	 * @param string    $ticketPrefix
	 * @param int       $ticketNumber
	 * @param RADConfig $config
	 *
	 * @return string The formatted ticket number
	 */
	public static function formatTicketNumber($ticketPrefix, $ticketNumber, $config)
	{
		return $ticketPrefix . str_pad($ticketNumber, $config->ticket_number_length ? $config->ticket_number_length : 5, '0', STR_PAD_LEFT);
	}

	/**
	 * Generate Ticket PDFs
	 *
	 * @param EventbookingTableRegistrant $row
	 * @param RADConfig                   $config
	 */
	public static function generateTicketsPDF($row, $config)
	{
		if (EventbookingHelper::isMethodOverridden('EventbookingHelperOverrideHelperTicket', 'generateTicketsPDF'))
		{
			EventbookingHelperOverrideTicket::generateTicketsPDF($row, $config);

			return;
		}

		require_once JPATH_ROOT . "/components/com_eventbooking/tcpdf/tcpdf.php";
		require_once JPATH_ROOT . "/components/com_eventbooking/tcpdf/config/lang/eng.php";

		$pdf = new TCPDF($config->get('ticket_page_orientation', PDF_PAGE_ORIENTATION), PDF_UNIT, $config->get('ticket_page_format', PDF_PAGE_FORMAT), true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(JFactory::getConfig()->get("sitename"));
		$pdf->SetTitle('Ticket');
		$pdf->SetSubject('Ticket');
		$pdf->SetKeywords('Ticket');
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT);
		$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->setFooterMargin(PDF_MARGIN_FOOTER);
		//set auto page breaks
		$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		$font = empty($config->pdf_font) ? 'times' : $config->pdf_font;
		$pdf->SetFont($font, '', 8);

		EventbookingHelper::loadLanguage();

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);;
		$query->select('*')
			->from('#__eb_events')
			->where('id = ' . (int) $row->event_id);

		if ($fieldSuffix = EventbookingHelper::getFieldSuffix($row->language))
		{
			EventbookingHelperDatabase::getMultilingualFields($query, array('title', 'short_description', 'description'), $fieldSuffix);
		}


		$db->setQuery($query);
		$rowEvent = $db->loadObject();


		if (EventbookingHelper::isValidMessage($rowEvent->ticket_layout))
		{
			$ticketLayout = $rowEvent->ticket_layout;
		}
		else
		{
			$ticketLayout = $config->default_ticket_layout;
		}

		if ($rowEvent->ticket_bg_image)
		{
			$backgroundImage = $rowEvent->ticket_bg_image;
		}
		else
		{
			$backgroundImage = $config->get('default_ticket_bg_image');
		}

		if ($backgroundImage && file_exists(JPATH_ROOT . '/' . $backgroundImage))
		{
			$backgroundImagePath = JPATH_ROOT . '/' . $backgroundImage;
		}
		else
		{
			$backgroundImagePath = '';
		}

		if ($rowEvent->collect_member_information === '')
		{
			$collectMemberInformation = $config->collect_member_information;
		}
		else
		{
			$collectMemberInformation = $rowEvent->collect_member_information;
		}

		if ($row->is_group_billing && $collectMemberInformation)
		{
			$query->clear()
				->select('*')
				->from('#__eb_registrants')
				->where('group_id = ' . $row->id);
			$db->setQuery($query);
			$rowMembers = $db->loadObjectList();

			foreach ($rowMembers as $rowMember)
			{
				$pdf->AddPage();

				if ($backgroundImagePath)
				{
					$pdf->Image($backgroundImagePath, $rowEvent->ticket_bg_left, $rowEvent->ticket_bg_top);
				}

				$rowFields = EventbookingHelperRegistration::getFormFields($row->event_id, 0);

				$form = new RADForm($rowFields);
				$data = EventbookingHelperRegistration::getRegistrantData($rowMember, $rowFields);
				$form->bind($data);
				$form->buildFieldsDependency();

				if (is_callable('EventbookingHelperOverrideRegistration::buildTags'))
				{
					$replaces = EventbookingHelperOverrideRegistration::buildTags($rowMember, $form, $rowEvent, $config);
				}
				elseif (is_callable('EventbookingHelperOverrideHelper::buildTags'))
				{
					$replaces = EventbookingHelperOverrideHelper::buildTags($rowMember, $form, $rowEvent, $config);
				}
				else
				{
					$replaces = EventbookingHelperRegistration::buildTags($rowMember, $form, $rowEvent, $config);
				}

				$replaces['ticket_number']     = self::formatTicketNumber($rowEvent->ticket_prefix, $rowMember->ticket_number, $config);
				$replaces['registration_date'] = JHtml::_('date', $row->register_date, $config->date_format);

				$output = $ticketLayout;

				foreach ($replaces as $key => $value)
				{
					$key    = strtoupper($key);
					$output = str_ireplace("[$key]", $value, $output);
				}

				$output = static::processQRCODE($rowMember, $output);

				$pdf->writeHTML($output, true, false, false, false, '');
			}
		}
		else
		{
			$pdf->AddPage();

			if ($backgroundImagePath)
			{
				$pdf->Image($backgroundImagePath, $rowEvent->ticket_bg_left, $rowEvent->ticket_bg_top);
			}

			if ($config->multiple_booking)
			{
				$rowFields = EventbookingHelperRegistration::getFormFields($row->id, 4);
			}
			elseif ($row->is_group_billing)
			{
				$rowFields = EventbookingHelperRegistration::getFormFields($row->event_id, 1);
			}
			else
			{
				$rowFields = EventbookingHelperRegistration::getFormFields($row->event_id, 0);
			}

			$form = new RADForm($rowFields);
			$data = EventbookingHelperRegistration::getRegistrantData($row, $rowFields);
			$form->bind($data);
			$form->buildFieldsDependency();

			if (is_callable('EventbookingHelperOverrideRegistration::buildTags'))
			{
				$replaces = EventbookingHelperOverrideRegistration::buildTags($row, $form, $rowEvent, $config);
			}
			elseif (is_callable('EventbookingHelperOverrideHelper::buildTags'))
			{
				$replaces = EventbookingHelperOverrideHelper::buildTags($row, $form, $rowEvent, $config);
			}
			else
			{
				$replaces = EventbookingHelperRegistration::buildTags($row, $form, $rowEvent, $config);
			}

			$replaces['ticket_number']     = self::formatTicketNumber($rowEvent->ticket_prefix, $row->ticket_number, $config);
			$replaces['registration_date'] = JHtml::_('date', $row->register_date, $config->date_format);

			foreach ($replaces as $key => $value)
			{
				$key          = strtoupper($key);
				$ticketLayout = str_ireplace("[$key]", $value, $ticketLayout);
			}

			$ticketLayout = static::processQRCODE($row, $ticketLayout);

			$pdf->writeHTML($ticketLayout, true, false, false, false, '');
		}

		$filePath = JPATH_ROOT . '/media/com_eventbooking/tickets/ticket_' . str_pad($row->id, 5, '0', STR_PAD_LEFT) . '.pdf';

		$pdf->Output($filePath, 'F');
	}

	/**
	 * Generate TICKET_QRCODE
	 *
	 * @param $row
	 */
	public static function generateTicketQrcode($row)
	{
		$filename = $row->ticket_code . '.png';

		if (!file_exists(JPATH_ROOT . '/media/com_eventbooking/qrcodes/' . $filename))
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/vendor/phpqrcode/qrlib.php';
			QRcode::png($row->ticket_code, JPATH_ROOT . '/media/com_eventbooking/qrcodes/' . $filename);
		}
	}

	/**
	 * Process QRCODE for ticket. Support [QRCODE] and [TICKET_QRCODE] tag
	 *
	 * @param $row
	 * @param $output
	 *
	 * @return mixed
	 */
	protected static function processQRCODE($row, $output)
	{
		if (strpos($output, '[QRCODE]') !== false)
		{
			EventbookingHelper::generateQrcode($row->id);
			$imgTag = '<img src="media/com_eventbooking/qrcodes/' . $row->id . '.png" border="0" />';
			$output = str_ireplace("[QRCODE]", $imgTag, $output);
		}

		if ($row->ticket_code && strpos($output, '[TICKET_QRCODE]') !== false)
		{
			static::generateTicketQrcode($row);
			$imgTag = '<img src="media/com_eventbooking/qrcodes/' . $row->ticket_code . '.png" border="0" />';
			$output = str_ireplace("[TICKET_QRCODE]", $imgTag, $output);
		}

		return $output;
	}
}
