<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Services_administrator extends Administrator_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->languages(array(
				'services/services_attr',
				'services/services_administator',
				'core/common',
				'common'
				),$this->appmanager->LANGUAGE);
		$this->load->models(array(
				'services/mservices_administrator',
				'services/mservices_order_administrator',
				'customer/mcustomer_administrator'
				));
		$this->model = $this->mservices_administrator;
		$this->modelO = $this->mservices_order_administrator;
		$this->modelC  = $this->mcustomer_administrator;
	}
	function index()
	{
		if(mypost())
		switch(mypost('action')){
			case 'add':
			myredirect('administrator/services/add');exit();
			break;
			case 'edit':
			myredirect('administrator/services/edit/'.array_shift(mypost('id')));
			break;
			case 'inactive':
			$this->db->where_in('id',@mypost('id'));
			$this->model->update(array('sc_status'=>0));	
			break;
			case 'active':
			$this->db->where_in('id',@mypost('id'));
			$this->model->update(array('sc_status'=>1));
			break;
			case 'delete':
			$this->db->where_in('id',@mypost('id'));
			$this->model->delete();
			break;
		}
		$this->MYROWS = $this->model->select();
		myview('index',array('CONTENT'=>array('services/index',$this)));
	}
	function add()
	{
		$rule = array(
		array('key','lang:key','required|trim|min_length[3]|max_length[10]'),
		array('title','lang:title','required|trim'),				
		);
		switch(mypost('action'))
		{
			case 'save':
			case 'save_back':
			case 'save_new':				
				$this->ER = myvalid(mypost(),$rule);
				if(@$this->ER) break;
				if($this->model->insert(array(
				'title'=>mypost('title'),
				'key'=>mypost('key'),
				'slug'=>(mypost('slug')=='')?stringseo(mypost('title')):stringseo(mypost('slug')),
				'sc_ctime'=>time(),
				'content'=>mypost('content'),
				'options'=>mypost('options'),
				'sc_status'=>@intval(@mypost('sc_status')),		
				))){
					$id = $this->db->insert_id();
					$this->SU = 'Data is saved';
					mypost('action')=='save' && myredirect('administrator/services/edit/'.@$id);
					mypost('action')=='save_back' && myredirect('administrator/services/');
					mypost('action')=='save_new' && $_POST = null;
				}else {				
					$this->ER = 'Error : Data not save';
					break;
				} 				
			break;
		}
		myview('index',array('CONTENT'=>array('services/edit',$this)));
	}
	function edit($id = 0)
	{
		$this->ROW = array_shift($this->model->select(array('id'=>$id)));
		$this->ROW or show_404();
		$rule = array(
				array('key','lang:key','required|trim|min_length[3]|max_length[10]'),
				array('title','lang:title','required|trim'),
		);
		switch(mypost('action'))
		{
			case 'save':
			case 'save_back':
			case 'save_new':
				$this->ER = myvalid(mypost(),$rule);
				if(@$this->ER) break;
				$data = array(
						'title'=>mypost('title'),
						'key'=>mypost('key'),
						'slug'=>(mypost('slug')=='')?stringseo(mypost('title')):stringseo(mypost('slug')),
						'sc_ctime'=>time(),
						'content'=>mypost('content'),
						'options'=>mypost('options'),
						'sc_status'=>@intval(@mypost('sc_status')),
				);
				if($this->model->update($data,array('id'=>$id))){
					$this->ROW = $data;
					$this->SU = 'Data is saved';
					mypost('action')=='save' && myredirect('administrator/services/edit/'.@$id);
					mypost('action')=='save_back' && myredirect('administrator/services/');
					mypost('action')=='save_new' && $_POST = null;
				}else {
					$this->ER = 'Error : Data not save';
					break;
				}					
			break;
		}
		myview('index',array('CONTENT'=>array('services/edit',$this)));
	}
	function services($id_customer = 0)
	{
		$this->CUSTOMER = array_shift($this->modelC->select(array('id'=>$id_customer)));
		$this->CUSTOMER or show_404();
		@$ids = @mypost('id');
		switch(mypost('action'))
		{
			case 'add':	myredirect('administrator/services/addServices/'.$id_customer); exit();break;
			case 'edit': 
			myredirect('administrator/services/editServices/'.$ids[0]);
			break;
			case 'inactive':
			if(mypost('key') == 'domain')
				$this->modelC->update(array('activedomain'=>0),array('id'=>$id_customer));
			$this->db->where_in('id',$ids);	
			$this->modelO->update(array('sc_status'=>0));
			break;
			case 'active':
				echo $id_customer;
			if(mypost('key') == 'domain')
				$this->modelC->update(array('activedomain'=>1),array('id'=>$id_customer));
			$this->db->where_in('id',$ids);
			$this->modelO->update(array('sc_status'=>1));
			
			break;
			case 'delete':
			$this->db->where_in('id',$ids);	
			$this->modelO->delete();
			break;
		}
		$this->ROWS = $this->modelO->select(array('customer_id'=>$id_customer));
		myview('index',array('CONTENT'=>array('services/services',$this)));
	}
	function addServices($id_customer = 0){
		$this->CUSTOMER = @array_shift($this->modelC->select(array('id'=>intval($id_customer))));
		$this->CUSTOMER or show_404();
		$this->ACTION = 'add';
		$rule = array(array('sc_dtime','lang:sc_dtime','required|trim'));
		switch(mypost('action'))
		{
			case 'save':
			case 'save_new':
			case 'save_back':
			$this->ER = myvalid(mypost(),$rule);
			if(@$this->ER) break;
			if($this->modelO->insert(array(
				'customer_id'=>$id_customer,
				'id_services'=>mypost('id_services'),
				'sc_ctime'=>mypost('sc_ctime')!=''?strtotime(mypost('sc_ctime')):time(),
				'sc_dtime'=>strtotime(mypost('sc_dtime')),
				'sc_utime'=>time(),	
				'sc_status'=>mypost('sc_status'),	
			))){
				$id = $this->modelO->insert_id();
				@$this->SU[] = 'Data is saved';
				mypost('action')=='save' && myredirect('administrator/services/editServices/'.$id);
				mypost('action')=='save_back' && myredirect('administrator/services/services/'.$id_customer);
			}else
			{
				@$this->ER[] = 'Data not saved';
			}
			break;
		}
		$this->SERVICES = arraymake($this->model->select(),'id','title');
		myview('index',array('CONTENT'=>array('services/addservices',$this)));
	}
	function editServices($id=0){
		$this->ROW = @array_shift($this->modelO->select(array('services_order.id'=>intval($id))));
		$this->ROW or show_404();
		$this->ACTION = 'edit';
		$rule = array(array('sc_dtime','lang:sc_dtime','required|trim'));
		switch(mypost('action'))
		{
			case 'save':
			case 'save_new':
			case 'save_back':
				$this->ER = myvalid(mypost(),$rule);
				if(@$this->ER) break;
				$data = array(
						'id_services'=>mypost('id_services'),
						'sc_ctime'=>mypost('sc_ctime')!=''?strtotime(mypost('sc_ctime')):time(),
						'sc_dtime'=>strtotime(mypost('sc_dtime')),
						'sc_utime'=>time(),
						'sc_status'=>mypost('sc_status')?1:0,
				);
				if($this->modelO->update($data,array('id'=>$id))){
					$id = $this->modelO->insert_id();
					mypost('action')=='save_new' && myredirect('administrator/services/addServices/'.$this->ROW['customer_id']);
					mypost('action')=='save_back' && myredirect('administrator/services/services/'.$this->ROW['customer_id']);
					@$this->SU[] = 'Data is saved';
					$this->ROW = array_replace_recursive($this->ROW,$data);
				}else
				{
					@$this->ER[] = 'Data not saved'; break;
				}
				break;
		}
		$this->SERVICES = arraymake($this->model->select(),'id','title');
		myview('index',array('CONTENT'=>array('services/addServices',$this)));
	}
	function managerOrder($page = 1,$search = 0)
	{
		@$ids = mypost('id');
		switch (mypost('action'))
		{
			case 'edit':myredirect('administrator/services/editServices/'.$ids[0]);
			break;
			case 'inactive':
			$this->db->where_in('id',$ids);
			$this->modelO->update(array('sc_status'=>0));
			break;
			case 'active':
			$this->db->where_in('id',$ids);
			$this->modelO->update(array('sc_status'=>1));
			break;
			case 'delete':
			$this->db->where_in('id',$ids);
			$this->modelO->delete();
			break;	
		}
		$pp = 20;
		$tr 		= $this->modelO->countrows();
		$tp 		= ceil($tr/$pp);
		$page 	= (($page<1)?1:($page>$tp?(($tp<1)?1:$tp):$page));
		$this->PAGING = htmlpagging($page,$tp,mysiteurl('administrator/services/managerOrder/[x]'));		
		$this->ROWS = $this->modelO->select('',array('sc_dtime'=>'asc'),$pp*($page-1),$pp);
		myview('index',array('CONTENT'=>array('services/managerOrder',$this)));
	}
	
}

?>