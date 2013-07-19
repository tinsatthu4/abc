<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Widget_widgets_site_action_admin extends Widget
{
	function __construct()
	{
		parent::__construct();
		$this->load->languages(array('core/common'),$this->appmanager->LANGUAGE);
		echo $this->APPMANAGER->LANGUAGE;
	}
	function render($_options = array()){
		//agrument MODULES,ACTION,ID
		if($this->appmanager->IS_ADMIN_LAYOUT == true){
		return myview('widget',array('CONTENT'=>array('widgets_site/action_admin',$_options)),true);
		}
		else return '';
	}
}
?>