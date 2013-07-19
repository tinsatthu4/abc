<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Pages_customer_admin extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->models(array(
				'pages_customer/mpages_customer_admin'
				));
		$this->load->helper(array('core/ckeditor'));
		$this->load->languages(array(
				'core/common',
				'pages_customer/pages_customer_admin',
				),$this->appmanager->LANGUAGE);
		$this->model = $this->mpages_customer_admin;
	}
	function index($page=1)
	{
		$this->cachefile->appendDir('pages_customer');
		if(mypost()){
			$ids = is_array(mypost('id'))?mypost('id'):array();
			switch(mypost('action'))
			{
				case 'doadd':myredirect('admin/pages_customer/add');break;	
				case 'doedit':myredirect('admin/pages_customer/edit/'.array_shift($ids));break;
				case 'inactive':
					$this->db->where_in('id',$ids);
					$this->model->update(array('sc_status'=>0)); 
					foreach($ids as $id) $this->cachefile->setFile($id) && $this->cachefile->delete();
					break;
				case 'active':
					$this->db->where_in('id',$ids);
					$this->model->update(array('sc_status'=>1));
					foreach($ids as $id) $this->cachefile->setFile($id) && $this->cachefile->delete();
					break;
				case 'delete':
				$this->db->where_in('id',$ids);
				$this->model->delete('');	
				foreach($ids as $id) $this->cachefile->setFile($id) && $this->cachefile->delete();
				break;
				case 'order':
				$orders = mypost('order',array());
				if(@$orders)
				foreach($orders as $key=>$val)
					$this->model->update(array('sc_order'=>$val),array('id'=>intval($key)));
				break;
			}
		//clear menu_top
		$this->cachefile->config("menu_top",$this->cachefile->getRoot()."/common");
		$this->cachefile->delete();
		}
		$tr = $this->model->countrows();
		$pp = 25;
		$tp = ceil($tr/$pp);
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));
		$this->ROWS = $this->model->select('',array('sc_order'=>'asc','id'=>'desc'),$pp*($page-1),$pp);
		$this->PAGGING = htmlpagging($page,$tp,mysiteurl('admin/pages_customer/index/[x]'));
		myview('index',array('CONTENT'=>array('pages_customer/index',$this),
		'TITLE'=>lang('pages_customer_modules'),
		));
	}
	function add()
	{
		$ruleslist = array(
			array('title','lang:title','required|trim|strip_tags')
		);
		if(mypost())
		{
			$this->ER = myvalid(mypost(), $ruleslist);
			if(!$this->ER)
			switch(mypost('action'))
			{
				case 'save':
				case 'save_new':
				case 'save_back':
				$data = $this->model->makeData(mypost());
				$data['sc_ctime'] = time();
				$data['slug'] = stringseo(mypost('slug')==''?mypost('title'):mypost('slug'));
				$data['pid'] = 0;
				$data['options']['title'] = empty($data['options']['title'])?$data['title']:$data['options']['title'];
				$data['options']['description'] = empty($data['options']['description'])?(empty($data['content'])?$data['title']:stringsummary($data['content'],170)):$data['options']['title'];
				if($this->model->insert($data))
				{
					$id = $this->model->insert_id();
					//clear menu_top
					$this->cachefile->config("menu_top",$this->cachefile->getRoot()."/common");
					$this->cachefile->delete();						
					$this->SU = lang('su_db');
					mypost('action')=='save' && myredirect('admin/pages_customer/edit/'.$id);
					mypost('action')=='save_back' && myredirect('admin/pages_customer');
				}
				else 
					$this->ER = lang('er_db');
				break;
			}
		}
		$this->ACTION = 'add';
		myview('index',array('CONTENT'=>array('pages_customer/add',$this),
		'TITLE'=>lang('pages_customer_modules'),
		));
	}
	function edit($id=0)
	{	
		(!$id && show_404())
		or
		($this->ROW=array_shift($this->model->select(array('id'=>$id))) or show_404());
		$ruleslist = array(
				array('title','lang:title','required|trim|strip_tags')
		);
		if(mypost())
			{
				$this->ER = myvalid(mypost(), $ruleslist);
				if(!$this->ER)
				switch(mypost('action'))
				{
					case 'save':
					case 'save_new':
					case 'save_back':
					$data = $this->model->makeData(mypost());
					$data['sc_ctime'] = time();
					$data['slug'] = stringseo((mypost('slug')=='')?mypost('title'):mypost('slug'));
					$data['pid'] = 0;
					$data['options']['title'] = empty($data['options']['title'])?$data['title']:$data['options']['title'];
					$data['options']['description'] = empty($data['options']['description'])?(empty($data['content'])?$data['title']:stringsummary($data['content'],170)):$data['options']['title'];
					if($this->model->update($data,array('id'=>intval($id))))
					{
						mypost('action')=='save_new' && myredirect('admin/pages_customer/add');
						mypost('action')=='save_back' && myredirect('admin/pages_customer');
						$this->SU = lang('su_db');
						//clear page_customer
						$this->cachefile->config($id,$this->cachefile->getRoot().'/pages_customer');
						$this->cachefile->delete();
						//clear menu_top
						$this->cachefile->config("menu_top",$this->cachefile->getRoot()."/common");
						$this->cachefile->delete();
						$this->ROW = array_replace_recursive($this->ROW,$data);
					}
					else 
						$this->ER[] = lang('er_db');
					break;
				}
			}
// 		$this->PID = arraymer(array(0=>lang('pid_0')),arraymake($this->model->listchild(0,array('id <>'=>$id),array('sc_order'=>'asc','id'=>'desc')),'id','txttitle'));
		myview('index',array('CONTENT'=>array('pages_customer/add',$this),
		'TITLE'=>lang('pages_customer_modules'),
		));
		
	}
}

?>