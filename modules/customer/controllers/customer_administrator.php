<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Customer_administrator extends Administrator_Controller
{
	private $model;
	function __construct()
	{
		parent::__construct();
		$this->load->models(array('customer/mcustomer_administrator'));
		$this->load->languages(array(
			'core/common','customer/customer','user/user','common','customer/customer_attr'
		),$this->appmanager->LANGUAGE);
		$this->model = &$this->mcustomer_administrator;
		$this->config->load('form_validate');
		$this->RU = $this->config;
		$this->MYRULES 	= 	array(
			'password'				=>	array('password','lang:customer_password',
				'trim|required|min_length[5]|max_length[24]|matches[re_password]'),	
			'confirpassword'				=>	array('confirpassword','lang:customer_confirpassword',
					'trim|required|min_length[5]|max_length[24]|matches[password]'),
			'email' 					=>	array('email','lang:customer_email',
				'trim|required|valid_email'),
			'subdomain' =>array('subdomain','lang:subdomain','required|min_length[3]'),	
		);
	}
	function index($page = 1)
	{
		$this->action = __function__;
		/* Begin action */
		if(!empty($_POST))
		{
			$ids = mypost('id');
			switch(mypost('action'))
			{
				case 'add'		:	myredirect('administrator/customer/add'); break;
				case 'edit'		:	myredirect('administrator/customer/edit/'.$ids[0]); break;
				case 'delete'	: 	
				//delete thu muc user
				$this->db->where_in('id',$ids);
				foreach($this->model->select() as $val){
					@delete_files('uploadsys/'.md5($val['email']));
					@unlink('uploadsys/'.md5($val['email']));
				}
				$this->db->where_in('id',$ids); $this->model->delete(); break;
				case 'inactive'	:	
				$this->db->where_in('id',$ids); $this->model->update(array('sc_status'=>0)); break;
				case 'active'	: 	
				$this->db->where_in('id',$ids); $this->model->update(array('sc_status'=>1)); break;
				case 'order'	: 	
				$this->model->orderlist(mypost('sc_order')); break;
				case 'reorder'	: 	
				$this->model->ordersort(array('sc_ctime'=>'asc')); break;
				case 'viewservices':
				myredirect('administrator/services/services/'.$ids[0]);	
				exit();
				break;
			}
		}else{
			// kiem tra quyen module
			$this->model->check_group(__METHOD__);
			//  ket thuc kiem tra quyen module
		}
		/* End action */
		/* Begin pagging */
		$pp 		= 20;
		$tr 		= $this->model->countrows();
		$tp 		= ceil($tr/$pp);
		$page 	= (($page<1)?1:($page>$tp?(($tp<1)?1:$tp):$page));
		
		$this->MYPAGGING 	= htmlpagging($page,$tp,mysiteurl('administrator/customer/index/[x]'));
		$this->MYROWS 		= arraypagging ( $this->model->select ( array('id <>'=>0), array (
				'sc_order' => 'asc',"id"=>"desc" 
		), $pp * ($page - 1), $pp ), $pp, $page );
		/* End pagging */
		$this->count_rows	=	$tr;
		
		/* End pagging */
		myview('index',array('CONTENT'=>array('customer/index',$this)));
	}
	function add()
	{
		$this->action = __function__;
		if(!empty($_POST))
		{
			switch(mypost('action'))
			{
				case 'back'				: myredirect('administrator/customer'); break;
				case 'save_new'		:
				case 'save_back'	:
				case 'save'				:
					//Valid input
					$this->MYERROR = myvalid(mypost(),array($this->MYRULES['username'],$this->MYRULES['subdomain']));
					if(@$this->MYERROR) break;
					$this->MYERROR = array();
					//  check password
					if(mypost('password')!=null)
						if(mypost('re_password')==mypost('password'))
							$data['password']	=	md5(mypost('password'));
						else
							$this->MYERROR[]	= 'passwords are not the same';
					// check email 
					if($this->model->countrows(array('email'=>mypost('email')))>0)
						$this->MYERROR[] = sprintf(lang('customer_msg_exist'),mypost('email'));
					(!$this->isSubdomain(mypost('subdomain')) && $this->MYERROR[] = lang('notSubdomain'))
					or ($this->checkSubdomain(mypost('subdomain')) && $this->MYERROR[] = lang('subdomain_exist'));
					if(mypost('domain')!=''){
					(!myIsDomain(mypost('domain')) && $this->MYERROR[] = lang('notDomain'))
					or ($this->checkDomain(mypost('domain')) && $this->MYERROR[] = lang('domain_exist'));
					}
					//insert to database
					if(empty($this->MYERROR))
					if($this->model->insert(array(
						'password'		=>	md5(mypost('password')),
						'active'		=> 	1,	
						'subdomain'		=>	mypost('subdomain'),
						'domain'		=> 	mypost('domain'),
						'displayname'	=>	mypost('displayname'),
						'email'			=>	mypost('email'),
						'address'		=>	mypost('address'),
						'phone'			=>	mypost('phone'),
						'expried'		=> strtotime(mypost('expired')),
						'group'			=>	mypost('group'),			
						'sc_cuid'		=>	@$this->appmanager->user['username'],
						'sc_uuid'		=>	@$this->appmanager->user['username'],
						'sc_ctime'		=>	time(),
						'sc_utime'		=>	time(),
						'sc_status'		=>	1,
					)))
					{
						@mkdir('uploadsys/'.mypost('username'));
						!(mypost('action')=='save_new')  or myredirect('administrator/customer/add');
						!(mypost('action')=='save_back') or myredirect('administrator/customer');
						myredirect('administrator/customer/edit/'.$this->model->insert_id());
					}
				break;
			}
		}
		$this->action	=	__function__ ;
		myview('index',array('CONTENT'=>array('customer/add',$this)));
	}
	function edit($id=0)
	{	
		$this->model->check_group(__METHOD__);
		$this->action	=	__function__;
		$this->ROW = @array_shift($this->model->select(array('id'=>intval($id))));
		!empty($this->ROW) or show_404();
		if(!empty($_POST))
		{
			switch(mypost('action'))
			{
				case 'back'				: myredirect('administrator/customer'); break;
				case 'save_new'		:
				case 'save_back'	:
				case 'save'				:
					//Valid input
					$this->MYERROR = myvalid(mypost(),array($this->MYRULES['username'],$this->MYRULES['email']));
					if(@$this->MYERROR) break;
					(!$this->isSubdomain(mypost('subdomain')) && $this->MYERROR[] = lang('notSubdomain'))
					or ($this->model->countrows(array('id <>'=>$this->ROW['id'],'subdomain'=>mypost('subdomain'))) && $this->MYERROR[] = lang('subdomain_exist'));
					if(mypost('domain')!=''){
						(!myIsDomain(mypost('domain')) && $this->MYERROR[] = lang('notDomain'))
						or ($this->model->countrows(array('id <>'=>$this->ROW['id'],'domain'=>mypost('domain'))) && $this->MYERROR[] = lang('domain_exist'));
					}
					$data = array(
						'displayname'	=>	mypost('displayname'),
						'email'			=>	mypost('email'),
						'address'		=>	mypost('address'),
						'phone'			=>	mypost('phone'),
						'group'			=>	mypost('group'),
						'expried'		=> strtotime(mypost('expired')),						
						'sc_uuid'					=>	@$this->appmanager->customer['id'],
						'sc_utime'					=>	time()	
						);
					//  check password
					if(mypost('password')!=null)
						if(mypost('re_password')==mypost('password'))
							$data['password']	=	md5(mypost('password'));
						else
							$this->MYERROR[]	= 'passwords are not the same';

					if(empty($this->MYERROR)){
					if($this->model->update(
						$data
					,array(
						'id'						=>	$this->ROW['id']
					)))
					{
						//Delete old file
						empty($_FILES['file']['name'])	 or @unlink($this->ROW['file']);
						!(mypost('action')=='save_new')  or myredirect('administrator/customer/add');
						!(mypost('action')=='save_back') or myredirect('administrator/customer');
						myredirect('administrator/customer/edit/'.$this->ROW['id']);
					}}
					break;
			}
			$this->ROW = arraymerkey($this->ROW,mypost());
		}
		myview('index',array('CONTENT'=>array('customer/add',$this)));
	}
	function detail( $id = null){
		$this->model->check_group(__METHOD__);
		$this->MYROW = @array_shift($this->model->select(array('id'=>intval($id)))); 
		myview('index',array('CONTENT'=>array('customer/detail',$this)));
//abc
	}
	protected function checkDomain($domain)
	{
		return $this->model->countrows(array('domain'=>$domain));
	}
	protected function checkSubdomain($sub)
	{
		return $this->model->countrows(array('subdomain'=>$sub));
	}
	protected function isSubdomain($sub)
	{
		return preg_match('/^[a-z0-9]+$/',strtolower($sub));
	}	
}