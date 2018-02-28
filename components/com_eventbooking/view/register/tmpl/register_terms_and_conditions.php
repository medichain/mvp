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

/**
 * Layout variables
 * -----------------
 * @var   string $controlGroupClass
 * @var   int $articleId
 */

if (JLanguageMultilang::isEnabled())
{
	$associations = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $articleId);
	$langCode     = JFactory::getLanguage()->getTag();

	if (isset($associations[$langCode]))
	{
		$article = $associations[$langCode];
	}
}

if (!isset($article))
{
	$db    = JFactory::getDbo();
	$query = $db->getQuery(true);
	$query->select('id, catid')
		->from('#__content')
		->where('id = ' . (int) $articleId);
	$db->setQuery($query);
	$article = $db->loadObject();
}

require_once JPATH_ROOT . '/components/com_content/helpers/route.php';
EventbookingHelperJquery::colorbox('eb-colorbox-term');
$termLink = ContentHelperRoute::getArticleRoute($article->id, $article->catid) . '&tmpl=component&format=html';
?>
<div class="<?php echo $controlGroupClass;  ?>">
	<label class="checkbox">
		<input type="checkbox" name="accept_term" value="1" class="validate[required]" data-errormessage="<?php echo JText::_('EB_ACCEPT_TERMS');?>" />
		<?php
			echo JText::_('EB_ACCEPT') . ' '. "<a class=\"eb-colorbox-term\" href=\"".JRoute::_($termLink)."\">"."<strong>".JText::_('EB_TERM_AND_CONDITION')."</strong>"."</a>\n";
		?>
	</label>
</div>