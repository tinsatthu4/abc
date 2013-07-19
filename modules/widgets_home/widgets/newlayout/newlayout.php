<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Widget_widgets_home_newlayout extends Widget
{
	function __construct(){
		parent::__construct();
		$this->load->models(array(
				'layout/mlayout_home',
				));
		$this->model = $this->mlayout_home;
	}
	function render($_options = array())
	{
		$_options['ROWS'] = $this->model->select(array('sc_status'=>1),array('id'=>'desc'),0,10);
		return myview('widget',array('CONTENT'=>array(
				'widgets/newlayout',$_options)),true);
	}
}
?>