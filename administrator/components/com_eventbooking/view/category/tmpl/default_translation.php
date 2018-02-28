<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

$rootUri = JUri::root();

echo JHtml::_('bootstrap.addTab', 'category', 'translation-page', JText::_('EB_TRANSLATION', true));
echo JHtml::_('bootstrap.startTabSet', 'category-translation', array('active' => 'translation-page-'.$this->languages[0]->sef));

foreach ($this->languages as $language)
{
	$sef = $language->sef;
	echo JHtml::_('bootstrap.addTab', 'category-translation', 'translation-page-' . $sef, $language->title . ' <img src="' . $rootUri . 'media/com_eventbooking/flags/' . $sef . '.png" />');
	?>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_NAME'); ?>
		</label>
		<div class="controls">
			<input class="input-xlarge" type="text" name="name_<?php echo $sef; ?>" id="name_<?php echo $sef; ?>" size="" maxlength="250" value="<?php echo $this->item->{'name_' . $sef}; ?>"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_ALIAS'); ?>
		</label>
		<div class="controls">
			<input class="input-xlarge" type="text" name="alias_<?php echo $sef; ?>" id="alias_<?php echo $sef; ?>" size="" maxlength="250" value="<?php echo $this->item->{'alias_' . $sef}; ?>"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_PAGE_TITLE'); ?>
		</label>
		<div class="controls">
			<input class="input-xlarge" type="text" name="page_title_<?php echo $sef; ?>" id="page_title_<?php echo $sef; ?>" size="" maxlength="250" value="<?php echo $this->item->{'page_title_' . $sef}; ?>"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_PAGE_HEADING'); ?>
		</label>
		<div class="controls">
			<input class="input-xlarge" type="text" name="page_heading_<?php echo $sef; ?>" id="page_heading_<?php echo $sef; ?>" size="" maxlength="250" value="<?php echo $this->item->{'page_heading_' . $sef}; ?>"/>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_DESCRIPTION'); ?>
		</label>
		<div class="controls">
			<?php echo $editor->display('description_' . $sef, $this->item->{'description_' . $sef}, '100%', '250', '75', '10'); ?>
		</div>
	</div>
	<?php
	echo JHtml::_('bootstrap.endTab');
}
echo JHtml::_('bootstrap.endTabSet');
echo JHtml::_('bootstrap.endTab');

