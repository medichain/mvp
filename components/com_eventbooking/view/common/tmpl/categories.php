<?php
/**
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2017 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */

defined( '_JEXEC' ) or die;

$rootUri = JUri::root(true);

if ($categoryId)
{
?>
	<h2 class="eb-heading"><?php echo JText::_('EB_SUB_CATEGORIES'); ?></h2>
<?php
}
?>
<div id="eb-categories">
	<?php
	foreach ($categories as $category)
	{
		if (!$config->show_empty_cat && !$category->total_events)
		{
			continue ;
		}

		$categoryLink = JRoute::_(EventbookingHelperRoute::getCategoryRoute($category->id, $Itemid));
		?>
		<div class="eb-category">
			<div class="eb-box-heading">
				<h3 class="eb-category-title">
					<a href="<?php echo $categoryLink; ?>" class="eb-category-title-link">
						<?php
							echo $category->name;

							if ($config->show_number_events)
							{
							?>
								<small>( <?php echo $category->total_events ;?> <?php echo $category->total_events == 1 ? JText::_('EB_EVENT') :  JText::_('EB_EVENTS') ; ?> )</small>
							<?php
							}
						?>
					</a>
				</h3>
			</div>
			<?php
				if($category->description)
				{
				?>
					<div class="eb-description clearfix">
						<?php
							if ($category->image && file_exists(JPATH_ROOT . '/images/com_eventbooking/categories/thumb/' . basename($category->image)))
							{
							?>
								<a href="<?php echo $categoryLink ?>"><img src="<?php echo $rootUri . '/images/com_eventbooking/categories/thumb/' . basename($category->image); ?>" class="eb-thumb-left" /></a>
							<?php
							}

							echo $category->description;
						?>
					</div>
				<?php
				}
			?>
		</div>
	<?php
	}
	?>
</div>