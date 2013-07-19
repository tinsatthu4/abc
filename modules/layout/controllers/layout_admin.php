<?php 
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class layout_admin extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();
		show_404(); exit();
		in_array('layout',$this->appmanager->MODULES) or show_404();
		$this->load->models(array('layout/mlayout_admin','customer/mcustomer_admin'));
		$this->load->languages(array('core/common','layout/layout_admin'),$this->appmanager->LANGUAGE);
		$this->model = $this->mlayout_admin;
		$this->CUSTOMER = $this->mcustomer_admin;
	}
	function index()
	{		
		switch(mypost('action'))
		{
			case 'active':
			$id = intval(mypost('primary_key'));
			if($this->model->countrows(array('id'=>$id)))
			{
				$data['id_layout'] = $id;
				if($this->CUSTOMER->update($data,array("id"=>$_SESSION['customer']['id']))){
					$_SESSION['customer']['id_layout'] = $id;
					myredirect('admin/pages/updatePage');
				}
			}
			break;
		}
		$this->LAYOUT_ID = @$_SESSION['customer']['id_layout'];
		$this->ROWS = $this->model->select();
		myview('index',array('CONTENT'=>array('layout/index',$this),
		'TITLE'=>lang('layout_modules')
		));
	}
}

?>