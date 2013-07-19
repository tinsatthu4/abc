<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class menu_admin extends Admin_Controller {
	function __construct() {
		parent::__construct ();
		in_array('menu',$this->appmanager->MODULES) or show_404();
	}
	function index()
	{
		
	}
}