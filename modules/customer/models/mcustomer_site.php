<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class mcustomer_site extends Base_Model {
	protected $table = 'customer';
	function __construct() {
		parent::__construct ( $this->table );
		@session_start ();
	}
	
	function select($where = '', $order = '', $of = 0, $pp = 0) {
		$data = parent::select ( $where, $order, $of, $pp );
		foreach($data as &$val){
			$val['options'] = json_decode($val['options'],true);
			if(@$val['options']['logo'])
				$val['options']['logo'] = base_url($val['options']['logo']);
		}
		return $data;
	}
	
	function update($data, $where = '') {
		return parent::update ( $data, $where );
	}
	
	private $customer = array ();
	function login($username, $password) {
		$customer = array_shift ( $this->select ( array (
				'username' => $username,
				'password' => $password,
				'activeRegister <>' => 0,
				'sc_status ' => 1 
		) ) );
		if (! empty ( $customer )) {
			$this->update ( array (
					'sc_utime' => time () 
			), array (
					'id' => $customer ['id'] 
			) );
			$_SESSION ['customer'] = $customer;
			return true;
		} else
			$this->logout ();
		return false;
	}
	function is_login() {
		if (! isset ( $_SESSION ['customer'] ) || empty ( $_SESSION ['customer'] ))
			return false;
		return true;
	}
	function logout() {
		unset ( $_SESSION ['customer'] );
		$_SESSION ['customer'] = array ();
	}
	function makeData($mypost = array()) {
		$data = array ();
		$list = $this->db->list_fields ( $this->table );
		foreach ( $list as $val )
			if (isset ( $mypost [$val] ))
				$data [$val] = $mypost [$val];
		if (in_array ( "sc_utime", $list ))
			$data ['sc_utime'] = time ();
		return $data;
	}
}