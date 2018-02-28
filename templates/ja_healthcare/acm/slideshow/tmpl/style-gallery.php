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
		$count = $helper->getRows('data.image');
?>

<div class="acm-gallery acm-owl">
	<div id="acm-gallery-<?php echo $module->id; ?>">
		<div class="owl-carousel owl-theme <?php echo $helper->get('effect') ?>">
				<?php 
          for ($i=0; $i<$count; $i++) : 
        ?>
				<div class="item">
          <?php if($helper->get('data.image', $i)): ?>
          <img class="img-bg" alt="<?php echo $helper->get('data.title', $i) ?>" src="<?php echo $helper->get('data.image', $i); ?>" />
          <?php endif; ?>
				</div>
			 	<?php endfor ;?>
		</div>
	</div>
</div>

<script>
(function($){
  jQuery(document).ready(function($) {
    $("#acm-gallery-<?php echo $module->id; ?> .owl-carousel").owlCarousel({
      addClassActive: true,
      items: 1,
      loop: true,
      nav : true,
      navText : ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
      dots: true,
      merge: false,
      mergeFit: true,
      slideBy: 1,
      autoplay: true
    });
  });
})(jQuery);
</script>