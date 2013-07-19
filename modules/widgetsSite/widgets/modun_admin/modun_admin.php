<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Widget_widgetsSite_modun_admin extends Widget
{
	function __construct()
	{
		parent::__construct();
		$this->load->languages(array(),$this->appmanager->LANGUAGE);
		$this->load->models(array(
			'modun/mmodun_admin',
			'pages/mpage_admin',
			'widgets/mwidgets_admin',
			'layout/mlayout_home',	
		));
		$this->model = $this->mmodun_admin;
		$this->modelP = $this->mpage_admin;
		$this->modelW = $this->mwidgets_admin;
		$this->IDS = $this->modelW->selectWidget(@$_SESSION['customer']['id_layout']);
	}
	function render($_options = array()){
		if($this->appmanager->IS_ADMIN_LAYOUT == true){
			$customer = $this->appmanager->CUSTOMER;
			$_options['ROWS'] = $this->modelW->selectTotal($customer['id_layout'],array("sc_status"=>1),array("sc_order"=>"asc"));		
			return myview('widget',array('CONTENT'=>array('widgetsSite/modun_admin',$_options)),true);
		}
		else return '';
	}
}
?>