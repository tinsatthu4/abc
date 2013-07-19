<?php

class Widget_menu_admintop extends Widget {
	function __construct() {
		parent::__construct ();
		$this->load->languages(array(
				'customer/customer'
				),$this->appmanager->LANGUAGE);
	}
	function render($_options = array()) {
		$_options ['ROW'] = "";
		return myview ( 'widget', array (
				'CONTENT' => array (
						'menu/widgets/admintop',
						$_options 
				) 
		), true );
	}
}