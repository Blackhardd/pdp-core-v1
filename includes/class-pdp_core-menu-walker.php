<?php

class PDP_Core_Menu_Walker extends Walker_Nav_Menu {
	function start_lvl( &$output, $depth = 0, $args = NULL ){
		$indent = str_repeat( "\t", $depth );
		$output .= "\n$indent<div class='sub-menu-wrap'><ul class='sub-menu'>\n";
	}

	function end_lvl( &$output, $depth = 0, $args = NULL ){
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul></div>\n";
	}
}