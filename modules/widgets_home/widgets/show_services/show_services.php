<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Widget_widgets_home_show_services extends Widget
{
	function __construct()
	{
		parent::__construct();
		$this->load->models(array(
				'services/mservices_home'
				));
		$this->model = $this->mservices_home;
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