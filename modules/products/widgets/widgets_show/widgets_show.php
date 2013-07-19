<?php 
class Widget_products_widgets_show extends Widget {
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
		$orders = array("sc_order"=>"asc","id"=>"desc");
		if(@in_array($_options['sort'],array('sc_utime','price','price_sale','title')))
			$orders = array($_options['sort']=>"desc");
		if(@in_array($_options['sort'],array('asc_price','asc_price_sale','asc_title')))
			$orders = array(str_replace('asc_','',$_options['sort'])=>"asc");
		if(@is_array($_options['key']) && !empty($_options['key'])){
			$this->db->where_in("id",$_options['key']);
			$_options['ROWS'] = $this->model->select(array('sc_status <>'=>0),$orders);
		}
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