<?php

class Widget_menu_left extends Widget {
	function __construct() {
		parent::__construct ();
	}
	function render($_options = array()) {
		return myview ( 'widget', array (
				'CONTENT' => array (
						'menu/widgets/left',
						$_options 
				) 
		), true );
	}
}