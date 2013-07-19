<?php 
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class Contacts_admin extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();
		in_array('contacts',$this->appmanager->MODULES) or show_404();
		$this->load->models(array('contacts/mcontacts_admin'));
		$this->load->languages(array(
				'core/common',
				'contacts/contacts_admin'
				),$this->appmanager->LANGUAGE);
		$this->model = $this->mcontacts_admin;
	}
	function index($page = 0)
	{
		if(mypost())
		{
			$ids = mypost('id');
			switch (mypost('action'))
			{
				case 'delete':
				$this->db->where_in("id",$ids);
				$this->model->delete('');
				break;
				case 'active':
				$this->db->where_in("id",$ids);
				$this->model->update(array("sc_status"=>1));
				break;
				case 'inactive':
				$this->db->where_in("id",$ids);
				$this->model->update(array("sc_status"=>0));
				break;
			}
		}
		$pp = 25;
		$tr = $this->model->countrows('',array('sc_status'=>'asc'));
		$tp = ceil($tr/$pp);
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));
		$this->ROWS = $this->model->select('',array('sc_status'=>'asc'),$pp*($page-1),$pp);
		$this->PAGGING = htmlpagging($page, $tp, mysiteurl('admin/contacts/index/[x]'));
		myview("index",array("CONTENT"=>array("contacts/index",$this)));
	}
}

?>