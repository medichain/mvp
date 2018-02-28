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

require_once JPATH_PLUGINS.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'bibtex'.DIRECTORY_SEPARATOR.'latex.php';

/**
* Creator (i.e. author or editor) of a piece in a bibliography entry.
*/
class EntryCreator {
	protected $given_name;
	protected $family_name;

	function __construct($family_name, $given_name = false) {
		$this->family_name = $family_name;
		$this->given_name = $given_name;
	}

	/**
	* Produces the full name of the creator.
	* @return A language-specific full name.
	*/
	function __toString() {
		if ($this->given_name) {
			$full_name = JText::sprintf('BIBTEX_NAME_FORMAT', $this->family_name, $this->given_name);
			if (!$full_name) {  /* language string missing, default to Western name order */
				$full_name = $this->given_name.' '.$this->family_name;
			}
			return $full_name;
		} else {
			return $this->family_name;
		}
	}
}

/**
* A list of creators (i.e. authors or editors) of a piece in a bibliography entry.
*/
class EntryCreatorList {
	protected $creators = array();

	public function add(EntryCreator $creator) {
		$this->creators[] = $creator;
	}

	public function addList(EntryCreatorList $creators) {
		$this->creators = array_merge($this->creators, $creators->creators);
	}

	public function getList() {
		return $this->creators;
	}

	public function isEmpty() {
		return count($this->creators) == 0;
	}
}

class EntryAuthorList extends EntryCreatorList {
	/**
	* Produces a formatted author field.
	* @return A properly delimited language-specific author list text.
	*/
	function __toString() {
		switch (count($this->creators)) {
			case 0:
				return '';
			case 1:
				return (string) $this->creators[0];
			default:
				$s = (string) $this->creators[0];
				for ($k = 1; $k < count($this->creators) - 1; $k++) {
					$s .= ', '.$this->creators[$k];
				}
				$s .= ' '.JText::_('BIBTEX_AND').' '.end($this->creators);
				return $s;
		}
	}
}

class EntryEditorList extends EntryCreatorList {
	/**
	* Produces a formatted editor field.
	* @return A properly delimited language-specific editor list text.
	*/
	function __toString() {
		switch (count($this->creators)) {
			case 0:
				return '';
			case 1:
				return $this->creators[0].' ('.JText::_('BIBTEX_EDITOR').')';
			default:
				$s = (string) $this->creators[0];
				for ($k = 1; $k < count($this->creators) - 1; $k++) {
					$s .= ', '.$this->creators[$k];
				}
				$s .= ' '.JText::_('BIBTEX_AND').' '.end($this->creators).' ('.JText::_('BIBTEX_EDITORS').')';
				return $s;
		}
	}
}

class EntryPageRange {
	private $start = false;
	private $end = false;

	function __construct($start_page, $end_page = false) {
		$this->start = $start_page;
		$this->end = $end_page;
	}
}

/**
* A BibTeX entry.
* For styling guidelines, see <http://verbosus.com/bibtex-style-examples.html>
*/
abstract class Entry {
	/**
	* Citation key. A unique identifier used to tag the article.
	*/
	public $citation = false;

	/**
	* A list of (unrecognized) fields attached to the bibliography entry.
	*/
	protected $fields = array();

	protected $authors;
	protected $editors;

	/** HTML content to print before publication title. */
	protected $title_prefix = "";
	/** HTML content to print after publication title. */
	protected $title_postfix = "";
	/** HTML content to print before venue. */
	protected $venue_prefix = "";
	/** HTML content to print before publication URL. */
	protected $url_prefix = "";
	/** HTML content to print before publication notes. */
	protected $notes_prefix = "";
	/** HTML content to print after publication notes. */
	protected $notes_postfix = "";

	/** Whether to show publication notes. This is a state variable. */
	protected $show_notes = false;
	/** Whether to show publication abstract. This is a state variable. */
	protected $show_abstract = false;

	function __construct() {
		$this->authors = new EntryAuthorList();
		$this->editors = new EntryEditorList();
	}

	function __set($key, $value) {
		$this->fields[$key] = $value;
	}

	function __get($key) {
		if (isset($this->fields[$key])) {
			return $this->fields[$key];
		} else {
			return null;
		}
	}

	/**
	* Gets the date associated with a bibliography entry.
	*/
	public function getDate(DateTime $default = null) {
		if (isset($this->fields['year'])) {
			$year = $this->fields['year'];
			if (isset($this->fields['month']) && ($month = get_month_ordinal_number($this->fields['month'])) !== false) {
				return new DateTime(sprintf('%04d-%02d-%02d', $year, $month, 1));
			} else {
				return new DateTime(sprintf('%04d-%02d-%02d', $year, 1, 1));
			}
		} else {
			return $default;  // a default value if no date is specified
		}
	}

	private static function compareDates(Entry $entry1, Entry $entry2, $null_last = true) {
		$date1 = $entry1->getDate();
		$date2 = $entry2->getDate();
		if (isset($date1) && isset($date2)) {
			if (version_compare(PHP_VERSION, '5.3') >= 0) {
				$interval = $date2->diff($date1);
				return $interval->invert ? -$interval->days : $interval->days;
			} else {
				return round(($date1->format('U') - $date2->format('U')) / (60*60*24));
			}
		} elseif (isset($date1)) {
			return $null_last ? -1 : 1;
		} elseif (isset($date2)) {
			return $null_last ? 1 : -1;
		} else {
			return 0;
		}
	}

	/**
	* Compares two dates, producing an order with the earlier date first.
	*/
	public static function compareDatesAscending(Entry $entry1, Entry $entry2) {
		return self::compareDates($entry1, $entry2, true);  // puts entries without date at end of list
	}

	/**
	* Compares two dates, producing an order with the later date first.
	*/
	public static function compareDatesDescending(Entry $entry1, Entry $entry2) {
		return self::compareDates($entry2, $entry1, false);  // puts entries without date at end of list
	}

	public function addAuthor(EntryCreator $author) {
		$this->authors->add($author);
	}

	public function addAuthors(EntryAuthorList $authors) {
		$this->authors->addList($authors);
	}

	public function addEditor(EntryCreator $editor) {
		$this->editors->add($editor);
	}

	public function addEditors(EntryEditorList $editors) {
		$this->editors->addList($editors);
	}

	/**
	* Produces a formatted bibliography entry for HTML output.
	* @return A human-readable bibliography reference.
	*/
	public function printFormatted($raw_bibtex = false, $raw_caption = false, EntryStyle $style = null, EntryCreatorList $authors_highlight = null) {
		if (!isset($style)) {
			$style = new EntryStyle();
		}
		if ($style->bold_titles) {
			$this->title_prefix = "<strong>";
			$this->title_postfix = "</strong>";
		}
		if ($style->separate_title) {
			$this->title_prefix = "<br>".$this->title_prefix;
		}
		if ($style->separate_venue) {
			$this->venue_prefix = "<br/>";
		}
		if ($style->separate_url) {
			$this->url_prefix = "<br/>";
		}
		$this->show_notes = $style->show_notes;
		if ($style->separate_notes) {
			$this->notes_prefix = "<br/>".$this->notes_prefix;
		}
		$this->show_abstract = $style->show_abstract;

		$id = get_class($this).'-'.preg_replace('/[^A-Za-z0-9_-]/', '_', $this->citation);
		print '<li id="'.$id.'">';
		if ($style->hanging_indent) {
			print '<p class="bibtex-hanging-indent">';
		} else {
			print '<p>';
		}
		$this->printEntry($authors_highlight);
		if ($raw_bibtex) {
			print ' <a href="#" class="bibtex-link bibtex-link-code">'.($raw_caption ? $raw_caption : 'BibTeX').'</a>';
		}
		print '</p>';
		if ($raw_bibtex) {
			print '<pre class="bibtex-code">';
			print $raw_bibtex;
			print '</pre>';
		}
		print '</li>';
	}

	public abstract function printEntry(EntryCreatorList $authors_highlight = null);

	private static function translateOrdinal(&$entry, $field) {
		if (isset($entry[$field])) {
			if (($value = get_ordinal_standard_name(str_replace('-', '_', $entry[$field]))) !== false) {
				$entry[$field] = JText::_($value);
			}
		}
	}

	protected static function printDot(&$usecomma) {
		if ($usecomma) {
			print '.';
		}
		$usecomma = false;
	}

	protected static function printField($entry, $field, &$usecomma) {
		if (isset($entry[$field])) {
			if ($usecomma) {
				print ',';
			}
			print ' '.$entry[$field];
			$usecomma = true;
		}
	}

	private static function printFormattedField($entry, $field, $formatkey, $stringkey, &$usecomma) {
		if (isset($entry[$field])) {
			if ($usecomma) {
				print ', ';
				JText::printf($formatkey, $entry[$field], JText::_($stringkey));
			} else {
				print ' ';
				print ucfirst(JText::sprintf($formatkey, $entry[$field], JText::_($stringkey)));
			}
			$usecomma = true;
		}
	}

	protected static function printSeries($entry, &$usecomma) {
		Entry::translateOrdinal($entry, 'series');
		Entry::printFormattedField($entry, 'series', 'BIBTEX_SERIES_FORMAT', 'BIBTEX_SERIES', $usecomma);
	}

	protected static function printEdition($entry, &$usecomma) {
		Entry::translateOrdinal($entry, 'edition');
		Entry::printFormattedField($entry, 'edition', 'BIBTEX_EDITION_FORMAT', 'BIBTEX_EDITION', $usecomma);
	}

	protected static function printVolume($entry, &$usecomma) {
		Entry::printFormattedField($entry, 'volume', 'BIBTEX_VOLUME_FORMAT', 'BIBTEX_VOLUME', $usecomma);
	}

	protected static function printNumber($entry, &$usecomma) {
		Entry::printFormattedField($entry, 'number', 'BIBTEX_NUMBER_FORMAT', 'BIBTEX_NUMBER', $usecomma);
	}

	protected static function printChapter($entry, &$usecomma) {
		Entry::printFormattedField($entry, 'chapter', 'BIBTEX_CHAPTER_FORMAT', 'BIBTEX_CHAPTER', $usecomma);
	}

	protected static function printPages($entry, &$usecomma) {
		if (isset($entry['pages'])) {
			print $usecomma ? ', ' : ' ';
			JText::printf('BIBTEX_PAGERANGE_FORMAT', $entry['pages'], ctype_digit($entry['pages']) ? JText::_('BIBTEX_PAGE') : JText::_('BIBTEX_PAGES'));
			$usecomma = true;
		}
	}

	protected static function printDate($entry, &$usecomma) {
		if (isset($entry['year'])) {
			print $usecomma ? ', ' : ' ';
			if (isset($entry['month']) && ($month = get_month_standard_name($entry['month'])) !== false) {
				JText::printf('BIBTEX_DATE_FORMAT_YEARMONTH', $entry['year'], JText::_($month));
			} else {
				JText::printf('BIBTEX_DATE_FORMAT_YEAR', $entry['year']);
			}
			$usecomma = true;
		}
	}

	protected function printUrl($entry, &$usecomma) {
		print $this->url_prefix;

		// print URL fields like "url", "arxiv", "preprint", "pdf"
		$url_tags = array("url" => "URL", "arxiv" => "arXiv", "preprint" => "pre-print", "pdf" => "PDF");
		foreach ($url_tags as $url_tag => $label) {
			if (isset($entry[$url_tag])) {
				$urlpattern =
					'(?:(?:ht|f)tps?://|~\/|\/)?'.  // protocol
					'(?:\w+:\w+\x40)?'.  // username and password, \x40 = @
					'(?:(?:[-\w]+\.)+(?:com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum|travel|[a-z]{2}))'.  // domain
					'(?::[\d]{1,5})?'.  // port
					'(?:(?:(?:\/(?:[-\w~!$+|.,=]|%[a-f\d]{2})+)+|\/)+|\?|#)?'.  // path
					'(?:(?:\?(?:[-\w~!$+|.,*:]|%[a-f\d{2}])+=?(?:[-\w~!$+|.,*:=]|%[a-f\d]{2})*)(?:&(?:[-\w~!$+|.,*:]|%[a-f\d{2}])+=?(?:[-\w~!$+|.,*:=]|%[a-f\d]{2})*)*)*'.  // query
					'(?:#(?:[-\w~!$+|.,*:=]|%[a-f\d]{2})*)?';  // anchor
				if (preg_match('@'.$urlpattern.'@', $entry[$url_tag])) {
					if (preg_match('/\.pdf$/i', $entry[$url_tag])) {  // PDF
						$linkclass = 'bibtex-link-pdf';
					} elseif (preg_match('/\.html?$/i', $entry[$url_tag])) {  // HTML
						$linkclass = 'bibtex-link-html';
					} elseif (preg_match('/\.txt$/i', $entry[$url_tag])) {  // plain text
						$linkclass = 'bibtex-link-text';
					} elseif (preg_match('/\.(zip|gz)$/i', $entry[$url_tag])) {  // compressed file
						$linkclass = 'bibtex-link-zip';
					} elseif (preg_match('/\.docx?$/i', $entry[$url_tag])) {  // word processor document
						$linkclass = 'bibtex-link-zip';
					} elseif (preg_match('/\.pptx?$/i', $entry[$url_tag])) {  // presentation
						$linkclass = 'bibtex-link-presentation';
					} else {
						$linkclass = 'bibtex-link-url';
					}

					print ' ';  // always print space between URL items
					print '<a target="_blank" class="bibtex-link '.$linkclass.'" href="'.$entry[$url_tag].'">'.$label.'</a>';
					$usecomma = true;
				}
			}
		}

		// print URL field "doi" (digital object identifier)
		if (isset($entry['doi'])) {
			print $usecomma ? ', ' : ' ';
			print '<a target="_blank" class="bibtex-link bibtex-link-url" href="http://dx.doi.org/'.$entry['doi'].'">DOI</a>';
			$usecomma = true;
		}
	}

	protected function printNotes($entry, &$usecomma) {
		if ($this->show_notes && (isset($entry['note']) || isset($entry['notes']))) {
			if (isset($entry['note'])) {
				$note = $entry['note'];
			} elseif (isset($entry['notes'])) {
				$note = $entry['notes'];
			} else {
				$note = '';
			}
			print $usecomma ? ', ' : ' ';
			print $this->notes_prefix.$note.$this->notes_postfix;
			$usecomma = true;
			$this->printDot($usecomma);
		}
	}

	protected function printAbstract($entry, &$usecomma) {
		if ($this->show_abstract && isset($entry['abstract'])) {
			print '<br/><a href="#" class="bibtex-link-abstract">'.JText::_('BIBTEX_ABSTRACT').'</a> <span class="bibtex-abstract">'.$entry['abstract'].'</span>';
			$usecomma = false;
		}
	}

	protected static function printHighlightedCreatorList(EntryCreatorList $authors, EntryCreatorList $authors_highlight = null) {
		// list of authors (or editors) as a formatted string
		$author_text = (string)$authors;

		// replace selected author (or editor) names with bold face text
		if (isset($authors_highlight)) {
			foreach ($authors_highlight->getList() as $author_highlight) {
				$author_highlight_text = (string)$author_highlight;
				$author_text = str_replace($author_highlight_text, '<b>'.$author_highlight_text.'</b>', $author_text);
			}
		}

		// print author (or editor) list with highlighted names
		print $author_text.'. ';
	}

	/**
	* Outputs a single raw BibTeX entry.
	* @param entry An associative array representing a parsed BibTeX entry returned by BibTexParser.
	*/
	public static function getRaw($entry) {
		$entry_type = $entry['bibtexEntryType'];
		$entry_citation = $entry['bibtexCitation'];
		unset($entry['bibtexEntryType']);
		unset($entry['bibtexCitation']);

		ob_start();
		print "@{$entry_type}{{$entry_citation}";
		foreach ($entry as $key => $value) {
			print ",\n\t".$key.' = ';
			if (ctype_digit($value)) {
				print $value;  // no need to escape integers
			} elseif (strpos($value, '@') !== false) {
				print '"'.str_replace('"', '{"}', $value).'"';
			} elseif (strpos($value, '"') !== false) {
				print '{'.$value.'}';
			} else {
				print '"'.$value.'"';
			}
		}
		print "\n}\n";
		return ob_get_clean();
	}
}

class ArticleEntry extends Entry {
	public function printEntry(EntryCreatorList $authors_highlight = null) {
		$e =& $this->fields;
		if (!$this->authors->isEmpty()) {
			self::printHighlightedCreatorList($this->authors, $authors_highlight);
		}
		print $this->title_prefix.$e['title'].$this->title_postfix.'.';
		print ' ';
		print $this->venue_prefix;
		print '<i>'.$e['journal'].'</i>';
		if (isset($e['volume']) && isset($e['number'])) {
			print ' '.$e['volume'].'('.$e['number'].')';
		} elseif (isset($e['volume'])) {
			print ' '.$e['volume'];
		} elseif (isset($e['number'])) {
			print ' ('.$e['number'].')';
		}
		$usecomma = true;
		if (isset($e['pages'])) {
			if (isset($e['volume']) || isset($e['number'])) {
				print ':'.$e['pages'];
			} else {
				$this->printPages($e, $usecomma);
			}
		}
		$this->printDate($e, $usecomma);
		$this->printDot($usecomma);
		$this->printNotes($e, $usecomma);
		$this->printAbstract($e, $usecomma);
		$this->printUrl($e, $usecomma);
	}
}

class BookEntry extends Entry {
	public function printEntry(EntryCreatorList $authors_highlight = null) {
		$e =& $this->fields;
		if (!$this->editors->isEmpty()) {
			self::printHighlightedCreatorList($this->editors, $authors_highlight);
		} elseif (!$this->authors->isEmpty()) {
			self::printHighlightedCreatorList($this->authors, $authors_highlight);
		}
		print '<i>'.$this->title_prefix.$e['title'].$this->title_postfix.'</i>.';
		print $this->venue_prefix;
		$usecomma = false;
		$this->printEdition($e, $usecomma);
		$this->printSeries($e, $usecomma);
		$this->printVolume($e, $usecomma);
		$this->printNumber($e, $usecomma);
		$this->printField($e, 'publisher', $usecomma);
		$this->printDate($e, $usecomma);
		$this->printDot($usecomma);
		$this->printNotes($e, $usecomma);
		$this->printAbstract($e, $usecomma);
		$this->printUrl($e, $usecomma);
	}
}

class BookletEntry extends Entry {
	public function printEntry(EntryCreatorList $authors_highlight = null) {
		$e =& $this->fields;
		if (!$this->authors->isEmpty()) {
			self::printHighlightedCreatorList($this->authors, $authors_highlight);
		}
		print '<i>'.$this->title_prefix.$e['title'].$this->title_postfix.'</i>.';
		print $this->venue_prefix;
		$usecomma = false;
		$this->printField($e, 'howpublished', $usecomma);
		$this->printField($e, 'address', $usecomma);
		$this->printDate($e, $usecomma);
		$this->printDot($usecomma);
		$this->printNotes($e, $usecomma);
		$this->printAbstract($e, $usecomma);
		$this->printUrl($e, $usecomma);
	}
}

class InBookEntry extends Entry {
	public function printEntry(EntryCreatorList $authors_highlight = null) {
		$e =& $this->fields;
		if (!$this->editors->isEmpty()) {
			self::printHighlightedCreatorList($this->editors, $authors_highlight);
		} elseif (!$this->authors->isEmpty()) {
			self::printHighlightedCreatorList($this->authors, $authors_highlight);
		}
		print '<i>'.$this->title_prefix.$e['title'].$this->title_postfix.'</i>.';
		print $this->venue_prefix;
		$usecomma = false;
		$this->printField($e, 'type', $usecomma);
		$this->printSeries($e, $usecomma);
		$this->printVolume($e, $usecomma);
		$this->printNumber($e, $usecomma);
		$this->printChapter($e, $usecomma);
		$this->printPages($e, $usecomma);
		$this->printField($e, 'publisher', $usecomma);
		$this->printDate($e, $usecomma);
		$this->printDot($usecomma);
		$this->printNotes($e, $usecomma);
		$this->printAbstract($e, $usecomma);
		$this->printUrl($e, $usecomma);
	}
}

class InCollectionEntry extends Entry {
	public function printEntry(EntryCreatorList $authors_highlight = null) {
		$e =& $this->fields;
		if (!$this->authors->isEmpty()) {
			self::printHighlightedCreatorList($this->authors, $authors_highlight);
		}
		print $this->title_prefix.$e['title'].$this->title_postfix.'.';
		print $this->venue_prefix;
		$usecomma = false;
		if (isset($e['booktitle'])) {
			print ' In ';
			if (!$this->editors->isEmpty()) {
				self::printHighlightedCreatorList($this->editors, $authors_highlight);
			}
			print '<i>'.$e['booktitle'].'</i>.';
		}
		$this->printSeries($e, $usecomma);
		$this->printVolume($e, $usecomma);
		$this->printNumber($e, $usecomma);
		$this->printField($e, 'publisher', $usecomma);
		$this->printDate($e, $usecomma);
		$this->printPages($e, $usecomma);
		$this->printDot($usecomma);
		$this->printNotes($e, $usecomma);
		$this->printAbstract($e, $usecomma);
		$this->printUrl($e, $usecomma);
	}
}

class ProceedingsPaperEntry extends Entry {
	public function printEntry(EntryCreatorList $authors_highlight = null) {
		$e =& $this->fields;
		if (!$this->authors->isEmpty()) {
			self::printHighlightedCreatorList($this->authors, $authors_highlight);
		}
		print $this->title_prefix.$e['title'].$this->title_postfix.'.';
		print $this->venue_prefix;
		$usecomma = false;
		if (isset($e['booktitle'])) {
			print ' In ';
			if (!$this->editors->isEmpty()) {
				self::printHighlightedCreatorList($this->editors, $authors_highlight);
			}
			print '<i>'.$e['booktitle'].'</i>';
			if (isset($e['volume']) && isset($e['number'])) {
				print ' '.$e['volume'].'('.$e['number'].')';
			} elseif (isset($e['volume'])) {
				print ' '.$e['volume'];
			} elseif (isset($e['number'])) {
				print ' ('.$e['number'].')';
			}
			print '.';
		}
		$this->printDate($e, $usecomma);
		if (isset($e['pages'])) {
			print $usecomma ? ', ' : ' ';
			print $e['pages'];
			$usecomma = true;
		}
		$this->printDot($usecomma);
		$this->printNotes($e, $usecomma);
		$this->printAbstract($e, $usecomma);
		$this->printUrl($e, $usecomma);
	}
}

class PosterPaperEntry extends Entry {
	public function printEntry(EntryCreatorList $authors_highlight = null) {
		$e =& $this->fields;
		if (!$this->authors->isEmpty()) {
			self::printHighlightedCreatorList($this->authors, $authors_highlight);
		}
		print $this->title_prefix.$e['title'].$this->title_postfix.'.';
		print $this->venue_prefix;
		$usecomma = false;
		if (isset($e['event'])) {
			print ' '.JText::_('BIBTEX_POSTER_AT').' ';
			print '<i>'.$e['event'].'</i>';
			$usecomma = true;
		}
		$this->printField($e, 'location', $usecomma);
		$this->printDate($e, $usecomma);
		$this->printDot($usecomma);
		$this->printNotes($e, $usecomma);
		$this->printAbstract($e, $usecomma);
		$this->printUrl($e, $usecomma);
	}
}

class ManualEntry extends Entry {
	public function printEntry(EntryCreatorList $authors_highlight = null) {
		$e =& $this->fields;
		if (!$this->authors->isEmpty()) {
			self::printHighlightedCreatorList($this->authors, $authors_highlight);
		}
		print '<i>'.$this->title_prefix.$e['title'].$this->title_postfix.'</i>.';
		print $this->venue_prefix;
		$usecomma = false;
		$this->printField($e, 'organization', $usecomma);
		$this->printField($e, 'address', $usecomma);
		$this->printEdition($e, $usecomma);
		$this->printDate($e, $usecomma);
		$this->printDot($usecomma);
		$this->printNotes($e, $usecomma);
		$this->printAbstract($e, $usecomma);
		$this->printUrl($e, $usecomma);
	}
}

class ThesisEntry extends Entry {
	public function printEntry(EntryCreatorList $authors_highlight = null) {
		$e =& $this->fields;
		if (!$this->authors->isEmpty()) {
			self::printHighlightedCreatorList($this->authors, $authors_highlight);
		}
		print $this->title_prefix.$e['title'].$this->title_postfix.'.';
		print $this->venue_prefix;
		$usecomma = false;
		$this->printField($e, 'type', $usecomma);
		$this->printField($e, 'school', $usecomma);
		$this->printField($e, 'address', $usecomma);
		$this->printDate($e, $usecomma);
		$this->printDot($usecomma);
		$this->printNotes($e, $usecomma);
		$this->printAbstract($e, $usecomma);
		$this->printUrl($e, $usecomma);
	}
}

class MastersThesisEntry extends ThesisEntry {}

class PhdThesisEntry extends ThesisEntry {}

class MiscellaneousEntry extends Entry {
	public function printEntry(EntryCreatorList $authors_highlight = null) {
		$e =& $this->fields;
		if (!$this->authors->isEmpty()) {
			self::printHighlightedCreatorList($this->authors, $authors_highlight);
		}
		print $this->title_prefix.$e['title'].$this->title_postfix.'.';
		print $this->venue_prefix;
		$usecomma = false;
		$this->printField($e, 'howpublished', $usecomma);
		$this->printDate($e, $usecomma);
		$this->printDot($usecomma);
		$this->printNotes($e, $usecomma);
		$this->printAbstract($e, $usecomma);
		$this->printUrl($e, $usecomma);
	}
}

class ProceedingsEntry extends Entry {
	public function printEntry(EntryCreatorList $authors_highlight = null) {
		$e =& $this->fields;
		if (!$this->editors->isEmpty()) {
			self::printHighlightedCreatorList($this->editors, $authors_highlight);
		}
		print '<i>'.$this->title_prefix.$e['title'].$this->title_postfix.'</i>';
		print $this->venue_prefix;
		if (isset($e['volume']) && isset($e['number'])) {
			print ' '.$e['volume'].'('.$e['number'].')';
		} elseif (isset($e['volume'])) {
			print ' '.$e['volume'];
		} elseif (isset($e['number'])) {
			print ' ('.$e['number'].')';
		}
		print '.';
		$usecomma = false;
		$this->printField($e, 'organization', $usecomma);
		$this->printField($e, 'publisher', $usecomma);
		$this->printField($e, 'address', $usecomma);
		$this->printDate($e, $usecomma);
		$this->printDot($usecomma);
		$this->printNotes($e, $usecomma);
		$this->printAbstract($e, $usecomma);
		$this->printUrl($e, $usecomma);
	}
}

class TechnicalReportEntry extends Entry {
	public function printEntry(EntryCreatorList $authors_highlight = null) {
		$e =& $this->fields;
		if (!$this->authors->isEmpty()) {
			self::printHighlightedCreatorList($this->authors, $authors_highlight);
		}
		print $this->title_prefix.$e['title'].$this->title_postfix.'.';
		print $this->venue_prefix;
		$usecomma = false;
		if (isset($e['type'])) {
			print $usecomma ? ', ' : ' ';
			if (isset($e['number'])) {
				print $e['type'].' '.$e['number'];
			} else {
				print $e['type'];
			}
			$usecomma = true;
		} elseif (isset($e['number'])) {
			$this->printNumber($e, $usecomma);
		}
		$this->printField($e, 'institution', $usecomma);
		$this->printDate($e, $usecomma);
		$this->printDot($usecomma);
		$this->printNotes($e, $usecomma);
		$this->printAbstract($e, $usecomma);
		$this->printUrl($e, $usecomma);
	}
}

class UnpublishedEntry extends Entry {
	public function printEntry(EntryCreatorList $authors_highlight = null) {
		$e =& $this->fields;
		if (!$this->authors->isEmpty()) {
			self::printHighlightedCreatorList($this->authors, $authors_highlight);
		}
		print $this->title_prefix.$e['title'].$this->title_postfix.'.';
		$usecomma = false;
		$this->printDate($e, $usecomma);
		$this->printNotes($e, $usecomma);
		$this->printAbstract($e, $usecomma);
		$this->printUrl($e, $usecomma);
	}
}

/**
* Base class for bibliography parsers.
*/
class BibliographyParser {
}