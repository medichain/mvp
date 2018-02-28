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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.framework');

// Create a shortcut for params.
$params  = & $this->item->params;
$images  = json_decode($this->item->images);
$info    = $params->get('info_block_position', 2);
$aInfo1 = 0;
$aInfo2 = 0;
$topInfo = 0;
$botInfo = 0;
$icons = $params->get('access-edit') || $params->get('show_print_icon') || $params->get('show_email_icon');
$url = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catslug));

// update catslug if not exists - compatible with 2.5
if (empty ($this->item->catslug)) {
  $this->item->catslug = $this->item->category_alias ? ($this->item->catid.':'.$this->item->category_alias) : $this->item->catid;
}

?>

<?php if ($this->item->state == 0 || strtotime($this->item->publish_up) > strtotime(JFactory::getDate())
|| ((strtotime($this->item->publish_down) < strtotime(JFactory::getDate())) && $this->item->publish_down != '0000-00-00 00:00:00' )) : ?>
<div class="system-unpublished">
<?php endif; ?>
  <a href="<?php echo $url; ?>" class="entry-link"></a>
	<!-- Article -->
	<article>

    <?php echo JLayoutHelper::render('joomla.content.intro_image', $this->item); ?>
  
    <?php if ($params->get('show_title')) : ?>
			<?php echo JLayoutHelper::render('joomla.content.item_title', array('item' => $this->item, 'params' => $params, 'title-tag'=>'h2')); ?>
    <?php endif; ?>

		<section class="article-intro clearfix" itemprop="articleBody">
			<?php if (!$params->get('show_intro')) : ?>
				<?php echo $this->item->event->afterDisplayTitle; ?>
			<?php endif; ?>

			<?php echo $this->item->event->beforeDisplayContent; ?>

			<?php echo $this->item->introtext; ?>
		</section>

    <?php if ($params->get('show_readmore') && $this->item->readmore) :
      if ($params->get('access-view')) :
        $link = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid));
      else :
        $menu      = JFactory::getApplication()->getMenu();
        $active    = $menu->getActive();
        $itemId    = $active->id;
        $link1     = JRoute::_('index.php?option=com_users&view=login&Itemid=' . $itemId);
        $returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid));
        $link      = new JURI($link1);
        $link->setVar('return', base64_encode($returnURL));
      endif;
      ?>
      <section class="readmore">
        <a class="btn btn-default" href="<?php echo $link; ?>">
          <span>
          <?php if (!$params->get('access-view')) :
            echo JText::_('TPL_SERVICE_REGISTER_TO_READ_MORE');
          elseif ($readmore = $this->item->alternative_readmore) :
            echo $readmore;
            if ($params->get('show_readmore_title', 0) != 0) :
              echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
            endif;
          elseif ($params->get('show_readmore_title', 0) == 0) :
            echo JText::sprintf('TPL_SERVICE_READ_MORE_TITLE');
          else :
            echo JText::_('TPL_SERVICE_READ_MORE');
            echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
          endif; ?>
          </span>
        </a>
      </section>
    <?php endif; ?>

	</article>
	<!-- //Article -->

<?php if ($this->item->state == 0 || strtotime($this->item->publish_up) > strtotime(JFactory::getDate())
|| ((strtotime($this->item->publish_down) < strtotime(JFactory::getDate())) && $this->item->publish_down != JFactory::getDbo()->getNullDate())) : ?>
</div>
<?php endif; ?>

<?php echo $this->item->event->afterDisplayContent; ?> 
