<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Widget_orders_admin_message extends Widget {
	function __construct()
	{
		parent::__construct();
		$this->load->models(array(
				'orders/morders_admin',
				));
		$this->load->languages(array('orders/orders_admin'),$this->appmanager->LANGUAGE);
		$this->model = $this->morders_admin;
	}
	function render($_options = array())
	{
		$_options['NUMBER'] = $this->model->countrows(array('sc_status'=>0));
		return myview('widget',array(
		'CONTENT' =>array(
				'orders/widgets/'.(@$_options['layout']?$_options['layout']:'admin_message'),
				$_options 
		) 
		), true );
	}
}
?>