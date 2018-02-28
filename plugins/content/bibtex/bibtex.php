<?php
/**
* @file
* @brief    BibTeX bibliography formatter Joomla plug-in
* @author   Levente Hunyadi
* @version  1.1.5
* @remarks  Copyright (C) 2009-2012 Levente Hunyadi
* @remarks  Licensed under GNU/GPLv3, see http://www.gnu.org/licenses/gpl-3.0.html
* @see      http://hunyadi.info.hu/projects/bibtex
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once JPATH_PLUGINS.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'bibtex'.DIRECTORY_SEPARATOR.'references.php';
if (version_compare(PHP_VERSION, '5.3') >= 0) {
	require_once JPATH_PLUGINS.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'bibtex'.DIRECTORY_SEPARATOR.'sort.php';
}

// Import library dependencies
jimport('joomla.plugin.plugin');
jimport('joomla.event.plugin');

class EntryStyle {
	public $bold_titles = false;
	public $hanging_indent = false;
	public $separate_title = false;
	public $separate_venue = false;
	public $separate_url = false;
	public $show_notes = false;
	public $separate_notes = false;
	public $show_abstract = false;
}

class plgContentBibTeX extends JPlugin {
    /** The file system folder in which to look for BibTeX files. */
	private $folder;
	private $sort_order = 'unsorted';
	private $show_raw_bibtex = true;
	private $cache = true;
	private $style;
	private $author_list = null;

    /**
    * Constructor.
    */
    public function __construct( &$subject, $config ) {
		parent::__construct( $subject, $config );

		$this->folder = $this->getParameterValue('folder', JPATH_ROOT.DIRECTORY_SEPARATOR.'bibtex');
		if (!preg_match('#^([A-Za-z]*:)?[/\\\\]#', $this->folder)) {  // not an absolute path (with drive letter) or url (with protocol)
			$this->folder = JPATH_ROOT.DIRECTORY_SEPARATOR.$this->folder;  // interpret relative to Joomla web site root
		}

		// initialize parameter values
		$initial_values = get_class_vars(get_class($this));
		$this->sort_order = $this->getParameterValue('sort_order', $initial_values['sort_order']);
		$this->show_raw_bibtex = $this->getParameterValue('show_raw_bibtex', $initial_values['show_raw_bibtex']);
		$this->cache = $this->getParameterValue('cache', $initial_values['cache']);
		$this->style = new EntryStyle();
		$this->getObjectParameterValue($this->style, 'bold_titles');
		$this->getObjectParameterValue($this->style, 'hanging_indent');
		$this->getObjectParameterValue($this->style, 'separate_title');
		$this->getObjectParameterValue($this->style, 'separate_venue');
		$this->getObjectParameterValue($this->style, 'separate_url');
		$this->getObjectParameterValue($this->style, 'show_notes');
		$this->getObjectParameterValue($this->style, 'separate_notes');
		$this->getObjectParameterValue($this->style, 'show_abstract');
		$author_list = $this->getParameterValue('author_list', '');
		if (!empty($author_list)) {
			$author_list = implode(' and ', array_filter(explode("\n", trim($author_list))));
			require_once(JPATH_PLUGINS.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'bibtex'.DIRECTORY_SEPARATOR.'bib.php');
			$this->author_list = BibTexParser::getAuthors($author_list);
		}

		$lang = JFactory::getLanguage();
		$lang->load('plg_content_bibtex', JPATH_ADMINISTRATOR);
	}

	private function getParameterValue($name, $default) {
		if ($this->params instanceof stdClass) {
			if (isset($this->params->$name)) {
				return $this->params->$name;
			}
		} else if ($this->params instanceof JRegistry) {  // Joomla 2.5 and earlier
			$paramvalue = $this->params->get($name);
			if (isset($paramvalue)) {
				return $paramvalue;
			}
		}
		return $default;
	}

	private function getObjectParameterValue(&$object, $name) {
		if ($this->params instanceof stdClass) {
			if (isset($this->params->$name)) {
				$object->$name = $this->params->$name;
				return true;
			}
		} else if ($this->params instanceof JRegistry) {  // Joomla 2.5 and earlier
			$paramvalue = $this->params->get($name);
			if (isset($paramvalue)) {
				$object->$name = $paramvalue;
				return true;
			}
		}

		$object_initial_values = get_class_vars(get_class($object));
		$object->$name = $object_initial_values[$name];
		return false;
	}

	/**
	* Produces an unformatted BibTeX entry list for text output.
	* @param entries An numerically indexed array of associative arrays representing parsed BibTeX entries.
	* @return A raw BibTeX bibliography.
	*/
	private static function getBibTeX($entries) {
		ob_start();
		foreach ($entries as $entry) {
			print Entry::getRaw($entry);
		}
		return ob_get_clean();
	}

	/**
	* Produces a raw PHP array listing for debug purposes.
	*/
	private static function getArray($entries) {
		ob_start();
		print_r($entries);
		return ob_get_clean();
	}

	/**
	* Reads and parses a bibliography or retrieves a parsed bibliography from the cache.
	*/
	private function readBibTex($bib_file) {
		if ($this->cache) {
			$bib_cache = JPATH_CACHE.DIRECTORY_SEPARATOR.'bibtex_'.md5($bib_file);
			$bib_cache_mtime = @filemtime($bib_cache);
			$bib_file_mtime = @filemtime($bib_file);
			if ($bib_cache_mtime !== false && $bib_file_mtime !== false
					&& $bib_cache_mtime > $bib_file_mtime  // bibliography source file has not been modified since cache file was created
					&& ($bib_cache_content = file_get_contents($bib_cache)) !== false  // cache data can be read
					&& count($bib_cache_array = unserialize($bib_cache_content)) == 2) {  // cache data can be parsed into an array
				return $bib_cache_array;
			}
		}

		require_once(JPATH_PLUGINS.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'bibtex'.DIRECTORY_SEPARATOR.'bib.php');
		$bibtex_entries = BibTexParser::read($bib_file);
		$entries = BibTexParser::parse($bibtex_entries);

		if ($this->cache) {
			file_put_contents($bib_cache, serialize(array($bibtex_entries, $entries)));  // save to cache
		}
		return array($bibtex_entries, $entries);
	}

	/**
	* Produces a formatted BibTeX entry.
	*/
	private function getFormattedBibTeX($bib_file) {
		list($bibtex_entries, $entries) = $this->readBibTex($bib_file);

		// sort entries as given by sort criterion
		switch ($this->sort_order) {
			case 'date_asc':
				if (version_compare(PHP_VERSION, '5.3') >= 0) {
					EntryOperations::sortDatesAscending($entries);
				} else {
					usort($entries, array('Entry', 'compareDatesAscending'));
				}
				break;
			case 'date_desc':
				if (version_compare(PHP_VERSION, '5.3') >= 0) {
					EntryOperations::sortDatesDescending($entries);
				} else {
					usort($entries, array('Entry', 'compareDatesDescending'));
				}
				break;
			case 'unsorted':
			default:
				// nothing to do
		}

		// print entries
		foreach ($entries as $entry) {
			if ($this->show_raw_bibtex) {
				$custom_text = Entry::getRaw($bibtex_entries[$entry->citation]);
			} else {
				$custom_text = false;
			}
			$entry->printFormatted($custom_text, 'BibTeX', $this->style, $this->author_list);
		}
	}

	/**
	* Produces a formatted RIS entry.
	*/
	private function getFormattedRIS($ris_file) {
		require_once(JPATH_PLUGINS.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'bibtex'.DIRECTORY_SEPARATOR.'ris.php');

		$ris_references = RisParser::read($ris_file);
		$entries = RisParser::parse($ris_references);

		// print entries
		foreach ($entries as $index => $entry) {
			if ($this->show_raw_bibtex) {
				$custom_text = implode("\n", $ris_references[$index]);
			} else {
				$custom_text = false;
			}
			$entry->printFormatted($custom_text, 'RIS', $this->style);
		}
	}

	/**
	* Replaces an occurrence of a plugin marker with a formatted bibliography.
	* This method is invoked indirectly through \c preg_replace_callback.
	*/
	private function getFormattedReferences($match) {
		$file = JPath::clean($this->folder.DIRECTORY_SEPARATOR.$match[1].'.'.$match[2]);
		if (!is_file($file)) {
			return $match[0];
		}

		$matched = false;  // whether any replacements have been made and output is to be generated
		ob_start();
		print '<ol class="bibtex-list">';
		switch ($match[2]) {
			case 'bib':
				$this->getFormattedBibTeX($file);
				$matched = true;
				break;
			case 'ris':
				$this->getFormattedRIS($file);
				$matched = true;
				break;
			default:
		}
		print '</ol>';
		$replacement = ob_get_clean();

		if ($matched) {
			return $replacement;
		} else {
			return $match[0];
		}
	}

 	/**
	* Executes the plugin method on an item content.
	* Finds all occurrences of {bibtex}bibliographyfile.bib{/bibtex} tags and replaces them with formatted bibliographies.
 	*/
	public function onContentPrepare($context, &$article, &$params, $page = 0) {
		// skip plug-in activation when the content is being indexed
		if ($context === 'com_finder.indexer') {
			return;
		}

		// short-circuit plugin activation without using regexp
		if (strpos($article->text, '{bibtex') === false) {
			return;
		}

		// do replacements of placeholders
		$regexp = '#{bibtex}([\d\w._-][/\d\w._-]*)\.(bib|ris){/bibtex}#s';
		$count = 0;
		$article->text = preg_replace_callback($regexp, array($this, 'getFormattedReferences'), $article->text, -1, $count);  // find fully POSIX compliant path
		if ($count == 0) {
			return;
		}

		// add stylesheet and JavaScript code to document
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::base(true).'/media/bibtex/css/bibtex.css');
		if ($this->show_raw_bibtex) {
			JHTML::_('behavior.framework');
			$document->addScript(JURI::base(true).'/media/bibtex/js/bibtex.js');
		}
	}
}