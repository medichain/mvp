/*!
* @file
* @brief    BibTeX formatted bibliography plug-in for Joomla, JavaScript functions
* @author   Levente Hunyadi
* @version  1.1.5
* @remarks  Copyright (C) 2009-2011 Levente Hunyadi
* @remarks  Licensed under GNU/GPLv3, see http://www.gnu.org/licenses/gpl-3.0.html
* @see      http://hunyadi.info.hu/projects/bibtex
*/

window.addEvent('domready', function () {
	function addBlockToggle(linkClass, blockClass, displayStyle) {
		$$(linkClass).each(function (obj) {
			// find element that contains linked block (e.g. BibTeX code or abstract text)
			var block;
			for (var parent = obj.getParent(); parent != null && block == null; parent = parent.getParent()) {
				block = parent.getElement(blockClass);
			};
			if (block != null) {
				// register click event to show/hide linked block (e.g. BibTeX code or abstract text)
				obj.addEvent('click', function () {
					// toggle linked block display
					block.setStyle('display', block.getStyle('display') != displayStyle ? displayStyle : 'none');
					
					// suppress event propagation
					return false;
				});
			}
		});

		$$(blockClass).setStyle('display', 'none');
	}
	
	addBlockToggle('a.bibtex-link-code', 'pre.bibtex-code', 'block');
	addBlockToggle('a.bibtex-link-abstract', 'span.bibtex-abstract', 'inline');
});