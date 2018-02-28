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

<?php
  $align = $helper->get('align');
  
  if ($align == 1): 
  	$alignClass 	= "features-content-right";
  	$contentPull 	= "col-xs-12 col-sm-6 pull-right";
  	$imgPull 			= "col-xs-12 col-sm-6 pull-left";
  else:
  	$alignClass = "features-content-left";
  	$contentPull 	= "col-xs-12 col-sm-6 pull-left";
  	$imgPull 			= "col-xs-12 col-sm-6 pull-right";
  endif;
?>
<div class="acm-features style-3">
	<div class="container">
		
		<div class="features-content <?php echo $alignClass; ?>">
			<div class="row">
				<?php if($helper->get('img-features')) : ?>
				<div class="features-image <?php echo $imgPull; ?>">
					<img src="<?php echo $helper->get('img-features'); ?>" alt="<?php echo $helper->get('title') ?>" />
				</div>
				<?php endif ; ?>

					<div class="features-item <?php echo $contentPull; ?>">
						
						<?php if($helper->get('title')) : ?>
							<h3 class="ja-animate" data-animation="move-from-right">
								<?php if ($helper->get('title-link')): ?>
									<a href="<?php echo $helper->get('data-s6.title-link'); ?>" title="<?php echo $helper->get('title') ?>">
								<?php endif; ?>
								
								<?php echo $helper->get('title') ?>
								
								<?php if ($helper->get('title-link')): ?>
									</a>
								<?php endif; ?>
							</h3>
						<?php endif ; ?>
						
						<?php if($helper->get('description')) : ?>
							<p class="ja-animate" data-animation="move-from-right" data-delay="200"><?php echo $helper->get('description') ?></p>
						<?php endif ; ?>
						
						<?php if($helper->get('button')) : ?>
							<a class="btn btn-primary ja-animate" data-animation="move-from-right" data-delay="400" href="<?php echo $helper->get('title-link'); ?>"><?php echo $helper->get('button') ?></a>
						<?php endif ; ?>
					</div>
			</div>
		</div>
	</div>
</div>
