<?php 
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class Customer_home extends Home_controller
{
	function __construct(){
		parent::__construct();
		$this->load->languages(array(
				'customer/customer_attr',
				'core/home',
				'core/validate',
				),$this->appmanager->LANGUAGE);
		$this->load->models(array('customer/mcustomer_home'));
		$this->model = $this->mcustomer_home;
	}
	function login()
	{
		$this->input->is_ajax_request() or show_404() && exit();
		$this->model->is_login() && show_404() && exit();
		$rule = array(
			array('email','lang:email','required|valid_email'),
			array('password','lang:password','trim|required|min_length[6]'),	
		);
		if(mypost()){
		$mess = myvalid(mypost(),$rule);
			if($mess){
				$mess['error'] = true;
				echo json_encode($mess); exit();
			}
			if($this->model->login(mypost('email'),mypost('password')))
			{
				$mess['error'] = false;
				$mess['success'] = lang('success_login');
				$mess['reload'] = true;
				echo json_encode($mess); exit();
			}
			else {
				$mess['error'] = true;
				$mess['other_error'] = lang('text_error_login');
				echo json_encode($mess); exit();
			}
		}
	}
	function logout()
	{
		!$this->model->is_login() && show_404() && exit();
		$this->model->logout();
		myredirect();
	}
	function register()
	{
		$this->input->is_ajax_request() or show_404();
		$rule = array(
		array('email','lang:email','valid_email|trim|required'),
		array('password','lang:password','trim|required|min_length[6]|max_length[20]'),
		array('confirmpassword','lang:confirmPassword','required|matches[password]'),		
		array('subdomain','lang:text_subdomain','trim|required|min_length[3]|max_length[20]'),		
		array('name','','trim|strip_tags'),
		array('address','','trim|strip_tags'),
		array('phone','','trim|strip_tags'),
		);	
		if(mypost())
		{
			$mess = myvalid(mypost(),$rule);
			if(@$mess){
			$mess['error'] = true;
			echo json_encode($mess); exit();
			}
			if($this->checkEmail(mypost('email'))) $mess['email'] = lang('email_exist');
			if($this->checkSubDomain(mypost('subdomain'))) $mess['subdomain'] = lang('subdomain_exist');
			if($this->isSubdomain(mypost('subdomain'))) $mess['subdomain'] = lang('notSubdomain');
			if(mypost('domain')!=''){
				(!myIsDomain(mypost('domain')) && $mess['domain'] = lang('notDomain')) or
				($this->checkDomain(myGetDomain(mypost('domain'))) && $mess['domain'] = lang('domain_exist'));	
			}
			if(@$mess) {$mess['error'] = true; echo json_encode($mess); exit();}
			$data = $this->model->makeData(mypost());
			$data['activeKey'] = base64_encode(mypost('email').rand(1000, 9999));
			$data['sc_ctime'] = time();
			$data['expried'] = time() + (15*24*60*60);
			$data['id_layout'] = 0;
			if(mypost('domain') != '')
			$data['domain'] = $this->getDomain(strtolower(mypost('domain')));
			if($this->model->insert($data))
			{
				$this->mailRegister($data);
				$this->mailInformation($data);				
				//tao thu muc cho customer
				$data['id'] = $this->model->insert_id();
				isset($_SESSION['customer']);
				$_SESSION['customer'] = $data;
				@mkdir('uploadsys/'.md5($data['email']));
				$mess['error'] = false;
				$mess['success'] = lang('success_register');
				$mess['reload'] = true;
				echo json_encode($mess);
				exit();
			}
			$mess['error'] = true;
			$mess['other_error'] = lang('error_db');
			echo json_encode($mess);
		}
	}
	function active($activeKey='')
	{
		$this->appmanager->IS_LOGIN && myredirect() && exit();
		$_POST['activekey'] = $activeKey;
		$ruleslist = array(array('activekey','activekey','required|valid_base64'),);
		
		myvalid(mypost(), $ruleslist) && show_404();
		
		if($this->model->countrows(array('activeKey'=>$activeKey)))
		{
			if($this->model->update(array(
					'activeKey'=>'',
					'activeTime'=>time(),
					'sc_status'=>1,
					'active'=>1				
					),array('activeKey'=>$activeKey)))
			{
				$this->SU = array(lang('text_success_active'));
			}
			else $this->ER = array(lang('text_error_db'));
		}else $this->ER = array(lang('text_error_active'));
		myview('index',array('CONTENT'=>array('customer/active',$this)));
	}
	function forgot()
	{
		$this->appmanager->IS_LOGIN && myredirect() && exit();
		$ruleslist = array(
				array('email','lang:text_email','required|trim|valid_email'),		
		);
		if(mypost())
		{
			$this->ER = myvalid(mypost(),$ruleslist);
			if(empty($this->ER) && $this->model->countrows(array("email"=>mypost('email'),'active'=>1)))
			{
				$data['activeKey'] = md5('smartwebvn.com-'.mypost('email').rand(100000,999999));
				$data['email'] = mypost('email');
				$data['href'] = mysiteurl('customer/changePassword/'.base64_encode($data['email']).'/'.$data['activeKey']);
				if($this->model->update(array('activeKey'=>$data['activeKey']),array('email'=>$data['email']))){
				$this->mailForgot($data);
				$this->SU = array(lang('text_mail_forgot_message'));
				}else
				$this->ER = array(lang('text_error_db'));
			}
			else if(empty($this->ER)) $this->ER = array(lang('text_mail_forgot_error'));
		}
		myview('index',array('CONTENT'=>array('customer/forgot',$this)));
	}	

	function changePassword($base64Email='',$activeKey='')
	{
		(empty($base64Email) or empty($activeKey)) && show_404();
		$_POST['email'] = base64_decode($base64Email);
		$_POST['activekey'] = $activeKey;
		$ruleslist = array(
			array('email','lang:text_email','required|valid_email|trim'),
			array('activekey','lang:text_activekey','required|valid_base64'),
		);
		myvalid(mypost(), $ruleslist) && show_404();
		if(mypost('changepassword') && $this->model->countrows(array('email'=>mypost('email'),'activeKey'=>mypost('activekey'),'active'=>1)))
		{
			$ruleslist = array(
				array('newpassword','lang:newpassword','required|trim|min_length[4]'),
			);
			$this->ER = myvalid(mypost(), $ruleslist);
			if(empty($this->ER)){
				$this->model->update(array(
						"password"=>md5(mypost('newpassword')),
						"activeKey"=>"",
				),array("email"=>mypost('email')));
				$this->SU = array(lang('text_success'));
			}
		} else if(mypost('changepassword')) 
			$this->ER = array(lang('text_error_changepassword'));
		myview('index',array('CONTENT'=>array('customer/changepassword',$this)));
	}
	protected function checkUsername($username)
	{
		$this->db->where('username',$username);
		return $this->model->countrows()!=0?true:false;
	}
	protected function checkEmail($email)
	{
		$this->db->where('email',$email);
		return $this->model->countrows()!=0?true:false;
	}
	protected function checkDomain($domain)
	{
		return $this->model->countrows(array('domain'=>strtolower($domain)))!=0?true:false;
	}
	protected function checkSubDomain($sub)
	{
		$this->db->where('subdomain',$sub);
		return $this->model->countrows()!=0?true:false;
	}	
	protected function isSubdomain($sub){
		return !preg_match('/^[a-zA-Z0-9]+$/', $sub);
	}
	protected function isDomain($domain){
		$domain = strtolower($domain);
		$pattern  = '/^(http:\/\/|https:\/\/)?(www\.)?\w{3,}\.{1}[a-z]{2,5}(\.{1}[a-z]{2,3})?\/?$/';
		return preg_match($pattern, $domain,$matches);
	}
	protected function getDomain($domain){
		$pattern = '/^(http:\/\/|https:\/\/)?(www\.)?([a-z0-9_]+\.{1}[a-z]{3}(\.{1}[a-z]{2,3})?)\/?$/';
		preg_match($pattern,strtolower($domain),$matches);
		return @$matches[3]; 
	}	
	protected function isUsername($user){
		return !preg_match('/^[a-zA-Z0-9_]+$/',$user);
	}
	protected function mailRegister($data)
	{
		$message = myview('customer/registermail',$data,true);
		myemail('',lang('text_from_name'), $data['email'],lang('text_mail_active_heading_title'),$message);	
	}
	protected function mailForgot($data)
	{
		$message = myview('customer/forgotmail',$data,true);
		myemail('',lang('text_from_name'), $data['email'],lang('text_mail_forgot_heading_title'),$message);
	}
	protected function mailInformation($data)
	{
		$message = myview('customer/informationmail',$data,true);
		myemail('',lang('text_from_name'), $data['email'],lang('text_mail_information_heading_title'),$message);
	}

	
}
?>