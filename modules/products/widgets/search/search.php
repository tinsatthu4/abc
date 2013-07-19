<?php

class Widget_products_search extends Widget {
	function __construct() {
		parent::__construct ();
	}
	function render($_options = array()) {
		return myview('widget',array(
				'CONTENT' =>array(
						'products/widgets/search',
						$_options 
				) 
		), true );
	}
}