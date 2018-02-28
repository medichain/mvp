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
	$featuresTitle 	= $helper->get('block-title');
	$featuresIntro 	= $helper->get('block-intro');
	$featuresImage 	= $helper->get('block-image');
	$count 					= $helper->getRows('data.title');
	$column 				= $helper->get('columns');				
?>

<div class="acm-features style-2">
	<div id="acm-feature-<?php echo $module->id; ?>" class="ja-animate" data-animation="move-from-left" >
		<div class="owl-carousel owl-theme">
			<?php 
				for ($i=0; $i<$count; $i++) : 
			?>
				<div class="features-item">
					<div class="features-item-inner row">
						<div class="features-text col-xs-12 col-sm-6">
							<?php if($helper->get('data.title', $i)) : ?>
								<h4><?php echo $helper->get('data.title', $i) ?></h4>
							<?php endif ; ?>
							
							<?php if($helper->get('data.description', $i)) : ?>
								<p><?php echo $helper->get('data.description', $i) ?></p>
							<?php endif ; ?>

							<?php if($helper->get('data.link', $i) && $helper->get('data.btn-value', $i)) : ?>
								<div class="feature-action">
									<a class="btn btn-<?php echo $helper->get('data.btn-type', $i) ?>" href="<?php echo $helper->get('data.link', $i) ?>"><?php echo $helper->get('data.btn-value', $i) ?></a>
									</div>
							<?php endif ; ?>
						</div>

						<?php if($helper->get('data.img', $i)) : ?>
							<div class="features-img col-xs-12 col-sm-6">
								<img src="<?php echo $helper->get('data.img', $i) ?>" alt="" />
							</div>
						<?php endif ; ?>
					</div>
				</div>
			<?php endfor ?>
		</div>
	</div>

	<div class="feature-info ja-animate" data-animation="move-from-right">
		<div class="row">
			<div class="col-sm-6">
				<div class="features-img">
					<img alt="" src="<?php echo $featuresImage; ?>">
				</div>
			</div>
			<div class="col-sm-6 info-text">
				<h4><?php echo $featuresTitle; ?></h4>
				<p><?php echo $featuresIntro; ?></p>
			</div>
		</div>
	</div>
</div>

<script>
(function($){
  jQuery(document).ready(function($) {
    $("#acm-feature-<?php echo $module->id; ?> .owl-carousel").owlCarousel({
      addClassActive: true,
      items: 1,
      loop: true,
      nav : true,
      navText : ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
      dots: false,
      autoPlay: false
    });
  });
})(jQuery);
</script>