<?php 
class Widget_products_widgets_show_new extends Widget {
	function __construct() {
		parent::__construct();
		$this->load->models(array(
				'products/mproducts_site'
				));
		$this->model = $this->mproducts_site;
	}
	function render($_options = array()){
		$this->cachefile->setDirectory("cache/".$this->appmanager->CUSTOMER['email']."/widgets");
		$this->cachefile->setFilename($_options['primary_key']);
		if($this->cachefile->checkCache()) $_options['ROWS'] = $this->cachefile->get();
		else {		
			$_options['ROWS'] = array();
			$_options['ROWS'] = $this->model->select(array('sc_status'=>1),array('id'=>'desc'),0,intval(isset($_options['limit'])?$_options['limit']:10));
		}
		if(!$this->cachefile->checkCache())
		$this->cachefile->create($_options['ROWS']);
		
		return myview('widget',array(
					'CONTENT' =>array(
							'products/widgets/'.$_options['layout'],
						$_options 
					) 
			), true );
	}	
}

?>