<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Widget_widgets_home_footer extends Widget
{
	function __construct(){
		parent::__construct();
		$this->load->languages(array('core/home'),$this->appmanager->LANGUAGE);
	}
	function render($_options = array())
	{
		return myview('widget',array('CONTENT'=>array(
				'widgets/footer',$_options)),true);
	}
}
?>