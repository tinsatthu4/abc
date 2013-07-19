<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class support_administrator extends Administrator_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->helpers(array(
				'core/ckeditor'
		));
		$this->load->languages(array(
				'support/support_attr',
				'support/support_administrator',
				'core/common',
		),$this->appmanager->LANGUAGE);
		$this->load->models(array('support/msupport_administrator'));
		$this->model = $this->msupport_administrator;
	}
	function index($page=1)
	{
		if(mypost())
		{
			$ids = mypost('id');
			switch(mypost('action')){
				case 'add':myredirect('administrator/support/add');break;
				case 'edit':myredirect('administrator/support/edit/'.@$ids[0]);break;
				case 'active':
				$this->db->where_in('id',$ids);
				$this->model->update(array('sc_status'=>1));	
				break;
				case 'inactive':
				$this->db->where_in('id',$ids);
				$this->model->update(array('sc_status'=>0));
				break;
				case 'delete':
				$this->db->where_in('id',$ids);
				$this->model->delete();
				break;
			}
		}
		$pp = 25;
		$tr = $this->model->countrows();
		$tp = ceil($tr/$pp);
		$page = $page>1?($page>$tp?($tp<1?1:$tp):$page):1;
		$this->MYPAGGING = htmlpagging($page,$tp,mysiteurl('administrator/support/index/[x]'));
		$this->MYROWS = $this->model->select('','',$pp*($page-1),$pp);
		myview('index',array('CONTENT'=>array('support/index',$this)));	
	}
	function add()
	{
		$this->ACTION = 'add';
		if(mypost())
		switch(mypost('action')){
			case 'save':
			case 'save_back':
			case 'save_new':
			$ruleslist = array(array('title','lang:title','required|trim|strip_tags'),
			array('options[images]','images','required|getPath'));		
			$this->MYERROR = myvalid(mypost(),$ruleslist);	
			if(@$this->MYERROR) break;
			$slug = mypost('slug')==''?stringseo(mypost('title')):stringseo(mypost('slug'));
			$data = array(
			'title'=>mypost('title'),
			'slug'=>$slug,
			'content'=>mypost('content'),
			'options'=>mypost('options'),	
			'sc_ctime'=>time(),
			'sc_utime'=>time(),		
			);
			if($this->model->insert($data))
			{
				$id = $this->model->insert_id();
				mypost('action')=='save' && myredirect('administrator/support/edit/'.$id);
				mypost('action')=='save_back' && myredirect('administrator/support');
				
			}	
			break;
		}
		myview('index',array('CONTENT'=>array('support/add',$this)));
	}
	function edit($id=0)
	{
		$this->MYROW = array_shift($this->model->select(array('id'=>$id)));
		$this->MYROW or show_404();
		$this->ACTION = 'edit';
		if(mypost())
			switch(mypost('action')){
				case 'save':
				case 'save_back':
				case 'save_new':
					$ruleslist = array(array('title','lang:title','required|trim|strip_tags'),
					array('options[images]','images','required|getPath'));
					$this->MYERROR = myvalid(mypost(),$ruleslist);
					$slug = (mypost('slug')==''?stringseo(mypost('title')):stringseo(mypost('slug')));
					if(@$this->MYERROR) break;
					$data = array(
							'title'=>mypost('title'),
							'slug'=>$slug,
							'content'=>mypost('content'),
							'options'=>mypost('options'),
							'sc_utime'=>time(),
					);
					if($this->model->update($data,array('id'=>$id)))
					{
						mypost('action')=='save_new' && myredirect('administrator/support/add');
						mypost('action')=='save_back' && myredirect('administrator/support');
						$this->MYROW = array_replace_recursive($this->MYROW,$data);
					}
					break;
		}
		myview('index',array('CONTENT'=>array('support/add',$this)));
	}
}
?>