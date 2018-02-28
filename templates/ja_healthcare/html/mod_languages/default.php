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

JHtml::_('stylesheet', 'mod_languages/template.css', array(), true);

?>
<div class="mod-languages<?php echo $moduleclass_sfx; ?>">
<?php if ($headerText) : ?>
	<div class="pretext"><p><?php echo $headerText; ?></p></div>
<?php endif; ?>

<?php if ($params->get('dropdown', 1)) : ?>
	<a class="dropdown-toggle" data-toggle="dropdown" href="<?php echo JUri::base(true) ?>">

	<?php foreach ($list as $language) : 
		if($language->active){
			echo $language->title_native; 
			break;
		} ?>
	<?php endforeach; ?>
	<i class="fa fa-angle-down"></i>
	</a>
	<ul class="dropdown-menu" role="menu" >
	<?php foreach($list as $language):?>
		<?php if ($params->get('show_active', 0) || !$language->active):?>
			<li class="<?php echo $language->active ? 'lang-active' : '';?>">
			<a href="<?php echo $language->link;?>">
			<?php if ($params->get('image', 1)):?>
				<?php echo JHtml::_('image', 'mod_languages/'.$language->image.'.gif', $language->title_native, array('title'=>$language->title_native), true);?>
				<span><?php echo $language->title; ?></span>
			<?php else : ?>
				<?php echo $params->get('full_name', 1) ? $language->title_native : strtoupper($language->sef);?>
			<?php endif; ?>
			</a>
			</li>
		<?php endif;?>
	<?php endforeach;?>
	</ul>
<?php else : ?>
	<ul class="<?php echo $params->get('inline', 1) ? 'lang-inline' : 'lang-block'; ?>">
	<?php foreach ($list as $language) : ?>
		<?php if ($params->get('show_active', 0) || !$language->active) : ?>
			<li class="<?php echo $language->active ? 'lang-active' : ''; ?>" dir="<?php echo $language->rtl ? 'rtl' : 'ltr'; ?>">
			<a href="<?php echo $language->link; ?>">
			<?php if ($params->get('image', 1)) : ?>
				<?php echo JHtml::_('image', 'mod_languages/' . $language->image . '.gif', $language->title_native, array('title' => $language->title_native), true); ?>
			<?php else : ?>
				<?php echo $params->get('full_name', 1) ? $language->title_native : strtoupper($language->sef); ?>
			<?php endif; ?>
			</a>
			</li>
		<?php endif; ?>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>

<?php if ($footerText) : ?>
	<div class="posttext"><p><?php echo $footerText; ?></p></div>
<?php endif; ?>
</div>
