<?php

class Widget_menu_administop extends Widget {
	function __construct() {
		parent::__construct ();
	}
	function render($_options = array()) {
		$_options ['ROW'] = "";
		return myview ( 'widget', array (
				'CONTENT' => array (
						'menu/widgets/administop',
						$_options 
				) 
		), true );
	}
}