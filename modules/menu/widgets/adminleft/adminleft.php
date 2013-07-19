<?php

class Widget_menu_adminleft extends Widget {
	function __construct() {
		parent::__construct ();
		
		$this->load->models ( array (
				'menu/mmenu' 
		) );
	}
	
	function render($_options = array()) {
		return myview ( 'widget', array (
				'CONTENT' => array (
						'menu/widgets/adminleft',
						$_options 
				) 
		), true );
	}

}