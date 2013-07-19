<?php

class Widget_user_menu extends Widget
{
	function __construct()
	{
		parent::__construct();
	}
	function render($_options = array())
	{
		return myview('widget',array("CONTENT"=>array("user/widgets/left",$_options)),true);
	}
}