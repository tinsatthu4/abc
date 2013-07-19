<?php

class Widget_products_widgets_show_category extends Widget {
	function __construct() {
		parent::__construct();
		$this->load->models(array('products/mproducts_cate_site'));
		$this->model = $this->mproducts_cate_site;
	}
	function render($_options = array()){
		
		$this->cachefile->setDirectory("cache/".$this->appmanager->CUSTOMER['email']."/widgets");
		$this->cachefile->setFilename($_options['primary_key']);

		if($this->cachefile->checkCache()) $_options['ROWS'] = $this->cachefile->get();
		else 		
		$_options ['ROWS'] = $this->model->listchild(0,array('sc_status'=>1),array('sc_order'=>'asc','id'=>'desc'));
				
		if(!$this->cachefile->checkCache())
		$this->cachefile->create($_options ['ROWS']);
		
		return myview('widget',array(
				'CONTENT' =>array(
						'products/widgets/' . $_options ['layout'].'_category',
						$_options 
				) 
		), true );
	}
}