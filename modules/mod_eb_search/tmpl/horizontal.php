<?php
/**
 * @package        Joomla
 * @subpackage     Event Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;
$output = '<input name="search" id="search_eb_box" maxlength="50"  class="inputbox" type="text" size="20" value="'.$text.'"  onblur="if(this.value==\'\') this.value=\''.$text.'\';" onfocus="if(this.value==\''.$text.'\') this.value=\'\';" />';		
?>
<form method="post" name="eb_search_form" id="eb_search_form" action="<?php echo JRoute::_('index.php?option=com_eventbooking&task=search&&Itemid='.$itemId);  ?>">
    <div class="row-fluid">
		<div class="span3">
    			<?php echo $output ; ?>	
    		</div>
    	<?php
    	    if ($showCategory)
	        {
    	    ?>
				<div class="span3">
					<?php echo $lists['category_id'] ; ?>
				</div>
    	    <?php    
    	    }
    	    if ($showLocation)
	        {
    	    ?>
				<div class="span4">
					<?php echo $lists['location_id'] ; ?>
				</div>
    	    <?php    
    	    }
    	?>
    		<div class="span2">
    			<input type="button" class="btn btn-primary button search_button" value="<?php echo JText::_('EB_SEARCH'); ?>" onclick="searchData();" /> 
    		</div>
    </div>
    <script language="javascript">
    	function searchData()
	    {
        	var form = document.eb_search_form ;
        	if (form.search.value == '<?php echo $text ?>')
	        {
            	form.search.value = '' ;
        	}
        	form.submit();
    	}
    </script>

	<input type="hidden" name="layout" value="<?php echo $layout; ?>" />
</form>
<style type="text/css">
.row-fluid [class*="span"]{margin-bottom:10px;box-sizing:border-box;}
@media screen and (min-width: 768px) {
	.row-fluid [class*="span"]{display:block;float:left;width:100%;min-height:30px;margin-left:2.127659574468085%;*margin-left:2.074468085106383%;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}
	.row-fluid [class*="span"]:first-child {
		margin-left: 0;
	}
	.row-fluid .span4{width:31.914893617021278%;*width:31.861702127659576%}
	.row-fluid .span3{width:23.404255319148934%;*width:23.351063829787233%}
	.row-fluid .span2{width:14.893617021276595%;*width:14.840425531914894%}
	#eb_search_form select, #eb_search_form input[type="text"], #eb_search_form .inputbox {
		width: 100%;
	}
}
</style>