<?php

class Widget_products_right extends Widget {
	function __construct() {
		parent::__construct ();
	}
	function render($_options = array()) {
		$_options ['ROW'] = "";
		return myview ( 'widget', array (
				'CONTENT' => array (
						'products/widgets/right',
						$_options 
				) 
		), true );
	}
}