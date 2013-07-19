<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Widget_widgets_home_menu_top extends Widget
{
	function __construct()
	{
		parent::__construct();
		$this->load->languages(array('core/home','widgets_home/menu_top'),$this->appmanager->LANGUAGE); 
	}
	function render($_options = array())
	{
		return myview('widget',array('CONTENT'=>array('widgets/menu_top',$_options)),true);
	}
}
?>