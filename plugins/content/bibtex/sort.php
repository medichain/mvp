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

// this file has PHP 5.3 syntax, anonymous functions require PHP 5.3 or later

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
* Stable sort operating on an array of objects using the Schwartzian Transform.
* @param {array} $array An array whose items are to be sorted.
* @param {callback} $callback A callback such as an anonymous function that takes an array value and returns a key according to which items are sorted.
*/
function array_stable_sort(array &$array, $callback) {
	array_walk($array, function (&$v, $k) use ($callback) {
		$v = array( call_user_func($callback, $v), $k, $v );
	});
	asort($array);
	array_walk($array, function (&$v, $k) {
		$v = $v[2];
	});
	return true;
}

/**
* Stable reverse sort operating on an array of objects using the Schwartzian Transform.
* @param {array} $array An array whose items are to be sorted.
* @param {callback} $callback A callback such as an anonymous function that takes an array value and returns a key according to which items are sorted.
*/
function array_stable_rsort(array &$array, $callback) {
	array_walk($array, function (&$v, $k) use ($callback) {
		$v = array( call_user_func($callback, $v), $k, $v );
	});
	arsort($array);
	array_walk($array, function (&$v, $k) {
		$v = $v[2];
	});
	return true;
}

class EntryOperations {
	public static function sortDatesAscending(array &$entries) {
		$def = new DateTime('9999-12-12');  // put entries without date at end
		array_stable_sort($entries, function ($entry) use ($def) {
			return $entry->getDate($def);
		});
	}

	public static function sortDatesDescending(array &$entries) {
		$def = new DateTime('1000-01-01');  // put entries without date at end
		array_stable_rsort($entries, function ($entry) use ($def) {
			return $entry->getDate($def);
		});
	}
}