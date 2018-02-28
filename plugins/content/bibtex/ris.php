<?php
/**
* @file
* @brief    BibTeX bibliography formatter Joomla plug-in RIS parser
* @author   Levente Hunyadi
* @version  1.1.5
* @remarks  Copyright (C) 2009-2011 Levente Hunyadi
* @remarks  Licensed under GNU/GPLv3, see http://www.gnu.org/licenses/gpl-3.0.html
* @see      http://hunyadi.info.hu/projects/bibtex
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
* Parses a bibliography in RIS (Research Information Systems) format.
*/
class RisParser extends BibliographyParser {
	/**
	* Maps RIS entry types into Entry object types.
	*/
	private static $mapping = array(
		'ABST' => false,  // Abstract
		'ADVS' => false,  // Audiovisual material
		'ART' => false,  // Art Work
		'BOOK' => 'BookEntry',  // Whole book
		'CASE' => false,  // Case
		'CHAP' => 'InBookEntry',  // Book chapter
		'COMP' => false,  // Computer program
		'CONF' => 'ProceedingsPaperEntry',  // Conference proceeding
		'CTLG' => false,  // Catalog
		'DATA' => false,  // Data file
		'ELEC' => false,  // Electronic Citation
		'GEN' => false,  // Generic
		'HEAR' => false,  // Hearing
		'ICOMM' => false,  // Internet Communication
		'INPR' => false,  // In Press
		'JFULL' => 'JournalEntry',  // Journal (full)
		'JOUR' => 'ArticleEntry',  // Journal
		'MAP' => false,  // Map
		'MGZN' => false,  // Magazine article
		'MPCT' => false,  // Motion picture
		'MUSIC' => false,  // Music score
		'NEWS' => false,  // Newspaper
		'PAMP' => false,  // Pamphlet
		'PAT' => false,  // Patent
		'PCOMM' => false,  // Personal communication
		'RPRT' => 'TechnicalReportEntry',  // Report
		'SER' => false,  // Serial publication
		'SLIDE' => false,  // Slide
		'SOUND' => false,  // Sound recording
		'STAT' => false,  // Statute
		'THES' => 'PhdThesisEntry',  // Thesis/Dissertation
		'UNPB' => 'UnpublishedEntry',  // Unpublished work
		'VIDEO' => false  // Video recording
	);

	/**
	* Maps RIS tags into Entry class properties.
	*/
	private static $tag_mapping = array(
		'T1' => 'title',      // Primary title
		'TI' => 'booktitle',  // Book title
		'CT' => false,        // Title of unpublished reference
		'Y1' => 'year',       // Primary date
		'N1' => 'notes',      // Notes
		'KW' => false,        // Keywords (each keyword must be on separate line preceded KW -)
		'RP' => false,        // Reprint status (IN FILE, NOT IN FILE, ON REQUEST (MM/DD/YY))
		'JF' => 'journal',    // Periodical full name
		'JO' => 'journal',    // Periodical standard abbreviation
		'JA' => 'journal',    // Periodical in which article was published
		'J1' => false,        // Periodical name - User abbreviation 1
		'J2' => false,        // Periodical name - User abbreviation 2
		'VL' => 'volume',     // Volume number
		'IS' => 'number',     // Issue number
		'T2' => false,        // Title secondary
		'CY' => false,        // City of Publication
		'PB' => false,        // Publisher
		'U1' => false,        // User definable 1
		'U5' => false,        // User definable 5
		'T3' => false,        // Title series
		'N2' => false,        // Abstract
		'SN' => false,        // ISSN/ISBN (e.g. ISSN XXXX-XXXX)
		'AV' => false,        // Availability
		'M1' => false,        // Misc. 1
		'M3' => false,        // Misc. 3
		'AD' => false,        // Address
		'UR' => false,        // Web/URL
		'L1' => false,        // Link to PDF
		'L2' => false,        // Link to Full-text
		'L3' => false,        // Related records
		'L4' => false         // Images
	);
	
	/**
	* Adds a RIS date to an Entry object.
	*/
	private static function addDate(&$entry, $data) {
		list($year,$month,$day,$rest) = explode('/', $data, 4);
		$entry->year = $year;
		$entry->month = $month;
		$entry->day = $day;
	}
	
	/**
	* Adds a RIS author name to an Entry object.
	*/
	private static function addAuthor(&$entry, $data) {
		$comma_pos = strpos($data, ',');
		if ($comma_pos !== false) {
			$family_name = substr($data, 0, $comma_pos);
			$given_name = substr($data, $comma_pos+1);
			$entry->addAuthor(new EntryCreator($family_name, $given_name));
			return;
		}
		$entry->addAuthor(new EntryCreator($data));
	}
	
	/*
	* Reads a RIS file with line unfolding.
	* @return A numeric array of unfolded lines.
	*/
	public static function read($filename) {
		if (($file = fopen($filename, 'rb')) === false) {
			return false;
		}
		$records = array();  // the list of RIS reference in a file
		$record = array();   // a single RIS reference with possibly multiple tags
		$line = false;
		while (!feof($file)) {
			$folded_line = stream_get_line($file, 8000, "\r\n");
			if (ltrim($folded_line) == '') {  // skip empty lines
				continue;
			}
			if ($line !== false && ctype_space($folded_line[0])) {  // unfold folded line
				$line .= $folded_line;
				continue;
			}
			if ($line !== false && strlen($line) > 0) {  // copy unfolded line to lines in file array
				$data[] = $line;
				$line = '';
			}
			$line = $folded_line;
			if (strlen($line) < 2) {  // ill-formatted tag
				continue;
			}
			switch (substr($line, 0, 2)) {
				case 'TY':  // start of reference
					$record = array();
					$record[] = $line;
					break;
				case 'ER':  // end of reference
					$record[] = $line;
					$records[] = $record;
					$record = array();
					break;
				default:
					$record[] = $line;
			}
		}
		fclose($file);
		return $records;
	}

	/**
	* Parses lines of a RIS reference into an Entry object.
	* @return An Entry object.
	*/
	public static function parseSingle($lines) {
		if (count($lines) == 0) {
			return false;
		}
		$line = array_shift($lines);
		$tag = substr($line, 0, 2);   // "TY"
		if ($tag != 'TY') {  // invalid RIS reference
			return false;
		}
		$tagdata = substr($line, 6);
		if (isset(RisParser::$mapping[$tagdata]) && ($entry_type = RisParser::$mapping[$tagdata]) !== false) {
			$entry = new $entry_type();
		} else {
			return false;
		}
		$page_start = false;
		$page_end = false;
		foreach ($lines as $line) {
			$tag = substr($line, 0, 2);   // "A1" from "A1  - Levente Hunyadi"
			$tagdata = substr($line, 6);  // "Levente Hunyadi" from "A1  - Levente Hunyadi"
			switch ($tag) {
				case 'TY':  // Type of reference (must be the first tag)
					return false;
				case 'ID':  // Reference ID (not imported to reference software)
					$entry->citation = $tagdata;
					break;
				case 'A1':  // Primary author
					RisParser::addAuthor($entry, $tagdata);
					break;
				case 'A2':  // Secondary author (each name on separate line)
					RisParser::addAuthor($entry, $tagdata);
					break;
				case 'AU':  // Author (syntax. Last name, First name, Suffix)
					break;
				case 'PY':  // Publication year (YYYY/MM/DD)
					RisParser::addDate($entry, $tagdata);
					break;
				case 'SP':  // Start page number
					$page_start = $tagdata;
					break;
				case 'EP':  // Ending page number
					$page_end = $tagdata;
					break;
				case 'ER':  // End of Reference (must be the last tag)
					if ($page_start !== false && $page_end !== false) {
						$entry->pages = $page_start.'--'.$page_end;
					} elseif ($page_start !== false) {
						$entry->pages = $page_start;
					}
					return $entry;
				default:
					if (isset(RisParser::$tag_mapping[$tag])) {
						$field = RisParser::$tag_mapping[$tag];
						if ($field !== false) {
							$entry->$field = $tagdata;
						}
					}
			}
		}
	}
	
	/**
	* Parses a list of RIS references into a list of Entry objects.
	* @return A numerically-indexed array of Entry objects.
	*/
	public static function parse($references) {
		$entries = array();
		foreach ($references as $index => $reference) {
			$entry = RisParser::parseSingle($reference);
			if ($entry !== false) {
				$entries[$index] = $entry;
			}
		}
		return $entries;
	}
}