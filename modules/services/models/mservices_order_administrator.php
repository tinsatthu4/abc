<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mservices_order_administrator extends Administrator_model
{
	function __construct(){
		parent::__construct('services_order');
	}
	function select($where='',$order='',$of=0,$pp=0)
	{
		$this->db->join('services','services.id=id_services');
		$this->db->join('customer','customer.id=customer_id');
		$this->db->select('services_order.*,services.title,services.key,customer.username as customer');
		return parent::select($where,$order,$of,$pp);
	}
	function delete($where='')
	{
		parent::_delete('services_order',$where);
	}
}
?>