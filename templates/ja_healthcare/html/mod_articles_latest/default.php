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
<ul class="latestnews<?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $item) :  ?>
	<li itemscope itemtype="https://schema.org/Article" >
		<?php if(json_decode($item->images)->image_intro) :?>
			<img src="<?php echo json_decode($item->images)->image_intro; ?>" alt="<?php echo $item->title; ?>" />
		<?php endif;?>
		
		<a href="<?php echo $item->link; ?>" itemprop="url">
			<span itemprop="name">
				<?php echo $item->title; ?>
			</span>
		</a>
		<span class="author">
			<i class="fa fa-user"></i><?php echo ($item->modified_by_name) ;?>
		</span>
	</li>
<?php endforeach; ?>
</ul>
