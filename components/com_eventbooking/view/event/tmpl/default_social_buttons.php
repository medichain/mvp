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

if (empty($this->config->social_sharing_buttons))
{
	$shareOptions = array(
		'Delicious',
		'Digg',
		'Facebook',
		'Google',
		'Stumbleupon',
		'Technorati',
		'Twitter',
		'LinkedIn'
	);
}
else
{
	$shareOptions = explode(',', $this->config->social_sharing_buttons);
}
?>
	<div id="itp-social-buttons-box" class="row-fluid">
		<div id="eb-share-text"><?php echo JText::_('EB_SHARE_THIS_EVENT'); ?></div>
		<div id="eb-share-button">
			<?php
				$title = $this->item->title;
				$html  = '';

				if (in_array('Delicious', $shareOptions))
				{
					$html .= EventbookingHelper::getDeliciousButton($title, $socialUrl);
				}

				if (in_array('Digg', $shareOptions))
				{
					$html .= EventbookingHelper::getDiggButton($title, $socialUrl);
				}

				if (in_array('Facebook', $shareOptions))
				{
					$html .= EventbookingHelper::getFacebookButton($title, $socialUrl);
				}

				if (in_array('Google', $shareOptions))
				{
					$html .= EventbookingHelper::getGoogleButton($title, $socialUrl);
				}

				if (in_array('Stumbleupon', $shareOptions))
				{
					$html .= EventbookingHelper::getStumbleuponButton($title, $socialUrl);
				}

				if (in_array('Technorati', $shareOptions))
				{
					$html .= EventbookingHelper::getTechnoratiButton($title, $socialUrl);
				}

				if (in_array('Twitter', $shareOptions))
				{
					$html .= EventbookingHelper::getTwitterButton($title, $socialUrl);
				}

				if (in_array('LinkedIn', $shareOptions))
				{
					$html .= EventbookingHelper::getLinkedInButton($title, $socialUrl);
				}

				echo $html ;
			?>
		</div>
	</div>
<?php
