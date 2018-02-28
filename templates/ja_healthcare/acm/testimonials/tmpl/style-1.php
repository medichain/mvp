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
  $count = $helper->getRows('data.testimonial-text');
?>


<div class="acm-testimonials default <?php echo $helper->get('acm-style'); ?>">

    <!-- BEGIN: TESTIMONIALS STYLE 1 -->
  	<div id="acm-testimonials-<?php echo $module->id ?>" class="testimonial-content owl-carousel">

       <?php for ($i=0; $i<$count; $i++) : ?>
        <div class="item <?php if($i<1) echo "active"; ?> row">
          <?php if ($helper->get ('data.testimonial-img', $i)) : ?>
          <div class="testimonial-img col-md-2">
             <img src="<?php echo $helper->get ('data.testimonial-img', $i) ?>" alt="<?php echo $helper->get ('data.testimonial-text', $i) ?>" />
          </div>
          <?php endif; ?>

          <div class="col-md-10">
            <?php if ($helper->get ('data.author-img', $i)) : ?>
              <span class="author-image"><img src="<?php echo $helper->get ('data.author-img', $i) ?>" alt="<?php echo $helper->get ('data.author-name', $i) ?>" /></span>
            <?php endif; ?>

            <?php if ($helper->get ('data.testimonial-text', $i)) : ?>
               <p class="testimonial-text"><?php echo $helper->get ('data.testimonial-text', $i) ?></p>
            <?php endif; ?>

            <div class="author-info">
              <?php if ($helper->get ('data.author-name', $i)) : ?>
                <div class="author-info-text">
                  <span class="author-name"><?php echo $helper->get ('data.author-name', $i) ?></span>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
       <?php endfor ?>

    </div>
    <!-- END: TESTIMONIALS STYLE 1 -->
  
</div>

<script>
(function($){
  jQuery(document).ready(function($) {
    $("#acm-testimonials-<?php echo $module->id ?>.owl-carousel").owlCarousel({
      addClassActive: true,
      items: 1,
      singleItem : true,
      itemsScaleUp : true,
      navigation : false,
      navigationText : ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
      pagination: true,
      paginationNumbers : false,
      merge: false,
      mergeFit: true,
      slideBy: 1,
      autoPlay: false
    });
  });
})(jQuery);
</script>