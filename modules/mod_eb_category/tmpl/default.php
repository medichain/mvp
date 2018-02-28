<?php
/**
 * @package        Joomla
 * @subpackage     Event Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 - 2017 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

if (count($rows))
{
?>
	<ul class="menu">
		<?php				
			foreach ($rows  as $row)
			{
			?>
				<li>
					<a href="<?php echo JRoute::_(EventbookingHelperRoute::getCategoryRoute($row->id, $itemId)); ?>">
						<?php
							echo $row->name;

							if ($config->show_number_events)
							{
							?>
								<span class="number_events">( <?php echo $row->total_events .' '. ($row->total_events > 1 ? JText::_('EB_EVENTS') : JText::_('EB_EVENT')) ?>)</span>
							<?php
							}
						?>
					</a>
				</li>
			<?php	
			}
		?>			
	</ul>
<?php
}
?>					

