<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Widget_pages_customer_widget extends Widget
{
	function __construct(){
		parent::__construct();
		$this->load->models(array(
				'pages_customer/mpages_customer_site',
				));
		$this->model = $this->mpages_customer_site;
	}
	function render($_options = array())
	{
		$this->db->where('customer_id',$_options['']);
	}
}

?>