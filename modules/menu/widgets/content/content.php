<?php

class Widget_products_content extends Widget {
	function __construct() {
		parent::__construct ();
	}
	function render($_options = array()) {
		$_options ['ROW'] = "";
		return myview ( 'widget', array (
				'CONTENT' => array (
						'products/widgets/content',
						$_options 
				) 
		), true );
	}
}