/**
 * Laconi.ca directory
 * Copyright (C) 2009 Federico Marani
 *
 * Released under Apache 2.0 license
 *
 * Javascript support functions
 */


$(document).ready(function() {

	$(".langcell").hide();
	$(".langspecificdata #langselect").change(function() {
			$(".langcell").hide()
			$(".langspecificdata #langselect option:selected").each(function() {
				var $lang_selected = $(this).attr('value');
				var $selector = ".langcell#" + $lang_selected;
				var $langcell = $($selector);
				$langcell.show();
			})
	})
	
});
