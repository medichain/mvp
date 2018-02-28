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
?>
<div class="category-module<?php echo $moduleclass_sfx; ?> blog-department">
	<div class="container">
		<div class="row items-row">
			<?php if ($grouped) : ?>
				<?php foreach ($list as $group_name => $group) : ?>
				<div>
					<div class="mod-articles-category-group"><?php echo $group_name;?></div>
					<div>
						<?php foreach ($group as $item) : ?>
							<div>
								<?php if ($params->get('link_titles') == 1) : ?>
									<a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
										<?php echo $item->title; ?>
									</a>
								<?php else : ?>
									<?php echo $item->title; ?>
								<?php endif; ?>
			
								<?php if ($item->displayHits) : ?>
									<span class="mod-articles-category-hits">
										(<?php echo $item->displayHits; ?>)
									</span>
								<?php endif; ?>
			
								<?php if ($params->get('show_author')) : ?>
									<span class="mod-articles-category-writtenby">
										<?php echo $item->displayAuthorName; ?>
									</span>
								<?php endif;?>
			
								<?php if ($item->displayCategoryTitle) : ?>
									<span class="mod-articles-category-category">
										(<?php echo $item->displayCategoryTitle; ?>)
									</span>
								<?php endif; ?>
			
								<?php if ($item->displayDate) : ?>
									<span class="mod-articles-category-date"><?php echo $item->displayDate; ?></span>
								<?php endif; ?>
			
								<?php if ($params->get('show_introtext')) : ?>
									<p class="mod-articles-category-introtext">
										<?php echo $item->displayIntrotext; ?>
									</p>
								<?php endif; ?>
			
								<?php if ($params->get('show_readmore')) : ?>
									<p class="mod-articles-category-readmore">
										<a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
											<?php if ($item->params->get('access-view') == false) : ?>
												<?php echo JText::_('MOD_ARTICLES_CATEGORY_REGISTER_TO_READ_MORE'); ?>
											<?php elseif ($readmore = $item->alternative_readmore) : ?>
												<?php echo $readmore; ?>
												<?php echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit')); ?>
													<?php if ($params->get('show_readmore_title', 0) != 0) : ?>
														<?php echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit')); ?>
													<?php endif; ?>
											<?php elseif ($params->get('show_readmore_title', 0) == 0) : ?>
												<?php echo JText::sprintf('MOD_ARTICLES_CATEGORY_READ_MORE_TITLE'); ?>
											<?php else : ?>
												<?php echo JText::_('MOD_ARTICLES_CATEGORY_READ_MORE'); ?>
												<?php echo JHtml::_('string.truncate', ($item->title), $params->get('readmore_limit')); ?>
											<?php endif; ?>
										</a>
									</p>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endforeach; ?>
			<?php else : ?>
				<?php $i=0;foreach ($list as $item) : ?>
					<div class="col-sm-4 col-md-3">
						<div class="item ja-animate" data-animation="pop-up" data-delay="item-<?php echo $i ?>">
							<?php $iconArticle = json_decode($item->attribs)->jdepartment_icon ;?>
							<?php if($iconArticle) echo '<i class="flaticon '.$iconArticle.'"></i>'; ?>
							<a class="entry-link" href="<?php echo $item->link; ?>"></a>
							<?php if ($params->get('link_titles') == 1) : ?>
								<a class="mod-articles-category-title article-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
									<?php echo $item->title; ?>
									
								</a>
							<?php else : ?>
								<?php echo $item->title; ?>
							<?php endif; ?>

							<?php if ($item->displayHits) : ?>
								<div class="mod-articles-category-hits">
									<i class="fa fa-eye"></i><?php echo $item->displayHits; ?>
								</div>
							<?php endif; ?>
				
							<?php if ($params->get('show_author')) : ?>
								<div class="mod-articles-category-writtenby">
									<i class="fa fa-user"></i><?php echo $item->displayAuthorName; ?>
								</div>
							<?php endif;?>
				
							<?php if ($item->displayCategoryTitle) : ?>
								<div class="mod-articles-category-category">
									<i class="fa fa-folder-o"></i><?php echo $item->displayCategoryTitle; ?>
								</div>
							<?php endif; ?>
				
							<?php if ($item->displayDate) : ?>
								<div class="mod-articles-category-date">
									<i class="fa fa-calendar"></i><?php echo $item->displayDate; ?>
								</div>
							<?php endif; ?>

							<?php if ($params->get('show_introtext')) : ?>
								<p class="mod-articles-category-introtext">
									<?php echo $item->displayIntrotext; ?>
								</p>
							<?php endif; ?>
				
							<?php if ($params->get('show_readmore')) : ?>
								<p class="mod-articles-category-readmore">
									<a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
										<?php if ($item->params->get('access-view') == false) : ?>
											<?php echo JText::_('MOD_ARTICLES_CATEGORY_REGISTER_TO_READ_MORE'); ?>
										<?php elseif ($readmore = $item->alternative_readmore) : ?>
											<?php echo $readmore; ?>
											<?php echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit')); ?>
										<?php elseif ($params->get('show_readmore_title', 0) == 0) : ?>
											<?php echo JText::sprintf('MOD_ARTICLES_CATEGORY_READ_MORE_TITLE'); ?>
										<?php else : ?>
											<?php echo JText::_('MOD_ARTICLES_CATEGORY_READ_MORE'); ?>
											<?php echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit')); ?>
										<?php endif; ?>
									</a>
								</p>
							<?php endif; ?>
						</div>
					</div>
					<?php $i++; endforeach; ?>
				<?php endif; ?>
		</div>
	</div>
</div>
