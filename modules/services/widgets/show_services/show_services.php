<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Widget_services_show_services extends Widget
{
	function __construct()
	{
		parent::__construct();
		$this->load->models(array(
				'services/mservices_administrator'
				));
		$this->model = $this->mservices_administrator;
	}
	function render($_options=array()){
		$_options['ROWS'] = $this->model->select();
		return myview('widget',array(
		'CONTENT' =>array(
				'services/widgets/show_services',
				$_options 
		) 
		), true );
	}
}
?>