<?php 
if(!defined('BASEPATH'))
	exit('No direct script access allowed');
class files_admin extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();
		in_array('files',$this->appmanager->MODULES) or show_404();
		$this->load->languages(array(
				'files/files_admin'
				),$this->appmanager->LANGUAGE);
	}
	function index($page = 1)
	{
		myview('index',array('CONTENT'=>array('files/index',$this),
		'TITLE'=>lang('files_modules')
		));
	}
}
?>