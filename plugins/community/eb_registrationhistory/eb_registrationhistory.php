<?php
/**
 * @package		Joomla
 * @subpackage	Event Booking
 * @author  Tuan Pham Ngoc
 * @copyright	Copyright (C) 2010 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die ;
require_once JPATH_BASE . '/components/com_community/libraries/core.php';

class plgCommunityEB_RegistrationHistory extends CApplications
{
	public $name 		= "Event Booking Application";
	public $_name		= 'EventBooking';
	public $_path		= '';
	public $_user		= '';
	public $_my		= '';

    public function plgCommunityEB_RegistrationHistory(& $subject, $config)
    {
    	$this->_path	= JPATH_BASE . '/administrator/components/com_eventbooking';
		$this->_user	= CFactory::getActiveProfile();
		$this->_my		= CFactory::getUser();
		parent::__construct($subject, $config);
    }

	public function onProfileDisplay()
	{
		if(!file_exists($this->_path . '/eventbooking.php')){
			$content = "<div class=\"icon-nopost\"><img src='" . JURI::base() . "components/com_community/assets/error.gif' alt=\"\" /></div>";
			$content .= "<div class=\"content-nopost\">" . JText::_('Event Booking is not installed. Please contact site administrator.') . "</div>";
		}else{
			$user		= CFactory::getActiveProfile();
			$userId = $user->id;
			$currentUserId = $this->_my->id ;
			if ($userId != $currentUserId)
				return;
			$numberRecord = $this->params->get('number_records', 10);
			$rows	= $this->_getRegistrationHistorys($userId, $numberRecord);
			$total = $this->_getTotal($userId) ;
			$content = $this->_getRegistrationHistoryHTML($rows, $userId, $currentUserId, $total) ;
		}

		return $content;
	}
	/**
	 * Draw HTML for registration history
	 *
	 * @param array $rows
	 * @param int $userId
	 * @param int $currentUserId
	 * @param int $total
	 * @return string
	 */
	public function _getRegistrationHistoryHTML($rows, $userId, $currentUserId, $total){
		require_once JPATH_ROOT . '/components/com_eventbooking/helper/helper.php';
		EventBookingHelper::loadLanguage();
		$symbol = EventBookingHelper::getConfigValue('currency_symbol') ;
		$dateFormat = EventBookingHelper::getConfigValue('date_format') ;
		$itemId = EventBookingHelper::getItemid() ;
		ob_start();
		if (count($rows)) {
		?>
			<table width="100%" cellspacing="3" cellpadding="3" class="eb_registration_history">
				<tr>
					<td class="sectiontableheader">
						<?php echo JText::_('EB_NO'); ?>
					</td>
					<td class="sectiontableheader" width="55%">
						<?php echo JText::_('EB_EVENT') ; ?>
					</td>
					<td class="sectiontableheader" align="center">
						<?php echo JText::_('EB_NUMBER_REGISTRANTS') ; ?>
					</td>					
					<td class="sectiontableheader">
						<?php echo JText::_('EB_REGISTRATION_DATE') ; ?>
					</td>
					<td class="sectiontableheader" align="right">
						<?php echo JText::_('EB_AMOUNT') . ' (' . $symbol . ')';?>
					</td>									
				</tr>
				<?php
					$tabs = array('sectiontableentry1', 'sectiontableentry2');
					$k = 0;
					for($i=0, $n = count($rows); $i < $n; $i++) {
						$row = $rows[$i];
						$tab = $tabs[$k];
					?>
					<tr class="<?php echo $tab; ?>">
						<td>
							<?php echo $i + 1; ?>
						</td>
						<td>
							<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=view_event&event_id=' . $row->event_id . '&Itemid=' . $itemId); ?>" target="_blank"><?php echo $row->event_title ; ?></a>
						</td>
						<td align="center">
							<?php echo $row->number_registrants ; ?>
						</td>						
						<td>
							<?php echo JHTML::_('date', $row->register_date, $dateFormat); ?>
						</td>
						<td align="right">
							<?php echo  number_format($row->amount, 2); ?>					
						</td>						
					</tr>
					<?php	
						$k =  1 - $k;
					}
				?>												
			</table>
		<?php	
		}
		$output = ob_get_contents();
		ob_end_clean();

		return $output ;
	}
	/**
	 * Get list of registration records of the current user
	 *
	 * @param int $userId
	 * @param int $numberRecords
	 * @return array
	 */
	public function _getRegistrationHistorys($userId, $numberRecords)
	{
		$db		= & JFactory::getDBO();
		$sql=" SELECT a.*, b.title AS event_title FROM #__eb_registrants AS a INNER JOIN #__eb_events AS b ON a.event_id=b.id WHERE (a.published = 1 OR a.payment_method = 'os_offline') AND a.user_id=$userId "
			. " Order by a.id DESC "
			. "\n LIMIT " . $numberRecords;
		$db->setQuery($sql);

		return $db->loadObjectList();
	}
	/**
	 * Get total number of registration records
	 */
	public function _getTotal($userId) {
		$db		= JFactory::getDBO();
		$sql = 'SELECT COUNT(*) FROM #__eb_registrants  WHERE user_id=' . $userId . ' AND (published=1 OR payment_method = "os_offline") ';
		$db->setQuery($sql);

		return $db->loadResult();
	}
}
