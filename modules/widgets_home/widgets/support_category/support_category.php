<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Widget_widgets_home_support_category extends Widget
{
	function __construct(){
		parent::__construct();
	}
	function render($_options = array())
	{
		return myview('widget',array('CONTENT'=>array(
				'widgets/support_category',$_options)),true);
	}
}
?>