<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class modun_admin extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();
		in_array('modun',$this->appmanager->MODULES) or show_404();
	}
}

?>