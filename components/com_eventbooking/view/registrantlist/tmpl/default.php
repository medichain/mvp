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

$hiddenPhoneClass = $this->bootstrapHelper->getClassMapping('hidden-phone');
$cols = 3;

$pageHeading = JText::_('EB_REGISTRANT_LIST');
$pageHeading = str_replace('[EVENT_TITLE]', $this->event->title, $pageHeading);
?>
<div id="eb-registrants-list-page" class="eb-container">
<h1 class="eb_title"><?php echo $pageHeading; ?></h1>
<?php
if (count($this->items))
{
	$showNumberRegistrants = false;
	foreach($this->items as $item)
	{
		if ($item->number_registrants > 1)
		{
			$showNumberRegistrants = true;
			$cols++;
			break;
		}
	}

	if (in_array('last_name', $this->coreFields))
	{
		$cols++;
		$showLastName = true;
	}
	else
	{
		$showLastName = false;
	}
?>
	<table class="table table-striped table-bordered table-condensed">
	<thead>
		<tr>
			<th width="5" class="<?php echo $hiddenPhoneClass; ?>">
				<?php echo JText::_( 'NUM' ); ?>
			</th>
			<th>
				<?php echo JText::_('EB_FIRST_NAME'); ?>
			</th>
			<?php
				if ($showLastName)
				{
				?>
					<th>
						<?php echo JText::_('EB_LAST_NAME'); ?>
					</th>
				<?php
					$cols++;
				}
				if ($showNumberRegistrants)
				{
				?>
					<th class="<?php echo $hiddenPhoneClass; ?>">
						<?php echo JText::_('EB_REGISTRANTS'); ?>
					</th>
				<?php
				}
			?>
			<th class="<?php echo $hiddenPhoneClass; ?>">
				<?php echo JText::_('EB_REGISTRATION_DATE'); ?>
			</th>
			<?php
				if ($this->displayCustomField)
				{
					foreach($this->fields as $fieldId)
					{
						$cols++;
					?>
						<th class="hidden-phone">
							<?php echo $this->fieldTitles[$fieldId] ; ?>
						</th>
					<?php
					}
				}
			?>
		</tr>
	</thead>
		<tfoot>
			<tr>
				<?php
				if ($this->pagination->total > $this->pagination->limit)
				{
				?>
					<td colspan="<?php echo $cols; ?>">
						<?php echo $this->pagination->getPagesLinks();?>
					</td>
				<?php
				}
				?>
			</tr>
		</tfoot>
	<tbody>
	<?php	
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = $this->items[$i];
		?>
		<tr>
			<td class="<?php echo $hiddenPhoneClass; ?>">
				<?php echo $this->pagination->getRowOffset( $i ); ?>
			</td>
			<td>
					<?php echo $row->first_name ?>
			</td>
			<?php
				if ($showLastName)
				{
				?>
					<td>
						<?php echo $row->last_name ; ?>
					</td>
				<?php
				}
				if ($showNumberRegistrants)
				{
				?>
					<td class="<?php echo $hiddenPhoneClass; ?>">
						<?php echo $row->number_registrants ; ?>
					</td>
				<?php
				}
			?>
			<td class="<?php echo $hiddenPhoneClass; ?>">
				<?php echo JHtml::_('date', $row->register_date, $this->config->date_format) ; ?>
			</td>
			<?php
				if ($this->displayCustomField)
				{
					foreach($this->fields as $fieldId)
					{
						if (isset($this->fieldValues[$row->id][$fieldId]))
						{
							$fieldValue = $this->fieldValues[$row->id][$fieldId];
						}
						else
						{
							$fieldValue = '';
						}
					?>
						<td class="<?php echo $hiddenPhoneClass; ?>">
							<?php echo $fieldValue ?>
						</td>
					<?php
					}
				}
			?>
		</tr>
		<?php		
	}
	?>
	</tbody>
</table>
<?php
}
else
{
?>
	<div class="eb-message"><?php echo JText::_('EB_NO_REGISTRATION_RECORDS');?></div>
<?php
}
?>
</div>