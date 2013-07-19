<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class mcustomer_admin extends Base_Model {
	protected $table = 'customer';
	function __construct() {
		parent::__construct ( $this->table );
		@session_start ();
	}
	function select($where = '', $order = '', $of = 0, $pp = 0) {
		$data = parent::select ( $where, $order, $of, $pp );
		foreach($data as &$val)
			$val['options'] = json_decode($val['options'],true);
		return $data;
	}
	function update($data, $where = '') {
		if(isset($data['options']))
		{
			$data['options'] = json_encode(empty($data['options'])?array():$data['options']);
		}
		return parent::update ( $data, $where );
	}
	private $customer = array ();
	function login($email, $password) {
		$subdomain = '/^(www\.)?([^www]+)\.smartwebvn\.com$/';
		if(preg_match($subdomain,$_SERVER['HTTP_HOST'],$maches))
			$this->db->where('subdomain',$maches[2]);
		else if($_SERVER['HTTP_HOST'] !='www.smartwebvn.com' && $_SERVER['HTTP_HOST'] !='smartwebvn.com')
			$this->db->where(array('domain'=>myGetDomain($_SERVER['HTTP_HOST']),'activedomain'=>1));
		$customer = array_shift ( $this->select( array (
				'email' => $email,
				'password' => $password,
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
			$_SESSION ['customer']['time_life'] = time()+30*60;
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
	function selectAllServices($where='',$order='',$of=0,$pp=0)
	{
		$customer = $this->select($where,$order,$of,$pp);
		foreach($customer as &$val){
		$this->db->where('customer_id',$val['id']);
		$this->db->join('services_order','services.id=id_services');
		$this->db->select('services_order.*,title,key');
		$val['services'] = arraymakes(parent::_select('services'),'key',array('sc_dtime','sc_ctime','title'));
		}
		return $customer;
	}
}