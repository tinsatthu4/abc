<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Widget_widgets_home_layout_category extends Widget
{
	function __construct(){
		parent::__construct();
		$this->load->models(array(
				'layout/mlayout_category_home'
				));
		$this->model = $this->mlayout_category_home;
	}
	function render($_options = array())
	{
		$_options['ROWS'] = $this->model->listchild(0,array('sc_status'=>1));
		return myview('widget',array('CONTENT'=>array(
				'widgets/layout_category',$_options)),true);
	}
}
?>