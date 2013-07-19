<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class User_admin extends Admin_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'user/muserAdmin' );
		$this->load->languages ( array (
				"user/userAdmin",
				"core/common" 
		), $this->appmanager->LANGUAGE );
		$this->model = &$this->muser;
		$this->config->load ( 'form_validate' );
		$this->RU = $this->config;
	}
	function index() {
	}
	function logout() {
		$this->model->logout ();
		myredirect ( 'admin/user/login' );
	}
	function login($url = '') {
		if ($this->model->is_admin ())
			myredirect ( 'admin' );
		if (mypost ()) {
			$this->MYERROR = myvalid ( mypost (), $this->RU->item ( 'ad_login' ) );
			if (empty ( $this->MYERROR )) {
				if ($this->model->login ( mypost ( 'username' ), md5 ( mypost ( 'password' ) ) )) {
					$this->model->update ( array (
							'last_login' => time () 
					), array (
							'username' => mypost ( 'username' ) 
					) );
					$url = (isset ( $_SESSION ['redirect'] )) ? $_SESSION ['redirect'] : mysiteurl ( 'admin' );
					unset ( $_SESSION ['redirect'] );
					redirect ( $url );
				
				}
				$this->MYERROR [] = lang ( 'user_err' );
			}
		}
		myview ( 'login', array (
				'CONTENT' => array (
						'_login',
						$this 
				) 
		) );
	}
	function forgot() {
		if ($this->model->is_login ())
			myredirect ( 'admin' );
		if (mypost ()) {
			$this->ER = myvalid ( mypost (), array (
					$this->RU->item ( 'email' ) 
			) );
			if (! $this->ER) {
				$row = @array_shift ( $this->model->select ( array (
						'group >=' => 5,
						'email' => mypost ( 'email' ) 
				) ) );
				if (@$row) {
					$newpass = random_string ();
					$this->model->update ( array (
							'password' => md5 ( $newpass ) 
					), array (
							"id" => $row ['id'] 
					) );
					myemail ( '', 'administrator', mypost ( 'email' ), lang ( 'subject_mail_forgot' ), "password : " . $newpass );
					$this->SU = lang ( 'su_forgot' );
				} else
					$this->ER = lang ( "err_forgot" );
			}
		}
		myview ( 'login', array (
				'CONTENT' => array (
						'forgot',
						$this 
				) 
		) );
	}
	function profile() {
		if (! $this->model->is_admin ())
			myredirect ( "admin" );
		if (mypost ()) {
			$this->ER = myvalid ( mypost (), array (
					$this->MYRULES ['password'],
					$this->MYRULES ['email'],
					$this->MYRULES ['confirpassword'] 
			) );
			if (! $this->ER) {
				if ($this->model->update ( array (
						"password" => md5 ( mypost ( 'password' ) ),
						"email" => mypost ( 'email' ) 
				), array (
						"username" => mypost ( 'username' ),
						"password" => md5 ( mypost ( 'old_password' ) ) 
				) ))
					$this->SU = lang ( 'su_profile' );
				else
					$this->ER = lang ( "er_profile" );
			}
			$this->ROW = mypost ();
		} else
			$this->ROW = array_shift ( $this->model->select ( array (
					"username" => $_SESSION ['user'] ['username'] 
			) ) );
		
		myview ( "index", array (
				"CONTENT" => array (
						'user/profile',
						$this 
				) 
		) );
	}
	function active() {
	
	}
}