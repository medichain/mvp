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

use Joomla\Registry\Registry;

class plgEventBookingMailchimp extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		JFactory::getLanguage()->load('plg_eventbooking_mailchimp', JPATH_ADMINISTRATOR);
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_eventbooking/table');
	}

	/**
	 * Render settings form
	 *
	 * @param EventbookingTableEvent $row
	 *
	 * @return array
	 */
	public function onEditEvent($row)
	{
		ob_start();
		$this->drawSettingForm($row);

		return array('title' => JText::_('PLG_EB_MAILCHIMP_SETTINGS'),
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
		// $row of table EB_plans
		$params = new Registry($row->params);
		$params->set('mailchimp_list_ids', implode(',', $data['mailchimp_list_ids']));
		$row->params = $params->toString();

		$row->store();
	}

	/**
	 * Run when registration record stored to database
	 *
	 * @param EventbookingTableRegistrant $row
	 */
	public function onAfterStoreRegistrant($row)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Only add subscribers to newsletter if they agree.
		if ($subscribeNewsletterField = $this->params->get('subscribe_newsletter_field'))
		{
			$query->select('name, fieldtype')
				->from('#__eb_fields')
				->where('id = ' . $db->quote((int) $subscribeNewsletterField));
			$db->setQuery($query);
			$field     = $db->loadObject();
			$fieldType = $field->fieldtype;
			$fieldName = $field->name;

			if ($fieldType == 'Checkboxes')
			{
				if (!isset($_POST[$fieldName]))
				{
					return;
				}
			}
			else
			{
				$fieldValue = strtolower(JFactory::getApplication()->input->getString($fieldName));

				if (empty($fieldValue) || $fieldValue == 'no' || $fieldValue == '0')
				{
					return;
				}
			}
		}

		$event = JTable::getInstance('EventBooking', 'Event');
		$event->load($row->event_id);
		$params  = new Registry($event->params);
		$listIds = $params->get('mailchimp_list_ids', '');

		if ($listIds != '')
		{
			$listIds = explode(',', $listIds);

			if (count($listIds))
			{
				require_once dirname(__FILE__) . '/api/MailChimp.php';

				$this->subscribeToMailchimpMailingLists($row, $listIds);

				if ($row->is_group_billing && $this->params->get('add_group_members_to_newsletter'))
				{
					$query->clear()
						->select('user_id, first_name, last_name, email')
						->from('#__eb_registrants')
						->where('group_id = ' . (int) $row->id);
					$db->setQuery($query);
					$groupMembers = $db->loadObjectList();

					foreach ($groupMembers as $groupMember)
					{
						$this->subscribeToMailchimpMailingLists($groupMember, $listIds);
					}
				}
			}
		}
	}


	/**
	 * @param EventbookingTableRegistrant $row
	 * @param array                       $listIds
	 */
	private function subscribeToMailchimpMailingLists($row, $listIds)
	{
		if (!JMailHelper::isEmailAddress($row->email))
		{
			return;
		}

		$mailchimp = new MailChimp($this->params->get('api_key'));

		foreach ($listIds as $listId)
		{
			if ($listId)
			{
				$mailchimp->call('lists/subscribe', array(
					'id'                => $listId,
					'email'             => array('email' => $row->email),
					'merge_vars'        => array('FNAME' => $row->first_name, 'LNAME' => $row->last_name),
					'double_optin'      => false,
					'update_existing'   => true,
					'replace_interests' => false,
					'send_welcome'      => false,
				));
			}
		}
	}

	/**
	 * Display form allows users to change settings on event add/edit screen
	 *
	 * @param EventbookingTableEvent $row
	 */
	private function drawSettingForm($row)
	{
		require_once dirname(__FILE__) . '/api/MailChimp.php';

		$mailchimp = new MailChimp($this->params->get('api_key'));
		$lists     = $mailchimp->call('lists/list');

		if ($lists === false)
		{
			return;
		}

		$params  = new Registry($row->params);

		if($row->id)
		{
			$listIds = explode(',', $params->get('mailchimp_list_ids', ''));
		}
		else
		{
			$lists = explode(',', $this->params->get('default_list_ids', ''));
		}

		$options = array();
		$lists   = $lists['data'];

		if (count($lists))
		{
			foreach ($lists as $list)
			{
				$options[] = JHtml::_('select.option', $list['id'], $list['name']);
			}
		}
		?>
		<table class="admintable adminform" style="width: 90%;">
			<tr>
				<td width="220" class="key">
					<?php echo JText::_('PLG_EB_MAILCHIMP_ASSIGN_TO_LISTS'); ?>
				</td>
				<td>
					<?php echo JHtml::_('select.genericlist', $options, 'mailchimp_list_ids[]', 'class="inputbox" multiple="multiple" size="10"', 'value', 'text', $listIds) ?>
				</td>
				<td>
					<?php echo JText::_('PLG_EB_ACYMAILING_ASSIGN_TO_LISTS_EXPLAIN'); ?>
				</td>
			</tr>
		</table>
		<?php
	}
}
