<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Services_home extends Home_controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->languages(array(
				'services/services_attr'
				),$this->appmanager->LANGUAGE);
		$this->load->models(array(
				'services/mservices_home'
				));
		$this->model = $this->mservices_home;
	}
	function index()
	{
		$this->ROW = @array_shift($this->model->select(array('sc_status'=>1,'key'=>'domain')));
		myview('index',array('CONTENT'=>array('services/index',$this),
		'METHOD'=>'services_home::index',
		));
	}
	function detail($id = 0)
	{
		$this->ROW = @array_shift($this->model->select(array('sc_status'=>1,'id'=>intval($id))));
		$this->ROW or show_404();
		myview('index',array('CONTENT'=>array('services/index',$this),
		'METHOD'=>'services_home::index'
		));
	}	
}

?>