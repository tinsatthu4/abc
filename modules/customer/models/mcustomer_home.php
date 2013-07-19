<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class mcustomer_home extends Home_model {
	protected $table = 'customer';
	private $customer = array ();
	function __construct(){
		parent::__construct($this->table);
	}
	
	function select($where = '', $order = '', $of = 0, $pp = 0) {
		$data = parent::select($where,$order,$of,$pp);
		return $data;
	}
	
	function update($data, $where = '') {
		
		if(isset($data['options']))
		{
			$data['options'] = json_encode(empty($data['options'])?array():$data['options']);
		}
		return parent::update ( $data, $where );
	}
	function insert($data)
	{
		$data['password'] = md5($data['password']);
		if(!isset($data['options'])) $data['options'] = json_encode(array());
		return parent::insert($data);
	}
	function login($email, $password) {
		$customer = array_shift ( $this->select ( array (
				'email' => $email,
				'password' => md5($password),
// 				'active	' => 1,
// 				'sc_status ' => 1, 
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
		$_SESSION ['customer'] = array ();
		unset ( $_SESSION ['customer'] );
	}

}
?>