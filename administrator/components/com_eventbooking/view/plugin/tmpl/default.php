<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;

?>
<form action="index.php?option=com_eventbooking&view=plugin" method="post" name="adminForm" id="adminForm" class="adminform form form-horizontal">
<div class="row-fluid">
<div class="span7">
	<fieldset class="adminform">
		<legend><?php echo JText::_('EB_PLUGIN_DETAIL'); ?></legend>
				<div class="control-group">
					<label class="control-label">
						<?php echo  JText::_('EB_NAME'); ?>
					</label>
					<div class="controls">
						<?php echo $this->item->name ; ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">
						<?php echo  JText::_('EB_TITLE'); ?>
					</label>
					<div class="controls">
						<input class="text_area" type="text" name="title" id="title" size="40" maxlength="250" value="<?php echo $this->item->title;?>" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('EB_AUTHOR'); ?>
					</label>
					<div class="controls">
						<input class="text_area" type="text" name="author" id="author" size="40" maxlength="250" value="<?php echo $this->item->author;?>" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('Creation date'); ?>
					</label>
					<div class="controls">
						<?php echo $this->item->creation_date; ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('Copyright') ; ?>
					</label>
					<div class="controls">
						<?php echo $this->item->copyright; ?>
					</div>
				</div>	
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('License'); ?>
					</label>
					<div class="controls">
						<?php echo $this->item->license; ?>
					</div>
				</div>							
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('Author email'); ?>
					</label>
					<div class="controls">
						<?php echo $this->item->author_email; ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('Author URL'); ?>
					</label>
					<div class="controls">
						<?php echo $this->item->author_url; ?>
					</div>
				</div>				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('Version'); ?>
					</label>
					<div class="controls">
						<?php echo $this->item->version; ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('Description'); ?>
					</label>
					<div class="controls">
						<?php echo $this->item->description; ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">
						<?php echo  JText::_('EB_ACCESS'); ?>
					</label>
					<div class="controls">
						<?php echo $this->lists['access']; ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('Published'); ?>
					</label>
					<div class="controls">
						<?php					
							echo $this->lists['published'];					
						?>						
					</div>
				</div>
	</fieldset>				
</div>						
<div class="span5">
	<fieldset class="adminform">
		<legend><?php echo JText::_('Plugins Parameter'); ?></legend>
		<?php
			foreach ($this->form->getFieldset('basic') as $field)
			{
			?>
			<div class="control-group">
				<label class="control-label">
					<?php echo $field->label ;?>
				</label>
				<div class="controls">
					<?php echo  $field->input ; ?>
				</div>
			</div>	
			<?php
			}					
		?>				
	</fieldset>				
</div>
</div>		
<div class="clearfix"></div>	
	<?php echo JHtml::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_eventbooking" />
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />
</form>