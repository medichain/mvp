<?php
/**
 * ------------------------------------------------------------------------
 * JA Healthcare Template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
*/

defined('_JEXEC') or die;

$class = ' first';
JHtml::_('bootstrap.tooltip');
$lang	= JFactory::getLanguage();

if (count($this->items[$this->parent->id]) > 0 && $this->maxLevelcat != 0) :
?>
	<?php foreach($this->items[$this->parent->id] as $id => $item) : ?>
		<?php
		if ($this->params->get('show_empty_categories_cat') || $item->numitems || count($item->getChildren())) :
		if (!isset($this->items[$this->parent->id][$id + 1]))
		{
			$class = ' last';
		}
		?>
		<div class="category-item<?php echo $class; ?>">
			<div class="category-inner ja-animate" data-animation="move-from-bottom" data-delay="item-<?php echo $id ?>">
			<?php $class = ''; ?>
				<?php if ($this->params->get('show_description_image') && $item->getParams()->get('image')) : ?>
					<div class="category-image"><img src="<?php echo $item->getParams()->get('image'); ?>"/></div>
				<?php endif; ?>
				
				<h3 class="page-header item-title">
					<a href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($item->id));?>">
					<?php echo $this->escape($item->title); ?></a>
					<?php if ($this->params->get('show_cat_num_articles_cat') == 1) :?>
						<span class="badge badge-info tip hasTooltip" title="<?php echo T3J::tooltipText('COM_CONTENT_NUM_ITEMS'); ?>">
							<?php echo $item->numitems; ?>
						</span>
					<?php endif; ?>
					<?php if (count($item->getChildren()) > 0) : ?>
						<a href="#category-<?php echo $item->id;?>" data-toggle="collapse" data-toggle="button" class="btn btn-mini pull-right">
							<i class="fa fa-plus"></i>
						</a>
					<?php endif;?>
				</h3>
				<?php if ($this->params->get('show_subcat_desc_cat') == 1) :?>
					<?php if ($item->description) : ?>
						<div class="category-desc">
							<?php echo JHtml::_('content.prepare', $item->description, '', 'com_content.categories'); ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>

				<?php if (count($item->getChildren()) > 0) :?>
					<div class="collapse fade" id="category-<?php echo $item->id;?>">
					<?php
					$this->items[$item->id] = $item->getChildren();
					$this->parent = $item;
					$this->maxLevelcat--;
					echo $this->loadTemplate('items');
					$this->parent = $item->getParent();
					$this->maxLevelcat++;
					?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>
