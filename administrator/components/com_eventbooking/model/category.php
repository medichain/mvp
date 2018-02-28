<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2017 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
use Joomla\String\StringHelper;

class EventbookingModelCategory extends RADModelAdmin
{
	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param JTable $row A reference to a JTable object.
	 *
	 * @return void
	 */
	protected function prepareTable($row, $task, $sourceId = 0)
	{
		// Prevent choosing itself as parent category
		if ($row->parent == $row->id)
		{
			$row->parent = 0;
		}

		$row->level = 1;

		if ($row->parent > 0)
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			// Calculate level
			$query->clear();
			$query->select('`level`')
				->from('#__eb_categories')
				->where('id = ' . (int) $row->parent);
			$db->setQuery($query);
			$row->level = (int) $db->loadResult() + 1;
		}

		parent::prepareTable($row, $task, $sourceId);
	}

	/**
	 * Create category thumbnail if category is selected
	 *
	 * @param EventbookingTableCategory $row
	 * @param RADInput                  $input
	 * @param bool                      $isNew
	 */
	protected function afterStore($row, $input, $isNew)
	{
		parent::afterStore($row, $input, $isNew);

		if ($row->image && file_exists(JPATH_ROOT . '/' . $row->image))
		{
			$config = EventbookingHelper::getConfig();

			$thumbPath   = JPATH_ROOT . '/images/com_eventbooking/categories/thumb/' . basename($row->image);
			$thumbWidth  = $config->get('category_thumb_width') ?: 200;
			$thumbHeight = $config->get('category_thumb_height') ?: 200;
			$image       = new JImage(JPATH_ROOT . '/' . $row->image);

			$fileExt = StringHelper::strtoupper(JFile::getExt($row->image));

			if ($fileExt == 'PNG')
			{
				$imageType = IMAGETYPE_PNG;
			}
			elseif ($fileExt == 'GIF')
			{
				$imageType = IMAGETYPE_GIF;
			}
			elseif (in_array($fileExt, ['JPG', 'JPEG']))
			{
				$imageType = IMAGETYPE_JPEG;
			}
			else
			{
				$imageType = '';
			}

			if ($config->get('resize_image_method') == 'crop_resize')
			{
				$image->cropResize($thumbWidth, $thumbHeight, false)
					->toFile($thumbPath, $imageType);
			}
			else
			{
				$image->resize($thumbWidth, $thumbHeight, false)
					->toFile($thumbPath, $imageType);
			}
		}
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param JTable $row A JTable object.
	 *
	 * @return array An array of conditions to add to ordering queries.
	 */

	protected function getReorderConditions($row)
	{
		return array('`parent` = ' . (int) $row->parent);
	}

	/**
	 * Initialize data for new category
	 */
	public function initData()
	{
		parent::initData();

		$this->data->submit_event_access = 1;
	}

	/**
	 * Override beforeDelete method to delete the urls related to categoroes before categories are delete
	 *
	 * @param array $cid
	 *
	 * @return void
	 */
	protected function beforeDelete($cid)
	{
		$cids  = implode(',', $cid);
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Delete the URLs related to category
		$query->clear()
			->delete('#__eb_urls')
			->where($db->quoteName('view') . '=' . $db->quote('category'))
			->where('record_id IN (' . $cids . ')');
		$db->setQuery($query);
		$db->execute();
	}
}
