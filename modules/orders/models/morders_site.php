<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class morders_site extends Customer_Model {
	function __construct() {
		parent::__construct ( 'orders' );
	}
	function select($where = '', $order = '', $of = 0, $pp = 0) {
		$data = parent::select ( $where, $order, $of, $pp );
		foreach ( $data as &$val ) {
			$this->db->join ( 'products', 'id_pro = id' );
			$val ['orders_info'] = parent::_select ( 'orders_info', array (
					'id_order' => $val ['id'] 
			) );
			$val ['options'] = json_decode ( $val ['options'], true );
		}
		return $data;
	}
	function insert($data) {
		$data ['options'] = json_encode ( $data ['options'] );
		return parent::insert ( $data );
	}
	function insertOrders_info($data) {
		return parent::_insert ( 'orders_info', $data );
	}
	function deleteOrders_info($where = '') {
		return parent::_delete ( 'orders_info', $where );
	}
}
?>