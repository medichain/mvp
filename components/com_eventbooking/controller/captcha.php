<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

trait EventbookingControllerCaptcha
{
	/**
	 * Method to validate captcha
	 *
	 * @param RADInput $input
	 *
	 * @return bool|mixed
	 */
	protected function validateCaptcha($input)
	{
		$user   = JFactory::getUser();
		$config = EventbookingHelper::getConfig();

		if ($config->enable_captcha && ($user->id == 0 || $config->bypass_captcha_for_registered_user !== '1'))
		{
			$captchaPlugin = JFactory::getConfig()->get('captcha');

			if (!$captchaPlugin)
			{
				// Hardcode to recaptcha, reduce support request
				$captchaPlugin = 'recaptcha';
			}

			$plugin = JPluginHelper::getPlugin('captcha', $captchaPlugin);

			if ($plugin)
			{
				return JCaptcha::getInstance($captchaPlugin)->checkAnswer($input->post->get('recaptcha_response_field', '', 'string'));
			}
		}

		return true;
	}
}