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

class EventbookingViewInviteHtml extends RADViewHtml
{
	use EventbookingViewCaptcha;

	/**
	 * Display invitation form for an event
	 *
	 * @throws Exception
	 */
	public function display()
	{
		$layout = $this->getLayout();

		if ($layout == 'complete')
		{
			$this->displayInviteComplete();
		}
		else
		{
			$user        = JFactory::getUser();
			$config      = EventbookingHelper::getConfig();
			$message     = EventbookingHelper::getMessages();
			$fieldSuffix = EventbookingHelper::getFieldSuffix();

			if (strlen(trim(strip_tags($message->{'invitation_form_message' . $fieldSuffix}))))
			{
				$inviteMessage = $message->{'invitation_form_message' . $fieldSuffix};
			}
			else
			{
				$inviteMessage = $message->invitation_form_message;
			}

			// Load captcha
			$this->loadCaptcha();

			$eventId = $this->input->getInt('id');
			$name    = $this->input->getString('name');

			if (empty($name))
			{
				$name = $user->get('name');
			}

			$this->event           = EventbookingHelperDatabase::getEvent($eventId);
			$this->name            = $name;
			$this->inviteMessage   = $inviteMessage;
			$this->friendNames     = $this->input->getString('friend_names');
			$this->friendEmails    = $this->input->getString('friend_emails');
			$this->mesage          = $this->input->getString('message');
			$this->bootstrapHelper = new EventbookingHelperBootstrap($config->twitter_bootstrap_version);

			parent::display();
		}
	}

	/**
	 * Display invitation complete message
	 */
	protected function displayInviteComplete()
	{
		$message     = EventbookingHelper::getMessages();
		$fieldSuffix = EventbookingHelper::getFieldSuffix();

		if (strlen(trim(strip_tags($message->{'invitation_complete' . $fieldSuffix}))))
		{
			$this->message = $message->{'invitation_complete' . $fieldSuffix};
		}
		else
		{
			$this->message = $message->invitation_complete;
		}

		parent::display();
	}
}
