<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Widget_widgets_menu_top extends Widget
{
	function __construct()
	{
		parent::__construct();
		$this->load->models(array(
				'pages/mpage_site',
				'pages_customer/mpages_customer_site',
				));
		$this->model = $this->mpage_site;
		$this->modelP = $this->mpages_customer_site;
	}
	function render($_options = array())
	{
		$this->cachefile->config('menu_top',$this->cachefile->getRoot().'/common');
		if(!$this->cachefile->checkCache()){
		$this->db->where('method <>','pages_customer_site::index');
		$page_default = arraymakes($this->model->getPage($_options['id_layout']),"method", array("method","title","link"));
		$pages = arraymakes($this->modelP->select(array('sc_status'=>1),array('sc_order'=>'asc','id'=>'desc')),"id",array("method","title","link"));
		$total = arraymerkey($page_default,$pages);
		$_options['ROWS'] = array();
		$orders = getMyconfig('config_nav_top');
		if(!empty($orders))
		$orders = json_decode($orders['value'],true);
		foreach($orders as $key)
		if(isset($total[$key]))	
		{
			$_options['ROWS'][$key] = $total[$key];
			unset($total[$key]);
		}
		$_options['ROWS'] = arraymerkey($_options['ROWS'],$total);
		$this->cachefile->create($_options['ROWS']);
		}else
			$_options['ROWS'] = $this->cachefile->get();
		return myview('widget',array(
				'CONTENT' =>array(
						'widgets/menu_top/view' ,
						$_options
				)
		), true );
	}
}

?>