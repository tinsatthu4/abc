<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Widget_customer_my_services extends Widget {
	function __construct()
	{
		parent::__construct();
	}
	function render($_options = array())
	{
		return myview('widget',array(
		'CONTENT' =>array(
				'customer/widgets/my_services',
				$_options 
		) 
		), true );
	}
}
?>