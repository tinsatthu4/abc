<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Customer_admin extends Admin_Controller {
	private $model;
	function __construct() {
		parent::__construct ();
		$this->load->helpers ( array (
				'core/ckeditor',
				'form'
		) );
		$this->load->model ( 'customer/mcustomer_admin' );
		$this->load->languages ( array (
				"customer/customer",
				"customer/customer_attr",
				"customer/customer_valid",
				'core/common' 
		), $this->appmanager->LANGUAGE);
		$this->model = $this->mcustomer_admin;
		$this->config->load('form_validate');
		$this->RU = $this->config;
	}
	function logout() {		
		$this->model->logout ();
		myredirect ( 'admin/customer/login' );
	}
	function login($url = '') {
		if ($this->model->is_login ())
		if(empty($url)) myredirect('admin');
		else 
			redirect ( prep_url(base64_decode($url)) );
		if (mypost ()) {
			$this->MYERROR = myvalid ( mypost (), $this->RU->item ( 'ad_login' ) );
			if (empty ( $this->MYERROR )) {	
				if(mypost('captcha') != @$_SESSION['captcha'])
					@$this->MYERROR[] = lang('login_error_captcha');
				else if ($this->model->login ( mypost ( 'email' ), md5 ( mypost ( 'password' ) ) )) {
					$this->model->update ( array (
							'sc_utime' => time () 
					), array (
							'email' => mypost ( 'email' ) 
					) );
					if(empty($url)) myredirect('admin');
					else 
					redirect ( prep_url(base64_decode($url)) );

				}else
				$this->MYERROR [] = lang ( 'customer_err' );
			}
		}
		myview ( 'login', array (
				'CONTENT' => array (
						'_login',
						$this 
				) 
		) );
	}
	function edit()
	{
		switch(mypost('action'))
		{
			case 'profile':
				$this->ER = myvalid(mypost(),array($this->RU->item('ad_cus_name'),$this->RU->item('email')));
				$checkEmail = array_shift($this->model->select(array(
						"id <>" => $_SESSION['customer']['id'],
						"email" => mypost('email')
				)));
				$checkEmail && $this->ER = array_merge($this->ER,array(lang('er_email')));
				if($this->ER  || $checkEmail) break;
				$data = $this->model->makeData(mypost());
				if($this->model->update ($data,array(
						"id" => $this->appmanager->CUSTOMER_ID ))){
					$this->SU = lang('_dbsu');
				}
				else $this->ER = lang('_dber');
				break;
			case 'account':
				$this->ER = myvalid(mypost(),$this->RU->item('customer_account'),'','Customer');
				if (! $this->ER) {
					$checkPass = array_shift ($this->model->select(array(
							"username" => $_SESSION ['customer'] ['username'],
							"password" => md5 ( mypost ( 'oldpassword' ) )
					) ) );
					if ($checkPass)
						$this->model->update ( array (
								"password" => md5 ( mypost ( 'password' ) )
						), array (
								"username" => $_SESSION ['customer'] ['username']
						) ) && $this->SU = lang ( 'su_profile' );
					else
						$this->ER = lang ( 'er_pass' );
				}				
				break;
		}
		$_SESSION['customer'] = $this->ROW = array_shift($this->model->select(array("id"=>$this->appmanager->CUSTOMER_ID)));
		myview("index",array("CONTENT"=>array('customer/edit',$this)));
	}
	function config()
	{
		if(mypost('config_common'))
		{
			$data['options'] = mypost('options');
			$data['options']['footer'] = mypost('content');
			if(isUrlImg($data['options']['config_logo']))
				$data['options']['config_logo'] = getPath($data['options']['config_logo']);
			if(isUrlImg($data['options']['config_favicon']))
				$data['options']['config_favicon'] = getPath($data['options']['config_favicon']);
				
			$this->model->update($data,array('id'=>$_SESSION['customer']['id']));
		}
		if(mypost('updateDomain'))
		{
			$rule = array(
			array('subdomain','lang:subdomain','required|trim|min_length[3]|max_length[20]'),		
			);
			$this->ER = myvalid(mypost(),$rule);
			if(mypost('domain')!=$_SESSION['customer']['domain'] && mypost('domain') !='')
			{
				(!myIsDomain(mypost('domain')) && $this->ER[] = lang('notDomain')) or
				($this->model->countrows(array('id <>'=>$_SESSION['customer']['id'],'domain'=>myGetDomain(mypost('domain')))) 
				&& $this->ER[] = lang('domain_exist'));
			}
			if(mypost('subdomain') != $_SESSION['customer']['subdomain']){
				(!preg_match('/^[a-zA-Z0-9]+$/', mypost('subdomain')) && $this->ER[] = lang('notSubdomain')) or
				($this->model->countrows(array('id <>'=>$_SESSION['customer']['id'],
						'subdomain'=>mypost('subdomain'))) && $this->ER[] = lang('subdomain_exist'));
			}
			if(!@$this->ER)
			{
				$this->SU = array();
				$data['subdomain'] = strtolower(mypost('subdomain'));
				(mypost('domain')!='' && $data['domain'] = myGetDomain(mypost('domain')))
				or 
				$data['domain'] = '';
				$this->model->update($data,array('id'=>$_SESSION['customer']['id'])) && $this->SU[] = lang('su_domain');
			}
		}
		$this->ROW = array_shift($this->model->select(array('id'=>$_SESSION['customer']['id'])));
		$this->ROW or show_404();
		myview("index",array("CONTENT"=>array('customer/config',$this)));
	}

}
