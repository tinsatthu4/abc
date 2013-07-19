<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Widget_customer_domain_message extends Widget {
	function __construct()
	{
		parent::__construct();
		$this->load->languages ( array (
				"customer/customer_attr",
		), $this->appmanager->LANGUAGE);
		$this->load->models(array(
				'customer/mcustomer_admin',
				));	
		$this->model = $this->mcustomer_admin;
	}
	function render($_options = array())
	{
		$_options['services'] = $this->model->selectAllServices();
		return myview('widget',array(
		'CONTENT' =>array(
				'customer/widgets/'.(@$_options['layout']?$_options['layout']:'domain_message'),
				$_options 
		) 
		), true );
	}
}
?>