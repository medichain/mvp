<?php
/**
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2017 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */

defined( '_JEXEC' ) or die;
?>
<div id="eb-categories">
	<?php
	foreach ($categories as $category)
	{
		if (!$config->show_empty_cat && !$category->total_events)
		{
			continue ;
		}
		?>
		<div class="row-fluid clearfix">
			<h3 class="eb-category-title">
				<a href="<?php echo JRoute::_(EventbookingHelperRoute::getCategoryRoute($category->id, $Itemid)); ?>" class="eb-category-title-link">
					<?php
						echo $category->name;
					?>
				</a>
			</h3>
			<?php
				if($category->description)
				{
				?>
					<div class="clearfix"><?php echo $category->description;?></div>
				<?php
				}

				if (count($category->events))
				{
					$user = JFactory::getUser();
					$bootstrapHelper = new EventbookingHelperBootstrap($config->twitter_bootstrap_version);

					echo EventbookingHelperHtml::loadCommonLayout('common/tmpl/events_table.php', array('items' => $category->events, 'config' => $config, 'Itemid' => $Itemid, 'nullDate' => JFactory::getDbo()->getNullDate(), 'ssl' => (int) $config->use_https, 'viewLevels' => $user->getAuthorisedViewLevels(), 'categoryId' => $category->id, 'bootstrapHelper' => $bootstrapHelper));
				}
			?>
		</div>
	<?php
	}
	?>
</div>