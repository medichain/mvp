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
	$count = $helper->getRows('data.position');
	$blockClass = $helper->get('block-extra-class');
	$moduleStyle = $helper->get('module-title');
  $moduleFull         = $helper->get('module-full-width','0');
  $moduleHeight         = $helper->get('module-equal-height','0');
?>

<div class="acm-spotlight <?php if($moduleFull) echo "full-width"; ?>">
	<?php if(!$moduleFull) echo '<div class="container">'; ?>
	<div class="row <?php if($moduleHeight) echo "equal-height equal-height-child" ?>">
	<?php 
		for ($i=0; $i<$count; $i++) : 
		$screensXs = $helper->get('data.xs',$i);
		$screensSm = $helper->get('data.sm',$i);
		$screensMd = $helper->get('data.md',$i);
		$screensLg = $helper->get('data.lg',$i);
	?>
	<div class="col <?php echo $screensXs.' '.$screensSm.' '.$screensMd.' '.$screensLg; ?>">
		<?php
			$spotlight_position = $helper->get('data.position',$i);
		 	echo $helper->renderModules($spotlight_position,array('style'=>$moduleStyle));
		?>
	</div>
	<?php endfor; ?>
	</div>
	<?php if(!$moduleFull) echo '</div>'; ?>
</div>