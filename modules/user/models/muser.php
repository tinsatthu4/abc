<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Muser extends Order_Model {
	function __construct() {
		parent::__construct ( 'user' );
		@session_start ();
	}
	
	function select($where = '', $order = '', $of = 0, $pp = 0) {
		$rows = parent::select ( $where, $order, $of, $pp );
		foreach ( $rows as &$row )
			$row ['activeInfo'] = json_decode ( @$row ['activeInfo'], true );
		return $rows;
	}
	function insert($data) {
		$data ['activeInfo'] = json_encode ( is_array ( @$data ['activeInfo'] ) ? $data ['activeInfo'] : array () );
		return parent::insert ( $data );
	}
	function update($data, $where = '') {
		if (array_key_exists ( 'activeInfo', $data ))
			$data ['activeInfo'] = json_encode ( is_array ( @$data ['activeInfo'] ) ? $data ['activeInfo'] : array () );
		return parent::update ( $data, $where );
	}
	
	private $user = array ();
	function now() {
		return $this->is_login () ? $this->user : array ();
	}
	function login($username, $password) {
		$user = array_shift ( $this->select ( array (
				'username' => $username,
				'password' => $password,
				'activeRegister <>' => 0,
				'sc_status ' => 1 
		) ) );
		
		if (! empty ( $user )) {
			
			$this->update ( array (
					'last_update' => time () 
			), array (
					'id' => $user ['id'] 
			) );
			$_SESSION ['user'] = $user;
			
			if ($user ['group'] >= 3)
				$_SESSION ['user'] ['admin'] = true;
			$this->user = $user;
			
			return true;
		} else
			$this->logout ();
		return false;
	}
	
	function is_admin() {
		if (! $this->is_login () || ! isset ( $_SESSION ['user'] ['admin'] ) || $_SESSION ['user'] ['admin'] == false)
			return false;
		return true;
	}
	
	function is_login() {
		if (empty ( $_SESSION ['user'] ['username'] ) && empty ( $_SESSION ['user'] ['password'] ))
			return false;
		if (! empty ( $user ['username'] ) && ! empty ( $user ['password'] ) && @$_SESSION ['user'] ['username'] == @$user ['username'] && @$_SESSION ['user'] ['password'] == @$user ['password'])
			return true;
		return $this->login ( $_SESSION ['user'] ['username'], $_SESSION ['user'] ['password'] );
	}
	function logout() {
		if (isset ( $_SESSION ['user'] ['admin'] ))
			$_SESSION ['user'] ['admin'] = false;
		$_SESSION ['user'] = array ();
	}
	
	function delete($where = '') {
		$rows = $this->select ( $where );
		
		foreach ( $rows as $row )
			if (parent::delete ( array (
					'id' => $row ['id'] 
			) )) {
				$this->db->delete ( 'user', array (
						'id' => $row ['id'] 
				) );
				@unlink ( $this->config->config ['mod_path'] . $row ['file'] );
			}
		return true;
	}
}