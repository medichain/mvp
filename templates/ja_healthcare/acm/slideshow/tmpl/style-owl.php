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
		$count = $helper->getRows('data.title');
?>

<div class="acm-slideshow acm-owl">
	<div id="acm-slideshow-<?php echo $module->id; ?>" class="items-<?php echo $count ;?>">
		<div class="owl-carousel owl-theme <?php echo $helper->get('effect') ?>">
				<?php 
          for ($i=0; $i<$count; $i++) : 
        ?>
				<div class="item">
          <?php if($helper->get('data.image', $i)): ?>
          <img class="img-bg" alt="<?php echo $helper->get('data.title', $i) ?>" src="<?php echo $helper->get('data.image', $i); ?>" />
          <?php endif; ?>
          <div class="mask container"></div>
          <div class="slider-content container">
            <div class="slider-content-inner">  
				      <?php if($helper->get('data.title', $i)): ?>
				        <h1 class="item-title ja-animation" data-delay-transtion="2">
				          <?php if($helper->get('data.btn-link', $i)): ?>
					         <a href="<?php echo $helper->get('data.btn-link', $i); ?>" title="<?php echo $helper->get('data.title', $i) ?>">
				          <?php endif; ?>

  					     <?php echo $helper->get('data.title', $i) ?>

        				  <?php if($helper->get('data.btn-link', $i)): ?>
        					</a>
        				  <?php endif; ?>
      				  </h1>
      				<?php endif; ?>

              <?php if($helper->get('data.desc', $i)): ?>
                <p class="ja-animation" data-delay-transtion="3"><?php echo $helper->get('data.desc', $i) ?></p>
              <?php endif; ?>

              <?php if($helper->get('data.btn-link', $i)): ?>
                <a href="<?php echo $helper->get('data.btn-link', $i) ?>" class="btn btn-default btn-lg ja-animation" data-delay-transtion="4"><?php echo $helper->get('data.btn-title', $i) ?></a>
              <?php endif; ?>
            </div>
          </div>
				</div>
			 	<?php endfor ;?>
		</div>
	</div>
</div>

<script>
(function($){
  jQuery(document).ready(function($) {
    $("#acm-slideshow-<?php echo $module->id; ?> .owl-carousel").owlCarousel({
      addClassActive: true,
      items: 1,
      loop: true,
      dots: true,
      dotsEach: true,
      merge: false,
      mergeFit: true,
      nav: false,
      slideBy: 1,
      autoPlay: true
    });
  });
})(jQuery);
</script>