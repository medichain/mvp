<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

 // no direct access
defined( '_JEXEC' ) or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.tabstate');

$document = JFactory::getDocument();
$document->addStyleDeclaration(".hasTip{display:block !important}");
JHtml::_('formbehavior.chosen', 'select');

$editor = JEditor::getInstance(JFactory::getConfig()->get('editor'));
$translatable = JLanguageMultilang::isEnabled() && count($this->languages);
$hasCustomSettings = file_exists(__DIR__ . '/default_custom_settings.php');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton)
	{
		if (pressbutton == 'cancel')
		{
			Joomla.submitform( pressbutton );
		}
		else
		{
			<?php echo $editor->save('description'); ?>
			Joomla.submitform( pressbutton );
		}
	}
</script>
<form action="index.php?option=com_eventbooking&view=category" method="post" name="adminForm" id="adminForm" class="form form-horizontal">
<?php
	echo JHtml::_('bootstrap.startTabSet', 'category', array('active' => 'general-page'));
	echo JHtml::_('bootstrap.addTab', 'category', 'general-page', JText::_('EB_GENERAL', true));
?>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_NAME'); ?>
		</label>
		<div class="controls">
			<input class="text_area" type="text" name="name" id="name" size="40" maxlength="250" value="<?php echo $this->item->name;?>" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_ALIAS'); ?>
		</label>
		<div class="controls">
			<input class="text_area" type="text" name="alias" id="alias" maxlength="250" value="<?php echo $this->item->alias;?>" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_PARENT'); ?>
		</label>
		<div class="controls">
			<?php echo $this->lists['parent']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo JText::_('EB_IMAGE'); ?></label>
		<div class="controls">
			<?php echo EventbookingHelperHtml::getMediaInput($this->item->image, 'image'); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_LAYOUT'); ?>
		</label>
		<div class="controls">
			<?php echo $this->lists['layout']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('text_color', JText::_('EB_TEXT_COLOR'), JText::_('EB_TEXT_COLOR_EXPLAIN')); ?>
		</label>
		<div class="controls">
			<input type="text" name="text_color" class="inputbox color {required:false}" value="<?php echo $this->item->text_color; ?>" size="10" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('color_code', JText::_('EB_COLOR'), JText::_('EB_COLOR_EXPLAIN')); ?>
		</label>
		<div class="controls">
			<input type="text" name="color_code" class="inputbox color {required:false}" value="<?php echo $this->item->color_code; ?>" size="10" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_ACCESS_LEVEL'); ?>
		</label>
		<div class="controls">
			<?php echo $this->lists['access']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo  JText::_('EB_SUBMIT_EVENT_ACCESS_LEVEL'); ?>
		</label>
		<div class="controls">
			<?php echo $this->lists['submit_event_access']; ?>
		</div>
	</div>
	<?php
	if (JLanguageMultilang::isEnabled())
	{
	?>
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('EB_LANGUAGE'); ?>
			</label>
			<div class="controls">
				<?php echo $this->lists['language'] ; ?>
			</div>
		</div>
	<?php
	}
	?>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_PUBLISHED'); ?>
		</label>
		<div class="controls">
			<?php echo $this->lists['published']; ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php echo JText::_('EB_DESCRIPTION'); ?>
		</label>
		<div class="controls">
			<?php echo $editor->display( 'description',  $this->item->description , '100%', '250', '75', '10' ) ; ?>
		</div>
	</div>
<?php
echo JHtml::_('bootstrap.endTab');

echo JHtml::_('bootstrap.addTab', 'category', 'seo-options-page', JText::_('EB_SEO_OPTIONS', true));
echo $this->loadTemplate('seo_options');
echo JHtml::_('bootstrap.endTab');

// Add support for custom settings layout
if ($hasCustomSettings)
{
	echo JHtml::_('bootstrap.addTab', 'category', 'custom-settings-page', JText::_('EB_CATEGORY_CUSTOM_SETTINGS', true));
	echo $this->loadTemplate('custom_settings', array('editor' => $editor));
	echo JHtml::_('bootstrap.endTab');
}

if ($translatable)
{
	echo $this->loadTemplate('translation', array('editor' => $editor));
}

echo JHtml::_('bootstrap.endTabSet');
?>
<div class="clearfix"></div>
<?php echo JHtml::_( 'form.token' ); ?>
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
</form>