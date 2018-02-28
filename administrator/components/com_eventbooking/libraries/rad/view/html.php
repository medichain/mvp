<?php
/**
 * @package     RAD
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2015 Ossolution Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

/**
 * Joomla CMS Base View Html Class
 *
 * @package      RAD
 * @subpackage   View
 * @since        2.0
 */
jimport('joomla.filesystem.path');

class RADViewHtml extends RADView
{
	/**
	 * The view layout.
	 *
	 * @var string
	 */
	protected $layout = 'default';

	/**
	 * The paths queue.
	 *
	 * @var array
	 */
	protected $paths = array();

	/**
	 * Default Itemid variable value for the links in the view
	 *
	 * @var int
	 */
	public $Itemid;

	/**
	 * The input object passed from the controller while creating the view
	 *
	 * @var RADInput
	 */

	protected $input;

	/**
	 * This is a front-end or back-end view.
	 * We need this field to determine whether we need to addToolbar or build the filter
	 *
	 * @var boolean
	 */
	protected $isAdminView = false;

	/**
	 * Options to allow hide default toolbar buttons from backend view
	 *
	 * @var array
	 */
	protected $hideButtons = array();

	/**
	 * The device type is accessing to the view, it can be desktop, tablet or mobile
	 *
	 * @var string
	 */
	protected $deviceType = 'desktop';

	/**
	 * The menu parameter of the view, for frontend
	 *
	 * @var \Joomla\Registry\Registry
	 */
	protected $params;

	/**
	 * Method to instantiate the view.
	 *
	 * @param array $config A named configuration array for object construction
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		if (isset($config['layout']))
		{
			$this->layout = $config['layout'];
		}

		if (isset($config['paths']))
		{
			$this->paths = $config['paths'];
		}
		else
		{
			$this->paths = array();
		}

		if (!empty($config['is_admin_view']))
		{
			$this->isAdminView = $config['is_admin_view'];
		}

		if (!empty($config['Itemid']))
		{
			$this->Itemid = $config['Itemid'];
		}

		if (isset($config['input']))
		{
			$this->input = $config['input'];
		}

		if (isset($config['hide_buttons']))
		{
			$this->hideButtons = $config['hide_buttons'];
		}

		$this->deviceType = EventbookingHelper::getDeviceType();
	}

	/**
	 * Method to display the view
	 */
	public function display()
	{
		echo $this->render();
	}

	/**
	 * Magic toString method that is a proxy for the render method.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->render();
	}

	/**
	 * Method to escape output.
	 *
	 * @param string $output The output to escape.
	 *
	 * @return string The escaped output.
	 */
	public function escape($output)
	{
		return htmlspecialchars($output, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * Method to get the view layout.
	 *
	 * @return string The layout name.
	 */
	public function getLayout()
	{
		return $this->layout;
	}

	/**
	 * Method to get the layout path.
	 *
	 * @param string $layout The layout name.
	 *
	 * @return mixed The layout file name if found, false otherwise.
	 */
	public function getPath($layout)
	{
		// Try to find the layout file with the following priority order: Device type, Joomla version, Default Layout
		$filesToFind = array($layout);

		if ($this->deviceType !== 'desktop')
		{
			array_unshift($filesToFind, $layout . '.' . $this->deviceType);
		}

		foreach ($filesToFind as $fileLayout)
		{
			$file = JPath::clean($fileLayout . '.php');
			$path = JPath::find($this->paths, $file);

			if ($path)
			{
				break;
			}
		}

		return $path;
	}

	/**
	 * Method to get the view paths.
	 *
	 * @return array The paths queue.
	 */
	public function getPaths()
	{
		return $this->paths;
	}

	/**
	 * Method to render the view.
	 *
	 * @return string The rendered view.
	 *
	 * @throws RuntimeException
	 */
	public function render()
	{
		// Get the layout path.
		$path = $this->getPath($this->getLayout());

		// Check if the layout path was found.
		if (!$path)
		{
			throw new RuntimeException('Layout Path Not Found');
		}

		// Start an output buffer.
		ob_start();

		// Load the layout.
		include $path;

		// Get the layout contents.
		return ob_get_clean();
	}

	/**
	 * Load sub-template for the current layout
	 *
	 * @param string $template
	 *
	 * @throws RuntimeException
	 *
	 * @return string The output of sub-layout
	 */
	public function loadTemplate($template, $data = array())
	{
		// Get the layout path.
		$path = $this->getPath($this->getLayout() . '_' . $template);

		// Check if the layout path was found.
		if (!$path)
		{
			throw new RuntimeException('Layout Path Not Found');
		}

		extract($data);
		// Start an output buffer.
		ob_start();
		// Load the layout.
		include $path;

		// Get the layout contents.
		return ob_get_clean();
	}

	/**
	 * Load common template for the view
	 *
	 * @param string $layout
	 *
	 * @throws RuntimeException
	 *
	 * @return string The output of common layout
	 */
	public function loadCommonLayout($layout, $data = array())
	{
		$app       = JFactory::getApplication();
		$template  = $app->getTemplate();
		$themeFile = str_replace('/tmpl', '', $layout);

		$deviceType = EventbookingHelper::getDeviceType();

		$paths = array($layout);

		if ($deviceType != 'desktop')
		{
			$paths[] = JPATH_THEMES . '/' . $template . '/html/com_eventbooking/' . str_replace('.php', '.' . $deviceType . '.php', $themeFile);
			$paths[] = JPATH_ROOT . '/components/com_eventbooking/view/' . str_replace('.php', '.' . $deviceType . '.php', $layout);
		}

		$paths[] = JPATH_THEMES . '/' . $template . '/html/com_eventbooking/' . $themeFile;
		$paths[] = JPATH_ROOT . '/components/com_eventbooking/view/' . $layout;

		$path = '';

		foreach ($paths as $possiblePath)
		{
			if (JFile::exists($possiblePath))
			{
				$path = $possiblePath;
				break;
			}
		}

		if (empty($path))
		{
			throw new RuntimeException(JText::sprintf('The given common layout %s does not exist', $layout));
		}

		// Start an output buffer.
		ob_start();
		extract($data);

		// Load the layout.
		include $path;

		// Get the layout contents.
		return ob_get_clean();
	}

	/**
	 * Method to set the view layout.
	 *
	 * @param string $layout The layout name.
	 *
	 * @return RADViewHtml Method supports chaining.
	 */
	public function setLayout($layout)
	{
		$this->layout = $layout;

		return $this;
	}

	/**
	 * Method to set the view paths.
	 *
	 * @param array $paths The paths queue.
	 *
	 * @return RADViewHtml Method supports chaining.
	 */
	public function setPaths($paths)
	{
		$this->paths = $paths;

		return $this;
	}

	/**
	 * Get page params of the given view
	 *
	 * @param array $views
	 * @param array $query
	 *
	 * @return \Joomla\Registry\Registry
	 */
	protected function getParams($views = array(), $query = array())
	{
		// Default to current view
		if (empty($views))
		{
			$views = array($this->getName());
		}

		$active = JFactory::getApplication()->getMenu()->getActive();

		if ($active && isset($active->query['view']) && in_array($active->query['view'], $views))
		{
			$params = $active->params;

			if ($active->query['view'] != $this->getName() || array_diff($query, $active->query))
			{
				$params->set('page_title', '');
				$params->set('page_heading', '');
				$params->set('show_page_heading', true);
			}

			return $params;
		}

		return new \Joomla\Registry\Registry;
	}

	/**
	 * Set document meta data
	 *
	 * @return void
	 */
	protected function setDocumentMetadata()
	{
		/* @var JDocumentHtml $document */
		$document         = JFactory::getDocument();
		$siteNamePosition = JFactory::getConfig()->get('sitename_pagetitles');
		$siteName         = JFactory::getConfig()->get('sitename');

		if ($pageTitle = $this->params->get('page_title'))
		{
			if ($siteNamePosition == 0)
			{
				$document->setTitle($pageTitle);
			}
			elseif ($siteNamePosition == 1)
			{
				$document->setTitle($siteName . ' - ' . $pageTitle);
			}
			else
			{
				$document->setTitle($pageTitle . ' - ' . $siteName);
			}
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$document->setMetaData('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('menu-meta_description'))
		{
			$document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('robots'))
		{
			$document->setMetaData('robots', $this->params->get('robots'));
		}
	}

	/**
	 * Add feed links to current view
	 *
	 * @return void
	 */
	protected function addFeedLinks()
	{
		/* @var JDocumentHtml $document */
		$document = JFactory::getDocument();
		$link     = '&format=feed&limitstart=';
		$attribs  = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
		$document->addHeadLink(JRoute::_($link . '&type=rss'), 'alternate', 'rel', $attribs);
		$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
		$document->addHeadLink(JRoute::_($link . '&type=atom'), 'alternate', 'rel', $attribs);
	}

	/**
	 * Set active menu item used for links generated within the view
	 *
	 * @return void
	 */
	protected function findAndSetActiveMenuItem()
	{
		// Attempt to find the correct menu item for the view if required
		$active = JFactory::getApplication()->getMenu()->getActive();

		if ($active && isset($active->query['view']))
		{
			$view = $active->query['view'];
		}
		else
		{
			$view = '';
		}

		if ($view != strtolower($this->getName()))
		{
			$menuId = EventbookingHelperRoute::findView('calendar');

			if ($menuId)
			{
				$this->Itemid = $menuId;
			}
		}
	}
}
