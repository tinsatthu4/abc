<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class mcustomer_administrator extends Order_Model {
	function __construct() {
		parent::__construct ( 'customer' );
		@session_start ();
	}
	function select($where = '', $order = '', $of = 0, $pp = 0) {
		$rows = parent::select ( $where, $order, $of, $pp );
		foreach ( $rows as &$row ){
			$row ['activeInfo'] = json_decode ( @$row ['activeInfo'], true );
		}	
		return $rows;
	}
	function insert($data) {
		return parent::insert ( $data );
	}
	function update($data, $where = '') {
		if (array_key_exists ( 'activeInfo', $data ))
			$data ['activeInfo'] = json_encode ( is_array ( @$data ['activeInfo'] ) ? $data ['activeInfo'] : array () );
		return parent::update ( $data, $where );
	}
	private $customer = array ();
	function now() {
		return $this->is_login () ? $this->customer : array ();
	}
	function login($username, $password) {
		$customer = array_shift ( $this->select ( array (
				'username' => $username,
				'password' => $password,
				'active' => 1,
				'sc_status ' => 1 
		) ) );
		if (! empty ( $customer )) {
			$this->update ( array (
					'last_update' => time () 
			), array (
					'id' => $customer ['id'] 
			) );
			$_SESSION ['customer'] = $customer;
			if ($customer ['group'] >= 5)
				$_SESSION ['customer'] ['admin'] = true;
			$this->customer = $customer;
			return true;
		} else
			$this->logout ();
		return false;
	}
	function is_admin() {
		if (! $this->is_login () || ! isset ( $_SESSION ['customer'] ['admin'] ) || $_SESSION ['customer'] ['admin'] == false)
			return false;
		return true;
	}
	function is_login() {
		if (empty ( $_SESSION ['customer'] ['username'] ) && empty ( $_SESSION ['customer'] ['password'] ))
			return false;
		if (! empty ( $customer ['username'] ) && ! empty ( $customer ['password'] ) && @$_SESSION ['customer'] ['username'] == @$customer ['username'] && @$_SESSION ['customer'] ['password'] == @$customer ['password'])
			return true;
		return $this->login ( $_SESSION ['customer'] ['username'], $_SESSION ['customer'] ['password'] );
	}
	function logout() {
		if (isset ( $_SESSION ['customer'] ['admin'] ))
			$_SESSION ['customer'] ['admin'] = false;
		$_SESSION ['customer'] = array ();
	}
}
