<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Widget_widgetsSite_menu_admin extends Widget
{
	function __construct()
	{
		parent::__construct();
		$this->load->languages(array(),$this->appmanager->LANGUAGE);
	}
	function render($_options = array()){
		if($this->appmanager->IS_ADMIN_LAYOUT == true){
			return parent::render($_options);
// 			return myview('widget',array('CONTENT'=>array('widgets_site/menu_admin',$_options)),true);
		}
		else return '';
	}
}
?>