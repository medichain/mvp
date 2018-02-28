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
	$count 					= $helper->getRows('data.title');
	$column 				= $helper->get('columns');				
?>

<div class="acm-features style-1">
	<div class="container">
		<div class="row">
			<div class="col-md-4 col-lg-3">
				<?php if($featuresIntro): ?>
				<h3 class="acm-features-title">
					<?php echo $featuresTitle; ?>
				</h3>
				<?php endif; ?>

				<?php if($featuresIntro): ?>
				<p class="acm-features-intro">
					<?php echo $featuresIntro; ?>
				</p>
				<?php endif; ?>
			</div>

			<div class="col-md-8 col-lg-9">
				<div id="acm-feature-<?php echo $module->id; ?>">
					<div class="owl-carousel owl-theme">
						<?php 
							for ($i=0; $i<$count; $i++) : 
						?>
							<div class="features-item col">
								<div class="features-item-inner ja-animate" data-animation="move-from-bottom" data-delay="item-<?php echo $i ?>">
									<?php if($helper->get('data.img', $i)) : ?>
										<div class="features-img">
											<img src="<?php echo $helper->get('data.img', $i) ?>" alt="" />
										</div>
									<?php endif ; ?>
									
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
							</div>
						<?php endfor ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
(function($){
  jQuery(document).ready(function($) {
    $("#acm-feature-<?php echo $module->id; ?> .owl-carousel").owlCarousel({
      addClassActive: true,
      items: <?php echo $column; ?>,
      responsive : {
      	0 : {
      		items: 1,
      	},

      	768 : {
      		items: 2,
      	},

      	979 : {
      		items: 2,
      	},

      	1199 : {
      		items: <?php echo $column; ?>,
      	}
      },
      loop: true,
      nav : true,
      navText : ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
      dots: false,
      autoplay: false
    });
  });
})(jQuery);
</script>