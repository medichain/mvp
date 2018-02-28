<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

trait EventbookingViewCaptcha
{
	/**
	 * The flag to determine whether captcha is shown on the view
	 *
	 * @var bool
	 */
	protected $showCaptcha = false;

	/**
	 * Name of captcha plugin used
	 *
	 * @var string
	 */
	protected $captchaPlugin;

	/**
	 * The string contain HTML code to render captcha
	 *
	 * @var string
	 */
	protected $captcha = null;

	/**
	 * Load captcha and store it into properties
	 *
	 * @param bool $initOnly
	 */
	protected function loadCaptcha($initOnly = false)
	{
		$config = EventbookingHelper::getConfig();
		$user   = JFactory::getUser();

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
				$this->showCaptcha = true;

				if ($initOnly)
				{
					JCaptcha::getInstance($captchaPlugin)->initialise('dynamic_recaptcha_1');
				}
				else
				{
					$this->captcha = JCaptcha::getInstance($captchaPlugin)->display('dynamic_recaptcha_1', 'dynamic_recaptcha_1', 'required');
				}
			}
			else
			{
				JFactory::getApplication()->enqueueMessage(\JText::_('EB_CAPTCHA_NOT_ACTIVATED_IN_YOUR_SITE'), 'error');
			}

			$this->captchaPlugin = $captchaPlugin;
		}
	}
}