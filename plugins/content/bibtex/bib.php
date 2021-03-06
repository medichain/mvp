<?php
/**
* @file
* @brief    BibTeX bibliography formatter Joomla plug-in BIB parser
* @author   Levente Hunyadi
* @version  1.1.5
* @remarks  Copyright (C) 2009-2011 Levente Hunyadi
* @remarks  Licensed under GNU/GPLv3, see http://www.gnu.org/licenses/gpl-3.0.html
* @see      http://hunyadi.info.hu/projects/bibtex
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once JPATH_PLUGINS.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'bibtex'.DIRECTORY_SEPARATOR.'bib'.DIRECTORY_SEPARATOR.'parseentries.php';
require_once JPATH_PLUGINS.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'bibtex'.DIRECTORY_SEPARATOR.'bib'.DIRECTORY_SEPARATOR.'parsecreators.php';

/**
* Parses a bibliography in BibTeX format.
*/
class BibTexParser extends BibliographyParser {
	/**
	* Maps BibTeX entry types to Entry object types.
	*/
	private static $mapping = array(
		'ARTICLE' => 'ArticleEntry',
		'BOOK' => 'BookEntry',
		'BOOKLET' => 'BookletEntry',
		'CONFERENCE' => 'ProceedingsPaperEntry',
		'INBOOK' => 'InBookEntry',
		'INCOLLECTION' => 'InCollectionEntry',
		'INPROCEEDINGS' => 'ProceedingsPaperEntry',
		'MANUAL' => 'ManualEntry',
		'MASTERSTHESIS' => 'MastersThesisEntry',
		'MISC' => 'MiscellaneousEntry',
		'PHDTHESIS' => 'PhdThesisEntry',
		'POSTER' => 'PosterPaperEntry',
		'PROCEEDINGS' => 'ProceedingsEntry',
		'TECHREPORT' => 'TechnicalReportEntry',
		'UNPUBLISHED' => 'UnpublishedEntry'
	);

	/**
	* Reads BibTeX entries in a file into an array.
	* Each BibTeX entry is parsed into an associative array with the key being the BibTeX field name and the value being the field data.
	* The citation key and the entry type are mapped to special array keys 'bibtexCitation' and 'bibtexEntryType'.
	* @return An array whose keys are citation keys and values are BibTeX entries.
	*/
	public static function read($filename) {
		$entry_parser = new ParseEntries();
		$entry_parser->openBib($filename);
		$entry_parser->extractEntries();
		$entry_parser->closeBib();
		list($bibtex_preamble, $bibtex_strings, $bibtex_entries, $bibtex_undefined_strings) = $entry_parser->returnArrays();
		$entries = array();
		foreach ($bibtex_entries as $bibtex_entry) {
			$citation = $bibtex_entry['bibtexCitation'];

			// ignore duplicate citation keys
			if (isset($entries[$citation])) {
				// get a handle to the Joomla application object
				$application = JFactory::getApplication();

				// add a message to the message queue
				$application->enqueueMessage(sprintf(JText::_('BIBTEX_DUPLICATE_KEY_IGNORED'), $citation, basename($filename)), 'warning');
			} else {
				$entries[$citation] = $bibtex_entry;
			}
		}
		return $entries;
	}

	/**
	* Parses raw BibTeX entries into Entry class objects.
	*/
	public static function parse($bibtex_entries) {
		$entries = array();
		foreach ($bibtex_entries as $bibtex_entry) {
			// instantiate proper entry class based on entry type
			$bibtex_entry_type = strtoupper($bibtex_entry['bibtexEntryType']);
			if (!isset(BibTexParser::$mapping[$bibtex_entry_type])) {  // unrecognized entry type
				continue;
			}
			$entry_type = BibTexParser::$mapping[$bibtex_entry_type];
			$entry = new $entry_type();
			$entry->citation = $bibtex_entry['bibtexCitation'];

			// unescape special LaTeX characters
			unset($bibtex_entry['bibtexEntryType']);
			unset($bibtex_entry['bibtexCitation']);
			foreach ($bibtex_entry as $key => $value) {
				if (ctype_alpha($key[0]) && ctype_alnum($key)) {  // ensure valid PHP property name
					$entry->$key = latex2plain($value);
				}
			}

			// convert author field into list of authors
			if (isset($bibtex_entry['author'])) {
				$entry->addAuthors(BibTexParser::getAuthors(latex2plain($bibtex_entry['author'])));
			}

			// convert editor field into list of editors
			if (isset($bibtex_entry['editor'])) {
				$entry->addEditors(BibTexParser::getEditors(latex2plain($bibtex_entry['editor'])));
			}

			// add newly created entry to list of entries
			$entries[] = $entry;
		}
		return $entries;
	}

	/**
	* Parses a BibTeX author (or editor) field.
	* @param creator_field A BibTeX author (or editor) field value to parse.
	* @param creator_list The container to populate with parsed authors (or editors).
	*/
	private static function getCreators($creator_field, &$creator_list) {
		$creator_parser = new ParseCreators();
		$parsed_list = $creator_parser->parse($creator_field);
		foreach ($parsed_list as $parsed_creator) {
			list ($given_name, $initials, $family_name) = $parsed_creator;
			$family_name = trim($family_name);
			$given_name = trim($given_name.' '.$initials);
			$creator_list->add(new EntryCreator($family_name, $given_name));
		}
	}

	/**
	* Parses a BibTeX author field.
	* @param author_field A BibTeX author field value to parse.
	* @return An EntryAuthorList object.
	*/
	public static function getAuthors($author_field) {
		$authors = new EntryAuthorList();
		BibTexParser::getCreators($author_field, $authors);
		return $authors;
	}

	/**
	* Parses a BibTeX editor field.
	* @param editor_field A BibTeX editor field value to parse.
	* @return An EntryEditorList object.
	*/
	public static function getEditors($editor_field) {
		$editors = new EntryEditorList();
		BibTexParser::getCreators($editor_field, $editors);
		return $editors;
	}
}
