<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_administrator extends Administrator_Controller
{
	private $model;
	function __construct()
	{
		parent::__construct();
		$this->load->models(array('user/muser_administrator'));
		$this->load->languages(array(
			'core/common','user/user','common'
		),$this->appmanager->LANGUAGE);
		$this->model = &$this->muser_administrator;
		$this->config->load('form_validate');
		$this->RU = $this->config;
		$this->MYRULES 	= 	array(
			'username'				=>	array('username','lang:user_username',
				'trim|required|min_length[5]|max_length[24]'),
			'password'				=>	array('password','lang:user_password',
				'trim|required|min_length[5]|max_length[24]'),
			'confirpassword'				=>	array('confirpassword','lang:user_confirpassword',
					'trim|required|min_length[5]|max_length[24]|matches[password]'),
			'email' 					=>	array('email','lang:user_email',
				'trim|required|valid_email'),
			'captcha'				=> array('captcha','captcha','required|matches[re_captcha]'),
		);
		
	}
	function index($page = 1)
	{
		/* Begin action */
		if(!empty($_POST))
		{
			$ids = mypost('id');
			switch(mypost('action'))
			{
				case 'add'		:	myredirect('administrator/user/add'); break;
				case 'edit'		:	myredirect('administrator/user/edit/'.$ids[0]); break;
				case 'delete'	: 	
				// kiem tra quyen module
				$this->model->check_group('User_administrator::delete');
				$this->db->where_in('id',$ids); $this->model->delete(); break;
				case 'inactive'	:	
				$this->model->check_group('User_administrator::edit');
				$this->db->where_in('id',$ids); $this->model->update(array('sc_status'=>0)); break;
				case 'active'	: 	
				$this->model->check_group('User_administrator::edit');
				$this->db->where_in('id',$ids); $this->model->update(array('sc_status'=>1)); break;
				case 'order'	: 	
				$this->model->check_group('User_administrator::edit');
				$this->model->orderlist(mypost('sc_order')); break;
				case 'reorder'	: 	
				$this->model->check_group('Modules_administrator::edit');
				$this->model->ordersort(array('sc_ctime'=>'asc')); break;
			}
		}else{
			// kiem tra quyen module
			$this->model->check_group(__METHOD__);
			//  ket thuc kiem tra quyen module
		}
		/* End action */
		//* Begin pagging */
		
		$pp = 20;
		$tr = $this->model->countrows ();
		$tp = ceil ( $tr / $pp );
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));
		
		$this->MYPAGGING 	= htmlpagging($page,$tp,mysiteurl('administrator/user/index/[x]'));
		$this->MYROWS = arraypagging ( $this->model->select ( '', array (
				'sc_order' => 'desc' 
		), $pp * ($page - 1), $pp ), $pp, $page );
		/* End pagging */

		$this->count_rows	=	$tr;

		myview('index',array('CONTENT'=>array('user/index',$this)));
	}
	function logout()
	{
		$this->model->logout();
		myredirect('administrator/user/login');
	}
	function login($url='')
	{
		if($this->model->is_admin()) myredirect('administrator');
		if(mypost()){
			//Valid input
			$_POST['re_captcha'] = @$_SESSION['captcha'];
			$this->MYERROR = myvalid(mypost(),array(
				$this->MYRULES['username'],
				$this->MYRULES['password'],
				$this->MYRULES['captcha'],	
			));
			if(empty($this->MYERROR) && $_SESSION['captcha']==mypost('captcha'))
			{
				if($this->model->login(mypost('username'),md5(mypost('password'))))
				{
					$this->model->update(array('last_login'=>time()),array('username'=>mypost('username')));	
					myredirect("administrator");		
				}
				$this->MYERROR[] = lang('user_err');
			}
		}
		myview('login',array('CONTENT'=>array('_login',$this)));
	}
	function forgot()
	{
		if($this->model->is_admin()) myredirect('admin');
		if(mypost())
		{
			$this->ER = myvalid(mypost(),array($this->MYRULES['email']));
			if(!$this->ER)
			{
				$row = @array_shift($this->model->select(array('group >='=>5,'email'=>mypost('email'))));
				if(@$row)
				{
					$newpass = random_string();
					$this->model->update(array('password'=>md5($newpass)),array("id"=>$row['id']));
					myemail('', 'administrator',mypost('email'),lang('subject_mail_forgot'),"password : ".$newpass);
					$this->SU = lang('su_forgot').$newpass;
				}
			}
		}
		myview('login',array('CONTENT'=>array('forgot',$this)));
	}
	function profile()
	{	
		if(!$this->model->is_admin()) myredirect("administrator");
		if(mypost())
		{
			$this->ER = myvalid(mypost(),array($this->MYRULES['password'],$this->MYRULES['email'],$this->MYRULES['confirpassword']));
			if(!$this->ER)
			{
				if($this->model->update(array(
						"password"=>md5(mypost('password')),
						"email"=>mypost('email')		
						),
						array("username"=>$_SESSION['user']['username'],"password"=>md5(mypost('old_password')))
						))
				$this->SU = lang('su_profile');
				else $this->ER = lang("er_profile");
			}
			$this->ROW = mypost();
		} else 
		$this->ROW = array_shift($this->model->select(array("username"=>$_SESSION['user']['username'])));
		
		myview("index",array("CONTENT"=>array('user/profile',$this)));
	}

	function add()
	{
		$this->action = __function__;

		$this->model->check_group(__METHOD__);
		if(!empty($_POST))
		{
			switch(mypost('action'))
			{
				case 'back'				: myredirect('administrator/user'); break;
				case 'save_new'		:
				case 'save_back'	:
				case 'save'				:
					//Valid input
					$this->MYERROR = myvalid(mypost(),array($this->MYRULES['username']));

					/* Begin upload */
					$file = '';
					if(!empty($_FILES['file']['name']))
					{
						$upload = myupload('file',$this->MYRULES['file']);
						if(!empty($upload))
							$file 	= $this->MYRULES['file']['upload_dir'].@$upload['file_name'];
						else 
							$this->MYERROR[]	=	$this->MYRULES['file']['note'];
					}
					
					/* End upload */

					//  check password
					if(mypost('password')!=null)
						if(mypost('re_password')==mypost('password'))
							$data['password']	=	md5(mypost('password'));
						else
							$this->MYERROR[]	= 'passwords are not the same';
					// check username
					if($this->model->countrows(array('username'=>mypost('username')))>0)
						$this->MYERROR[] = sprintf(lang('user_msg_exist'),mypost('username'));
					// check email 
					if($this->model->countrows(array('email'=>mypost('email')))>0)
						$this->MYERROR[] = sprintf(lang('user_msg_exist'),mypost('email'));
					// die();
					if(empty($this->MYERROR))
					if($this->model->insert(array(
						'username'		=>	mypost('username'),
						'password'		=>	md5(mypost('password')),
						'displayname'	=>	mypost('displayname'),
						'email'			=>	mypost('email'),
						'image'			=>	mypost('image'),
						'info'			=>	mypost('info'),
						'about'			=>	mypost('about'),
						'address'		=>	mypost('address'),
						'phone'			=>	mypost('phone'),
						'group_id'		=>	mypost('group'),
						'group'			=>	mypost('group'),
						'file'			=>	@$file,
						
						'sc_cuid'		=>	@$this->appmanager->USER['username'],
						'sc_uuid'		=>	@$this->appmanager->USER['username'],
						'sc_ctime'		=>	time(),
						'sc_utime'		=>	time(),
						'sc_status'		=>	1,
						'sc_order'		=>	$this->model->ordermax()+1
					)))
					{
						!(mypost('action')=='save_new')  or myredirect('administrator/user/add');
						!(mypost('action')=='save_back') or myredirect('administrator/user');
						myredirect('administrator/user/edit/'.$this->model->insert_id());
					}
					//Delete file uploaded
					empty($file) or @unlink($file);
				break;
			}
		}
		$this->action	=	__function__ ;
		$this->MYGROUPS	=	$this->mgroup_administrator->select();
		myview('index',array('CONTENT'=>array('user/add',$this)));
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
				case 'back'				: myredirect('administrator/user'); break;
				case 'save_new'		:
				case 'save_back'	:
				case 'save'				:
					//Valid input
					$this->MYERROR = myvalid(mypost(),array($this->MYRULES['username'],$this->MYRULES['email']));
					
					/* Begin upload */
					$file = $this->ROW['file'];
					if(!empty($_FILES['file']['name']))
					{
						$upload = myupload('file',$this->MYRULES['file']);
						if(!empty($upload))
							$file 	= $this->MYRULES['file']['upload_dir'].@$upload['file_name'];
						else $this->MYERROR[]	=	$this->MYRULES['file']['note'];
					}

					/* End upload */
					// mybugview(mypost()); 
					// die();
					$data = array(
						'username'		=>	mypost('username'),
						'displayname'	=>	mypost('displayname'),
						'email'			=>	mypost('email'),
						'image'			=>	mypost('image'),
						'info'			=>	mypost('info'),
						'about'			=>	mypost('about'),
						'address'		=>	mypost('address'),
						'phone'			=>	mypost('phone'),
						'group_id'		=>	mypost('group'),
						'group'			=>	mypost('group'),
						'file'			=>	@$file,
						
						'sc_uuid'					=>	@$this->appmanager->USER['id'],
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
						!(mypost('action')=='save_new')  or myredirect('administrator/user/add');
						!(mypost('action')=='save_back') or myredirect('administrator/user');
						myredirect('administrator/user/edit/'.$this->ROW['id']);
					}}
					else{
						mybugview($this->MYERROR);
					}

					//Delete uploaded file
					empty($_FILES['file']['name']) or empty($file) or @unlink($file);
				break;
			}
			$this->ROW = arraymerkey($this->ROW,mypost());
		}
		$this->MYGROUPS	=	$this->mgroup_administrator->select();
		$this->MYPIDS = arraymake(arraymer(
			array(array('id'=>0,'_txttitle'=>lang('_pid_root')))
		),'id','_txttitle');
		myview('index',array('CONTENT'=>array('user/add',$this)));
	}
	function detail( $id = null){
		$this->model->check_group(__METHOD__);
		$this->MYROW = @array_shift($this->model->select(array('id'=>intval($id)))); 
		myview('index',array('CONTENT'=>array('user/detail',$this)));
	}
	function uploadimage(){
		myview('uploadimage',array('CONTENT'=>array('user/upload',$this)));	
	}
	function config(){
		$this->ROW = array_shift($this->model->select(array('id'=>$_SESSION['user']['id'])));
		$options = $this->ROW['options'];
		if(mypost())
		switch (mypost('action')){
			case 'SEO':
				if(!@$options['title'] && !@$options['keywords'] && !@$options['description']){
					$options = @array_merge($options,mypost('SEO')); 
				}else
				$options = @array_replace_recursive($options,mypost('SEO'));
				$this->model->update(array('options'=>json_encode($options)),array('id'=>$this->ROW['id']));
				$this->ROW['options'] = @array_replace_recursive($this->ROW['options'],$options); 
			break;
			case 'yahoo':
			if(!@$options['yahoo_nick'] && !@$options['yahoo_name'])
			$options = array_merge($options,mypost('yahoo'));
			else
			$options = array_replace_recursive($options,mypost('yahoo'));
			$this->model->update(array('options'=>json_encode($options)),array('id'=>$this->ROW['id']));
			$this->ROW['options'] = @array_replace_recursive($this->ROW['options'],$options);
			break;
			case 'skype':
			if(!@$options['skype_nick'] && !@$options['skype_name'])
				$options = array_merge($options,mypost('skype'));
			else
				$options = array_replace_recursive($options,mypost('skype'));
			$this->model->update(array('options'=>json_encode($options)),array('id'=>$this->ROW['id']));
			$this->ROW['options'] = @array_replace_recursive($this->ROW['options'],$options);			
			break;
			case 'other':
			if(!@$options['sologan'])
				$options = array_merge($options,mypost('sologan'));
			else
				$options = array_replace_recursive($options,mypost('sologan'));
			if(!@$options['logo'])
				$options = array_merge($options,mypost('logo'));
			else
				$options = array_replace_recursive($options,mypost('logo'));
		
			$this->model->update(array('options'=>json_encode($options)),array('id'=>$this->ROW['id']));
			$this->ROW['options'] = @array_replace_recursive($this->ROW['options'],$options);		
			break;
		}
		myview('index',array('CONTENT'=>array('user/config',$this)));
	}
}
